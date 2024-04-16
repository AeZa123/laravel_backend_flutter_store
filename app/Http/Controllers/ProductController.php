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
        $products = Product::join('categories', 'products.product_category_id', '=', 'categories.id')->paginate(25);

        $reponse = [
            'list_products' => $products,
            'status_code' => 201,
        ];

        return response($reponse, 201);
    }

  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            // 'product_code' => 'required|string|unique:products,product_code',
            // 'proudct_barcode' => 'required|string|unique:products,proudct_barcode',
            'product_name' => 'required|string',
            'product_stock' => 'required|integer',
            'product_price' => 'required|integer',
           
        ]);

        $category_input = $request->input('product_category_id');
        $category = isset($category_input) ? $category_input : 1;

        $data_product = array(
            'product_code' => $request->input('product_code'),
            'product_barcode' => $request->input('product_barcode'),
            'product_name' => $request->input('product_name'),
            'product_description' => $request->input('product_description'),
            'product_stock' => $request->input('product_stock'),
            'product_price' => $request->input('product_price'),
            'product_category_id' => $category,
            'product_user_id' => auth()->user()->id,
            
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
            $data_product['product_image'] = url('/').'/images/products/thumbnail/'.$file_name;

        }else{
            $data_product['product_image'] = url('/').'/images/products/thumbnail/no_img.jpg';
        }


        $result = Product::create($data_product);


        if ($result) {

            $msg = 'create product success';
            $status_code = 201;

            $reponse = [
                'msg' => $msg,
                'product' => $result,
                'status_code' => $status_code,
            ];
        }else{

            $msg = 'something went wrong';
            $status_code = 500;

            $reponse = [
                'msg' => $msg,
                'product' => '',
                'status_code' => $status_code,
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
    public function show($id)
    {

        $product = Product::join('categories', 'products.product_category_id', '=', 'categories.id')->find($id);
        if (!$product) {
            return response()->json([
                'msg' => 'Product not found',
            ], 404);
        }else{
            $reponse = [
                'msg' => 'get product success',
                'product' => $product,
                'status_code' => 201,
            ];
        }

        return response($reponse, 201);
        
    }

   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return $request->all();
       
        $request->validate([
            // 'product_code' => 'required|string|unique:products,product_code',
            // 'proudct_barcode' => 'required|string|unique:products,proudct_barcode',
            'product_name' => 'required|string',
            'product_stock' => 'required|integer',
            'product_price' => 'required|integer',
           
        ]);

        $category_input = $request->input('product_category_id');
        $category = isset($category_input) ? $category_input : 1;

        $data_product = array(
            'product_code' => $request->input('product_code'),
            'product_barcode' => $request->input('product_barcode'),
            'product_name' => $request->input('product_name'),
            'product_description' => $request->input('product_description'),
            'product_stock' => $request->input('product_stock'),
            'product_price' => $request->input('product_price'),
            'product_category_id' => $category,
            'product_user_id_update' => auth()->user()->id,
            
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
            $data_product['product_image'] = url('/').'/images/products/thumbnail/'.$file_name;

        }else{
            $data_product['product_image'] = url('/').'/images/products/thumbnail/no_img.jpg';
        }

        $product = Product::find($id);
        if(!$product) {
            $msg = 'Product not found';
            $status_code = 404;
            $reponse = [
                'msg' => $msg,
                'product' => '',
                'status_code' => $status_code,
            ];
            return response($reponse, $status_code);
        }else{
            $result = $product->update($data_product);
        }

        if ($result) {
            $msg = 'update product success';
            $status_code = 201;
            $reponse = [
                'msg' => $msg,
                'product' => $result,
                'status_code' => $status_code,
            ];
        }else{

            $msg = 'something went wrong';
            $status_code = 500;
            $reponse = [
                'msg' => $msg,
                'product' => '',
                'status_code' => $status_code,
            ];
        }

        return response($reponse, $status_code);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $product = Product::find($id);
        if(!$product) {
            $msg = 'Product not found';
            $status_code = 404;
            $reponse = [
                'msg' => $msg,
                'product' => '',
                'status_code' => $status_code,
            ];
            return response($reponse, $status_code);
        }else{
            $result = $product->delete();
        }

        if ($result) {
            $msg = 'delete product success';
            $status_code = 201;
            $reponse = [
                'msg' => $msg,
                'product' => $result,
                'status_code' => $status_code,
            ];
        }else{
            $msg = 'something went wrong';
            $status_code = 500;
            $reponse = [
                'msg' => $msg,
                'product' => '',
                'status_code' => $status_code,
            ];
        }

        return response($reponse, $status_code);
    }
}
