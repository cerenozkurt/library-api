<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublisherRequest;
use App\Http\Resources\PublisherResource;
use App\Models\Publisher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublisherController extends ApiResponseController
{

    /* public function __construct()
    {
        $this->middleware('content.store')->only('update');
    }*/

    public function index()
    {
        try {
            $publisher = Publisher::nameList();
            if ($publisher) {
                return $this->apiResponse(true, 'Publishers List', 'publishers', PublisherResource::collection($publisher)->pluck('name', 'id'), JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(false, 'No registered publishers.', null, null, JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function store(PublisherRequest $request)
    {
        $result = Publisher::create([
            'name' => $request->name
        ]);

        if ($result) {
            return $this->apiResponse(true, 'New Publisher Add Successfully', 'publisher', new PublisherResource($result), JsonResponse::HTTP_OK);
        } else {
            return $this->apiResponse(false, 'New Publisher Add Unsuccessfully', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function update(PublisherRequest $request, $id)
    {
        $publisher = publisher::find($id);
        if (Publisher::name($request->name)->first() == null || $publisher->name == $request->name) {
            $publisher->name = $request->name ?? $publisher->name;
            $result = $publisher->save();
        } else {
            return $this->apiResponse(false, 'Publisher already exists.', null, null, JsonResponse::HTTP_NOT_FOUND);
        }

        if ($result) {
            return $this->apiResponse(true, 'Publisher Update Successfully!', 'publisher', new PublisherResource($publisher), JsonResponse::HTTP_OK);
        } else {
            return $this->apiResponse(false, 'Publisher Update Unsuccessfully!', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function getBooks()
    {
        try {
            $publisher = Publisher::nameList();
            if ($publisher) {
                return $this->apiResponse(true, "Publishers' Books.", 'publishers', PublisherResource::collection($publisher), JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(false, "No registered publishers.", null, null, JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function getBooksById($id)
    {
        $publisher = Publisher::find($id);
        return $this->apiResponse(true, "Publisher's Books.", 'publisher', new PublisherResource($publisher), JsonResponse::HTTP_OK);
    }

    public function search($search)
    {
        try {
            $publishersearch = publisher::search($search)->get();
            if ($publishersearch) {
                return $this->apiResponse(true, "Publisher Search", 'publisher', publisherResource::collection($publishersearch)->pluck('name', 'id'), JsonResponse::HTTP_OK);
            }
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function delete($id)
    {
        $result = Publisher::find($id)->delete();
        if ($result) {
            return $this->apiResponse(true, "Publisher Delete Successfully!", null, null, JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false, "Publisher Delete Unsuccessfully!", null, null, JsonResponse::HTTP_NOT_FOUND);
    }
}
