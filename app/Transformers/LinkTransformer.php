<?php
/**
 *
 * LinkTransformer.php.
 * User: kitty.cheng
 * Mail: 450038893@qq.com
 * Date: 2020/9/22
 * Time: 21:13
 */

namespace App\Transformers;


use App\Models\Link;
use League\Fractal\TransformerAbstract;

class LinkTransformer extends TransformerAbstract
{
    /**
     * @param Link $link
     * @return array
     */
    public function transform(Link $link)
    {
        return [
            'id' => $link->id,
            'title' => $link->title,
            'link' => $link->link,
            'created_at' => (string) $link->created_at,
            'updated_at' => (string) $link->updated_at,
        ];
    }
}
