<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UploadFileRequest;
use App\Http\Requests\UploadNewFolderRequest;
use App\Services\UploadsManager;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class UploadController extends Controller
{
    protected $manager;

    public function __construct(UploadsManager $manager)
    {
        $this->manager = $manager;
    }


    /**
     * 查上传文件列表
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $folder = $request->get('folder');
        $data = $this->manager->folderInfo($folder);
        return view('admin.upload.index', $data);
    }

    /**
     * 创建文件夹
     * @param UploadNewFolderRequest $request
     * @return RedirectResponse
     */
    public function createFolder(UploadNewFolderRequest $request)
    {
        $new_folder = $request->get('new_folder');
        $folder = $request->get('folder') . DIRECTORY_SEPARATOR . $new_folder;
        $result = $this->manager->createDirectory($folder);

        if ($result === true) {
            return redirect()->back()->with('success', '目录【' . $new_folder . '】创建成功');
        }

        $error = $result ?: '创建目录出错';
        return redirect()->back()->withErrors([$error]);
    }


    /**
     * 删除文件
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteFile(Request $request)
    {
        $delFile = $request->get('del_file');
        $path = $request->get('folder') . DIRECTORY_SEPARATOR . $delFile;

        $result = $this->manager->deleteFile($path);

        if ($result === true) {
            return redirect()->back()->with('success', '文件【' . $delFile . '】已删除');
        }
        $error = $result ?: '文件删除出错';
        return redirect()->back()->withErrors([$error]);
    }

    /**
     * 删除文件夹
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteFolder(Request $request)
    {
        $delFolder = $request->get('del_folder');
        $folder = $request->get('folder') . DIRECTORY_SEPARATOR . $delFolder;

        $result = $this->manager->deleteDirectory($folder);
        if ($result === true) {
            return redirect()->back()->with('success', '目录【' . $delFolder . '】已删除');
        }
        $error = $result ?: '文件夹删除出错';
        return redirect()->back()->withErrors([$error]);
    }

    /**
     * 上传文件
     * @param UploadFileRequest $request
     * @return RedirectResponse
     * @throws FileNotFoundException
     */
    public function uploadFile(UploadFileRequest $request)
    {
        $file = $_FILES['file'];
        $fileName = $request->get('file_name') ?: $file['name'];
        $path = str_finish($request->get('folder'), '/') . $fileName;
        $content = File::get($file['tmp_name']);

        $result = $this->manager->saveFile($path, $content);

        if ($result === true) {
            return redirect()->back()->with('success', '文件【' . $fileName . '】上传成功');
        }
        $error = $result ? : '文件上传失败';
        return redirect()->back()->withErrors([$error]);
    }



}
