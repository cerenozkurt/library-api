<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('content.store')->only('update');
    }

    public function index()
    {
        try {
            $Category = Category::orderBy('name')->get();
            if ($Category) {
                return response()->json([
                    'status code' => 200,
                    'succes' => true,
                    'Categorys' => CategoryResource::collection($Category)->pluck('name', 'id'),


                ]);
            }
            return response()->json([
                'status code' => 204,
                'success' => true,
                'message' => 'No registered Categorys.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 401,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        if (Category::where('name', '=', $request->name)->first() == null) {
            $result = Category::create([
                'name' => $request->name
            ]);

            if ($result) {
                return response()->json([
                    'status code' => 201,
                    'success' => true,
                    'message' => "New Category Add Successfully",
                ]);
            } else {
                return response()->json([
                    'status code' => 404,
                    'success' => false,
                    'message' => "New Category Add Unsuccessfully",
                ]);
            }
        }
        return response()->json([
            'status code' => 401,
            'success' => false,
            'message' => 'Category already exists.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (Category::where('name', '=', $request->name)->first() == null || $category->name == $request->name) {
            
            $this->middleware(function ($request, $next) {
                return $next($request);
            });

            $category->name = $request->name;
            $result = $category->save();

            if ($result) {
                return response()->json([
                    'status code' => 200,
                    'success' => true,
                    'message' => 'Category Update Successfully'
                ]);
            } else {
                return response()->json([
                    'status code' => 401,
                    'success' => false,
                    'message' => 'Category Update Unsuccessfully'
                ]);
            }
        }
        return response()->json([
            'status code' => 401,
            'success' => false,
            'message' => 'Category already exists.'
        ]);
    }

    public function getBooks()
    {
        try {
            $category = Category::orderBy('name')->get();
            if ($category) {
                return response()->json([
                    'status code' => 200,
                    'succes' => true,
                    'categorys' => CategoryResource::collection($category)
                ]);
            }
            return response()->json([
                'status code' => 204,
                'success' => true,
                'message' => 'No registered Categorys.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 401,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getBooksById($id)
    {
        $category = Category::find($id);

        return response()->json([
            'status code' => 200,
            'success' => true,
            'author' =>  new CategoryResource($category)
        ]);
    }

    public function search($search)
    {
        try {
            $categorysearch = Category::where('name', 'LIKE', '%' . $search . '%')->orderBy('id', 'desc')->get();
            if ($categorysearch) {
                return response()->json([
                    'success' => true,
                    'category' =>  CategoryResource::collection($categorysearch)->pluck('name', 'id')
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete($id)
    {
        $result = Category::find($id)->delete();
        if ($result) {
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => "Category Delete Successfully",
            ]);
        }
        return response()->json([
            'status_code' => 401,
            'success' => false,
            'message' => "Category Delete Unsuccessfully",
        ]);
    }
}
