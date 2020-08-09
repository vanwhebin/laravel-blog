<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Services\PostService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
    	$tag = $request->get('tag', null);
        $postService = new PostService($tag);
        $data = $postService->lists();
        $layout = $tag ? Tag::layout($tag): 'blog.index';
//        dd($data);
//        dd($layout);
//        exit;
        return view($layout, $data);
    }


    public function showPost($slug, Request $request)
    {
        $post = Post::with('tags')->where('slug', $slug)->firstOrFail();
        $tag = $request->get('tag', null);
        if ($tag) {
        	$tag = Tag::where('tag', $tag)->firstOrFail();
        }

        return view($post->layout, compact('post', 'tag'));
    }

}
