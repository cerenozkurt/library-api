<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends ApiResponseController
{

    /*public function __construct()
    {
        $this->middleware('content.store')->only('update');
    }*/

    public function index()
    {
        try {
            $Category = Category::namelist();
            if ($Category) {
                return $this->apiResponse(true, "Categorys List.", 'categories', CategoryResource::collection($Category)->pluck('name', 'id'), JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(true, "No registered category.", null, null, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function store(CategoryRequest $request)
    {
        $result = Category::create([
            'name' => $request->name
        ]);

        if ($result) {
            return $this->apiResponse(true, "New Category Add Successfully", 'category', new CategoryResource($result), JsonResponse::HTTP_OK);
        } else {
            return $this->apiResponse(false, "New Category Add Unsuccessfully", null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = Category::find($id);
        if (Category::name($request->name)->first() == null || $category->name == $request->name) {
            $category->name = $request->name ?? $category->name;
            $result = $category->save();
        } else {
            return $this->apiResponse(false, "Category already exists.", null, null, JsonResponse::HTTP_NOT_FOUND);
        }



        if ($result) {
            return $this->apiResponse(true, "Category Update Successfully.", 'category', new CategoryResource($category), JsonResponse::HTTP_OK);
        } else {
            return $this->apiResponse(false, "Category Update Unsuccessfully!", null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }


    public function getBooks()
    {
        try {
            $category = Category::namelist();
            if ($category) {
                return $this->apiResponse(true, "Books of Categories", 'categories', CategoryResource::collection($category), JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(true, "No registered categories.", null, null, JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function getBooksById($id)
    {
        $category = Category::find($id);
        return $this->apiResponse(true, "Category books", 'category', new CategoryResource($category), JsonResponse::HTTP_OK);
    }

    public function search($search)
    {
        try {
            $categorysearch = Category::search($search)->get();
            if ($categorysearch) {
                return $this->apiResponse(true, "Category search", 'category', CategoryResource::collection($categorysearch)->pluck('name', 'id'), JsonResponse::HTTP_OK);
            }
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function delete($id)
    {
        $result = Category::find($id)->delete();
        if ($result) {
            return $this->apiResponse(true, 'Category Delete Successfully.', null, null, JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false, 'Category Delete Unsuccessfully!', null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    
    function deneme()
    {
        return 'deneme';
    }
}
