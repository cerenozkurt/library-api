<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiResponseController;
use App\Http\Requests\LibraryRequest;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookQuotesResource;
use App\Http\Resources\BookResource;
use App\Http\Resources\UserBookResource;
use App\Http\Resources\UserResource;
use App\Models\Author;
use App\Models\BookQuotes;
use App\Models\Books;
use App\Models\User;
use App\Models\UserBook;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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

    public function booksForStatus($status)
    {
        switch ($status) {
            case 'will-read':
                $user_id = Auth::user()->id;
                $userbooks = UserBook::userbook($user_id)->where('status', 'will_read')->get();
                return $this->apiResponse(true, 'will read', 'books', UserBookResource::collection($userbooks));
                break;
            case 'readed':
                $user_id = Auth::user()->id;
                $userbooks = UserBook::userbook($user_id)->where('status', 'readed')->get();
                return $this->apiResponse(true, 'reade', 'books', UserBookResource::collection($userbooks));
                break;
            case 'reading':
                $user_id = Auth::user()->id;
                $userbooks = UserBook::userbook($user_id)->where('status', 'reading')->get();
                return $this->apiResponse(true, 'reading', 'books', UserBookResource::collection($userbooks));
                break;
        }
    }


    public function userAddToLibrary(LibraryRequest $request)
    {


        $user_id = Auth::user()->id; //(UserBook::where('user_id', '=', $user_id)->where('book_id', '=', $request->book_id)->first() == null
        if (UserBook::userbook($user_id)->where('book_id', '=', $request->book_id)->first() == null) {
            $result = UserBook::create([
                'user_id' => $user_id,
                'book_id' => $request->book_id,
                'status' => $request->status
            ]);


            if ($request->status == 'readed') {
                $book = Books::find($request->book_id);
                $book->read_count = $book->read_count + 1;
                $book->save();
                $author = Author::find($book->author_id);
                $author->read_count = $author->read_count + 1;
                $author->save();
            }


            $userbook = UserBook::where('book_id', $request->book_id)->first();
            if ($result) {
                $book = Books::find($result->book_id);
                return $this->apiResponse(true, "New Book Add Successfully.", 'books', new UserBookResource($userbook), JsonResponse::HTTP_OK);
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

            if ($userbook->status == 'readed') {
                $books = Books::find($id);
                $books->read_count = $books->read_count - 1;
                $books->save();
                $author = Author::find($books->author_id);
                $author->read_count = $author->read_count - 1;
                $author->save();
            }


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

    public function updateStatus(LibraryRequest $request, $id)
    {
        $user = auth()->user();
        $usersbook =  UserBook::where('user_id', $user->id)->pluck('book_id');
        $usersbook = Books::wherein('id', $usersbook)->get();

        if ($usersbook->where('id', $id)->first()) {

            $book = UserBook::where('book_id', $id)->first();
            if ($request->status == 'readed') {
                if ($book->status != 'readed') {
                    $books = Books::find($id);
                    $books->read_count = $books->read_count + 1;
                    $books->save();
                    $author = Author::find($books->author_id);
                    $author->read_count = $author->read_count + 1;
                    $author->save();
                }
            } else {
                if ($book->status == 'readed') {
                    $books = Books::find($id);
                    $books->read_count = $books->read_count - 1;
                    $books->save();
                    $author = Author::find($books->author_id);
                    $author->read_count = $author->read_count - 1;
                    $author->save();
                }
            }


            $book->update(['status' => $request->status ?? $book->status]);

            return $this->apiResponse(true, 'Book status updated.', 'book', new UserBookResource($book), JsonResponse::HTTP_OK);
        }

        return $this->apiResponse(false, 'Book status not update.', null, null, JsonResponse::HTTP_NOT_FOUND);
    }


    public function updateComment(LibraryRequest $request, $id)
    {
        $user = auth()->user();
        $usersbook =  UserBook::userbook($user->id)->pluck('book_id');
        $usersbook = Books::wherein('id', $usersbook)->get();
        if ($usersbook->where('id', $id)->first()) {
            $ubook = UserBook::where('book_id', $id)->first();
            $ubook->comment = $request->comment ?? $ubook->comment;
            $ubook->save();
        } else {
            return $this->apiResponse(false, 'There are no book reviews.', null, null, JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->apiResponse(true, 'Book comment updated.', 'book_comment', new UserBookResource($ubook), JsonResponse::HTTP_OK);
    }

    public function updatePoint(LibraryRequest $request, $id)
    {
        $user = auth()->user();
        $usersbook =  UserBook::userbook($user->id)->pluck('book_id');
        $usersbook = Books::wherein('id', $usersbook)->get();

        if ($usersbook->where('id', $id)->first()) {
            $ubook = UserBook::where('book_id', $id)->first();
            $ubook->point = $request->point ?? $ubook->point;
            $ubook->save();
            
            $readbooks = UserBook::where('book_id',$id)->pluck('point');
            $ortpuan =  array_sum($readbooks->toArray())/$readbooks->count();
            $book = Books::find($id);
            $book->point = $ortpuan;
            $book->save();

        } else {
            return $this->apiResponse(false, 'There are no book reviews.', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->apiResponse(true, 'Book point updated.', 'book_comment', new UserBookResource($ubook), JsonResponse::HTTP_OK);
    }

    public function getQuotes($id)
    {
        $user = BookQuotes::where('user_id',$id)->get();
        return $this->apiResponse(true, 'User quotes', 'quotes', BookQuotesResource::collection($user));
    }
}
