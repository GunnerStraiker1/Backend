<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\Product as ProductResource;
use App\Http\Resources\ProductCollection;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(Request $request, Product $product) {
        $this->request = $request;
        $this->product = $product;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $product = $this->product->all();
        if (!$product->isEmpty()) {
            return (new ProductCollection($product))->response()->setStatusCode(200);
        }
        else {
            return response()->json(NULL, 200);
        }
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
     * @param  \App\Http\StoreProductRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $validate = $request->validated();
         // Create a new product
        // $product = Product::create($request->all());
        $product = Product::create([
            'name' => $request->input('data.attributes.name'),
            'price' => $request->input('data.attributes.price')
        ]);

        // Return a response with a product json
        // representation and a 201 status code   
        // return response()->json($product,201);
        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $product = $this->product->find($id);
        if(!is_null($product)){
            return (new ProductResource($product))->response()->setStatusCode(200);
        }
        else {
            $error = ['errors' => ['code' => 'Error-2', 'title' => 'ID does not exist']];
            return response()->json($error,404);
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Request\UpdateProductRequest  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $validate = $request->validated();

        $product = $this->product->find($id);

        if (!is_null($product)) {
            $product->update([
                'name' => $request->input('data.attributes.name'),
                'price' => $request->input('data.attributes.price'),
            ]);
            return (new ProductResource($product))->response()->setStatusCode(200);
        }
        else{
            $error = ['errors' => ['code' => 'Error-2', 'title' => 'ID does not exist']];
            return response()->json($error, 404);
        }   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $product = $this->product->find($id);

        if(!is_null($product)){
            $product->delete();
            return response()->json(NULL,204);
        }
        else {
            $error = ['errors' => ['code' => 'Error-2', 'title' => 'ID does not exist']];
            return response()->json($error,404);
        }
    }
}
