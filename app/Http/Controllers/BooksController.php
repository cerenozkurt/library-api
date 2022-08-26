<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Models\Books;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BooksController extends ApiResponseController
{

    public function index()
    {

        try {
            $book = Books::nameList();
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
            Books::isbn($request->isbn)->first() == null || $books->isbn == $request->isbn
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
            $booksearch = Books::search($search)->get();
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

    public function uploadBookPicture(BookRequest $request, $id)
    {
        $book = Books::find($id);

        $disk = Storage::build([
            'driver' => 'local',
            'root' => public_path('books'),
        ]);

        $path_with_filename = $disk->put('', $request->image);
        $filename = basename($path_with_filename);
        $bookmedia = Media::find($book->media_id);


        if ($bookmedia) {
            $oldFileName = $bookmedia->filename;
        } else {
            $oldFileName = null;
        }
        $media = Media::query()
            ->updateOrCreate(
                ['id' => $book->media_id ?? 0],
                ['filename' => $filename]
            );


        $book->media_id = $media->id;
        $book->save();

        if (!is_null($book['media'])) {
            $book['media']->filename = $filename;
        } else {
            unset($book['media']);
        }

        if (public_path("books/" . $oldFileName)) {

            File::delete(public_path("books/" . $oldFileName));
        }

        $bookmedia2 = DB::table('media')->get()->firstWhere('id', $book->media_id);

        if ($bookmedia2) {
            return $this->apiResponse(true, 'media uploaded', 'bookpicture', asset('profile/' . Media::find($book->media_id)->filename), JsonResponse::HTTP_OK);
        } else {
            return $this->apiResponse(false, 'media not upload', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function deleteBookPicture($id)
    {
        $book =Books::find($id);

        if ($book->media_id) {

            $bookmedia = DB::table('media')->get()->firstWhere('id', $book->media_id);
            $filename = $bookmedia->filename;
            $bookmediatemp = $book->media_id;
            $book->media_id = null;
            $book->save();
            //Storage::delete("public_html/profile" . $filename);
            File::delete(public_path("books/".$filename));
            DB::table('media')->where('id', $bookmediatemp)->delete();
           
            return $this->apiResponse(true, 'book picture deleted.',null, null, JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false,'book picture not found.', null, null, JsonResponse::HTTP_NOT_FOUND);
    }
}
