<?php

namespace App\Http\Resources;

use App\Models\Books;
use App\Models\Like;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BookQuotesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'time' => $this->time($this->created_at),
            'user_id' => new UserResource(User::find($this->user_id)),
            'book' =>new BookResource(Books::find($this->book_id)),
            'title' => $this->title ?? '',
            'quotes' => $this->quotes,
            'page' => $this->page ?? '',
            'like' => [
                'count' => Like::where('book_quotes_id',$this->id)->count(),
                'users' => UserResource::collection(User::wherein('id',Like::where('book_quotes_id',$this->id)->select('user_id')->get())->get())
            ]
           
        ];
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
