<?php
/**
 * Created by PhpStorm.
 * User: huizi
 * Date: 2019/6/3
 * Time: 14:21
 */

interface NewsSpider
{
    public function run();
    public function getQueryContent($url);
    public function getQueryData($url);
    public function writeSql($data);
}