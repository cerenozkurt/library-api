<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthorController extends ApiResponseController
{
    public function index()
    {
        try {
            $author = Author::namelist();
            if ($author) {
                return $this->apiResponse(true, 'Authors List', 'authors', AuthorResource::collection($author)->pluck('name', 'id'), JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(false, 'No registered authors.', null, null, JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }


    public function store(AuthorRequest $request)
    {
        $result = Author::create([
            'name' => $request->name,
            'read_count' => 0,
        ]);

        if ($result) {
            return $this->apiResponse(true, 'New Author Add Successfully', 'author', new AuthorResource($result), JsonResponse::HTTP_OK);
        } else {
            return $this->apiResponse(false, 'New Author Add Unsuccessfully!', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }


    public function update(AuthorRequest $request, $id)
    {
        $author = Author::find($id);

        if (Author::name($request->name)->first() == null) {
            $author->name = $request->name ?? $author->name;
            $result = $author->save();
        } else if ($request->name == $author->name) {
            $result = $author;
        } else {
            return $this->apiResponse(false, 'Author already exists.', null, null, JsonResponse::HTTP_NOT_FOUND);
        }

        if ($result) {
            return $this->apiResponse(true, 'Author Update Successfully.', 'author', new AuthorResource($author), JsonResponse::HTTP_OK);
        } else {
            return $this->apiResponse(false, 'Author Update Unsuccessfully.', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }


    public function getBooks()
    {
        try {
            $author = Author::namelist();
            if ($author) {
                return $this->apiResponse(false, "Authors' books.", 'authors', AuthorResource::collection($author), JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(false, "No registered authors.", null, null, JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }


    public function getBooksById($id)
    {
        $author = Author::find($id);
        return $this->apiResponse(true, "Author's books.", 'author', new AuthorResource($author), JsonResponse::HTTP_OK);
    }


    public function search($search)
    {
        try {
            $authorsearch = Author::search($search)->get();
            if ($authorsearch) {
                return $this->apiResponse(true, 'Author Search', 'author', AuthorResource::collection($authorsearch)->pluck('name', 'id'), JsonResponse::HTTP_OK);
            }
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }


    public function delete($id)
    {
        $result = Author::find($id)->delete();
        if ($result) {
            return $this->apiResponse(true, "Author Delete Successfully.", null, null, JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false, "Author Delete Unsuccessfully!", null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    public function uploadAuthorPicture(AuthorRequest $request, $id)
    {
        $author = Author::find($id);

        $disk = Storage::build([
            'driver' => 'local',
            'root' => public_path('authors'),
        ]);

        $path_with_filename = $disk->put('', $request->image);
        $filename = basename($path_with_filename);
        $authormedia = Media::find($author->media_id);


        if ($authormedia) {
            $oldFileName = $authormedia->filename;
        } else {
            $oldFileName = null;
        }
        $media = Media::query()
            ->updateOrCreate(
                ['id' => $author->media_id ?? 0],
                ['filename' => $filename]
            );


        $author->media_id = $media->id;
        $author->save();

        if (!is_null($author['media'])) {
            $author['media']->filename = $filename;
        } else {
            unset($author['media']);
        }

        if (public_path("authors/" . $oldFileName)) {

            File::delete(public_path("authors/" . $oldFileName));
        }

        $authormedia2 = DB::table('media')->get()->firstWhere('id', $author->media_id);

        if ($authormedia2) {
            return $this->apiResponse(true, 'media uploaded', 'authorpicture', asset('profile/' . Media::find($author->media_id)->filename), JsonResponse::HTTP_OK);
        } else {
            return $this->apiResponse(false, 'media not upload', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function deleteAuthorPicture($id)
    {
        $author =Author::find($id);

        if ($author->media_id) {

            $authormedia = DB::table('media')->get()->firstWhere('id', $author->media_id);
            $filename = $authormedia->filename;
            $authormediatemp = $author->media_id;
            $author->media_id = null;
            $author->save();
            //Storage::delete("public_html/profile" . $filename);
            File::delete(public_path("authors/".$filename));
            DB::table('media')->where('id', $authormediatemp)->delete();
           
            return $this->apiResponse(true, 'author picture deleted.',null, null, JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false,'author picture not found.', null, null, JsonResponse::HTTP_NOT_FOUND);
    }
}
