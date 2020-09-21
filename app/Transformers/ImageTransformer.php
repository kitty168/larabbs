<?php
/**
 *
 * ImageTransformer.php.
 * User: kitty.cheng
 * Mail: 450038893@qq.com
 * Date: 2020/9/21
 * Time: 13:44
 */

namespace App\Transformers;


use App\Models\Image;
use League\Fractal\TransformerAbstract;

class ImageTransformer extends TransformerAbstract
{

    public function transform(Image $image)
    {
        return [
            'id' => $image->id,
            'user_id' => $image->user_id,
            'type' => $image->type,
            'path' => $image->path,
            'created_at' => (string) $image->created_at,
            'updated_at' => (string) $image->updated_at,
        ];
    }
}
