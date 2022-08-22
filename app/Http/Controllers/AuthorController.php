<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorController extends ApiResponseController
{
    public function index()
    {
        try {
            $author = Author::orderBy('name')->get();
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

        if (Author::where('name', $request->name)->first() == null) {
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
            $author = Author::orderBy('name')->get();
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
            $authorsearch = Author::where('name', 'LIKE', '%' . $search . '%')->orderBy('id', 'desc')->get();
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

    
}
