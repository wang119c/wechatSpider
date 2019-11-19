<?php
/**
 * Created by PhpStorm.  区块链相关
 * User: huizi
 * Date: 2019/3/21
 * Time: 12:56
 */
require "./vendor/autoload.php";
use houdunwang\db\Db;
use Medoo\Medoo;
use ninvfeng\mysql\mysql;
use QL\QueryList;
use think\Cache;


//初始化db
function db($config, $table = 'null')
{
    static $_db;
    if (!$_db) {
        $_db = new mysql($table, $config);
    }
    return $_db;
}

//随机代理
function proxy()
{

    $arr = [

    ];
    return $arr[array_rand($arr, 1)];
}

/**
 * 微信文章爬虫(转用于链塔)
 * Class BlockChainSpider
 */
class WechatSpiderlianta
{
    //定义header
    private $_header = [
        "Accept" => "application/json, text/javascript, */*; q=0.01",
        "Accept-Encoding"=>"gzip, deflate, br",
        "Accept-Language"=> "zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2",
        "Cookie"=> "noticeLoginFlag=1; ua_id=UQMq0nL45KG4QEgAAAAAAFyJ86-KB3ZWfNUwHVJ-zNs=; pgv_pvi=5044765696; xid=1128bb3e73182cc26120d0f64669324b; mm_lang=zh_CN; pgv_si=s1175367680; uuid=6edcdb523c2e27f2597c3a4f2e8d0b20; bizuin=3233150208; ticket=5fba3e00316c1620198c66b4483ac612d7c4bf22; ticket_id=gh_c31c55239d77; cert=BQ0V5pH0IDla1Uyf5z06aLAtMTQioTmM; data_bizuin=3233150208; data_ticket=I/q3/ZVB117v3/AMIOCTHv3YtlY5by8Rigllt9MmZqB9LwsZ2/VpspzYGaxxMifZ; slave_sid=WGlieWV6Uk5FbTg5MHE0Y1pFRm1LdFNpVTRXM3U0X1B5ck9SaVgzWENPYXhQeXdETU4zaWVBVVg0TEdieVBvVVJmUFNxQXpvdGRobUtNYkJuMnhleVljZlZnMmZqQ3g1MERZWHdVc0VZY0dFaU94c3lpT3NGSXdGZHZpWllCNmFucXBKQWFrMm9kOERkcVIx; slave_user=gh_c31c55239d77; openid2ticket_oAPW1wHgP27IBOK8eK7prj7Ko8yo=cVeVaEY6EAoBgexFOYFw6I5KmAJt7Me/kaGT0Yju3FU=",
        "Host"=> "mp.weixin.qq.com",
        "TE"=>"Trailers",
        "User-Agent"=> "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0",
        "X-Requested-With"=> "XMLHttpRequest"
    ];
    //定义token
    private $_token = 1381543811;

    private $db;

    private $num = 0 ; 

    /**
     * 开始
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/14-19:01
     * @throws Exception
     */
    public function __construct()
    {
        $this->db = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'topai',
            'server' => '',//服务器地址
            'username' => '',//账号名称
            'password' => ''//账号密码
        ]);
    }

    /**
     * 生成随机函数
     * @param int $min
     * @param int $max
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/14-19:11
     * @return float|int
     */
    private function _randFloat($min = 0, $max = 1)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    /**
     * 生成md5加密函数
     * @param $url
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/15-11:02
     * @return string
     */
    private function _midBuildMd5($url)
    {
        $urlArr = parse_url($url);
        parse_str($urlArr['query'], $queryArr);
        return md5($queryArr['mid']);
    }


    /**
     * 获取微信数据
     * @param $wechatNum
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/15-15:02
     * @return array
     */
    protected function getWechatNum($wechatNum)
    {
        echo "抓取公众号信息" . $wechatNum . "...\n\r";
        try {
//            $wechatUrl = "https://mp.weixin.qq.com/cgi-bin/searchbiz?action=search_biz&token=1326854404&lang=zh_CN&f=json&ajax=1&random=0.8034056611217777&query=chainnewscom&begin=0&count=5";

//            https://mp.weixin.qq.com/cgi-bin/searchbiz?action=search_biz&token=2136755505&lang=zh_CN&f=json&ajax=1&random=0.06142084902089029&query=hxcj24h&begin=0&count=5
            $ql = QueryList::get('https://mp.weixin.qq.com/cgi-bin/searchbiz', [
                'action' => 'search_biz',
                'token' => $this->_token,
                'lang' => 'zh_CN',
                'f' => 'json',
                'ajax' => '1',
                'random' => $this->_randFloat(),
                'query' => $wechatNum,
                'begin' => 0,
                'count' => 5
            ], [
                'headers' => $this->_header
            ]);
            $html = $ql->getHtml();
            $wechatRes = \GuzzleHttp\json_decode($html, true);

            if (isset($wechatRes['base_resp']['err_msg']) && $wechatRes['base_resp']['err_msg'] == "ok") {  //可以抓取公众号
                $this->num = 0 ;
                $wechatInfoArr = [];
                $wechatInfoArr["fakeid"] = $wechatRes['list'][0]['fakeid'];
                $wechatInfoArr["nickname"] = $wechatRes['list'][0]['nickname'];
                $wechatInfoArr["alias"] = $wechatRes['list'][0]['alias'];
                $wechatInfoArr["round_head_img"] = $wechatRes['list'][0]['round_head_img'];
                $wechatInfoArr["service_type"] = $wechatRes['list'][0]['service_type'];
                //echo json_encode($wechatInfoArr);
                //获取公众号文章的列表
                $page = 3 ;
                $begin = 15 ;
                $this->getWechatArticleList($wechatInfoArr,$page,$begin);
            } else {
                //这里进行尝试抓取5次,如果不行就直接把程序挂掉,说明已经被限制,需要更换ip或者账号解决
                $this->num = $this->num + 1 ; 
                if($this->num > 5){
                    echo "---程序停止运行(可能原因被限制,需要更换ip或者账号解决),需更换后重新启动---";
                    die;
                }
                $this->getWechatNum($wechatNum);
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }


    /**
     * 文章列表
     * @param $wechatInfoArr
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/15-17:17
     */

    protected function getWechatArticleList($wechatInfoArr,&$page,&$begin)
    {
        while(true){
            sleep(mt_rand(5, 10));
            echo "抓取文章列表第-".$page."-页,开始于-".$begin."-数量...\n\r";
            //$url = "https://mp.weixin.qq.com/cgi-bin/appmsg?token=2136755505&lang=zh_CN&f=json&ajax=1&random=0.7231723217024018&action=list_ex&begin=0&count=5&query=&fakeid=MzUxMTcwNDQzMA%3D%3D&type=9";
            $ql = QueryList::get($url = "https://mp.weixin.qq.com/cgi-bin/appmsg", [
                'token' => $this->_token,
                'lang' => 'zh_CN',
                'f' => 'json',
                'ajax' => 1,
                'random' => $this->_randFloat(),
                'action' => 'list_ex',
                'begin' => $begin,
                'count' => 5,
                'query' => '',
                'fakeid' => $wechatInfoArr['fakeid'],
                'type' => 9
            ], [
                'headers' => $this->_header
            ]);
            $html = $ql->getHtml();
            $articleRes = \GuzzleHttp\json_decode($html, true);

            if (isset($articleRes['base_resp']['err_msg']) && $articleRes['base_resp']['err_msg'] == "ok") {

                if(!$articleRes['app_msg_list']){
                    die;
                }

                foreach ($articleRes['app_msg_list'] as $key => $val) {
                    $data = [
                        'title' => $val['title'],
                        'cover' => $val['cover'],
                        'source' => $wechatInfoArr['nickname'],
                        'message' => htmlspecialchars($this->getContent($val['link'])),
                        'url' => $val['link'],
                        'summary' => $val['digest'],
                        'uuid' => md5($val['link']),
                    ];
                    echo "写入" . $val['title'] . "-----\n\r";
                    $this->writeSql($data);
                }
                $page = $page+1 ; 
                var_dump($page);
                $begin = $page * 5 ;
            } else {
                var_dump($articleRes);
                $this->getWechatArticleList($wechatInfoArr,$page,$begin);
            }    
        }
    }


    /**
     * 获取微信文章
     * @param $url
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/15-12:02
     * @return mixed
     */
    protected function getContent($url)
    {
        sleep(mt_rand(5, 15));
        $ql = QueryList::getInstance();
        //注册一个myHttp方法到QueryList对象
        $ql->bind('myHttp', function ($url) {
            $html = file_get_contents($url);
            $this->setHtml($html);
            return $this;
        });
        //获取微信文章的内容
        $content = $ql->myHttp($url)->find("#js_content.rich_media_content")->html();
        return $content;
    }


    /**
     * 写入数据库
     * @param $data
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/3/21-15:40
     */
    protected function writeSql($data)
    {
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
                'updatetime' => $_SERVER["REQUEST_TIME"]
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
                'createtime' => $_SERVER["REQUEST_TIME"],
                'updatetime' => $_SERVER["REQUEST_TIME"]
            ]);
        }
    }



    /**
     * 运行
     */
    public function run(){
        //这里添加公众号
        $wechatNum = "liantazhiku";
        $this->getWechatNum($wechatNum);
    }
}

$wechat = new WechatSpiderlianta();
$wechat->run();