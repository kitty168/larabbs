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


use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Str;

class ImageUploadHandler
{
    // 允许上传的文件后缀名
    protected $allowed_ext = ['png', 'jpg', 'gif', 'jpeg'];

    /**
     * 文件上传
     * @param UploadedFile $file 文件对象
     * @param string $folder 上传目录
     * @param string $file_prefix 文件前缀
     * @param bool $max_width 图片最大宽度
     * @return array|bool
     */
    public function save($file, $folder, $file_prefix, $max_width = false)
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

        if ($max_width && $extension != 'gif') {
            //图片裁剪
            $this->reduceSize($upload_path . DIRECTORY_SEPARATOR . $filename, $max_width);
        }

        return [
            'path' => config('app.url') . "/$folder_name/$filename",
        ];

    }

    /**
     * @param string $file_path
     * @param int $max_width
     */
    protected function reduceSize(string $file_path, $max_width)
    {
        // 先实例化，传参是文件的磁盘物理路径
        $image = Image::make($file_path);

        // 进行大小调整的操作
        $image->resize($max_width, null, function($constraint) {
            // 设定宽度是$max_width, 高度等比缩放
            $constraint->aspectRatio();

            // 防止裁图是尺寸变大
            $constraint->upsize();
        });

        // 保存裁剪后的图片
        $image->save();

    }


}
