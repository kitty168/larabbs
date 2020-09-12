<?php

/**
 * 转换当前路由名称为的所需的css中class的名称
 * @return mixed
 */
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

/**
 * 封装分类导航中 active 的显隐
 * @param int $category_id 分类id
 * @return string active
 */
function category_nav_active($category_id)
{
    return active_class((if_route('categories.show')) && (if_route_param('category', $category_id)));
}

/**
 * @param $value
 * @param int $length
 * @return string
 */
function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return str_limit($excerpt, $length);
}
