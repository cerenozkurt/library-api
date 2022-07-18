<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserBookResource;
use App\Models\User;
use App\Models\UserBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{

    public function getBooksById($id)
    {
        $user = User::find($id);
        $userbooks = UserBook::where('user_id', '=', $id)->get();

        return response()->json([
            'status code' => 200,
            'success' => true,
            'user' => User::find($id)->name,
            'users library' => UserBookResource::collection($userbooks)
        ]);
    }


    public function getMyLibrary()
    {
        $user_id = Auth::user()->id;
        $userbooks = UserBook::where('user_id', '=', $user_id)->get();
        return response()->json([
            'status code' => 200,
            'success' => true,
            'user' => User::find($user_id)->name,
            'users library' => UserBookResource::collection($userbooks)
        ]);
    }


    public function userAddToLibrary(Request $request)
    {
        $user_id = Auth::user()->id;
        if (UserBook::where('user_id', '=', $user_id)->where('book_id', '=', $request->book_id)->first() == null) {
            $result = UserBook::create([
                'user_id' => $user_id,
                'book_id' => $request->book_id
            ]);
            if ($result) {
                return response()->json([
                    'status code' => 201,
                    'success' => true,
                    'message' => "New Book Add Successfully",
                ]);
            } else {
                return response()->json([
                    'status code' => 404,
                    'success' => false,
                    'message' => "New Book Add Unsuccessfully",
                ]);
            }
        } else {
            return response()->json([
                'status code' => 401,
                'success' => false,
                'message' => 'Book already exists.'
            ]);
        }
    }

    public function userDeleteFromLibrary($id)
    {
        $user_id = Auth::user()->id;
        $userbook = UserBook::where('user_id', '=', $user_id)->where('book_id', '=', $id)->first();
        if ($userbook) {
            $result = $userbook->delete();

            if ($result) {
                return response()->json([
                    'status_code' => 200,
                    'success' => true,
                    'message' => "Book Delete From Library Successfully",
                ]);
            }
            return response()->json([
                'status_code' => 401,
                'success' => false,
                'message' => "Book Delete From Library Unsuccessfully",
            ]);
        } else {
            return response()->json([
                'status_code' => 404,
                'success' => false,
                'message' => "Book Not Found.",
            ]);
        }
    }

    public function mostReadBooks()
    {
        try {
            $userbook = UserBook::select('book_id', UserBook::raw('count(*) as count'))->groupby('book_id')->orderby('count', 'desc')->get();
            if ($userbook) {
                return response()->json([
                    'status code' => 200,
                    'succes' => true,
                    'most read books' => UserBookResource::collection($userbook),

                ]);
            }
            return response()->json([
                'status code' => 204,
                'success' => true,
                'message' => 'No registered books.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 401,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }


    // !!!//
    public function mostReadAuthors()
    {
        try {
            $userbook = UserBook::select('book_id', UserBook::raw('count(*) as count'))->groupby('book_id')->orderby('count', 'desc')->get();
            if ($userbook) {
                return response()->json([
                    'status code' => 200,
                    'succes' => true,
                    'most read books' => UserBookResource::collection($userbook)
                
                   

                ]);
            }
            return response()->json([
                'status code' => 204,
                'success' => true,
                'message' => 'No registered books.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 401,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
