<?php
/**
 * 第二个 相关 (账号来源)李伟
 * 所用账号 ： lianta13001270579@163.com
 * 密码: liantazhiku2019
 * 命令: php WechatSpider1.php
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
class WechatSpider1 extends BaseWechatSpider
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
            // 'Cookie' => 'noticeLoginFlag=1; remember_ac…5e7OnBzMfZEWyaFHYMe/uXvi+PCQ=',
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
            "cookie"  => "noticeLoginFlag=1; remember_acct=lianta13001270579%40163.com; pgv_pvi=916880384; noticeLoginFlag=1; ptui_loginuin=1052430943@qq.com; RK=CLqxNg5cEz; ptcz=2bb285ad4008b35b201e2e756fff580ac57098e45b77f3c097e9d382554d1182; pgv_si=s5644584960; cert=Kqc1NnPXDbhkMrbg7qejtZSmow5v2UQr; uuid=720e5dd0a779307122a8954fbaee7dab; ticket=1007f851e43e2f0b64d73e551557e69d2a6cb7e3; ticket_id=gh_c0c834e58333; data_bizuin=3251678971; bizuin=3251678971; data_ticket=KYKn2nsExrSu9fyk6ndl3iNMvoByTkNeiEGYi/h4NYFLIhKRY6Zdt0go+h7IWKAc; ua_id=JN7L59uOV7n3tMtHAAAAAK0FEOFzB2wWue0FAvQWGlU=; slave_sid=ZG1aSTdBaXpyaXhFS2t4YVRyd3ZmdzVaSENFblNneWlrakR6RU91MkdLckZrWlJvSTdvOHZfWkIwXzBna2J3REZkMGZtbUU1SEVoVG5NTHY2X3B0X1pyTkRGdlNlNzkwcEVVQjFjcExBRFI5VkVFX2xHdVYyOWRWT2hjSUlZUVZGWmdNZW9VeWdzYWExaloy; slave_user=gh_c0c834e58333; xid=85fdced0874a3314dad9f78ec950d940; openid2ticket_o-67QweT_Sl1KqFGMJXWYV3iwkgA=EQmyRFeeqHQ95GxtUiYMPB39j5pyVoERfIzOGwlI9q4=; mm_lang=zh_CN",
            "sec-fetch-mode"  => "cors",
            "sec-fetch-site"  => "same-origin",
            "user-agent"   => "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.70 Safari/537.36",
            "x-requested-with" => "XMLHttpRequest"
        ];
        //定义token
        $token = 915409709;
        $this->setHeader($header);
        $this->setToken($token);
    }

    public function run(){
        //这里添加公众号
        $wechatNum = [
            "ichart360","jiu_cai","Fighting_Pub","lang-club","laoduansx","laosijicj","ltgd888","lixunlei0722","unitedratings","Lhtz_Jqxx","linqikanpan999","longshuyetan","CIB_Research_MacEcon","miemierap","simuppw","Pydp888","Rating-Utopia","pfyhjrscyw","pystandard","mymoney888","gh_51b4540572ef","gh_2da997b8c1e3","Ezhers","abscloud","cssgs2019","ccefccef","tantianshuozhai","tongyebu2017","xiaobingxiaoshuo","touhang888","sinaxxm","gh_a849fd3eaab7","visualfinance","wutongshuxiabw","wugenotes","kenficc","gh_841e622c5ced","TinyTimesBigAM","xintuowang","xuetao_macro","xunxiajun","yiduanmianxian","yizhangtuyanbao","kopleader","usetrust","gh_0d8a147b24ac","yuanyuan_abs","TJlingle8","yuefenginvest","yyruyu","zhouyue_ficc","ynducai","zhaijidi998","zhaiquanquan2013","Bondfamily","chinabond_monthly ","bondreview","quakeficc","i-bonds","gushequ","whatever201812","xintuoxiehui","chinabond_ccdc","cehzhaishi","FICC_CICC","CBR_2010 ","ziguanjiurijun","ziguanjun","xintuoquaner","i199it","wow36kr","weixin51cto","CSDNnews","deeptechchina","ilovedonews","fintech007","it168_weixin","vittimes","ithomenews","iheima","zMiHomes","o-daily","p2pguancha","wepingwest","TechWeb","tech618","ZOLTech","aliresearch","ifanr","BBT_JLHD",
        ];
        $cacheFile = "./cache/WechatSpider1.txt" ;
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

$wechat = new WechatSpider1();
$wechat->run();
