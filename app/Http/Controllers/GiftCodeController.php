<?php

namespace App\Http\Controllers;

use App\Models\GiftCode;
use Illuminate\Http\Request;

class GiftCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit');
        $GiftCodes = GiftCode::query()->orderBy('id', 'desc')->paginate($limit);
        return $this->success($GiftCodes);
    }

    public function import(Request $request)
    {
        $product_id= $request->input('product_id');
        $file  = $request->file('file');
        $newfileName= date('YmdHis') . '_' . $file->getClientOriginalName();
        $newfile = $file->move(public_path('uploads'), $newfileName);
        //public_path('uploads/' . $file->getClientOriginalName())
        $reader = fopen($newfile, 'r');
        $gdata = array();
        while (($data = fgetcsv($reader, 1000, "\n")) !== FALSE) {

//            $GiftCode = GiftCode::create([
//                'product_id' => $request->input('product_id'),
//                'code' => $data[0],
//            ]);
            $ts =now();
            $gdata[] = [
                'product_id' => $product_id,
                'code' => $data[0],
                'created_at'=>$ts,
                'updated_at'=>$ts,
            ];
        }
        GiftCode::query()->insert($gdata);
        fclose($reader);
        return $this->success(null, 'Gift card imported successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|integer',
            'code' => 'required|string|unique:gift_cards,code',
        ]);

        $GiftCode = GiftCode::create($validatedData);
        return $this->success($GiftCode, 'Gift card created successfully', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GiftCode  $GiftCode
     * @return \Illuminate\Http\Response
     */
    public function show(GiftCode $GiftCode)
    {
        return $this->success($GiftCode);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GiftCode  $GiftCode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GiftCode $GiftCode)
    {
        $validatedData = $request->validate([
            'product_id' => 'sometimes|required|integer',
            'code' => 'sometimes|required|string|unique:gift_cards,code,' . $GiftCode->id,
            'used' => 'sometimes|required|boolean',
        ]);

        $GiftCode->update($validatedData);
        return $this->success($GiftCode, 'Gift card updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GiftCode  $GiftCode
     * @return \Illuminate\Http\Response
     */
    public function destroy(GiftCode $GiftCode)
    {
        $GiftCode->delete();
        return $this->success(null, 'Gift card deleted successfully', 204);
    }
}
