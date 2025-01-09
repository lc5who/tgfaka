<?php

namespace App\Http\Controllers;

use App\Jobs\OrderCreated;
use App\Lib\mazhifu\EpayCore;
use App\Mail\InvoiceShipped;
use App\Models\Category;
use App\Models\GiftCode;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\InvoicePaid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class IndexController extends Controller
{
    //
    public function index()
    {
        $list = Category::with('products')->orderByDesc('weigh')->take(2)->get();
        $setting = Cache::get('faka',config('faka'));
        $data = [
            'list' => $list,
            'qq' => $setting['basic']['customerQQ']
        ];
        return $this->success($data);
    }

    public function search(Request $request)
    {
        $contact = $request->input('contact', "123123");
        $order= Order::with(['giftCode','product'])->where('buyer_contact', $contact)->firstOrFail();
        return $this->success($order);

    }

    public function createOrder(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'email_notify' => 'boolean',
            'payment' => 'required|string|in:alipay,wechatpay,banktransfer', // 根据实际情况调整
            'contact' => 'required|string|min:3|max:100', // 根据实际需求调整
        ]);
        $productId = $request->input('product_id', 1);
        $quantity = $request->input('quantity', 1);
        $is_email = (int)$request->input('email_notify', false);
        $selectedPayment = $request->input('payment', 'alipay');
        $buyerContact = $request->input('contact', "123123");
        $cacheKey = 'product_stock_' . $productId;
        $lockKey = 'product_stock_lock_' . $productId;
        $retryTimes = 3;
        for ($i = 0; $i < $retryTimes; $i++) {
            try {
                DB::beginTransaction();
                $productData = $this->getProductStockFromCacheOrDB($productId, $cacheKey, $lockKey);

                if (!$productData) {
                    Db::rollBack();
                    return $this->fail([], '商品不存在');
                }
                if ($productData['stock'] < $quantity) {
                    DB::rollBack();
                    return $this->fail([], '库存不足');
                }
                $newStock = $productData['stock'] - $quantity;
                $updateResult = Product::where('id', $productId)->where('version', $productData['version'])->update(['stock' => $newStock, 'version' => $productData['version'] + 1]);

                if ($updateResult === 0) {
                    DB::rollBack();
                    continue;
                }
                Cache::put($cacheKey, ['stock' => $newStock, 'version' => $productData['version'] + 1], 300);
                $total_price = $quantity * $productData['price'];
                $payLoad = [
                    'product_id' => $productId,
                    'order_sn' => $this->createOrderSn(),
                    'buyer_contact' => $buyerContact,
                    'gift_code' => '',
                    'quantity' => $quantity,
                    'total_price' => $total_price,
                    'status' => 'pending',
                    'is_email' => $is_email,
                    'payment' => $selectedPayment,
                ];
                $order = Order::query()->create($payLoad);
                DB::commit();
//                $this->createOrderAsync($buyerContact, $productId, $quantity, $productData['price'],$is_email);
                Log::info('Order created successfully');
                return $this->success([
                    'order_id' => $order->order_sn,
                    'quantity' => $quantity,
                    'total_price' => $total_price,
                    'pay_url' => url('api/getPay', ['order_id' => $order['order_sn'], 'payment' => $selectedPayment]),
                ], 'Order created successfully');
            } catch (\Exception $e) {
                DB::rollBack();

                Log::error('Order creation failed: ' . $e->getMessage());
            }
        }
        return $this->fail([], '系统繁忙');
        //掉支付接口
    }

    //订单支付回调接收方法
    public function notify(Request $request)
    {

        $orderId = $request->input('order_id');
        $order = Order::query()->with("giftCode")->where('order_sn', $orderId)->firstOrFail();

        if ($order->status === 'pending') {
            $order->status = 'completed';
            GiftCode::query()->where('used',0)->where('product_id',$order->product_id)
                ->where('order_id',0)->take($order->quantity)->update(['used'=>1,'order_id'=>$order->id]);
            $order->save();
            $order->refresh();
//            dd($order->toArray());
            if ($order->is_email) {
                Mail::to($order->buyer_contact)->send(new InvoiceShipped($order));
            }
            Log::info('Order paid successfully');
            return $this->success([], 'Order paid successfully');
        } else {
            Log::info('Order already paid');
            return $this->success([], 'Order already paid');
        }
    }


    public function getPay(Request $request)
    {
        $orderId = $request->input('order_id');
        $epay_config = [
            'apiurl' => 'https://code.330bk.com/',
            'pid' => '140118999',
            'key' => 'p0KH8o7F6Y5OH0fa6eHu56UOo7kaF7Kf',
        ];
        $parameter = array(
            "pid" => $epay_config['pid'],
            "type" => 'alipay',
            "notify_url" => "http://www.baidu.com",
            "return_url" => "http://www.baidu.com",
            "out_trade_no" => $orderId,
            "name" => "测试",
            "money" => 10,
        );
        $epay = new EpayCore($epay_config);
        return $epay->pagePay($parameter);
    }


//生成唯一订单号
    protected function createOrderSn()
    {
        return uniqid();
    }


    protected function createOrderAsync($buyerContact, $productId, $quantity, $price, $is_email)
    {
        $payLoad = [
            'product_id' => $productId,
            'order_sn' => $this->createOrderSn(),
            'buyer_contact' => $buyerContact,
            'gift_code' => '',
            'quantity' => $quantity,
            'total_price' => $quantity * $price,
            'status' => 'pending',
            'is_email' => $is_email
        ];
        $order = Order::query()->create($payLoad);
        OrderCreated::dispatch($order);
        return $order;
    }


    protected function getProductStockFromCacheOrDB($productId, $cacheKey, $lockKey): ?array
    {
        // 先从缓存读取库存
        $cachedProduct = Cache::get($cacheKey);
        if ($cachedProduct) {
            return $cachedProduct;
        }

        //加锁, 如果加锁失败，则等待重试
        $lock = Cache::lock($lockKey, 10); // 锁10秒
        try {
            if ($lock->get()) {
                //从数据库读取
                $product = Product::lockForUpdate()->find($productId);
                if ($product) {
                    // 更新缓存
                    Cache::put($cacheKey, ['stock' => $product->stock, 'version' => $product->version, 'price' => $product->price], 300); // 缓存5分钟
                    return ['stock' => $product->stock, 'version' => $product->version, 'price' => $product->price];
                }
            }
            // 等待其他进程完成
            sleep(1);
            $product = Product::lockForUpdate()->find($productId);
            if ($product) {
                // 更新缓存
                Cache::put($cacheKey, ['stock' => $product->stock, 'version' => $product->version, 'price' => $product->price], 300); // 缓存5分钟
                return ['stock' => $product->stock, 'version' => $product->version, 'price' => $product->price];
            }
            return null;


        } finally {
            optional($lock)->release();
        }
    }



}
