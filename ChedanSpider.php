<?php
require "./vendor/autoload.php";

use Medoo\Medoo;
use QL\QueryList;

/**
 * Created by PhpStorm.
 * User: huizi
 * Date: 2019/6/13
 * Time: 15:06
 */
class ChedanSpider
{
    protected $urls;
    protected $headers;
    protected $db;

    public function __construct()
    {
        $this->db = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'gaoxiao',
            'server' => '127.0.0.1',
            'username' => 'root',
            'password' => 'root'
        ]);
        $this->urls = [
            "http://apis.yyuehd.com/api/v2/article/load/list?pageSize=15&category=COMMEND&userId=53b9da50-5cdd-4e74-8e5e-ea943ab3bd4f&net=WIFI&androidId=1017044525367825",
            "http://apis.yyuehd.com/api/v2/article/load/list?pageSize=15&category=VIDEO&userId=53b9da50-5cdd-4e74-8e5e-ea943ab3bd4f&net=WIFI&androidId=1017044525367825",
            "http://apis.yyuehd.com/api/v2/article/load/list?pageSize=15&category=ODDPHOTO&userId=53b9da50-5cdd-4e74-8e5e-ea943ab3bd4f&net=WIFI&androidId=1017044525367825",
            "http://apis.yyuehd.com/api/v2/article/load/list?pageSize=15&category=EMOJI&userId=53b9da50-5cdd-4e74-8e5e-ea943ab3bd4f&net=WIFI&androidId=1017044525367825"
        ];
        $this->headers = [
//    'Referer' => 'https://querylist.cc/',
//    'User-Agent' => 'testing/1.0',
//    'Accept' => 'application/json',
//    'X-Foo' => ['Bar', 'Baz'],
//    // 携带cookie
//    'Cookie' => 'abc=111;xxx=222',
//    'cache' => $cache_path,
//    'cache_ttl' => 600
        ];
    }

    public function run()
    {
        while (true) {
            $list = $this->getQueryData("http://apis.yyuehd.com/api/v2/article/load/list?pageSize=15&category=COMMEND&userId=53b9da50-5cdd-4e74-8e5e-ea943ab3bd4f&net=WIFI&androidId=1017044525367825");
            $listArray = (array)json_decode($list,true);

            foreach ($listArray['data'] as $item) {
                echo "写入".$item["title"]."----\r\n";
                $content['sign'] = isset($item["id"]) ? md5($item["id"]) : "";
                $content['title'] = isset($item["title"]) ? $item["title"] : "";
                $content['content'] = isset($item["content"]) ? $item["content"] : "";
                $content['cate_code'] = isset($item["categoryCode"]) ? $item["categoryCode"] : "";
                $content['cmt'] = isset($item["cmt"]) ? $item["cmt"] : "";
                $content['up'] = isset($item["up"]) ? $item["up"] : "";
                $content['down'] = isset($item["down"]) ? $item["down"] : "";
                $content['share'] = isset($item["share"]) ? $item["share"] : "";
                $content['updatetime'] = time();
                $content['createtime'] = time();
                $this->writeContentSql($content);
                if (isset($item['mediaDTOList'])) {
                    foreach ($item['mediaDTOList'] as $key => $val) {
                        $media['key'] = $val['key'];
                        $media['cover_url'] = $val['coverUrl'];
                        $media['original_url'] = $val['originalUrl'];
                        $media['type'] = $val['type'];
                        $media['content_sign'] = $content['sign'] ;
                        $media['updatetime'] = time();
                        $media['createtime'] = time();
                        $this->writeMediaSql($media);
                    }
                }
            }
            sleep(mt_rand(5, 10));
        }

    }

    public function getQueryData($url)
    {
        $ql = QueryList::getInstance();
        $html = $ql->get($url)->getHtml();
        return $html;
    }

    public function writeContentSql($data)
    {
        if (!$this->db->get('gx_content', "id", [
            'sign' => $data['sign']
        ])) {
           $this->db->insert('gx_content', $data);
        }
    }

    public function writeMediaSql($data)
    {
        if (!$this->db->get('gx_media', "id", [
            'key' => $data['key']
        ])) {
            $this->db->insert('gx_media', $data);
        }
    }

}

$chedan = new ChedanSpider();
$chedan->run();

