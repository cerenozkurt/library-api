<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowRequest;
use App\Http\Resources\UserResource;
use App\Models\Friends;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends ApiResponseController
{
    public function followSent()
    {
        //Arkadaşlık isteği gönderilenler
        $user = auth()->user();
        return $user->friendsTo;
    }

    public function followFrom()
    {
        //Gelen arkadaşlık istekleri
        $user = auth()->user();
        return $user->friendsFrom;
    }

    public function rejectFollow($id)
    {
        //isteği reddetme
        $user_id = auth()->user()->id;
        $friends = Friends::find($id);
        if ($user_id == $friends->friend_id) {
            if ($friends->accepted == 0) {
                $friends->delete();
                return $this->apiResponse(true, 'follow request deleted', null, null, JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(false, 'follow request not found', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->apiResponse(false, 'no permission', null, null, JsonResponse::HTTP_FORBIDDEN);
    }

    public function acceptFollow($id)
    {
        //gelen isteği kabul etme
        $user_id = auth()->user()->id;
        $friends = Friends::find($id);
        if ($user_id == $friends->friend_id) {
            if ($friends->accepted == 0) {
                $friends->accepted = '1';
                $friends->save();
                return $this->apiResponse(true, 'follow request accepted', null, null, JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(false, 'follow request already accepted', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->apiResponse(false, 'error', null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    public function getMyFriends()
    {
        //kabul edilmiş arkadaşlar

        $user_id = auth()->user()->id;
        $user = Friends::where('accepted', '1')->where('user_id', $user_id)
            ->get()->pluck('friend_id')->toarray();
        $friend = Friends::where('accepted', '1')->where('friend_id', $user_id)
            ->get()->pluck('user_id')->toarray();
        $friends = array_merge($user, $friend);
        $friends = User::wherein('id', $friends)->get();
        if ($friends->count() == 0) {
            return $this->apiResponse(false, 'My has no friends.', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->apiResponse(true, 'My Friends', 'friends', UserResource::collection($friends), JsonResponse::HTTP_OK);
    }

    public function getUsersFriends($id)
    {
        //kullanıcının arkadaşları

        $user = Friends::where('accepted', '1')->where('user_id', $id)
            ->get()->pluck('friend_id')->toarray();
        $friend = Friends::where('accepted', '1')->where('friend_id', $id)
            ->get()->pluck('user_id')->toarray();
        $friends = array_merge($user, $friend);
        $friends = User::wherein('id', $friends)->get();
        if ($friends->count() == 0) {
            return $this->apiResponse(false, 'This user has no friends.', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->apiResponse(true, 'Users Friends', 'friends', UserResource::collection($friends), JsonResponse::HTTP_OK);
    }

    public function deleteFriend($id)
    {
        //arkadaş silme
        $user_id = auth()->user()->id;
        $friends = Friends::find($id);
        if ($user_id == $friends->friend_id || $user_id == $friends->user_id) {
            if ($friends->accepted == 1) {
                $friends->delete();
                return $this->apiResponse(true, 'friend deleted', null, null, JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(false, 'friend request found', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->apiResponse(false, 'no permission', null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    public function addFriend(FollowRequest $request)
    {
        $user = auth()->user();
        if (Friends::where('user_id', $user->id)->where('friend_id', $request->friend_id)->first() || Friends::where('user_id', $request->friend_id)->where('friend_id', $user->id)->first()) {
            return $this->apiResponse(false, 'request already exists.', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
        //eğer kendi idsi ise ekleme

        elseif ($user->id == $request->friend_id) {
            return $this->apiResponse(false, 'no permission', null, null, JsonResponse::HTTP_FORBIDDEN);
        }

        $add = Friends::create([
            'user_id' => $user->id,
            'friend_id' => $request->friend_id,
            'accepted' => '0',
        ]);

        if ($add) {
            return $this->apiResponse(true, 'friend added', null, null, JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false, 'friend not found', null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    public function deleteRequest($id)
    {
        $friends = Friends::find($id);
        $user_id = auth()->user()->id;
        if ($user_id == $friends->user_id) {
            if ($friends->accepted == 0) {
                $friends->delete();
                return $this->apiResponse(true, 'friend request withdrawn', null, null, JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(false, 'friend request found', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->apiResponse(false, 'no permission', null, null, JsonResponse::HTTP_FORBIDDEN);
    }

}
