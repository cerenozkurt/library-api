<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Admin\BooksStoreMiddleware;
use App\Http\Resources\BookResource;
use App\Models\book;
use App\Models\Books;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BooksController extends Controller
{
    public function __construct()
    {
        $this->middleware('books.store')->only('update');
    }

    public function index()
    {

        try {
            $book = Books::orderBy('name')->get();
            if ($book) {
                return response()->json([
                    'status code' => 200,
                    'succes' => true,
                    'books' => BookResource::collection($book),

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

    public function store(Request $request)
    {
        if (Books::where('isbn', '=', $request->isbn)->first() == null) {
            $result = Books::create([
                'isbn' => $request->isbn,
                'name' => $request->name,
                'page_count' => $request->page_count,
                'publisher_id' => $request->publisher_id,
                'category_id' => $request->category_id,
                'author_id' => $request->author_id
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

    public function update(Request $request, $id)
    {
        $books = Books::find($id);
        if (
            Books::where('isbn', '=', $request->isbn)->first() == null || $books->isbn == $request->isbn
        ) {

            $this->middleware(function ($request, $next) {
                return $next($request);
            });

            $books->isbn = $request->isbn;
            $books->name = $request->name;
            $books->page_count = $request->page_count;
            $books->publisher_id = $request->publisher_id;
            $books->category_id = $request->category_id;
            $books->author_id = $request->author_id;
            $result = $books->save();

            if ($result) {
                return response()->json([
                    'status code' => 200,
                    'success' => true,
                    'message' => 'Book Update Successfully'
                ]);
            } else {
                return response()->json([
                    'status code' => 401,
                    'success' => false,
                    'message' => 'Book Update Unsuccessfully'
                ]);
            }
        }
        return response()->json([
            'status code' => 401,
            'success' => false,
            'message' => 'Book already exists.'
        ]);
    }

    public function delete($id)
    {
        $result = Books::find($id)->delete();
        if ($result) {
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => "Book Delete Successfully",
            ]);
        }
        return response()->json([
            'status_code' => 401,
            'success' => false,
            'message' => "Book Delete Unsuccessfully",
        ]);
    }

    public function search($search)
    {
        try {
            $booksearch = Books::where(
                'isbn', 'LIKE', '%' . $search . '%')
                ->orWhere( 'name', 'LIKE', '%' . $search . '%')
                ->orderBy('id', 'desc')->get();
            if ($booksearch) {
                return response()->json([
                    'success' => true,
                    'books' =>  BookResource::collection($booksearch)->pluck('name','isbn')
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    
    public function getBooksById($id)
    {
        $books = Books::find($id);
        return response()->json([
            'status code' => 200,
            'success' => true,
            'author' =>  new BookResource($books)
        ]);
    }
}
