<?php

require_once "NewsQQSpider.php";
require_once "NewsIfengSpider.php";
require_once "NewsSinaSpider.php";

/**
 * Created by PhpStorm.
 * User: huizi
 * Date: 2019/6/3
 * Time: 14:23
 */


class NewsFactory
{
    public static function factory($transport)
    {
        switch ($transport) {
            case 'ifeng':
                return new NewsIfengSpider();
                break;
            case 'qq':
                return new NewsQQSpider();
                break;
            case 'sina':
                return new NewsSinaSpider();
                break;
        }
    }
}

if (isset($argv[1])) {
    (NewsFactory::factory($argv[1]))->run();
} else {
    throw new Exception("参数错误");
}





