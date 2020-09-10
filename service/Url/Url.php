<?php
namespace utils\Url;

use think\Config;

class Url
{
    /**
     * 生成长连接
     * @param $data
     */
    public static function createLongUrl($data)
    {
        $longStr = urlencode(
            base64_encode(
                json_encode($data)
            )
        );
        return $longStr;
    }








}