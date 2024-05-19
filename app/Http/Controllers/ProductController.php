<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
// use Illuminate\Support\Facades\DB;
use Image;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // ==========================================================================
    //  get all products
    // ==========================================================================
    public function index()
    {
        // $products = Product::paginate(25); // 10 เป็นจำนวนรายการต่อหน้า
        // $products = Product::join('categories', 'products.product_category_id', '=', 'categories.id')->paginate(25);

        $products = Product::join('categories', 'products.product_category_id', '=', 'categories.id')
        ->select('products.*', 'categories.category_name')
        ->get();

      
        return response($products, 200);
    }

  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    //  ==========================================================================
    //  create product
    //  ==========================================================================
    public function store(Request $request)
    {


        $user = $request->auth_user;

        $validator = \Validator::make(
            $request->all(),
            [
                'product_name' => 'required|string',
                'product_stock' => 'required|integer',
                'product_price' => 'required|integer',
                
            ],
            [
                'product_name.required' => 'ระบุชื่อสินค้า',
                'product_name.string' => 'ชื่อสินค้าต้องเป็นตัวอักษร',
                'product_stock.required' => 'ระบุจำนวนสินค้า',
                'product_stock.integer' => 'จำนวนสินค้าต้องเป็นตัวเลข',
                'product_price.integer' => 'ราคาสินค้าต้องเป็นตัวเลข',
                'product_price.required' => 'ระบุราคาสินค้า',
              
            ]
        );

        //ถ้า validate ไม่ผ่านให้ส่ง error ไป  แต่ถ้าผ่านให้ทำการบันทึกข้อมูลลง database
        if (!$validator->passes()) {
            return response()->json(['status'=> 'error', 'code' => 400, 'error' => $validator->errors()->toArray()]);
        }


        $category_input = $request->input('product_category_id');
        $category = isset($category_input) ? $category_input : 1;


        $data = array(
            'product_code' => $request->product_code,
            'product_barcode' => $request->product_barcode,
            'product_name' => $request->product_name,
            'product_description' => $request->product_description,
            'product_stock' => $request->product_stock,
            'product_price' => $request->product_price,
            'product_category_id' => $category,
            'product_user_id' => $user->id,
        );

        
        $image = $request->file('product_image');

      

         // เช็คว่าผู้ใช้มีการอัพโหลดภาพเข้ามาหรือไม่
         if(!empty($image)){
                
          
     
            // อัพโหลดรูปภาพ
            // เปลี่ยนชื่อรูปที่ได้
            $file_name = "product_".time().".".$image->getClientOriginalExtension();

            // กำหนดขนาดความกว้าง และสูง ของภาพที่ต้องการย่อขนาด
            $imgWidth = 400;
            $imgHeight = 400;
            $folderupload = public_path('/images/products/thumbnail');
            $path = $folderupload."/".$file_name;

            // อัพโหลดเข้าสู่ folder thumbnail
            $img = Image::make($image->getRealPath());
            $img->orientate()->fit($imgWidth,$imgHeight, function($constraint){
                $constraint->upsize();
            });
            $img->save($path);

            // อัพโหลดภาพต้นฉบับเข้า folder original
            $destinationPath = public_path('/images/products/original');
            $image->move($destinationPath, $file_name);

            // กำหนด path รูปเพื่อใส่ตารางในฐานข้อมูล
            $data['product_image'] = url('/').'/images/products/thumbnail/'.$file_name;
           

        }else{
            $data['product_image'] = url('/').'/images/products/thumbnail/no_img.jpg';
        }


        $result = Product::create($data);


        if ($result) {

            $reponse = [
                'code' => 201,
                'status' => 'success',
                'error' => 0,
                'product' => $result,
            ];
        }else{

            $reponse = [
                'code' => 500,
                'status' => 'fail',
                'error' => 1,
                'product' => '',
            ];
        }


        return response($reponse, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // ==========================================================================
    //  get product by id
    // ==========================================================================
    public function show($id)
    {

        $product = Product::join('categories', 'products.product_category_id', '=', 'categories.id')->find($id);
        if (!$product) {

            $reponse = [
                'code' => 500,
                'status' => 'fail',
                'error' => 1,
                'product' => '',
            ];

            return response($reponse, 500);

            // return response()->json([
            //     'msg' => 'Product not found',
            // ], 404);
        }else{


            $reponse = [
                'code' => 200,
                'status' => 'success',
                'error' => 0,
                'product' => $product,
            ];

            return response($reponse, 200);
        }

        
    }

   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // ==========================================================================
    //  update product
    // ==========================================================================
    public function update(Request $request, $id)
    {
        // return $request->all();
        $user = $request->auth_user;
       
        // $request->validate([
        //     // 'product_code' => 'required|string|unique:products,product_code',
        //     // 'proudct_barcode' => 'required|string|unique:products,proudct_barcode',
        //     'product_name' => 'required|string',
        //     'product_stock' => 'required|integer',
        //     'product_price' => 'required|integer',
           
        // ]);


        $validator = \Validator::make(
            $request->all(),
            [
                'product_name' => 'required|string',
                'product_stock' => 'required|integer',
                'product_price' => 'required|integer',
                
            ],
            [
                'product_name.required' => 'ระบุชื่อสินค้า',
                'product_name.string' => 'ชื่อสินค้าต้องเป็นตัวอักษร',
                'product_stock.required' => 'ระบุจำนวนสินค้า',
                'product_stock.integer' => 'จำนวนสินค้าต้องเป็นตัวเลข',
                'product_price.integer' => 'ราคาสินค้าต้องเป็นตัวเลข',
                'product_price.required' => 'ระบุราคาสินค้า',
              
            ]
        );

        //ถ้า validate ไม่ผ่านให้ส่ง error ไป  แต่ถ้าผ่านให้ทำการบันทึกข้อมูลลง database
        if (!$validator->passes()) {
            return response()->json(['status'=> 'error', 'code' => 400, 'error' => $validator->errors()->toArray()]);
        }




        $category_input = $request->product_category_id;
        $category = isset($category_input) ? $category_input : 1;

        $data = array(
            'product_code' => $request->product_code,
            'product_barcode' => $request->product_barcode,
            'product_name' => $request->product_name,
            'product_description' => $request->product_description,
            'product_stock' => $request->product_stock,
            'product_price' => $request->product_price,
            'product_category_id' => $category,
            'product_user_id_update' => $user->id,
            
        );
        
        
        $image = $request->file('product_image');

         // เช็คว่าผู้ใช้มีการอัพโหลดภาพเข้ามาหรือไม่
         if(!empty($image)){
                
            // อัพโหลดรูปภาพ
            // เปลี่ยนชื่อรูปที่ได้
            $file_name = "product_".time().".".$image->getClientOriginalExtension();

            // กำหนดขนาดความกว้าง และสูง ของภาพที่ต้องการย่อขนาด
            $imgWidth = 400;
            $imgHeight = 400;
            $folderupload = public_path('/images/products/thumbnail');
            $path = $folderupload."/".$file_name;

            // อัพโหลดเข้าสู่ folder thumbnail
            $img = Image::make($image->getRealPath());
            $img->orientate()->fit($imgWidth,$imgHeight, function($constraint){
                $constraint->upsize();
            });
            $img->save($path);

            // อัพโหลดภาพต้นฉบับเข้า folder original
            $destinationPath = public_path('/images/products/original');
            $image->move($destinationPath, $file_name);

            // กำหนด path รูปเพื่อใส่ตารางในฐานข้อมูล
            $data['product_image'] = url('/').'/images/products/thumbnail/'.$file_name;

        }else{
            $data['product_image'] = url('/').'/images/products/thumbnail/no_img.jpg';
        }

        $product = Product::find($id);
        if(!$product) {
        
            $reponse = [
                'code' => 500,
                'status' => 'fail',
                'error' => 1,
                'product' => '',
            ];
            return response($reponse, 500);
        }else{
            $result = $product->update($data);

            $reponse = [
                'code' => 200,
                'status' => 'success',
                'error' => 0,
                'product' => $result,
            ];

            return response($reponse, 200);
        }

       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    // ==========================================================================
    //  delete product
    // ==========================================================================
    public function destroy($id)
    {

        $product = Product::find($id);
        if(!$product) {
           

            $reponse = [
                'code' => 500,
                'status' => 'fail',
                'error' => 1,
                'product' => '',
            ];
            return response($reponse, 500);
        }else{
            $result = $product->delete();
        }

        if ($result) {
            $reponse = [
                'code' => 200,
                'status' => 'success',
                'error' => 0,
                'product' => $result,
            ];

            return response($reponse, 200);
        }

       
    }





    // ==========================================================================
    //  search product
    // ==========================================================================
    




}
