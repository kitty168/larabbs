<?php
/**
 *
 * ImageUploadHandler.php.
 * User: kitty.cheng
 * Mail: 450038893@qq.com
 * Date: 2020/9/12
 * Time: 10:01
 */

namespace App\Handlers;


use Illuminate\Support\Str;

class ImageUploadHandler
{
    // 允许上传的文件后缀名
    protected $allowed_ext = ['png', 'jpg', 'gif', 'jpeg'];

    /**
     * 文件上传
     * @param $file 文件对象
     * @param $folder 上传目录
     * @param $file_prefix 文件前缀
     * @return array|bool
     */
    public function save($file, $folder, $file_prefix)
    {
        // 定义上传根路径
        $root_path = 'uploads'. DIRECTORY_SEPARATOR.'images' . DIRECTORY_SEPARATOR;
        // 定义上传路径
        $folder_name = $root_path . $folder . DIRECTORY_SEPARATOR . date('Ym/d',time());

        // 定义上传的绝对路径
        $upload_path = public_path() . DIRECTORY_SEPARATOR . $folder_name;

        // 获取文件后缀名
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        if (!in_array($extension, $this->allowed_ext)) {
            return false;
        }

        // 组合新的文件名
        $filename = $file_prefix . '_' . time() . '_' . Str::random(10) . '.' . $extension;

        // 文件移动到指定的存储路径
        $file->move($upload_path, $filename);

        return [
            'path' => config('app.url') . "/$folder_name/$filename",
        ];

    }



}
