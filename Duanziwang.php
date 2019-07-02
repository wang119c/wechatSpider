<?php

require './vendor/autoload.php';
require './func.php';

use Medoo\Medoo;
use QL\QueryList;

//http: //duanziwang.com/
class Duanziwang
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
    }

    /**
     * 运行数据.
     */
    public function run()
    {
        $page = 1;
        while (true) {
            $this->url = sprintf('http://duanziwang.com/page/%s/', $page);
            $data = $this->getQueryData($this->url);
            if (!$data) {
                break;
            }
            foreach ($data as $item) {
                echo '写入'.$item['title']."----\r\n";
                $content['sign'] = isset($item['url']) ? md5($item['url']) : '';
                $content['title'] = isset($item['title']) ? emoji_encode($item['title']) : '';
                $content['content'] = isset($item['content']) ? emoji_encode($item['content']) : '';
                $content['cate_code'] = 'DUANZI';
                $content['author_id'] = $this->getUserId();
                $content['cmt'] = 0;
                $content['up'] = 0;
                $content['down'] = 0;
                $content['share'] = 0;
                $content['update_type'] = 4;
                $content['updatetime'] = time();
                $content['createtime'] = time();
                $this->writeContentSql($content);
            }
            sleep(mt_rand(5, 10));
            ++$page;
        }
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
        }
    }

    /**
     * 获取查询数据.
     */
    public function getQueryData($url)
    {
        // 定义采集规则
        $rules = [
            // 采集文章标题
            'title' => ['.post-title a', 'text'],
            // 采集文章内容
            'content' => ['.post-content', 'text'],
            'url' => ['.post .post-head .post-title a', 'href'],
        ];
        $rt = QueryList::get($url)->rules($rules)->query()->getData();

        return $rt->all();
    }
}

$duzi = new Duanziwang();
$duzi->run();