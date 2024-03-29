<?php  //product::create() handling error ?
              //::update()
       //DB::transaction doesn't work in storage delete, store dont rollback them if error

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\products\CreateProductRequest;
use App\Http\Requests\products\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Category;
use App\Traits\ResponseTrait;


class ProductController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = Product::all();
        return $this->responseSuccess(ProductResource::collection( $result ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request)
    {
        $category= Category::where('name',$request->category)->first() ;
        $request_array= $request->all();  
        $file = $request->file('image');
        $ext  = $file->getClientOriginalExtension(); 
        $image_name=   now()->format('YmdHis').'.'.$ext;
        $path= Storage::disk('public')->putFileAs('products_images', $file, $image_name);
        $request_array['imagePath']= $path; 
        $request_array['category_id']= $category->id;
        Product::create($request_array);    
        return $this->responseSuccess(null,'Product Created Succefully'); 

    }

    /**
     * Display the specified resource.
     */
    public function show(String $product)
    {   
        $product= Product::find($product);
        if (!empty($product))
           return $this->responseSuccess(new ProductResource($product));
        return $this->responseError(null, 'Product not found.', 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request,string $product) 
    {   
        $product= Product::find($product);

        if (empty($product))
            return $this->responseError(null, 'Product not found.', 404);
        $request_array= $request->validated();
      /*   try{ 
        DB::beginTransaction();  */
        $product->update($request_array);  

        if ($request->hasFile('image')){
            Storage::delete($product->imagePath);
            $path= $request->file('image')->store('public/products_images'); 
            $product->update(['imagePath'=> $path]);
        }
       /*  DB::commit(); */
        return $this->responseSuccess(new ProductResource($product));
        /* }
        catch(\Exception $e){
            DB::rollback();
            Log::error($e->getMessage());
            return $this->responseError(null, 'Any error occured', 500);
         } */
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    { 
        $deleted= $product->delete();

        if (!$deleted) 
            return $this->responseError(null, 'Failed to delete the product.', Response::HTTP_INTERNAL_SERVER_ERROR);
        return $this->responseSuccess('Product Deleted Succefully');
    }
}
