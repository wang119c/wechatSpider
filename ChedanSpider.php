<?php

require './vendor/autoload.php';
require './func.php';

use Medoo\Medoo;
use QL\QueryList;

/**
 * Created by PhpStorm.
 * User: huizi
 * Date: 2019/6/13
 * Time: 15:06.
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
            'password' => 'root',
        ]);
        $this->urls = [
            'http://apis.yyuehd.com/api/v2/article/load/list?pageSize=15&category=COMMEND&userId=53b9da50-5cdd-4e74-8e5e-ea943ab3bd4f&net=WIFI&androidId=1017044525367825',
            'http://apis.yyuehd.com/api/v2/article/load/list?pageSize=15&category=VIDEO&userId=53b9da50-5cdd-4e74-8e5e-ea943ab3bd4f&net=WIFI&androidId=1017044525367825',
            'http://apis.yyuehd.com/api/v2/article/load/list?pageSize=15&category=ODDPHOTO&userId=53b9da50-5cdd-4e74-8e5e-ea943ab3bd4f&net=WIFI&androidId=1017044525367825',
            'http://apis.yyuehd.com/api/v2/article/load/list?pageSize=15&category=EMOJI&userId=53b9da50-5cdd-4e74-8e5e-ea943ab3bd4f&net=WIFI&androidId=1017044525367825',
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
            // $list = $this->getQueryData('http://apis.yyuehd.com/api/v2/article/load/list?pageSize=15&category=COMMEND&userId=53b9da50-5cdd-4e74-8e5e-ea943ab3bd4f&net=WIFI&androidId=1017044525367825');
            $list = $this->getQueryData('http://apis.yyuehd.com/api/v2/article/load/list?pageSize=15&category=COMMEND&userId=c4e585d8-4752-4e6a-9243-cc0111b9e5fd&net=WIFI&androidId=7078044313178878');
            $listArray = (array) json_decode($list, true);

            foreach ($listArray['data'] as $item) {
                echo '写入'.$item['title']."----\r\n";
                // $content['sign'] = isset($item["id"]) ? md5($item["id"]) : "";
                // $content['title'] = isset($item["title"]) ? emoji_encode($item["title"]) : "";
                // $content['content'] = isset($item["content"]) ? emoji_encode($item["content"]) : "";
                // $content['cate_code'] = isset($item["categoryCode"]) ? $item["categoryCode"] : "";
                // $content['cmt'] = isset($item["cmt"]) ? $item["cmt"] : "";
                // $content['up'] = isset($item["up"]) ? $item["up"] : "";
                // $content['down'] = isset($item["down"]) ? $item["down"] : "";
                // $content['share'] = isset($item["share"]) ? $item["share"] : "";
                // $content['updatetime'] = time();
                // $content['createtime'] = time();

                $content['sign'] = isset($item['id']) ? md5($item['id']) : '';
                $content['title'] = isset($item['title']) ? emoji_encode($item['title']) : '';
                $content['content'] = isset($item['content']) ? emoji_encode($item['content']) : '';
                $content['cate_code'] = isset($item['categoryCode']) ? $item['categoryCode'] : '';
                $content['author_id'] = $this->getUserId();
                $content['cmt'] = isset($item['cmt']) ? $item['cmt'] : '';
                $content['up'] = isset($item['up']) ? $item['up'] : '';
                $content['down'] = isset($item['down']) ? $item['down'] : '';
                $content['share'] = isset($item['share']) ? $item['share'] : '';
                $content['update_type'] = isset($item['mediaDTOList'][0]['type']) ? $item['mediaDTOList'][0]['type'] : 0;
                $content['updatetime'] = time();
                $content['createtime'] = time();

                $this->writeContentSql($content);
                if (isset($item['mediaDTOList'])) {
                    foreach ($item['mediaDTOList'] as $key => $val) {
                        $media['key'] = $val['key'];
                        $media['w'] = $val['w'];
                        $media['h'] = $val['h'];
                        $media['size'] = $val['size'];
                        $media['duration'] = $val['duration'];
                        $media['format'] = $val['format'];
                        $media['qiNiuUrl'] = $val['qiNiuUrl'];
                        $media['cover_url'] = $val['coverUrl'];
                        $media['original_url'] = $val['originalUrl'];
                        $media['type'] = $val['type'];
                        $media['content_sign'] = $content['sign'];
                        $media['updatetime'] = time();
                        $media['createtime'] = time();

                        // $media['key'] = $val['key'];
                        // $media['cover_url'] = $val['coverUrl'];
                        // $media['original_url'] = $val['originalUrl'];
                        // $media['type'] = $val['type'];
                        // $media['content_sign'] = $content['sign'];
                        // $media['updatetime'] = time();
                        // $media['createtime'] = time();
                        $this->writeMediaSql($media);
                    }
                }
            }
            sleep(mt_rand(10, 15));
        }
    }

    public function getQueryData($url)
    {
        $ql = QueryList::getInstance();
        $html = $ql->get($url)->getHtml();

        return $html;
    }

    public function getUserId()
    {
        $author = $this->db->rand('gx_user', 'id');
        $authorId = array_rand($author, 1);

        return $authorId;
    }

    public function writeContentSql($data)
    {
        $id = $this->db->get('gx_content', 'id', [
            'sign' => $data['sign'],
        ]);
        if (!$id) {
            $this->db->insert('gx_content', $data);
        } else {
            unset($data['sign']);
            unset($data['updatetime']);
            unset($data['createtime']);
            $this->db->update('gx_content', $data, ['id' => $id]);
        }
    }

    public function writeMediaSql($data)
    {
        $id = $this->db->get('gx_media', 'id', [
            'key' => $data['key'],
        ]);
        if (!$id) {
            $this->db->insert('gx_media', $data);
        } else {
            unset($data['key']);
            unset($data['updatetime']);
            unset($data['createtime']);
            $this->db->update('gx_content', $data, ['id' => $id]);
        }
    }

    //public function
}

$chedan = new ChedanSpider();
$chedan->run();