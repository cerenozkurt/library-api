<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class ProfileController extends ApiResponseController
{

    public function index()
    {
        
    }

    public function uploadProfilePicture(ProfileRequest $request)
    {
        $user = User::find(auth()->user()->id);

        $disk = Storage::build([
            'driver' => 'local',
            'root' => public_path('profile'),
        ]);

        $path_with_filename = $disk->put('', $request->image);
        $filename = basename($path_with_filename);
        $usersmedia = Media::find($user->media_id);


        if ($usersmedia) {
            $oldFileName = $usersmedia->filename;
        } else {
            $oldFileName = null;
        }
        $media = Media::query()
            ->updateOrCreate(
                ['id' => $user->media_id ?? 0],
                ['filename' => $filename]
            );


        $user->media_id = $media->id;
        $user->save();

        if (!is_null($user['media'])) {
            $user['media']->filename = $filename;
        } else {
            unset($user['media']);
        }

        if (public_path("profile/" . $oldFileName)) {

            File::delete(public_path("profile/" . $oldFileName));
        }

        $usersmedia2 = DB::table('media')->get()->firstWhere('id', $user->media_id);

        if ($usersmedia2) {
            return $this->apiResponse(true, 'media uploaded', 'profilepicture', $user->media_id ? asset('profile/' . Media::find($user->media_id)->filename) : $user->image, JsonResponse::HTTP_OK);
        } else {
            return $this->apiResponse(false, 'media not upload', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function deleteProfilePicture()
    {
        $user = User::find(auth()->user()->id);

        if ($user->media_id) {

            $usersmedia = DB::table('media')->get()->firstWhere('id', $user->media_id);
            $filename = $usersmedia->filename;
            $user_media = $user->media_id;
            $user->media_id = null;
            $user->save();
            //Storage::delete("public_html/profile" . $filename);
            File::delete(public_path("profile/".$filename));
            DB::table('media')->where('id', $user_media)->delete();
           
            return $this->apiResponse(true, 'profile picture deleted.',null, null, JsonResponse::HTTP_OK);
        }
        return $this->apiResponse(false,'profile picture not found.', null, null, JsonResponse::HTTP_NOT_FOUND);
    }



}
