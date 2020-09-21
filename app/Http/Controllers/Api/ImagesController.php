<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Api\ImageRequest;
use App\Models\Image;
use App\Transformers\ImageTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImagesController extends Controller
{
    /**
     * 图片上传
     * @param ImageRequest $request
     * @param ImageUploadHandler $uploader
     * @param Image $image
     * @return \Dingo\Api\Http\Response
     */
    public function store(ImageRequest $request, ImageUploadHandler $uploader, Image $image)
    {
        // 当前用户
        $user = $this->user();

        $size = $request->type == 'avatar' ? 362 : 1024;

        $result = $uploader->save($request->image, Str::plural($request->type), $user->id, $size);

        $image->path = $result['path'];
        $image->type = $request->type;
        $image->user_id = $user->id;

        $image->save();

        return $this->response->item($image, new ImageTransformer())->setStatusCode(201);

    }
}
