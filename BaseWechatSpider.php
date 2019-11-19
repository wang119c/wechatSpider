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
class BaseWechatSpider
{
    //数据库配置
    private $config = [];

    //定义header
    private $_header = [
        'Cookie' => 'noticeLoginFlag=1; ua_id=cH1FQb58Qbw2Jf6RAAAAACtdAYzPCJmmcT-KLpvQM3w=; pgv_pvi=9315850240; pgv_si=s8061798400; cert=fgwYTnw7f5l3LBdl_pnj8gB8wqCKm1FO; mm_lang=zh_CN; uuid=a8cd3a8c09f2e24be688193fd879e09e; ticket=dc7b910a4e9b338ed8983d180e464d78191cdded; ticket_id=gh_5cd8b6adaf72; noticeLoginFlag=1; data_bizuin=3088390918; bizuin=3093392608; data_ticket=ObIFQsY2J5o/XMwS9dKrxZFbx0jh6GHeRkKKwsvKBHtpW/qks3M2oD1QopGL+hEB; slave_sid=NTdseHp6X2NuZEhQZms4VU1SczNxOHV4ODQzeHhhbnVGN25xWl9kQUsyemVla3prNU5iRjN3NDA3ekdMekthWjFZcFNiM3BKVngzeEpVaEZWX0d2UGtsX2Q1QnlwWXpmM1pGRlpuZVQxRld2U2lYNW1Fd3hZUnlhOVdkdkJiT0Z1RWZSSzVUSDdJck54NklV; slave_user=gh_5cd8b6adaf72; xid=8f734cbd3a96e6af94cf6846c1d35cc6; openid2ticket_oBhsVuLsmr2QXsXqAvGYU_kwbtjw=7Rug6oz2WTomF2YNRc9iS/ULvanoEeDHtAZKQRC+rAM=; mm_lang=zh_CN',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36',
        'Accept' => 'application/json, text/javascript, */*; q=0.01',
        'X-Requested-With' => 'XMLHttpRequest',
        'Host' => 'mp.weixin.qq.com'
    ];
    //定义token
    private $_token = 1340758952;

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
            'server' => '',
            'username' => '',
            'password' => ''
        ]);
    }


    /**
     * 设置header
     */
    public function setHeader($header)
    {
        $this->_header = $header ;
    }

    /**
     * 设置token
     */
    public function setToken($token)
    {
       $this->_token =  $token ;
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
                $this->num = 0 ;
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
    protected function getWechatArticleList($wechatInfoArr)
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
                    'uuid' => md5($val['link']),
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
}