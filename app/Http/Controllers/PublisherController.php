<?php

namespace App\Http\Controllers;

use App\Http\Resources\PublisherResource;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublisherController extends Controller
{

    public function __construct()
    {
        $this->middleware('content.store')->only('update');
    }

    public function index()
    {
        try {
            $publisher = Publisher::orderBy('name')->get();
            if ($publisher) {
                return response()->json([
                    'status code' => 200,
                    'succes' => true,
                    'publishers' => PublisherResource::collection($publisher)->pluck('name', 'id'),


                ]);
            }
            return response()->json([
                'status code' => 204,
                'success' => true,
                'message' => 'No registered publishers.'
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
        if (Publisher::where('name', '=', $request->name)->first() == null) {
            $result = Publisher::create([
                'name' => $request->name
            ]);

            if ($result) {
                return response()->json([
                    'status code' => 201,
                    'success' => true,
                    'message' => "New Publisher Add Successfully",
                ]);
            } else {
                return response()->json([
                    'status code' => 404,
                    'success' => false,
                    'message' => "New Publisher Add Unsuccessfully",
                ]);
            }
        }
        return response()->json([
            'status code' => 401,
            'success' => false,
            'message' => 'Publisher already exists.'
        ]);
    }

    public function update(Request $request, $id)
    {   
        $publisher = publisher::find($id);
        if (Publisher::where('name', '=', $request->name)->first() == null || $publisher->name == $request->name) {
            
            $this->middleware(function ($request, $next) {
                return $next($request);
            });

            $publisher->name = $request->name;
            $result = $publisher->save();

            if ($result) {
                return response()->json([
                    'status code' => 200,
                    'success' => true,
                    'message' => 'Publisher Update Successfully'
                ]);
            } else {
                return response()->json([
                    'status code' => 401,
                    'success' => false,
                    'message' => 'Publisher Update Unsuccessfully'
                ]);
            }
        }
        return response()->json([
            'status code' => 401,
            'success' => false,
            'message' => 'Publisher already exists.'
        ]);
    }

    public function getBooks()
    {
        try {
            $publisher = Publisher::orderBy('name')->get();
            if ($publisher) {
                return response()->json([
                    'status code' => 200,
                    'succes' => true,
                    'publishers' => PublisherResource::collection($publisher)
                ]);
            }
            return response()->json([
                'status code' => 204,
                'success' => true,
                'message' => 'No registered publishers.'
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
        $publisher = Publisher::find($id);

        return response()->json([
            'status code' => 200,
            'success' => true,
            'author' =>  new PublisherResource($publisher)
        ]);
    }

    public function search($search)
    {
        try {
            $publishersearch = publisher::where('name', 'LIKE', '%' . $search . '%')->orderBy('id', 'desc')->get();
            if ($publishersearch) {
                return response()->json([
                    'success' => true,
                    'publisher' =>  publisherResource::collection($publishersearch)->pluck('name', 'id')
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
        $result = Publisher::find($id)->delete();
        if ($result) {
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => "Publisher Delete Successfully",
            ]);
        }
        return response()->json([
            'status_code' => 401,
            'success' => false,
            'message' => "Publisher Delete Unuccessfully",
        ]);
    }
}
