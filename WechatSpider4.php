<?php
/**
 * 第五个 相关 (账号来源)金科社
 * 所用账号 ： zhangfx@blockdata.group
 * 密码: lianta999
 * 命令: php WechatSpider4.php
 * Created by PhpStorm.
 * User: huizi
 * Date: 2019/3/21
 * Time: 12:56
 */
require "./vendor/autoload.php";
require_once "BaseWechatSpider.php";
use houdunwang\db\Db;
use Medoo\Medoo;
use ninvfeng\mysql\mysql;
use QL\QueryList;
use think\Cache;

/**
 * 微信文章爬虫
 * Class BlockChainSpider
 */
class WechatSpider4 extends BaseWechatSpider
{
    /**
     * 开始
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/14-19:01
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        //定义header
        $header = [
            // 'Cookie' => 'ua_id=ITsepi4ZHhI4kWNTAAAAAE199UJn7p5lTQNM12LJ99M=; pgv_pvi=7430504448; noticeLoginFlag=1; mm_lang=zh_CN; pgv_si=s3085710336; uuid=9b3265c7d8880534952f5889dd02862a; bizuin=3891198127; ticket=aa654f57f53c3601c7af266d3cdb2ee84c759314; ticket_id=gh_de619fef853f; cert=5boXZml4_UexARllPXptd9dM7xM1DU44; data_bizuin=3891198127; data_ticket=C+ofWz8WEYO3KXd3+jgbjwhZQInNYod9eoWK5qvLJmWtulaquX8MGOIrKlYDCd2V; slave_sid=UHJqS2Fyc3EyMmZzaWhMTEo2X2VYZTd6SnNvZzdDOTh4MWVRZlRkcGlGOGtUOEZuaXlSbEFoaWI1YmZ5eFBzdFF1dTJiTExsZFF0VG8zbGlRNWUyUERrVEVDMlNpb0d4cmdUZTNEcmwzVXllZkdDTDVoZzFEdW41MFowdm11aEZXMFBNMmMwQWdoTGlIRTly; slave_user=gh_de619fef853f; xid=fb596fb023b78798a89bff95329c479e; openid2ticket_or_ju50Hc18r5guL33UmLWm_L0P8=cqtNkaMhcipoiI8ZFQy8bs+tWyFwVnmePUZ3SBAwri4=',
            // 'user-agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.70 Safari/537.36',
            // 'accept' => 'application/json, text/javascript, */*; q=0.01',
            // 'x-requested-with' => 'XMLHttpRequest',
            // 'Host' => 'mp.weixin.qq.com'
            ":authority" => "mp.weixin.qq.com",
            ":method"   => "GET",
            ":scheme"   => "https",
            "accept"    => "application/json, text/javascript, */*; q=0.01",
            "accept-encoding" => "gzip, deflate, br",
            "accept-language" => "zh-CN,zh;q=0.9",
            "cookie"  => "ua_id=ITsepi4ZHhI4kWNTAAAAAE199UJn7p5lTQNM12LJ99M=; pgv_pvi=7430504448; noticeLoginFlag=1; mm_lang=zh_CN; pgv_si=s3085710336; cert=5boXZml4_UexARllPXptd9dM7xM1DU44; uuid=77224d85d08167ab6a5ae00bd84bc867; ticket=0c362fb912b0e4f32ee0ba13479d329720ab3028; ticket_id=gh_5cd8b6adaf72; data_bizuin=3088390918; bizuin=3093392608; data_ticket=3teAaqvL7AUQdDeO/OP81v7H/3uFbsvlDay/0rOHAMkVXV4eJ22/Hajavasf+5JJ; slave_sid=U2EwMVk2Y1l1TlNwaHBFSmVycFh2ZHJjMFhONTVTNEsyb25DNDNmU0xGVms0SmlPQVA2UkZEbVRWdmlZUHJWR0VuR21wMUtsYTN4YVZVdExkaERCMjYwSll6VnV5dXFJcXM2YmZtX0FoZ3JGcUNKRlpkaEE4UWQzd3FlM3BZd0Z0NlJKRVBNNlBVWXRmbjI4; slave_user=gh_5cd8b6adaf72; xid=a78f64323e80ab97f23546627db47e4f; openid2ticket_oBhsVuLsmr2QXsXqAvGYU_kwbtjw=ngWFyLdlkSaS71AGRf02+eA6I2h23LWRrDyU+xJ2awM=",
            "sec-fetch-mode"  => "cors",
            "sec-fetch-site"  => "same-origin",
            "user-agent"   => "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.70 Safari/537.36",
            "x-requested-with" => "XMLHttpRequest"
        ];
        //定义token
        $token = 371838304;
        $this->setHeader($header);
        $this->setToken($token);

    }

    public function run(){
        //这里添加公众号
        $wechatNum = [
            "qkl-hunt","BlockBeats","cc-value","MyTuoniao","wangfengshiwen","weilaicj","xinlianjie-","caijilicai","xinghuancj","yangtuoqkl","elementchain","gh_ae9197bdd2d3","CHC_Consultant_Group","cmic_gov","DigiHealthcare-ZXY","yigoonet","vcbeat","nfsyyjjb","DAYI2006","MIC366","TianJianMedi","medtac","iBio4P","Smart_HealthCare","HBMclub","emedclub_com","TheLancetChina","sanjiachuanzhen","LT0385","DingXiangYiSheng","huayiwang91","vom120","cmt_1999","wwwyaoqnet","yxsapp","medlive","herbalifedaily","yxtonline","yixuejiezazhi","scipmed","medieco-ykh","gh_6065f90351ed","chezhuzhijia","lmcs88865678","dianchehui","onion-automobile","QiCheBang","dabiaoche","rvtirble","TigerGaoChe","carfanr","CarBingoWX","i-Geek","gzwctv","ershixiongCar","wscs18nian","emaonews","Speedsters-Auto","sskc__","SeleCar","amscheping","idongche","beitaishuoche","xinchepingwang","haochebang","bofanshuoche","csh_csz","Channel_Max","speecar","gclungu","chebangjun","iautofuture","chedongxi","yuguo20141021","iweiche","cheyiquan2016","wupeipindao","xiaobaimaiche","zhongguoqichebao","bjqcb01","Auto-Industry-Review","automergers","dgchezhi","wowoauto","douzhiwenhua","qicheqingbaozu","lookarTV","Trendhc","qtbigdata","gh_89f18299688e","hangzhoucjw","JPMMedia"
        ];
        $cacheFile = "./cache/WechatSpider4.txt" ;
        if(!is_file($cacheFile)){
            file_put_contents($cacheFile,"");
        }
        while(true){
            try{
                //查询缓存里面有没有数据
                $data = file_get_contents($cacheFile);
                if( $data == ""  ){
                    //从索引0开始进行抓取,并写入索引0的数据
                    $currentIndex = 0 ;
                    $currentValue = $wechatNum[0] ;
                    file_put_contents($cacheFile, sprintf("%s::%d",$currentValue,$currentIndex));
                    $this->getWechatNum($currentValue);
                }else{
                    //获取当前的索引,并进行自增进行抓取
                    $findArr = explode("::",$data);
                    $currentIndex = $findArr[1] + 1  ;
                    //判断索引的范围
                    if(  $currentIndex  >=  count($wechatNum)  ){
                        //写入空的数据
                        file_put_contents($cacheFile, "");
                        echo "---本页面抓取完,请重新运行程序---";
                        die;
                    }
                    $currentValue = $wechatNum[$currentIndex] ;
                    //写入数据
                    file_put_contents($cacheFile, sprintf("%s::%d",$currentValue,$currentIndex));
                    //这里进行抓取
                    $this->getWechatNum($currentValue);
                }
            }catch(Exception $e){
                print(111);
            }
        }
    }
}

$wechat = new WechatSpider4();
$wechat->run();