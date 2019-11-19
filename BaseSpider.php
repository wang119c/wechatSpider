<?php
require "./vendor/autoload.php";
require "NewsSpider.php";

use Medoo\Medoo;

/**
 * Created by PhpStorm.
 * User: huizi
 * Date: 2019/6/3
 * Time: 14:55
 */
class BaseSpider implements NewsSpider
{

    protected $urls;
    protected $headers;
    protected $db;

    public function __construct($urls, $headers)
    {
        $this->urls = $urls;
        $this->headers = $headers;
        $this->db = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'topai',
            'server' => '',
            'username' => '',
            'password' => ''
        ]);
    }

    public function run()
    {
    }

    public function getQueryContent($url)
    {
    }

    public function getQueryData($url)
    {
    }

    public function writeSql($data)
    {
        echo "写入" . $data['title'] . "----------------";

        if ($this->db->get('data_temp_article', "id", [
            'uuid' => $data['uuid']
        ])) {
            $this->db->update("data_temp_article", [
                'title' => $data['title'],
                'cover' => $data['cover'],
                'source' => $data['source'],
                'message' => $data['message'],
                'url' => $data['url'],
                'summary' => $data['summary'],
                'catch_type' => $data['catch_type'],
                'updatetime' => time()
            ], [
                'uuid' => $data['uuid']
            ]);
        } else {
            $data = $this->db->insert('data_temp_article', [
                'title' => $data['title'],
                'cover' => $data['cover'],
                'source' => $data['source'],
                'message' => $data['message'],
                'url' => $data['url'],
                'summary' => $data['summary'],
                'uuid' => $data['uuid'],
                'catch_type' => $data['catch_type'],
                'createtime' => time(),
                'updatetime' => time()
            ]);
        }
    }
}