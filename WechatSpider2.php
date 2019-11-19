<?php
/**
 * 第三个账号相关（来源）常昊
 * 所用账号 ： liantaceshi@163.com
 * 密码: ltceshi2019
 * 命令: php WechatSpider2.php
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
class WechatSpider2 extends BaseWechatSpider
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
            // 'Cookie' => 'noticeLoginFlag=1; ua_id=ITsepi4ZHhI4kWNTAAAAAE199UJn7p5lTQNM12LJ99M=; pgv_pvi=7430504448; noticeLoginFlag=1; mm_lang=zh_CN; pgv_si=s8392474624; cert=tVpkcKgfuiQdSBaormS4hAIWiWVcwoqT; uuid=2a17004e1f0b82b91c59cc57be7aa087; bizuin=3891198127; ticket=7119fe41611dddb3947657df16ed7e5b5f44c32f; ticket_id=gh_de619fef853f; data_bizuin=3891198127; data_ticket=Na5hl1GJ/Z+CRG+e7FLvEiGktxj/K8c6OAJOgonDKjrivQqr/HrwcOfrFQrtfBG+; slave_sid=cjdFQTF0Vnc0T3hNNXZZVmRycVlKQmJlU0p4bW5IYmYwN016akF3NjFXT3Bsc2xmX1BYTWpvSHB2dWQ1Rk9Lb19BM24wdk5OTXpGbmdEMG1FVmZsREpQaDNyNngyUU1vR0ZQdVBxSElfZUx1Y2F1Mmo4cHd2Q3dOMTBqaHNKeFNXQkt2eVZZc25EVGZRNW1O; slave_user=gh_de619fef853f; xid=4d203e29b695312db28b5b0c030e3c6a; openid2ticket_or_ju50Hc18r5guL33UmLWm_L0P8=M2tkBwaXuRfuKEpgHKHPgagM9mZTaOJC76HG7izegnc=',
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
            "cookie"  => "noticeLoginFlag=1; remember_acct=liantaceshi%40163.com; ua_id=UQMq0nL45KG4QEgAAAAAAFyJ86-KB3ZWfNUwHVJ-zNs=; pgv_pvi=5044765696; xid=ddd32c7ea7b64af3a0e11dfab3fa4a46; mm_lang=zh_CN; pgv_pvid=6346027818; noticeLoginFlag=1; pgv_si=s1425084416; uuid=fda4e06b655df7165146be80894b9747; ticket=86e16b36a1464863749c4809a6ddbab73cefa61a; ticket_id=gh_de619fef853f; cert=ptTgk6JCxIbysPawCM7QzMdwMu0Py3oN; openid2ticket_or_ju50Hc18r5guL33UmLWm_L0P8=YvrsnJD0i7xSwC42pCUzjCg00YZw91gYSQC5xLlnU8Y=; rewardsn=; wxtokenkey=777; bizuin=3891198127; data_bizuin=3891198127; data_ticket=5OLxC/jNo4+8hRBdsLUVDvp3uLFK14uAC7MsSzeQWrvBxB30SGPvBK8X+/IU/GDJ; slave_sid=T25FTjdOQ2hjdGdjeFVPWjZ3c05rYm5RVlV2aldmdDdEbGlZTzh6dlNINmVDWk8xSTcxZGFXX3ZoQkZmS2ozU3lic3hTTzZIdHhzMGZ4YzhtcExIUVdFMGd5M24yd0pJQ3JXZ1ZjdEhtb2JQN0ZyRzZBQkVTNUxTNHhMNEQ5a3kwclpZN0dyTTllNEVEdVZk; slave_user=gh_de619fef853f",
            "sec-fetch-mode"  => "cors",
            "sec-fetch-site"  => "same-origin",
            "user-agent"   => "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.70 Safari/537.36",
            "x-requested-with" => "XMLHttpRequest"
        ];
        //定义token
        $token = 1742824595;
        $this->setHeader($header);
        $this->setToken($token);    

    }

    public function run(){
        //这里添加公众号
        $wechatNum = [
            "FinaceRun2018","chaping321","chuangxin2009","ichuangyebang","chuangyejia","caimao_shuangquan","dsjsd_zzs","BigDataDigest","technode","forbes_china","Guokr42","hbrchinese","hexunofficial","huxiu_com","webthinking","topwww001","ciweekly","geekpark","jazzyear","jjrbwx","matrixpartnerschina","ikanchai","kjrbwx","landongpro","ilanjingtmt","zhczyj ","ilieyun","nbdnews","pintu360","pencilnews","qianzhanw","TuyaSmart","iawtmt","rmrbwx","sycaijing","businessweek","cnmo2013","cbdioreview","souhukeji","sootoo123","suanlicaijing","pconline_cn","taimeiti","tencent_if","qqtech","yeskyopen","txws_txws","txxx-news","China-Venture","tianyanguanwei","WDZJ-OFFICIAL","weiphone_2007","znkedu","xinlianjie-","CJDYSWX","techsina","retailreview","pelink","zhenghedao","iceo-com-cn","CIIC_China","cns2012","chinanewsweekly","askci_com","uxuepai5g","huobi968 ","Five_G_WHH","tongxin5g","BigBang_5G","qq5g9098","fxjjqs666","IMT-2020","gh_6cd7cc7563e2","gh_d8a68befa1b0","gudan31456","gcw968","angsmartcar","BTC-5G","C114-5G","gh_e6883f1911ed","FiveGmeetsAI","Smart6500781","cailiao5G","Txjy-123","SH-5Gpack","gh_8c80cda076ad","Shenzhou5G","yanjiu5G","ysjd6699","huaweicorp","cmccguanfang","chinaunicomguanfang","chinatelecom-189","ai-front","HealthAI"
        ];
        $cacheFile = "./cache/WechatSpider2.txt" ;
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

$wechat = new WechatSpider2();
$wechat->run();