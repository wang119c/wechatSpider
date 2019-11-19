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

    private $num = 0 ; 

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
            'server' => '',
            'username' => '',
            'password' => ''
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
        $this->num = 0 ;
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
            // var_dump($wechatRes);
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
        sleep(mt_rand(10, 15));
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
                    'uuid' => md5($val['link'])
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
        sleep(mt_rand(10, 15));
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
    'Cookie' => 'noticeLoginFlag=1; remember_acct=1052430943%40qq.com; pgv_pvi=6201848832; ts_uid=3782855609; pgv_pvid=5985652995; ua_id=UIWI0O6P0Ba212A0AAAAANdyUFxNuBGGH3K0Ofgm3HM=; mm_lang=zh_CN; _ga=GA1.2.953096203.1545883403; RK=lhB4i3O9Up; ptcz=45967851669dca010c3028fca8e43f0c6858817469c6dce1cf3b23b4943d5f6b; __guid=166713058.3019338939412664000.1557800613384.7297; wxuin=67043954518077; openid2ticket_or_ju50Hc18r5guL33UmLWm_L0P8=wFZPHXUG+DYJ5F8uwbxHElzvnp73ZhM/WzA7TcYPCno=; noticeLoginFlag=1; pgv_si=s177581056; pgv_info=ssid=s6415961800; rewardsn=; wxtokenkey=777; uuid=45e42177e45ab911eb430b0b12bbc261; data_bizuin=3891198127; bizuin=3891198127; data_ticket=+rMtwqaoEhKrPNoanOzycsaFUwU0mYBh45fm2o7eeQFAC3BuPtUJ37EBhUeKiTUn; slave_sid=bXNuZDZjV2tQWkRLUFhMTWhzRWRxR3F6OGNQQTUxaVB1bFpSelJxSkRUMzJpVU11cERYMXNuZUJOZWpPTWtUQXhLWHdBQXdEcEcxR2pTdzVzQmltMEtnSERuakJhTnVqRDE0c0VvZXNBNlRhMVZuWklXQ3BocFdlYk1RaGF5dnphb3U0QTdUdGowUE5DVzNB; slave_user=gh_de619fef853f; xid=e36cba7b044901f96bf7c8f397a513f7; monitor_count=3',
    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36',
    'Accept' => 'application/json, text/javascript, */*; q=0.01',
    'X-Requested-With' => 'XMLHttpRequest',
    'Host' => 'mp.weixin.qq.com'
];

//这里添加公众号
$wechatNum = [
    "weixin21cbr","jjbd21","ipozaozhidao","i-caijing","yibencaijing","shzqbwx","cien_offical","chinafundnews","zxbccn","cetnews","ourcecn","chinabusinessjournal","xhszzb","cn-finance","lanjinghj","everydayP2P","hjtxs0","finance_ifeng","quanshangcn","kongfuf","wallstreetcn","ths518","gjjrb777","cctvyscj","Bloomberg_Daybook","touzishibao","pedaily2012","wabeiwang","sinacaijing","thf-review","jiemian_2014","cbn-yicai","financeapp","fengchao-caijing","rong-data","securitiesdaily","i-caijing","cailianpress","jrjnews2013","JPMMedia","xincaijing","xueqiujinghua","Finance_01","xunyugen","zhaiquanonline","absofchina","abssjqy","cmbjrsc","cn-abs","ftcweixin","REITsResearch","BOND_DCM","ibaoyouqu","baoxianbagua","bixianshanzhuang","FICC_DDRK","buzhoushan-view","cjlhh8888","mofzpy","chahuagujing","dalirufeng","zhihuwangdali","dao_bi_zhai","dudabs","gh_8073c41a1efe","banklawcn2","tradelikewater","fenxishixubiao","gh_674027a57de6","guzhiluoji1982","gh_226be3052602","gushoubinfa","GUSHOU_HUI","gh_14a120787007","glmacro","hanfaye-xu","gh_1f3b102d4a72","hey-stone-money","jiangchao8848","JYYSX13","baguanvpindao","banklawcn","jinrongjielaoqiu","Fivecrossing","jrxhb2014","JIN_FINANCE","TheEconomistGroup","cjlhh588","jiucaishuo",
    
    "ichart360","jiu_cai","Fighting_Pub","lang-club","laoduansx","laosijicj","ltgd888","lixunlei0722","unitedratings","Lhtz_Jqxx","linqikanpan999","longshuyetan","CIB_Research_MacEcon","miemierap","simuppw","Pydp888","Rating-Utopia","pfyhjrscyw","pystandard","mymoney888","gh_51b4540572ef","gh_2da997b8c1e3","Ezhers","abscloud","cssgs2019","ccefccef","tantianshuozhai","tongyebu2017","xiaobingxiaoshuo","touhang888","sinaxxm","gh_a849fd3eaab7","visualfinance","wutongshuxiabw","wugenotes","kenficc","gh_841e622c5ced","TinyTimesBigAM","xintuowang","xuetao_macro","xunxiajun","yiduanmianxian","yizhangtuyanbao","kopleader","usetrust","gh_0d8a147b24ac","yuanyuan_abs","TJlingle8","yuefenginvest","yyruyu","zhouyue_ficc","ynducai","zhaijidi998","zhaiquanquan2013","Bondfamily","chinabond_monthly ","bondreview","quakeficc","i-bonds","gushequ","whatever201812","xintuoxiehui","chinabond_ccdc","cehzhaishi","FICC_CICC","CBR_2010 ","ziguanjiurijun","ziguanjun","xintuoquaner","i199it","wow36kr","weixin51cto","CSDNnews","deeptechchina","ilovedonews","fintech007","it168_weixin","vittimes","ithomenews","iheima","zMiHomes","o-daily","p2pguancha","wepingwest","TechWeb","tech618","ZOLTech","aliresearch","ifanr","BBT_JLHD",
    
    "FinaceRun2018","chaping321","chuangxin2009","ichuangyebang","chuangyejia","caimao_shuangquan","dsjsd_zzs","BigDataDigest","technode","forbes_china","Guokr42","hbrchinese","hexunofficial","huxiu_com","webthinking","topwww001","ciweekly","geekpark","jazzyear","jjrbwx","matrixpartnerschina","ikanchai","kjrbwx","landongpro","ilanjingtmt","zhczyj ","ilieyun","nbdnews","pintu360","pencilnews","qianzhanw","TuyaSmart","iawtmt","rmrbwx","sycaijing","businessweek","cnmo2013","cbdioreview","souhukeji","sootoo123","suanlicaijing","pconline_cn","taimeiti","tencent_if","qqtech","yeskyopen","txws_txws","txxx-news","China-Venture","tianyanguanwei","WDZJ-OFFICIAL","weiphone_2007","znkedu","xinlianjie-","CJDYSWX","techsina","retailreview","pelink","zhenghedao","iceo-com-cn","CIIC_China","cns2012","chinanewsweekly","askci_com","uxuepai5g","huobi968 ","Five_G_WHH","tongxin5g","BigBang_5G","qq5g9098","fxjjqs666","IMT-2020","gh_6cd7cc7563e2","gh_d8a68befa1b0","gudan31456","gcw968","angsmartcar","BTC-5G","C114-5G","gh_e6883f1911ed","FiveGmeetsAI","Smart6500781","cailiao5G","Txjy-123","SH-5Gpack","gh_8c80cda076ad","Shenzhou5G","yanjiu5G","ysjd6699","huaweicorp","cmccguanfang","chinaunicomguanfang","chinatelecom-189","ai-front","HealthAI",
    
    "ai_xingqiu","Vogel-AI","rgznai100","aitechnews","VRtrends","aitechtalk","World_2078","AIViews","aicjnews","CAAI-1981","gkzhan","AItists","almosthuman2014","Auto_AI_Tech","tencent_ailab","AI_era","QbitAI","daomawuyu","dingdaoshi123","fstalk","ijincuodao","techread","it-reporter","leijunxiaomi","kaifu","gh_5de1cad32f50","runliu-pub","liu_xingliang","qspyq2015","tanhaojun1962","wang-guanxiong","wanglifen2014","wuxiaobopd","kanshi1314","lawyer_xiaosa","zepinghongguan","zhangming_iwep","Left-Right-007","the31area","finance-91","OKBS2018","findDapp","UPliancan","baweiziben","bitcoin8btc","hellobtc","FinaceRun2018","btc798","daslab","coin_poison","Gamer3477","BitBond007","gh_1d3c0013d7db","gh_2279df4d40e5","SNCrating","boliancaijing","block-edu","chengpishu","Bit-Analysis","i54daxiang","i_dianshi","erduocaijing","fengchao-caijing","ifengblockchain","ConsensusLab","svblock","HXQKL01","hufumoney","HiveEcon","qukuailtz","hxcj24h","gh_b00a8506012a","jiedian2018","nodecapital","lanhubiji","gh_4e1c17126b30","iqklbs","lianchaguan","ChainDD","Block-Chain-Law","gh_a560a2118e05","lianneican","liantazhiku","liantuancaijing","chainnewscom","maitiannews","bitbee24","nvxia9898","itxcl168","blockchain_camp","BCtoplist","qukuailian-lh",
    
    "qkl-hunt","BlockBeats","cc-value","MyTuoniao","wangfengshiwen","weilaicj","xinlianjie-","caijilicai","xinghuancj","yangtuoqkl","elementchain","gh_ae9197bdd2d3","CHC_Consultant_Group","cmic_gov","DigiHealthcare-ZXY","yigoonet","vcbeat","nfsyyjjb","DAYI2006","MIC366","TianJianMedi","medtac","iBio4P","Smart_HealthCare","HBMclub","emedclub_com","TheLancetChina","sanjiachuanzhen","LT0385","DingXiangYiSheng","huayiwang91","vom120","cmt_1999","wwwyaoqnet","yxsapp","medlive","herbalifedaily","yxtonline","yixuejiezazhi","scipmed","medieco-ykh","gh_6065f90351ed","chezhuzhijia","lmcs88865678","dianchehui","onion-automobile","QiCheBang","dabiaoche","rvtirble","TigerGaoChe","carfanr","CarBingoWX","i-Geek","gzwctv","ershixiongCar","wscs18nian","emaonews","Speedsters-Auto","sskc__","SeleCar","amscheping","idongche","beitaishuoche","xinchepingwang","haochebang","bofanshuoche","csh_csz","Channel_Max","speecar","gclungu","chebangjun","iautofuture","chedongxi","yuguo20141021","iweiche","cheyiquan2016","wupeipindao","xiaobaimaiche","zhongguoqichebao","bjqcb01","Auto-Industry-Review","automergers","dgchezhi","wowoauto","douzhiwenhua","qicheqingbaozu","lookarTV"
];
$token = 1750771037;
$wechatSpider = new WechatSpider($token, $header);

$allWechatInfo = [];
foreach ($wechatNum as $key => $value) {
    $wechatInfoArr = $wechatSpider->getWechatNum($value);
}


//$wechatSpider->getWechatArticleList($wechatInfoArr);

//Cache::set('val','value',600);
//// 获取缓存
//var_dump(Cache::get('val'));


