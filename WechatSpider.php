<?php
/**
 * Created by PhpStorm.
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
 * 微信文章爬虫
 * Class BlockChainSpider
 */
class WechatSpider
{
    //数据库配置
    private $config = [];

    //定义header
    private $_header = [];
    //定义token
    private $_token;

    private $db;

    /**
     * 开始
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/14-19:01
     * @throws Exception
     */
    public function __construct($token, $header)
    {
        $this->db = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'topai',
            'server' => '47.93.204.37',
            'username' => 'root',
            'password' => '9jXv57znY4D.B'
        ]);
        $this->_header = $header;
        $this->_token = $token;
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
    public function getWechatNum($wechatNum)
    {
        sleep(mt_rand(5, 8));
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
                $wechatInfoArr = [];
                $wechatInfoArr["fakeid"] = $wechatRes['list'][0]['fakeid'];
                $wechatInfoArr["nickname"] = $wechatRes['list'][0]['nickname'];
                $wechatInfoArr["alias"] = $wechatRes['list'][0]['alias'];
                $wechatInfoArr["round_head_img"] = $wechatRes['list'][0]['round_head_img'];
                $wechatInfoArr["service_type"] = $wechatRes['list'][0]['service_type'];
                //echo json_encode($wechatInfoArr);
                //获取公众号文章的列表
                $this->getWechatArticleList($wechatInfoArr);
            } else {
                var_dump($wechatRes);
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
    public function getWechatArticleList($wechatInfoArr)
    {
        sleep(mt_rand(5, 10));
        echo "抓取文章列表...\n\r";
        //$url = "https://mp.weixin.qq.com/cgi-bin/appmsg?token=2136755505&lang=zh_CN&f=json&ajax=1&random=0.7231723217024018&action=list_ex&begin=0&count=5&query=&fakeid=MzUxMTcwNDQzMA%3D%3D&type=9";
        $ql = QueryList::get($url = "https://mp.weixin.qq.com/cgi-bin/appmsg?token=2136755505&lang=zh_CN&f=json&ajax=1&random=0.7231723217024018&action=list_ex&begin=0&count=5&query=&fakeid=MzUxMTcwNDQzMA%3D%3D&type=9", [
            'token' => $this->_token,
            'lang' => 'zh_CN',
            'f' => 'json',
            'ajax' => 1,
            'random' => $this->_randFloat(),
            'action' => 'list_ex',
            'begin' => 0,
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
            foreach ($articleRes['app_msg_list'] as $key => $val) {
                $data = [
                    'title' => $val['title'],
                    'cover' => $val['cover'],
                    'source' => $wechatInfoArr['nickname'],
                    'message' => htmlspecialchars($this->getContent($val['link'])),
                    'url' => $val['link'],
                    'summary' => $val['digest'],
                    'uuid' => md5($val['appmsgid'])
                ];
                echo "写入" . $val['title'] . "-----\n\r";
                $this->writeSql($data);
            }
        } else {
            var_dump($articleRes);
            $this->getWechatArticleList($wechatInfoArr);
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
    public function getContent($url)
    {
        sleep(mt_rand(5, 10));
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
    public function writeSql($data)
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
}

$header = [
    'Cookie' => 'noticeLoginFlag=1; ua_id=dDyvniObJ0zSYnMjAAAAAKA8hgSy7LdpxoicsFTjvRc=; pgv_pvi=7205114880; mm_lang=zh_CN; pgv_pvid=334576536; pac_uid=0_5cda2438b42fb; noticeLoginFlag=1; pgv_si=s2334927872; uuid=16bfeb766c157e536b2ed0f487160393; ticket=afd20f9f2ad08adf9e38cfcf720831e367fbe796; ticket_id=gh_5cd8b6adaf72; cert=VJXnnbOpWDOE1KOhhfjNFd2rAn3i4tiP; data_bizuin=3088390918; bizuin=3093392608; data_ticket=JLVGfPXeJB8oNV6pM4Dwr7RiemgICA9CGFxNWqEwmiEllYr82zy2y58t7Pe4rtko; slave_sid=Ym1ROGFwTEwzWHhJMXlpVTY4cnd3dTlRdEQ1NjFlbEhfZUN3QTBkaUdwNDNmS0tLUXNCWVlEOW5SeEFPaUNFNzBPbU93STVYYl9rYlMydkszaFdiaGJWNTNjUjBtc1R1M0dUQW56MmlZSGVzSmdUT05KQXZQTVBrQ2hLOG9DTjBEUHowTE5oUFM5RlQ1WWJo; slave_user=gh_5cd8b6adaf72; xid=73efb3512d48da10f244eb4d638ba56e; openid2ticket_oBhsVuLsmr2QXsXqAvGYU_kwbtjw=pilbLfyf83B2qrG6NSE4192eHXpo/73Dty398+g8MTU=',
    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36',
    'Accept' => 'application/json, text/javascript, */*; q=0.01',
    'X-Requested-With' => 'XMLHttpRequest',
    'Host' => 'mp.weixin.qq.com'
];

$wechatNum = [
    "chainnewscom", "hxcj24h", "o-daily", "jscjhq", "suanlicaijing", "QbitAI", "AI_era", "jazzyear",
    "wuxiaobopd",
    "tancaijing", "finance_ifeng", "nbdnews", "businessweek", "qqtech", "ifanr", "matrixpartnerschina",
    "huxiu_com",
    "wow36kr", "Five_G_WHH", "comobs", "uxuepai5g", "huobi968", "angmobile"
];
$token = 869346968;
$wechatSpider = new WechatSpider($token, $header);

$allWechatInfo = [];
foreach ($wechatNum as $key => $value) {
    $wechatInfoArr = $wechatSpider->getWechatNum($value);
}


//$wechatSpider->getWechatArticleList($wechatInfoArr);

//Cache::set('val','value',600);
//// 获取缓存
//var_dump(Cache::get('val'));


