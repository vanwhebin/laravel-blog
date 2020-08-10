<?php


namespace App\Services;


use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SiteMap
{
    public function getSiteMap()
    {
        $siteMapIndex = "site-map";
        $siteMapExpire = 120;
        if (Cache::has($siteMapIndex)) {
            return Cache::get($siteMapIndex);
        }

        $siteMap = $this->buildSiteMap();
        Cache::add($siteMapIndex, $siteMap, $siteMapExpire);
        return $siteMap;
    }

    public function buildSiteMap()
    {
        $postsInfo = $this->getPostsInfo();
        $dates = array_values($postsInfo);
        sort($dates);
        $lastMod = last($dates);
        $url = trim(url('/'), '/'). '/';

        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?' . '>';
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $xml[] = '  <url>';
        $xml[] = "    <loc>$url</loc>";
        $xml[] = "    <lastmod>$lastMod</lastmod>";
        $xml[] = '    <changefreq>daily</changefreq>';
        $xml[] = '    <priority>0.8</priority>';
        $xml[] = '  </url>';

        foreach ($postsInfo as $slug => $lastmod) {
            $xml[] = '  <url>';
            $xml[] = "    <loc>{$url}Blog/$slug</loc>";
            $xml[] = "    <lastmod>$lastmod</lastmod>";
            $xml[] = "  </url>";
        }

        $xml[] = '</urlset>';

        return join("\n", $xml);

    }


    /**
     * 获取文章信息
     * @return mixed
     */
    protected function getPostsInfo()
    {
        return Post::where('published_at', '<=', Carbon::now())
            ->where('is_draft', 0)
            ->orderBy('published_at', 'desc')
            ->pluck('updated_at', 'slug')
            ->all();
    }
}
