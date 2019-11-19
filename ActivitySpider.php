<?php

require_once "BaseSpider.php";

use Medoo\Medoo;
use QL\QueryList;

/**
 * Created by PhpStorm.
 * User: huizi
 * Date: 2019/9/5
 * Time: 14:15
 */
class ActivitySpider extends BaseSpider
{

    public function __construct()
    {
        $industry = [
            "金融科技", "5G", "人工智能", "区块链", "物联网"
        ];
        $city = [
            "北京", "上海", "广州", "深圳", "成都", "南京", "苏州", "武汉", "天津", "重庆", "西安", "厦门", "宁波", "郑州", "青岛", "东莞", "佛山", "长沙", "石家庄"
        ];
        //构造请求url
        $urls = [];
        foreach ($industry as $key => $val) {
            foreach ($city as $k => $v) {
                $urls[] = "https://www.huodongxing.com/search?ps=12&pi=0&list=list&qs={$val}&st=1,4&city={$v}";
            }
        }
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

            $query = parse_url($val)['query'];
            parse_str($query, $uri);

            $list = $this->getQueryData($val);
            foreach ($list as $item) {
                if (strpos($item['href'], "event") === false || !isset($item['address']) || !isset($item['ava_img']) || !isset($item['username']) || !isset($item['time'])) {
                    continue;
                }
                $data['title'] = isset($item['title']) ? $item['title'] : "";
                $data['ac_url'] = isset($item['href']) ? "https://www.huodongxing.com" . $item['href'] : "";
                $data['report'] = $uri['qs'];
                $data['city'] = $uri['city'];
                $data['cover'] = isset($item['cover']) ? $item['cover'] : "";
                //处理时间
                $timeArr = explode("-", $item['time']);
                $data['ac_start_time'] = strtotime(str_replace(".", "-", $timeArr[0]));
                $data['ac_end_time'] = strtotime(str_replace(".", "-", $timeArr[1]));
                $data['ac_site'] = $item['address'];
                $data['ac_co_logo'] = $item['ava_img'];
                $data['ac_co_title'] = $item['username'];
                $data['ac_co_profile'] = "";

                if (isset($item['href'])) {
                    $content = $this->getQueryContent("https://www.huodongxing.com" . $item['href']);
                    if (!isset($content[0])) {
                        continue;
                    }
                    $data['content'] = $content[0]['content'];
                } else {
                    continue;
                }

                $data['uuid'] = md5($item['href']);
                $data['createtime'] = time();
                $data['updatetime'] = time();

                $this->writeSql($data);
            }


            //sleep(mt_rand(10, 15));
        }
    }

    public function getQueryContent($url)
    {
        try {
            sleep(mt_rand(5, 10));
            $ql = QueryList::getInstance();
            $ql->use(\QL\Ext\Chrome::class);
            $rules = [
                "content" => ["#event_desc_page", "html"],
            ];
            $html = $ql->chrome(function ($page, $browser) use ($url) {
                $page->goto($url);
                $html = $page->content();
                $browser->close();
                return $html;
            })->rules($rules)->queryData();
            return $html;
        } catch (Exception $e) {
            $this->getQueryContent($url);
        }
    }

    public function getQueryData($url)
    {
        $ql = QueryList::getInstance();
        $ql->use(\QL\Ext\Chrome::class);
        $rules = [
            'title' => ['.item-title', 'text'],
            'href' => [".item-title", "href"],
            'cover' => ['.item-logo', 'src'],
            'time' => ['.search-tab-content-item-right .item-data', 'text'],
            'address' => ['.item-dress', 'text'],
            'ava_img' => ['.user-logo', 'src'],
            'username' => ['.user-name', 'text']
        ];
        $html = $ql->chrome(function ($page, $browser) use ($url) {
            $page->goto($url);
            $html = $page->content();
            $browser->close();
            return $html;
        })->rules($rules)->queryData();
        return $html;

        //, [
        //    'headless' => false, // 启动可视化Chrome浏览器,方便调试
        //]
    }

    public function writeSql($data)
    {
        echo "写入" . $data['title'] . "----------------";

        if ($this->db->get('data_activity_temp', "id", [
            'uuid' => $data['uuid']
        ])) {

        } else {
            if ($data['content']) {
                $this->db->insert('data_activity_temp', [
                    'title' => $data['title'],
                    'cover' => $data['cover'],
                    'ac_url' => $data['ac_url'],
                    'report' => $data['report'],
                    'city' => $data['city'],
                    'ac_start_time' => $data['ac_start_time'],
                    'ac_end_time' => $data['ac_end_time'],
                    'ac_site' => $data['ac_site'],
                    'ac_co_logo' => $data['ac_co_logo'],
                    'ac_co_title' => $data['ac_co_title'],
                    'ac_co_profile' => $data['ac_co_profile'],
                    'content' => $data['content'],
                    'content1' => '不限',
                    'apply_num' => mt_rand(100, 200),
                    'uuid' => $data['uuid'],
                    'read_num' => mt_rand(100, 200),
                    'createtime' => time(),
                    'updatetime' => time(),
                    'type' => mt_rand(0, 1)
                ]);
            }
        }
    }
}

//启动
(new ActivitySpider())->run();