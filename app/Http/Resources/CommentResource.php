<?php

namespace App\Http\Resources;

use App\Models\BookQuotes;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        switch ($request->route()->getActionMethod()) {
            case 'postStore':
                return [
                    'id' => $this->id,
                    'time' => $this->time($this->created_at),
                    'post' => new PostResource(Post::find($this->post_id)),
                    'comment' => $this->comment,
                    'like' => [
                        'count' => Like::where('comment_id',$this->id)->count(),
                        'users' => UserResource::collection(User::wherein('id',Like::where('comment_id',$this->id)->select('user_id')->get())->get())
                    ]
                ];
                break;
            case 'bookQuotesStore':
                return [
                    'id' => $this->id,
                    'time' => $this->time($this->created_at),
                    'book_quotes' => new BookQuotesResource(BookQuotes::find($this->book_quotes_id)),
                    'comment' => $this->comment,
                    'like' => [
                        'count' => Like::where('comment_id',$this->id)->count(),
                        'users' => UserResource::collection(User::wherein('id',Like::where('comment_id',$this->id)->select('user_id')->get())->get())
                    ]
                ];
                break;
            case 'update' || 'show' || 'commentsOfThePost' || 'commentsOfTheQuotes' || 'userComments':

                return [
                    'id' => $this->id,
                    'time' => $this->time($this->created_at),
                    'user' => new UserResource(User::find($this->user_id)),
                    'comment' => $this->comment,
                    'like' => [
                        'count' => Like::where('comment_id',$this->id)->count(),
                        'users' => UserResource::collection(User::wherein('id',Like::where('comment_id',$this->id)->select('user_id')->get())->get())
                    ]

                ];
        }
    }

    public function time($time)
    {

        $simdiki_tarih = Carbon::now();
        $ileriki_tarih = $time;
        $saniye_farki = $simdiki_tarih -> diffInSeconds($ileriki_tarih, false);
        $dakika_farki = $simdiki_tarih->diffInMinutes($ileriki_tarih, false);
        $saat_farki   = $simdiki_tarih->diffInHours($ileriki_tarih, false);
        $gun_farki    = $simdiki_tarih->diffInDays($ileriki_tarih, false);
        $ay_farki     = $simdiki_tarih->diffInMonths($ileriki_tarih, false);
        $yil_farki    = $simdiki_tarih->diffInYears($ileriki_tarih, false);

        if (abs($saniye_farki) < 60) {
            return 'Now';
        } elseif (abs($dakika_farki) < 60) {
            return abs($dakika_farki).'min';
        } elseif (abs($saat_farki) < 24) {
            return abs($saat_farki) . 'h';
        } elseif (abs($gun_farki) < 31) {
            return abs($gun_farki) . 'd';
        } elseif (abs($ay_farki) < 12) {
            return abs($ay_farki) . 'm';
        }
        return abs($yil_farki) . 'y';
    }
}
