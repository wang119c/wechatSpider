<?php
/**
 * Created by PhpStorm.
 * User: huizi
 * Date: 2019/6/3
 * Time: 14:31
 */

require_once "BaseSpider.php";

use Nesk\Rialto\Data\JsFunction;
use QL\QueryList;

class NewsSouhuSpider extends BaseSpider
{
    public function __construct()
    {
        $urls = [
            "http://www.sohu.com/tag/59953?spm=smpc.null.side-nav.28.15655882033488cQCQ3j"
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
       // $data = $this->getQueryContent("https://tech.sina.com.cn/5g/i/2019-06-04/doc-ihvhiews6632234.shtml");


        foreach ($this->urls as $key => $val) {
            echo "抓取url:" . $val . "----------------";
            $list = $this->getQueryData($val);
            $time = time();
            foreach ($list as $item) {
                if(  !isset($item['title'])  || !isset($item['cover']) || !isset($item['href']) ){
                    return  ; 
                }

                $data['title'] = isset($item['title']) ? $item['title'] : "";
                $data['cover'] = isset($item['cover']) ? $item['cover'] : "";
                $data['summary'] = isset($item['summary']) ? $item['summary'] : "";
                $data['url'] = isset($item['href']) ? $item['href'] : "";
                if (isset($item['href'])) {
                    $detailData = $this->getQueryContent($item['href']);
                    $data['message'] = isset($detailData[0]['content']) ? $detailData[0]['content'] : "";
                    $data['source'] = isset($detailData[0]['source']) ? $detailData[0]['source'] : "";
                } else {
                    continue;
                }
                $data['uuid'] = md5($item['href']);
                $data['catch_type'] = 5;
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
            $ql = QueryList::getInstance();
            $ql->use(\QL\Ext\Chrome::class);
            $rules = [
                "source" => [".article-page .user-info h4", "text"],
                "content" => ["#mp-editor", "html"]
            ];
            $html = $ql->chrome(function ($page, $browser) use ($url) {
                $page->goto($url);
                $html = $page->content();
                $browser->close();
                return $html;
            })->rules($rules)->queryData();
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
            'title' => ['.news-list .news-box h4', 'text'],
            'href' => [".news-list .news-box h4 a", "href"],
            'cover' => ['.img-do img', 'src']
        ];
        $html = $ql->chrome(function ($page, $browser) use ($url) {
            $page->goto($url);
            $page->evaluate(JsFunction::createWithBody("
                 var i = 0 ;
                 var timer = setInterval(function(){
                    if(i>=3){
                        clearInterval(timer);
                    }
                    window.scrollBy(0, i*100);
                    i++;
                 },3000);
            "));
            $html = $page->content();
            $browser->close();
            return $html;
        })->rules($rules)->queryData();
        return $html;
    }
}


$cache_path = __DIR__ . '/temp/';

//$newsQQ = new NewsSinaSpider($urls, $headers);
//$newsQQ->run();