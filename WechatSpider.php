<?php
/**
 * 第一个 相关 (账号来源)杨源
 * 所用账号 ： 2924981863@qq.com
 * 密码: liantazhiku2019
 * 命令: php WechatSpider.php
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
class WechatSpider extends BaseWechatSpider
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
            "cookie"  => "noticeLoginFlag=1; remember_acct=2924981863%40qq.com; ua_id=ITsepi4ZHhI4kWNTAAAAAE199UJn7p5lTQNM12LJ99M=; pgv_pvi=7430504448; noticeLoginFlag=1; mm_lang=zh_CN; pgv_si=s8392474624; cert=tVpkcKgfuiQdSBaormS4hAIWiWVcwoqT; pgv_info=ssid=s8765013480; pgv_pvid=7656928684; ticket_id=gh_c31c55239d77; uuid=60dabad0640637929031668c60fc10ee; bizuin=3233150208; ticket=2e7b82c3ac29d7ef324783611cc3a588dc64d21c; data_bizuin=3233150208; data_ticket=mu+MToefr4hfdnBedw8nNRzPOVTGOd+azotvjTEbOMqJNv3lz9tOE01tybECT5aq; slave_sid=eTVac2ZYX0ZmaDJUdjdvc0UxeWlMeW1ZZnhvVjBCZzZRMWFVZV9oanZQZ2NuU1ZHQ2F3R3p6dEtkZmZId1Rad1VpMDhURENyQ3BGRGhtUm44UVNEUjBpcDF5WlNuMkJOZUlGUXBsRWdPaGN3T2M2bEdnaWxXb1N0ZFZHcmpLVVBBRUVZeEtFVFNPSms1VTk0; slave_user=gh_c31c55239d77; xid=d23921c688e61885410776b906c36173; openid2ticket_oAPW1wHgP27IBOK8eK7prj7Ko8yo=tItHpX6PMU8EvTtPRUdHnaL6w7S818ZGNw64pTqeXKg=",
            "sec-fetch-mode"  => "cors",
            "sec-fetch-site"  => "same-origin",
            "user-agent"   => "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.70 Safari/537.36",
            "x-requested-with" => "XMLHttpRequest"
        ];
        //定义token
        $token = 677965424;
        $this->setHeader($header);
        $this->setToken($token);

    }

    public function run(){
        
        //这里添加公众号
        $wechatNum = [
            "weixin21cbr","jjbd21","ipozaozhidao","i-caijing","yibencaijing","shzqbwx","cien_offical","chinafundnews","zxbccn","cetnews","ourcecn","chinabusinessjournal","xhszzb","cn-finance","lanjinghj","everydayP2P","hjtxs0","finance_ifeng","quanshangcn","kongfuf","wallstreetcn","ths518","gjjrb777","cctvyscj","Bloomberg_Daybook","touzishibao","pedaily2012","wabeiwang","sinacaijing","thf-review","jiemian_2014","cbn-yicai","financeapp","fengchao-caijing","rong-data","securitiesdaily","i-caijing","cailianpress","jrjnews2013","JPMMedia","xincaijing","xueqiujinghua","Finance_01","xunyugen","zhaiquanonline","absofchina","abssjqy","cmbjrsc","cn-abs","ftcweixin","REITsResearch","BOND_DCM","ibaoyouqu","baoxianbagua","bixianshanzhuang","FICC_DDRK","buzhoushan-view","cjlhh8888","mofzpy","chahuagujing","dalirufeng","zhihuwangdali","dao_bi_zhai","dudabs","gh_8073c41a1efe","banklawcn2","tradelikewater","fenxishixubiao","gh_674027a57de6","guzhiluoji1982","gh_226be3052602","gushoubinfa","GUSHOU_HUI","gh_14a120787007","glmacro","hanfaye-xu","gh_1f3b102d4a72","hey-stone-money","jiangchao8848","JYYSX13","baguanvpindao","banklawcn","jinrongjielaoqiu","Fivecrossing","jrxhb2014","JIN_FINANCE","TheEconomistGroup","cjlhh588","jiucaishuo",
        ];

        $cacheFile = "./cache/WechatSpider.txt" ;
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

$wechat = new WechatSpider();
$wechat->run();

