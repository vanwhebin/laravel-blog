<?php

namespace App\Models;

use App\Services\Markdown;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    protected $perPage = 5;

    protected $dates = ['published_at'];

    protected $fillable = [
        'title', 'subtitle', 'content_raw', 'page_image', 'meta_description',
        'layout', 'is_draft', 'published_at'
    ];

    /**
     * 设置文章和文章标签一对多的关系
     * @return BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag_pivot');
    }

    /**
     * 返回发布日期
     * @param $value
     * @return mixed
     */
    public function getPublishDateAttribute($value)
    {
        return $this->published_at->format('Y-m-d');
    }

    /**
     * 返回发布时间
     * @param $value
     * @return mixed
     */
    public function getPublishTimeAttribute($value)
    {
        return $this->published_at->format('g:i A');
    }

    /**
     * 设置发布文章的slug
     * @param $value
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (!$this->exists) {
            $this->attributes['slug'] = str_slug($value);
        }
    }

    /**
     * 获取文章的内容部分
     * @param $value
     * @return mixed
     */
    public function getContentAttribute($value)
    {
        return $this->content_raw;
    }


    /**
     * 设置文章唯一的slug
     * @param $title
     * @param $extra
     */
    protected function setUniqueSlug($title, $extra)
    {
        $slug = str_slug($title . '-' . $extra);
        if (static::where('slug', $slug)->exists()) {
            $this->setUniqueSlug($title, $extra + 1);
            return;
        }

        $this->attributes['slug'] = $slug;
    }

    /**
     * 转化内容
     * @param $value
     */
    public function setContentRawAttribute($value)
    {
        $markdown = new Markdown();
        $this->attributes['content_raw'] = $value;
        $this->attributes['content_html'] = $markdown->toHtml($value);
    }

    /**
     *同步文章标签
     * @param array $tags
     */
    public function syncTags(array $tags)
    {
        Tag::addNeededTags($tags);

        if (count($tags)) {
            $this->tags()->sync(
                Tag::whereIn('tag', $tags)->get()->pluck('id')->all()
            );
            return ;
        }
        $this->tags()->detach();
    }

	/**
	 * 返回url
	 * @param Tag|null $tag
	 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
	 */

    public function url(Tag $tag = null)
    {
    	$url = url('blog/' . $this->slug);

    	if ($tag) {
    		$url .= '?tag=' . urlencode($tag->tag);
	    }

    	return $url;

    }


	/**
	 * 返回所有标签链接
	 * @param string $base
	 * @return array
	 */
    public function tagLinks($base = '/blog?tag=%TAG%')
    {

    	$tags = $this->tags()->get()->pluck('tag')->all();
    	$return = [];
    	foreach ($tags as $tag) {
    		$url = str_replace('%TAG%', urlencode($tag), $base);
    		$return[] = '<a href="'. $url  .'">'. e($tag) .'</a>';
	    }

    	return $return;

    }

	/**
	 * 返回比当前更新的文章
	 * @param Tag|null $tag
	 * @return mixed
	 */
    public function newerPost(Tag $tag = null)
    {
    	$query = static::where('published_at', '>',  $this->published_at)
		    ->where('published_at', '<=', Carbon::now())
		    ->where('is_draft', 0)
		    ->orderBy('published_at', 'asc');
    	if ($tag) {
    		$query->whereHas('tags', function($q) use ($tag){
    			$q->where('tag', '=', $tag->tag);
		    });
	    }

    	return $query->first();
    }


	/**
	 * 返回之前的文章
	 * @param Tag|null $tag
	 * @return mixed
	 */
    public function olderPost(Tag $tag = null)
    {
    	$query = static::where('published_at', '<', $this->published_at)
		    ->where('is_draft', 0)
		    ->orderBy('published_at', 'desc');

    	if ($tag) {
    		$query = $query->whereHas('tags', function($q) use ($tag) {
    			$q->where('tag', '=', $tag->tag);
		    });
	    }

    	return $query->first();

    }



}
