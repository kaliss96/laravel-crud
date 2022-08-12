<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProductResource;

class ProductosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Producto::latest()->paginate(10);
        return ProductResource::collection($products);
    }

    public function create(Request $request, Producto $product)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'nombre' => 'required',
            'precio' => 'required',
            'descripcion' => 'required',
            'titulo' => 'required',
            'imagen' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }
		 
        $result_file=$request->file->store("public/product/image");
		
        $product->create($request->all());
        return new ProductResource($product);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }
        $result_file=$request->file->store("public/product/image");
		
        $product = Producto::create($data);
        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Producto $product)
    {
		 
        return new ProductResource($product);
    }
	 
	 public function listProducts(Producto $product)
    {
	$products = Producto::select("*")->get();
     return json_encode($products);
      // return new ProductResource($product);
    }
	
	

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
       public function update(Request $request, Producto $product)
    {
        $data = $request->all();
         
        $validator = Validator::make($data, [
            'nombre' => 'required',
            'precio' => 'required',
            'descripcion' => 'required',
            'titulo' => 'required',
            'imagen' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }
 
		//print_r($request->file);exit;
		$name_file=$request->file("imagen")->getClientOriginalName();
        $result_file=$request->file("imagen")->storeAs("image",$name_file,"public_images");
		
        $product->where('id',$data['id'])->update(['nombre'=>$data['nombre'],'titulo'=>$data['titulo'],'precio'=>$data['precio'],'descripcion'=>$data['descripcion'],'imagen'=>$name_file]);
        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Producto $product)
    {
        $product->delete();
        return new ProductResource($product);
    }

    /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($title)
    {
        $products = Producto::where('title', 'like', '%'.$title.'%')->get();
        return ProductResource::collection($products);
    }

   public function deleteProduct(Request $request)
    { 

	    $array=$request->all();
 
        $product_row = Producto::where('id', '=', $array['id'])->first();
		
		$res_delete['message']="Producto eliminado";
        $res_delete['result']=$product_row->delete();
		return json_encode($res_delete);
    }
   public function getProduct($id)
    { 

        $product_row = Producto::where('id', '=', $id)->first();
        return json_encode($product_row);
    }
}
