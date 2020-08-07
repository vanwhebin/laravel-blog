<?php


namespace App\Services;


use Carbon\Carbon;
use Dflydev\ApacheMimeTypes\PhpRepository;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Storage;

class UploadsManager
{
    protected $disk;
    protected $mineDetect;

    public function __construct(PhpRepository $mineDetect)
    {
        $this->disk = Storage::disk(config('blog.uploads.storage'));
        $this->mineDetect = $mineDetect;
    }


    public function folderInfo($folder)
    {
         $folder = $this->cleanFolder($folder);
         $breadCrumbs = $this->breadCrumbs($folder);
         $slice = array_slice($breadCrumbs, -1);
         $folderName = current($slice);
         $breadCrumbs = array_slice($breadCrumbs, 0, -1);

         $subFolders = [];
         foreach (array_unique($this->disk->directories($folder)) as $subFolder) {
         	$subFolders["/$subFolder"] = basename($subFolder);
         }

         $files = [];
         foreach($this->disk->files($folder) as $path){
         	$files[] = $this->fileDetails($path);
         }

         return compact('folder', 'folderName', 'breadCrumbs', 'subFolders', 'files');

    }


    /**
     * sanitize the folder name
     * @param $folder
     * @return string
     */
    protected function cleanFolder($folder)
    {
        return '/'. trim(str_replace('..', '', $folder), '/');
    }

    protected function breadCrumbs($folder)
    {
        $folder = trim($folder, '/');
        $crumbs = ['/' => 'root'];
        if (empty($folder)) {
            return $crumbs;
        }

        $folders = explode('/', $folder);
        $build = '';
        foreach ($folders as $folder) {
            $build .= "/". $folder;
            $crumbs[$build] = $folder;
        }

        return $crumbs;
    }

    public function fileDetails($path)
    {
        $path = "/" . ltrim($path, '/');
        return [
            'name' => basename($path),
            'fullPath' => $path,
            'webPath' => $this->fileWebpath($path),
            'size' =>  $this->fileSize($path),
            'modified' => $this->fileModified($path)
        ];
    }

    /**
     * 返回文件完整web路径
     * @param $path
     * @return UrlGenerator|string
     */
    public function fileWebpath($path)
    {
        $path = rtrim(config('blog.uploads.webpath'), '/'). '/' .ltrim($path, '/');
        return url($path);
    }

    /**
     * 返回文件mime类型
     * @param $path
     * @return mixed|string|null
     */
    public function fileMimeType($path)
    {
        return $this->mineDetect->findType(pathinfo($path, PATHINFO_EXTENSION));
    }


    /**
     * 返回最后修改时间
     * @param $path
     * @return Carbon
     */
    public function fileModified($path)
    {
        return Carbon::createFromTimeStamp($this->disk->lastModified($path));
    }



}
