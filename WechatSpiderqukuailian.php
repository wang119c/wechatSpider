<?php
/**
 * Created by PhpStorm.  区块链相关
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
class WechatSpiderqukuailian extends BaseWechatSpider
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
            "cookie"  => "ua_id=ITsepi4ZHhI4kWNTAAAAAE199UJn7p5lTQNM12LJ99M=; pgv_pvi=7430504448; noticeLoginFlag=1; mm_lang=zh_CN; pgv_si=s3085710336; uuid=9b3265c7d8880534952f5889dd02862a; bizuin=3891198127; ticket=aa654f57f53c3601c7af266d3cdb2ee84c759314; ticket_id=gh_de619fef853f; cert=5boXZml4_UexARllPXptd9dM7xM1DU44; data_bizuin=3891198127; data_ticket=C+ofWz8WEYO3KXd3+jgbjwhZQInNYod9eoWK5qvLJmWtulaquX8MGOIrKlYDCd2V; slave_sid=UHJqS2Fyc3EyMmZzaWhMTEo2X2VYZTd6SnNvZzdDOTh4MWVRZlRkcGlGOGtUOEZuaXlSbEFoaWI1YmZ5eFBzdFF1dTJiTExsZFF0VG8zbGlRNWUyUERrVEVDMlNpb0d4cmdUZTNEcmwzVXllZkdDTDVoZzFEdW41MFowdm11aEZXMFBNMmMwQWdoTGlIRTly; slave_user=gh_de619fef853f; xid=fb596fb023b78798a89bff95329c479e; openid2ticket_or_ju50Hc18r5guL33UmLWm_L0P8=cqtNkaMhcipoiI8ZFQy8bs+tWyFwVnmePUZ3SBAwri4=",
            "sec-fetch-mode"  => "cors",
            "sec-fetch-site"  => "same-origin",
            "user-agent"   => "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.70 Safari/537.36",
            "x-requested-with" => "XMLHttpRequest"
        ];
        //定义token
        $token = 798203196;
        $this->setHeader($header);
        $this->setToken($token);
    }

    /**
     * 运行
     */
    public function run(){
        //这里添加公众号
        $wechatNum = [
            "the31area","finance-91","OKBS2018","findDapp","UPliancan","baweiziben","bitcoin8btc","hellobtc","FinaceRun2018","btc798","daslab","coin_poison","Gamer3477","BitBond007","gh_1d3c0013d7db","gh_2279df4d40e5","SNCrating","boliancaijing","block-edu","chengpishu","Bit-Analysis","i54daxiang","i_dianshi","erduocaijing","fengchao-caijing","ifengblockchain","ConsensusLab","svblock","HXQKL01","hufumoney","HiveEcon","qukuailtz","hxcj24h","gh_b00a8506012a","jiedian2018","nodecapital"
        ];

        $cacheFile = "./cache/WechatSpiderqukuailian.txt" ;
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

$wechat = new WechatSpiderqukuailian();
$wechat->run();