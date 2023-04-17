<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Requests\categories\CreateCategoryRequest;
use App\Http\Requests\categories\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $result = Category::all();
         return $this->responseSuccess(CategoryResource::collection( $result ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryRequest $request)
    {
         $request_array= $request->validated();
         Category::create($request_array);
         return $this->responseSuccess(null);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category) //..
    {
        return $this->responseSuccess(  new CategoryResource($category)  );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category) // are we should cast the $id to string 
    {   

        $request_array= $request->validated();
        
        
        $category->update(['name'=>$request_array['name']]);  // if the DB down or error in update ORM how we handle this error

        return $this->responseSuccess(new CategoryResource($category));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)//when the name match the route paramater name 
                                  //ficha error if the record didn't exist 
                                  // but withother name no error showen  
    {
       
        $deleted= $category_object->delete();
        if (!$deleted)
           return $this->responseError(null, 'Failed to delete the category.', 500);
        return $this->responseSuccess('Category Deleted Succefully');
    }
}