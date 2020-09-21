<?php
/**
 *
 * CategoryTransformer.php.
 * User: kitty.cheng
 * Mail: 450038893@qq.com
 * Date: 2020/9/21
 * Time: 16:07
 */

namespace App\Transformers;


use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    public function transform(Category $category)
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
        ];
    }

}
