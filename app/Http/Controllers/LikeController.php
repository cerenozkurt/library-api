<?php

namespace App\Http\Controllers;

use App\Events\UserSave;
use App\Models\Like;
use App\Models\User;
use App\Notifications\UserSave as NotificationsUserSave;
use Illuminate\Http\Request;
use Illuminate\Notifications;
use Illuminate\Support\Facades\Notification;

class LikeController extends Controller
{
    public function addPostLike($id)
    {
        $user_id = auth()->user()->id;
        if (!(Like::where('user_id', $user_id)->where('post_id', $id)->first()))
            $like = Like::create([
                'user_id' => $user_id,
                'post_id' => $id,
                'book_quotes_id' => null,
                'comment_id' => null,
            ]);
        return 'basarili';
    }

    public function addQuotesLike($id)
    {
        $user_id = auth()->user()->id;
        if (!(Like::where('user_id', $user_id)->where('book_quotes_id', $id)->first()))
            $like = Like::create([
                'user_id' => $user_id,
                'post_id' => null,
                'book_quotes_id' => $id,
                'comment_id' => null,
            ]);
        return 'basarili';
    }

    public function addCommentLike($id)
    {
        $user_id = auth()->user()->id;
        if (!(Like::where('user_id', $user_id)->where('comment_id', $id)->first()))
            $like = Like::create([
                'user_id' => $user_id,
                'post_id' => null,
                'book_quotes_id' => null,
                'comment_id' => $id,
            ]);
        return 'basarili';
    }

    public function deleteLike($id)
    {

        $like = Like::find($id);
        $like->delete();
        return 'basarili';
    }


    public function bildirim()
    {   
        $userSchema = User::first();
        Notification::send($userSchema, new NotificationsUserSave());
   
        dd('Task completed!');
    }
}
