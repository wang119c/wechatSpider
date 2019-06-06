<?php

require_once "BaseSpider.php";

use Nesk\Rialto\Data\JsFunction;
use QL\QueryList;

/**
 * Created by PhpStorm.
 * User: huizi
 * Date: 2019/6/3
 * Time: 14:21
 */
class NewsIfengSpider extends BaseSpider
{

    public function __construct()
    {
        $urls = [
            "http://tech.ifeng.com/blockchain/"
        ];
        $headers = [
//    'Referer' => 'https://querylist.cc/',
//    'User-Agent' => 'testing/1.0',
//    'Accept' => 'application/json',
//    'X-Foo' => ['Bar', 'Baz'],
//    // 携带cookie
//    'Cookie' => 'abc=111;xxx=222',
//    'cache' => $cache_path,
//    'cache_ttl' => 600
        ];
        parent::__construct($urls, $headers);
    }

    public function run()
    {
        foreach ($this->urls as $key => $val) {
            echo "抓取url:" . $val . "----------------";
            $list = $this->getQueryData($val);
            $time = time();
            foreach ($list as $item) {
                $data['title'] = isset($item['title']) ? $item['title'] : "";
                $data['cover'] = isset($item['cover']) ? $item['cover'] : "";
                $data['source'] = isset($item['source']) ? $item['source'] : "";
                $data['summary'] = isset($item['summary']) ? $item['summary'] : "";
                $data['url'] = isset($item['href']) ? $item['href'] : "";
                if (isset($item['href'])) {
                    $data['message'] = $this->getQueryContent("http:" . $item['href']);
                } else {
                    continue;
                }
                $data['uuid'] = md5($item['href']);
                $data['catch_type'] = 3;
                $data['createtime'] = $time;
                $data['updatetime'] = $time;
                $this->writeSql($data);
            }
            sleep(mt_rand(10, 15));
        }
    }

    public function getQueryContent($url)
    {
        try{
            sleep(mt_rand(5, 10));
            $ql = QueryList::getInstance();
            $ql->use(\QL\Ext\Chrome::class);
            $rules = [
                "content1" => [".main_content-LcrEruCc", "html"],
                "content2" => [".main_content-1mF1eaKb", "html"]
            ];
            $html = $ql->chrome(function ($page, $browser) use ($url) {
                $page->goto($url);
                $html = $page->content();
                $browser->close();
                return $html;
            })->rules($rules)->queryData();
            if (isset($html[0]['content1']) && $html[0]['content1'] != "") {
                $html = $html[0]['content1'];
            } else {
                $html = $html[0]['content2'];
            }
            return $html;
        }catch (Exception $e){
            $this->getQueryContent($url);
        }
    }

    public function getQueryData($url)
    {
        $ql = QueryList::getInstance();
        $ql->use(\QL\Ext\Chrome::class);
        $rules = [
            'title' => ['.news-stream-newsStream-news-item-title a', 'text'],
            'href' => [".news-stream-newsStream-news-item-title a", "href"],
            'cover' => ['.news-stream-newsStream-image-link img', 'src'],
            'source' => ['.news-stream-newsStream-mr10', 'text']
        ];
        $html = $ql->chrome(function ($page, $browser) use ($url) {
            $page->goto($url);
            $page->waitForSelector(".news-stream-basic-more");
            for ($i = 0; $i < 9; $i++) {
                sleep(mt_rand(5, 10));
                $page->waitForSelector(".news-stream-basic-more");
                $page->click(".news-stream-basic-more");
            }
            $html = $page->content();
            $browser->close();
            return $html;
        })->rules($rules)->queryData();
        return $html;
    }
}




$cache_path = __DIR__ . '/temp/';



//$newsQQ = new NewsIfengSpider($urls, $headers);
//$newsQQ->run();