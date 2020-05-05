<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::query();
        if(request('sort_by') && request('sort_desc'))
            $products->orderBy(request('sort_by'), (request('sort_desc') === "true"?"desc":"asc"));
        
        return (request('items_per_page') == -1?$products->paginate():$products->paginate(request('items_per_page')));
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
     * Store a newly created resource in product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'unit' => 'required',
        ]);
        $product = New Product();
        $product->name = $request->name;
        $product->internal_id = $request->internal_id;
        $product->unit = $request->unit;
        $product->alert_level = $request->alert_level;
        $product->save();
        return Product::where('id',$product->id)->first();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return $product;
    }

    /**
     * Update the specified resource in product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'unit' => 'required',
        ]);
        
        $product->name = $request->name;
        $product->internal_id = $request->internal_id;
        $product->unit = $request->unit;
        $product->alert_level = $request->alert_level;
        $product->enabled = $request->enabled;
        $product->save();
        return $product;
    }

    /**
     * Remove the specified resource from product.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return 'Product deleted';
    }

    public function get()
    {
        $model = Product::select('id','name')->where('enabled', 1)->get();
        $formatted_model = array();
        foreach($model as $key=>$value){
            $formatted_model[$key]['value'] = $value['id'];
            $formatted_model[$key]['text'] = $value['name'];
        }
        return $formatted_model;
    }
}
