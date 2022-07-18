<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{

    public function __construct()
    {
        $this->middleware('content.store')->only('update');
    }

    public function index()
    {
        try {
            $author = Author::orderBy('name')->get();
            if ($author) {
                return response()->json([
                    'status code' => 200,
                    'succes' => true,
                    'authors' => AuthorResource::collection($author)->pluck('name', 'id'),


                ]);
            }
            return response()->json([
                'status code' => 204,
                'success' => true,
                'message' => 'No registered authors.'
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
        if (Author::where('name', '=', $request->name)->first() == null) {
            $result = Author::create([
                'name' => $request->name
            ]);

            if ($result) {
                return response()->json([
                    'status code' => 201,
                    'success' => true,
                    'message' => "New Author Add Successfully",
                ]);
            } else {
                return response()->json([
                    'status code' => 404,
                    'success' => false,
                    'message' => "New Author Add Unsuccessfully",
                ]);
            }
        }

        return response()->json([
            'status code' => 401,
            'success' => false,
            'message' => 'Author already exists.'
        ]);
    }

    public function update(Request $request, $id)
    {   
        $author = Author::find($id);
        if (Author::where('name', '=', $request->name)->first() == null || $author->name == $request->name) {

            $this->middleware(function ($request, $next) {
                return $next($request);
            });
            
            $author->name = $request->name;
            $result = $author->save();

            if ($result) {
                return response()->json([
                    'status code' => 200,
                    'success' => true,
                    'message' => 'Author Update Successfully'
                ]);
            } else {
                return response()->json([
                    'status code' => 401,
                    'success' => false,
                    'message' => 'Author Update Unsuccessfully'
                ]);
            }
        }
        return response()->json([
            'status code' => 401,
            'success' => false,
            'message' => 'Author already exists.'
        ]);
    }

    public function getBooks()
    {
        try {
            $author = Author::orderBy('name')->get();
            if ($author) {
                return response()->json([
                    'status code' => 200,
                    'succes' => true,
                    'authors' => AuthorResource::collection($author)
                ]);
            }
            return response()->json([
                'status code' => 204,
                'success' => true,
                'message' => 'No registered authors.'
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
        $author = Author::find($id);

        return response()->json([
            'status code' => 200,
            'success' => true,
            'author' =>  new AuthorResource($author)
        ]);
    }

    public function search($search)
    {
        try {
            $authorsearch = Author::where('name', 'LIKE', '%' . $search . '%')->orderBy('id', 'desc')->get();
            if ($authorsearch) {
                return response()->json([
                    'success' => true,
                    'author' =>  AuthorResource::collection($authorsearch)->pluck('name', 'id')
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
        $result = Author::find($id)->delete();
        if ($result) {
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => "Author Delete Successfully",
            ]);
        }
        return response()->json([
            'status_code' => 401,
            'success' => false,
            'message' => "Author Delete Unuccessfully",
        ]);
    }
}
