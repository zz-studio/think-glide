<?php
/**
 * +----------------------------------------------------------------------
 * | think-glide [thinkphp6]
 * +----------------------------------------------------------------------
 *  .--,       .--,             | FILE: Response.php
 * ( (  \.---./  ) )            | AUTHOR: byron
 *  '.__/o   o\__.'             | EMAIL: xiaobo.sun@qq.com
 *     {=  ^  =}                | QQ: 150093589
 *     /       \                | DATETIME: 2019/11/6 14:33
 *    //       \\               |
 *   //|   .   |\\              |
 *   "'\       /'"_.-~^`'-.     |
 *      \  _  /--'         `    |
 *    ___)( )(___               |-----------------------------------------
 *   (((__) (__)))              | 高山仰止,景行行止.虽不能至,心向往之。
 * +----------------------------------------------------------------------
 * | Copyright (c) 2019 http://www.zzstudio.net All rights reserved.
 * +----------------------------------------------------------------------
 */
declare(strict_types=1);

namespace think\glide;

use League\Flysystem\FilesystemInterface;
use League\Glide\Responses\ResponseFactoryInterface;
use think\Response;

class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * Create response
     * @param FilesystemInterface $cache
     * @param string              $path
     * @author Byron Sampson <xiaobo.sun@qq.com>
     * @return Response
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function create(FilesystemInterface $cache, $path)
    {
        $contentType = $cache->getMimetype($path);
        $contentLength = $cache->getSize($path);

        return Response::create()->data(stream_get_contents($cache->readStream($path)))
            ->header([
                'Content-Type' => $contentType,
                'Content-Length' => $contentLength
            ]);
    }
}