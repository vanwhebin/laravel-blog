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

    /**
     * 文件夹信息
     * @param $folder
     * @return array
     */
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
        foreach ($this->disk->files($folder) as $path) {
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
        return '/' . trim(str_replace('..', '', $folder), '/');
    }

    /**
     * 文件夹面包屑
     * @param $folder
     * @return array
     */
    protected function breadCrumbs($folder)
    {
        $folder = trim($folder, '/');
        $crumbs = ['/' => 'Root'];
        if (empty($folder)) {
            return $crumbs;
        }

        $folders = explode('/', $folder);
        $build = '';
        foreach ($folders as $folder) {
            $build .= "/" . $folder;
            $crumbs[$build] = $folder;
        }

        return $crumbs;
    }

    /**
     * 文件夹详情
     * @param $path
     * @return array
     */
    public function fileDetails($path)
    {
        $path = "/" . ltrim($path, '/');
        return [
            'name' => basename($path),
            'fullPath' => $path,
            'mimeType' => $this->fileMimeType($path),
            'webPath' => $this->fileWebpath($path),
            'size' => $this->fileSize($path),
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
        $path = rtrim(config('blog.uploads.webpath'), '/') . '/' . ltrim($path, '/');
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


    /**
     * 返回文件大小
     * @param $path
     * @return int
     */
    public function fileSize($path)
    {
        return $this->disk->size($path);
    }

    /**
     * 创建上传文件的文件夹
     * @param $folder
     * @return bool|string
     */
    public function createDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);
        if ($this->disk->exists($folder)) {
            return "文件夹 {$folder} 已存在";
        }

        return $this->disk->makeDirectory($folder);
    }


    /**
     * 删除文件夹
     * @param $folder
     * @return bool|string
     */
    public function deleteDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);
        $fileFolders = array_merge(
            $this->disk->directories($folder),
            $this->disk->files($folder)
        );

        if (!empty($fileFolders)) {
            return "文件夹不为空";
        }

        return $this->disk->deleteDirectory($folder);
    }

    /**
     * 删除文件
     * @param $path
     * @return bool|string
     */
    public function deleteFile($path)
    {
        $path = $this->cleanFolder($path);

        if (! $this->disk->exists($path)) {
            return "文件不存在";
        }

        return $this->disk->delete($path);
    }

    /**
     * 保存文件
     * @param $path
     * @param $content
     * @return bool|string
     */
    public function saveFile($path, $content)
    {
        $path = $this->cleanFolder($path);

        if ($this->disk->exists($path)) {
            return "文件已经存在";
        }

        return $this->disk->put($path, $content);
    }


}
