<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiResponseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\LibraryRequest;
use App\Http\Requests\StoreRequest;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookResource;
use App\Http\Resources\UserBookResource;
use App\Http\Resources\UserResource;
use App\Models\Author;
use App\Models\Books;
use App\Models\User;
use App\Models\UserBook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PharIo\Manifest\AuthorCollection;

class LibraryController extends ApiResponseController
{

    public function getBooksById($id)
    { 
        //$userbooks = UserBook::where('user_id', $id)->get();
        $userbooks = UserBook::userbook($id)->get();
        $user = User::find($id);
        $userlibrary = ['user' => new UserResource($user), 'books' => UserBookResource::collection($userbooks)];

        return $this->apiResponse(true, "User's Books", 'library', $userlibrary, JsonResponse::HTTP_OK,);
      
    }


    public function getMyLibrary()
    {
        $user_id = Auth::user()->id;
        $userbooks = UserBook::userbook($user_id)->get();
        $user = User::find($user_id);
        $userlibrary = ['user' => new UserResource($user), 'books' => UserBookResource::collection($userbooks)];

        return $this->apiResponse(true, 'My Library', 'mylibrary', $userlibrary, JsonResponse::HTTP_OK);
    }


    public function userAddToLibrary(LibraryRequest $request)
    {
        $user_id = Auth::user()->id;//(UserBook::where('user_id', '=', $user_id)->where('book_id', '=', $request->book_id)->first() == null
        if (UserBook::userbook($user_id)->where('book_id', '=', $request->book_id)->first() == null) {
            $result = UserBook::create([
                'user_id' => $user_id,
                'book_id' => $request->book_id
            ]);
            $book = Books::find($request->book_id);
            $book->read_count = $book->read_count +1;
            $book->save();

            $author = Author::find($book->author_id);
            $author->read_count = $author->read_count + 1;
            $author->save();

            if ($result) {
                $book = Books::find($result->book_id);
                return $this->apiResponse(true, "New Book Add Successfully.", 'books', new BookResource($book), JsonResponse::HTTP_OK);
            } else {
                return $this->apiResponse(false, "New Book Add Unsuccessfully.", null, null, JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return $this->apiResponse(false, "Book already exists.", null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }


    public function userDeleteFromLibrary($id)
    {
        $user_id = Auth::user()->id;
        $userbook = UserBook::userbook($user_id)->where('book_id', $id)->first();
        if ($userbook) {
            $book = Books::find($userbook->book_id);
            $result = $userbook->delete();
            $book->read_count = $book->read_count - 1;
            $book->save();
            if ($result) {
                return $this->apiResponse(true, "Book Delete From Library Successfully.", null, null, JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(false, "Book Delete From Library Unsuccessfully.", null, null, JsonResponse::HTTP_NOT_FOUND);
        } else {
            return $this->apiResponse(false, "Book not found on library.", null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }


    public function mostReadBooks()
    {
        try {
            $books = Books::readcount();
            if ($books) {
                return $this->apiResponse(true, "Most Read Books", 'books', BookResource::collection($books), JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(true, "No registered books.", null, null, JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }


    public function mostReadAuthors()
    {
        try {
            $author = Author::readcount();
            if ($author) {
                return $this->apiResponse(true, "Most Read Authors", 'authors', AuthorResource::collection($author), JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(true, "No registered authors.", null, null, JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }


}
