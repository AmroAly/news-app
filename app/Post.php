<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Feed\FeedItem;
use Carbon\Carbon;

class Post extends Model implements FeedItem
{
    protected $fillable = [
        'title', 'body', 'image'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFeedItemId()
    {
        return $this->id;
    }

    public function getFeedItemTitle() : string
    {
        return $this->title;
    }

    public function getFeedItemSummary() : string
    {
        return $this->body;
    }

    public function getFeedItemUpdated() : Carbon
    {
        return $this->updated_at;
    }

    public function getFeedItemLink() : string
    {
        return action('PostController@show', [$this->id]);
    }

    public function getFeedItemAuthor() : string
    {
        return $this->user->name;
    }

    public function getFeedItems()
    {
       return Post::latest()->limit(10)->get();
    }
}
