<?php


namespace App\Services;


use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Cache;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;

class RssFeed
{
    public function getRss()
    {
        if (Cache::has('rss-feed')) {
            return Cache::get('rss-feed');
        }

        $rss = $this->buildRssData();
        Cache::add('rss-feed', $rss, 120);
        return $rss;
    }

    protected function buildRssData()
    {
        $now = Carbon::now();
        $feed = new Feed();
        $channel = new \Suin\RSSWriter\Channel();

        $channel->title(config('blog.title'))
            ->description(config('blog.description'))
            ->url(url('/'))
            ->language('en')
            ->copyright('Copyright(c)' . config('blog.author'))
            ->lastBuildDate($now->timestamp)
            ->appendTo($feed);

        $posts = Post::where('published_at', '<=', $now)
            ->where('is_draft', 0)
            ->orderBy('published_at', 'desc')
            ->take(config('blog.rss_size'))
            ->get();


        foreach ($posts as $post) {
            $item = new Item();
            $item->title($post->title)
                ->description($post->subtitle)
                ->url($post->url())
                ->pubDate($post->published_at->timestamp)
                ->guid($post->url(), true)
                ->appendTo($channel);
        }

        $feed = (string) $feed;
        $feed = str_replace('<rss version="2.0">', '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">', $feed);
        $feed = str_replace(
            '<channel>',
            '<channel>'.PHP_EOL .'<atom:link href="' . url('/rss').'" rel="self" type="application/rss+xml" />',
                $feed
        );
        return $feed;
    }


}
