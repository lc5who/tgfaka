<?php

namespace Tests\Unit;


use App\Http\Controllers\IndexController;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{

//    protected function setUp(): void
//    {
//        parent::setUp();
//        $this->orderController = new IndexController();
//    }


    public function testCreateOrder_SuccessfulOrderCreation_ReturnsSuccess()
    {
        $data= ['product_id' => 1, 'buyer_contact' => '1234567890', 'quantity' => 1];
        $response = $this->post('/api/createOrder',$data);
        $this->assertEquals('Order created successfully', $response->original['message']);
//        $request = new Request(['product_id' => 1, 'buyer_contact' => '1234567890', 'quantity' => 1]);
//        $productData = ['stock' => 10, 'version' => 1, 'price' => 100];
////        $this->mockGetProductStockFromCacheOrDB($productData);
////        $this->mockUpdateProductStock(1);
//        $response = $this->orderController->createOrder($request);
//        $this->assertEquals('Order created successfully', $response->original['message']);
    }


}
