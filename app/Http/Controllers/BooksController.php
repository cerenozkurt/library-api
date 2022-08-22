<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Models\Books;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BooksController extends ApiResponseController
{
    /*public function __construct()
    {
        $this->middleware('books.store')->only('update');
    }*/

    public function index()
    {

        try {
            $book = Books::orderBy('name')->get();
            if ($book) {
                return $this->apiResponse(true, "Books List", 'books', BookResource::collection($book), JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(false, "No registered books.", null, null, JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function store(BookRequest $request)
    {
        if (Books::where('isbn', '=', $request->isbn)->first() == null) {
            $result = Books::create([
                'isbn' => $request->isbn,
                'name' => $request->name,
                'page_count' => $request->page_count,
                'publisher_id' => $request->publisher_id,
                'category_id' => $request->category_id,
                'author_id' => $request->author_id,
                'read_count' => 0,
            ]);

            if ($result) {
                return $this->apiResponse(true, "New Book Add Successfully.", 'book', new BookResource($result), JsonResponse::HTTP_OK);
            } else {
                return $this->apiResponse(false, "New Book Add Unsuccessfully!.", null, null, JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return $this->apiResponse(false, "Book already exists.", null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function update(BookRequest $request, $id)
    {
        $books = Books::find($id);
        if (
            Books::where('isbn', '=', $request->isbn)->first() == null || $books->isbn == $request->isbn
        ) {


            $books->isbn = $request->isbn ?? $books->isbn;
            $books->name = $request->name ?? $books->name;
            $books->page_count = $request->page_count == $books->page_count;
            $books->publisher_id = $request->publisher_id ?? $books->publisher_id;
            $books->category_id = $request->category_id ?? $books->category_id;
            $books->author_id = $request->author_id ?? $books->author_id;
            $result = $books->save();

            if ($result) {
                return $this->apiResponse(true, "Book Update Successfully.", 'book', new BookResource($books), JsonResponse::HTTP_OK);
            } else {
                return $this->apiResponse(true, "Book Update Unsuccessfully.", null, null, JsonResponse::HTTP_NOT_FOUND);
            }
        }
        return $this->apiResponse(true, "Book already exists.", null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    public function delete($id)
    {
        $result = Books::find($id)->delete();
        if ($result) {
            return $this->apiResponse(true, "Book Delete Successfully!", null, null, JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false, "Book Delete Unsuccessfully!", null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    public function search($search)
    {
        try {
            $booksearch = Books::where(
                'isbn',
                'LIKE',
                '%' . $search . '%'
            )
                ->orWhere('name', 'LIKE', '%' . $search . '%')
                ->orderBy('id', 'desc')->get();
            if ($booksearch) {
                return $this->apiResponse(true, "Book Search", 'books', BookResource::collection($booksearch)->pluck('name', 'isbn'), JsonResponse::HTTP_OK);
            }
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function getBooksById($id)
    {
        $books = Books::find($id);
        return $this->apiResponse(true, "Book Info", 'book', new BookResource($books), JsonResponse::HTTP_OK);
    }
}
