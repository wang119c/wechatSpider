<?php
require_once "../vendor/autoload.php";

use OSS\Core\OssException;
use OSS\OssClient;

ini_set("display_errors", "On");
error_reporting(E_ALL);

class AliOss
{
    private $accessKeyId = "LTAIW4bx6yxgz6jQ";
    private $accessKeySecret = "CkvsUAx7oTeqqydNx4tEESLEx1m2me";
    private $endpoint = "http://oss-cn-beijing.aliyuncs.com";
    private $bucket = "gaoxiaolianmengshe";

    public function __construct()
    {
        // $config = require_once "../config/config.php";
        // $this->accessKeyId = $config["accessKeyId"];
        // $this->accessKeySecret = $config["accessKeySecret"];
        // $this->endpoint = $config["endpoint"];
        // $this->bucket = $config["bucket"];
    }

    /**
     * 上传文件
     */
    public function uploadFile($url)
    {
        usleep(100 * 1000);
        //http: //file.yyuehd.com/FvEQUb5VanVFjUisaNhYDWa7LQ7M/videoFirstFrame
        //http: //dlvideo.izuiyou.com/zyvd/ff/71/6a22-8a8d-11e9-b3b0-00163e042306
        $mimes = array(
            'image/bmp' => 'bmp',
            'image/gif' => 'gif',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/x-icon' => 'ico',
            'video/mp4' => 'mp4',
        );
        $object = md5($url);
        // $data = getimagesize("http://file.izuiyou.com/img/view/id/676256473");
        // var_dump($data);

        //判断是不是图片，如果是图片用图片的方式处理, 如果不是图片是视频用视频的方式处理
        if (getimagesize($url) !== false) {
            $headers = getimagesize($url);
            $type = $headers['mime'];
        } else {
            $headers = get_headers($url, 1);
            $type = $headers['Content-Type'];
        }
        //get_header
        //  if (($headers = getimagesize($url)) !== false) {

        if (isset($mimes[$type])) {
            $extension = $mimes[$type];
            // 文件名称
            $object = md5($url) . '.' . $extension;
            // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt
            $content = file_get_contents($url);
            try {
                var_dump($this->accessKeyId);
                $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
                $ossClient->putObject($this->bucket, $object, $content);
                return $object;
            } catch (OssException $e) {
                print(__FUNCTION__ . ": FAILED\n");
                print($e->getMessage() . "\n");
                throw new Exception($e);
                return;
            }
            print(__FUNCTION__ . ": OK" . "\n");
        }
    }
    // }
}

// $url = 'http://dlvideo.izuiyou.com/zyvd/ff/71/6a22-8a8d-11e9-b3b0-00163e042306';
//(new AliOss())->uploadFile("http://dlvideo.izuiyou.com/zyvd/ff/71/6a22-8a8d-11e9-b3b0-00163e042306");
//(new AliOss())->uploadFile("http://file.izuiyou.com/img/view/id/674341787");