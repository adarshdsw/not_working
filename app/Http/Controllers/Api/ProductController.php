<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOffer;
use App\Models\ProductPrice;
use App\Models\ProductRatting;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $offset  = $request->input('offset');
        $limit   = $request->input('limit');
        $user_id = $request->input('user_id');

        $products = Product::with(['price', 'pcategory', 'brand', 'group', 'offer'])->where('user_id', $user_id)->offset($offset)->limit($limit)->orderBy('id', 'DESC')->get();;

        $total_count = Product::with(['price', 'pcategory', 'brand'])->where('user_id' ,$user_id)->count();
        
        if($products){
            $data = ['status' => true, 'code' => 200, 'products' => $products, 'total_count'=>$total_count];
        }else{
            $data = ['status' => false, 'code' => 404];
        }
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // echo "<pre>";print_r($request->all());die;
        $product = new Product;
        $product->user_id          = $request->input('user_id');
        $product->category_id      = $request->input('category_id');
        $product->subcategory_id   = $request->input('subcategory_id');
        $product->commodity_id     = $request->input('commodity_id');
        $product->brand_id     = ($request->input('brand_id')) ? $request->input('brand_id') : 0;
        $product->group_id     = ($request->input('group_id')) ? $request->input('group_id') : 0;
        $product->title        = ($request->input('title')) ? $request->input('title') : '';
        $product->description  = $request->input('description');
        $product->product_tags = ($request->input('product_tags')) ? $request->input('product_tags') : '';
        $product->product_url  = ($request->input('product_url')) ? $request->input('product_url') : '';
        $product->website_url  = ($request->input('website_url')) ? $request->input('website_url') : '';
        $product->package_size = ($request->input('package_size')) ? $request->input('package_size') : '';
        $product->package_unit = ($request->input('package_unit')) ? $request->input('package_unit') : '';
        $product->product_use  = ($request->input('product_use')) ? $request->input('product_use') : '';
        $product->specification = ($request->input('specification')) ? $request->input('specification') : '';
        $product->total_amount  = ($request->input('total_amount')) ? $request->input('total_amount') : '0.00';
        $product->state_id      = ($request->input('state_id')) ? $request->input('state_id') : '0';
        $product->district_id   = ($request->input('district_id')) ? $request->input('district_id') : '0';
        $product->city_id       = ($request->input('city_id')) ? $request->input('city_id') : '0';
        $product->is_offer       = ($request->input('is_offer')) ? '1' : '0';
        // upload product file / video
        $file = $request->file('feature_img');
        if($file){
            $filename   = $file->getClientOriginalName();
            $name       = "product";
            $extension  = $file->extension();
            $filenew    =  date('d-M-Y').'_'.str_replace($filename,$name,$filename).'_'.time().''.rand(). "." .$extension;
            $file->move(base_path('/public/uploads/products'), $filenew);
            $product->feature_img   = asset('/uploads/products/'.$filenew);
        }
        // upload product document
        /*$doc_file = $request->file('document');
        if($doc_file){
            $filename   = $doc_file->getClientOriginalName();
            $name       = "product_doc";
            $extension  = $doc_file->extension();
            $filenew    =  date('d-M-Y').'_'.str_replace($filename,$name,$filename).'_'.time().''.rand(). "." .$extension;
            $doc_file->move(base_path('/public/uploads/products'), $filenew);
            $product->document   = '/uploads/products/'.$filenew;
        }*/
        // $product->views = 0;
        $product->status = $request->input('status');
        $res = $product->save();
        if($res){
            // product price
            $product_price = new ProductPrice();
            $product_price->product_id   = $product->id;
            $product_price->approx_price = ($request->input('approx_price')) ? $request->input('approx_price') : '';
            $product_price->min_price   =  ($request->input('min_price')) ? $request->input('min_price') : '';
            $product_price->max_price   =  ($request->input('max_price')) ? $request->input('max_price') : '';
            $product_price->min_qty     =  ($request->input('min_qty')) ? $request->input('min_qty') : '';
            $product_price->unit        =  ($request->input('unit')) ? $request->input('unit') : '';
            $product_price->last_update = date('Y-m-d');
            $res = $product_price->save();

            if($request->input('is_offer') == 'true'){
                $product_offer = new ProductOffer();
                $product_offer->product_id  = $product->id;
                // $product_offer->offer_name  = $request->input('offer_name');
                $product_offer->discount    = ($request->input('discount')) ? $request->input('discount') : 0;
                $product_offer->amount      = ($request->input('amount')) ? $request->input('amount') : 0;
                $product_offer->start_offer = ($request->input('start_offer')) ? $request->input('start_offer') : '';
                $product_offer->end_offer   = ($request->input('end_offer')) ? $request->input('end_offer') : '';
                $product_offer->offer_day   = ($request->input('offer_day')) ? $request->input('offer_day') : '';
                $product_offer->offer_specification   = ($request->input('offer_specification')) ? $request->input('offer_specification') : '';
                $res = $product_offer->save();
            }
            // product offer
            return ['status' => true, 'code' => 200, 'data'=>$product, 'message'=>__('messages.response.success_product_store')];
        }else{
            return ['status' => false, 'code' => 500, 'message' => __('messages.response.failed_product_store')];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with(['price', 'offer', 'pcategory', 'psubcategory', 'commodity', 'brand', 'group'])->where('id', $id)->get();
        if($product){
            return ['status' => true, 'code' => 200, 'data'=>$product];
        }else{
            return ['status' => false, 'code' => 500, 'message' => __('messages.response.error_404')];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // dd($request->all());
        $product_id = $request->input('product_id');
        $product    = Product::find($product_id);
        if($product){
            $product->title        = ($request->input('title')) ? $request->input('title') : '';
            $product->description  = ($request->input('description')) ? $request->input('description') : '';
            $product->product_tags = ($request->input('product_tags')) ? $request->input('product_tags') : '';
            $product->product_url  = ($request->input('product_url')) ? $request->input('product_url') : '';
            $product->website_url  = ($request->input('website_url')) ? $request->input('website_url') : '';
            $product->package_size = ($request->input('package_size')) ? $request->input('package_size') : '';
            $product->package_unit = ($request->input('package_unit')) ? $request->input('package_unit') : '';
            $product->product_use  = ($request->input('product_use')) ? $request->input('product_use') : '';
            $product->specification = ($request->input('specification')) ? $request->input('specification') : '';
            $product->total_amount  = ($request->input('total_amount')) ? $request->input('total_amount') : '0.00';
            $product->is_offer       = ($request->input('is_offer')) ? '1' : '0';
            // upload product file / video
            $file = $request->file('feature_img');
            if($file){
                $filename   = $file->getClientOriginalName();
                $name       = "product";
                $extension  = $file->extension();
                $filenew    =  date('d-M-Y').'_'.str_replace($filename,$name,$filename).'_'.time().''.rand(). "." .$extension;
                $file->move(base_path('/public/uploads/products'), $filenew);
                $product->feature_img   = asset('/uploads/products/'.$filenew);
            }
            $product->status = $request->input('status');
            $res = $product->save();
            if($res){
                // product price
                $product_price = ProductPrice::find($product->id);
                if($product_price){
                    $product_price->approx_price = ($request->input('approx_price')) ? $request->input('approx_price') : '';
                    $product_price->min_price   =  ($request->input('min_price')) ? $request->input('min_price') : '';
                    $product_price->max_price   =  ($request->input('max_price')) ? $request->input('max_price') : '';
                    $product_price->min_qty     =  ($request->input('min_qty')) ? $request->input('min_qty') : '';
                    $product_price->unit        =  ($request->input('unit')) ? $request->input('unit') : '';
                    $product_price->last_update = date('Y-m-d');
                    $res = $product_price->save();
                }

                if($request->input('is_offer') == 'true'){
                    $product_offer = ProductOffer::find($product->id);
                    if($product_offer){
                        $product_offer->discount    = ($request->input('discount')) ? $request->input('discount') : 0;
                        $product_offer->amount      = ($request->input('amount')) ? $request->input('amount') : 0;
                        $product_offer->start_offer = ($request->input('start_offer')) ? $request->input('start_offer') : '';
                        $product_offer->end_offer   = ($request->input('end_offer')) ? $request->input('end_offer') : '';
                        $product_offer->offer_day   = ($request->input('offer_day')) ? $request->input('offer_day') : '';
                        $product_offer->offer_specification   = ($request->input('offer_specification')) ? $request->input('offer_specification') : '';
                        $res = $product_offer->save();
                    }
                }
                // product offer
                return ['status' => true, 'code' => 200, 'data'=>$product, 'message'=>__('messages.response.success_product_store')];
            }else{
                return ['status' => false, 'code' => 500, 'message' => __('messages.response.failed_product_store')];
            }
        }else{
            return ['status' => false, 'code' => 404, 'message' => __('messages.response.error_404')];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->input('product_id');
        $product = Product::find($id);
        if($product){
            $product->delete ();
            return ['status' => true, 'code' => 200, 'data'=>$product, 'message'=>__('messages.response.success_product_delete')];
        }else{
            return ['status' => false, 'code' => 404, 'message'=>__('messages.response.error_404')];
        }
    }
    /**
     * get all list of product group
     *
     * @return \Illuminate\Http\Response
     */
    public function productGroup(Request $request){
        $category_id = $request->input('category_id');
        $product_groups = DB::table('product_groups')->where('category_id', $category_id)->get();
        if($product_groups){
            return ['status' => true, 'code' => 200, 'data'=>$product_groups];
        }else{
            return ['status' => false, 'code' => 404, 'message' => __('messages.response.error_404')];
        }
    }

    public function userProducts(Request $request){
        $user_id    = $request->input('user_id');
        $offset     = $request->input('offset');
        $limit      = $request->input('limit');
        $search     = ($request->input('search'))?$request->input('search'):'';
        if($search){
            $products = Product::with(['pcategory', 'psubcategory', 'commodity', 'brand', 'group', 'price', 'offer'])->where('user_id', $user_id)->where('title', 'like', $search.'%')->offset($offset)->limit($limit)->orderBy('title', 'ASC')->get();
        }else{
            $products = Product::with(['pcategory', 'psubcategory', 'commodity', 'brand', 'group', 'price', 'offer'])->where('user_id', $user_id)->offset($offset)->limit($limit)->orderBy('title', 'ASC')->get();
        }
        if($products){
            return ['status' => true, 'code' => 200, 'data' => $products];
        }else{
            return ['status' => false, 'code' => 404, 'message' => __('messages.response.error_404')];
        }
    }
}
