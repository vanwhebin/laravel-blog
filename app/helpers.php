<?php

use Illuminate\Config\Repository;

/**
 * 返回可读性更好的文件尺寸
 * @param $bytes
 * @param int $decimals
 * @return string
 */
function humanFilesize($bytes, $decimals = 2)
{
    $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

/**
 * 判断文件的MIME类型是否为图片
 * @param $mineType
 * @return bool
 */
function isImage($mineType)
{
    return starts_with($mineType, 'image/');
}

/**
 * 查看是否为真，为真返回‘checked’
 * @param $value
 * @return string
 */
function checked($value)
{
    return $value ? 'checked' : '';
}

/**
 * 返回图片的完整路径
 * @param null $value
 * @return Repository|mixed|string|null
 */
function pageImage($value = null)
{
    if (empty($value)) {
        $value = config('blog.page_image');
    }

    if (!starts_with($value, 'http') && $value[0] !== '/') {
        $value = config('blog.uploads.webpath') . '/' . $value;
    }
    return $value;
}




