<?php
/**
 * Created by PhpStorm.
 * User: huizi
 * Date: 2019/3/21
 * Time: 12:56
 */
require "./vendor/autoload.php";

use ninvfeng\mysql\mysql;
use QL\QueryList;


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
        "https://58.218.200.247:8814",
        "https://58.218.201.74:8944",
        "https://58.218.201.74:3908",
        "https://58.218.201.74:3055",
        "https://58.218.200.229:4938",
        "https://58.218.200.229:8159",
        "https://58.218.201.74:8080",
        "https://58.218.200.229:6461",
        "https://58.218.200.247:8368",
        "https://58.218.200.248:5049"
    ];
    return $arr[array_rand($arr, 1)];
}


/**
 * 区块链爬虫
 * Class BlockChainSpider
 */
class BlockChainSpider
{
    //列表的url
    private static $listUrl = "http://www.pss-system.gov.cn/sipopublicsearch/patentsearch/showSearchResult-startWa.shtml";
    //详情的url
    private static $detailUrl = "http://www.pss-system.gov.cn/sipopublicsearch/patentsearch/viewAbstractInfo0529-viewAbstractInfo.shtml";
    //http://www.pss-system.gov.cn/sipopublicsearch/patentsearch/viewAbstractInfo0529-viewAbstractInfo.shtml
    //法律状态url
    private static $lowStatusUrl = "http://www.pss-system.gov.cn/sipopublicsearch/patentsearch/showPatentInfo0405-showPatentInfo.shtml";


    //数据库配置
    private static $config = [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'test',
        'user' => 'root',
        'pass' => 'root'
    ];

    //定义header
    private static $header = [
        'Cookie' => 'wee_username=d2FuZ18xMTlj; wee_password=d2FuZzExOWM%3D; JSESSIONID=nzSjI6OLyrCxNp-PtN9QvL0Zyuqqfwpusew7vz__sCFRxIBjowyk!-529919397!1193910941',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36',
        'X-Requested-With' => 'XMLHttpRequest'
    ];

    /**
     * 开始
     * @param $startPage
     * @param $endPage
     * @param string $lang
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/3/21-15:54
     * @throws Exception
     */
    public static function start($startPage, $endPage, $lang = "zh")
    {
        self::doSpider($startPage, $endPage, $lang);
    }


    /**
     * 运行爬虫
     * @param $startPage
     * @param $endPage
     * @param $lang
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/3/21-15:54
     * @throws Exception
     */
    public static function doSpider($startPage, $endPage, $lang)
    {
        for ($i = $startPage; $i <= $endPage; $i += 12) {
            if ($i >= $endPage) {
                $page = $endPage;
            } else {
                $page = $i;
            }
            if ($lang == "zh") {

                //self::getDetailData("CN201811306195", "CN201811306195.720190308FM", "CN201811306195.720190308FM", "0201101");
            } else {
                self::getEnListData($page);
            }
            sleep(mt_rand(15, 20));
        }

        self::getZhListData(24);

    }

    /**
     * 获取列表数据
     * @param $page
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/3/21-17:37
     * @return bool
     */
    public static function getZhListData($page)
    {
        sleep(mt_rand(2,5));
        echo "抓取列表数据--------" . $page . '------------\r\n';
        $cache_path = __DIR__ . '/temp/';
        try {
            // 发送post请求
            $ql = QueryList::post(self::$listUrl, [
                'resultPagination.limit' => 12,
                'resultPagination.sumLimit' => 10,
                'resultPagination.start' => $page,
                'resultPagination.totalCount' => 6261,
                'searchCondition.sortFields' => '-APD,+PD',
                'searchCondition.searchType' => 'Sino_foreign',
                'searchCondition.originalLanguage' => '',
                "searchCondition.extendInfo['MODE']" => 'MODE_SMART',
                "searchCondition.extendInfo['STRATEGY']" => '',
                'searchCondition.searchExp' => "复合文本=(区块链)",
                "searchCondition.executableSearchExp" => "VDB:(TBI='区块链')",
                "searchCondition.dbId" => "",
                "searchCondition.literatureSF" => "复合文本=(区块链)",
                "searchCondition.targetLanguage" => "",
                "searchCondition.resultMode" => "undefined",
                "searchCondition.strategy" => "",
                "searchCondition.searchKeywords" => "[区][ ]{0,}[块][ ]{0,}[链][ ]{0,}"
            ], [
                'cache' => $cache_path,
//                'proxy' => proxy(),
                'timeout' => 30,
                'headers' => self::$header
            ]);
            $dataArr = json_decode($ql->getHtml(), true);
            var_dump($dataArr);
            foreach ($dataArr['searchResultDTO']['searchResultRecord'] as $key => $val) {
                $nrdAn = $val['fieldMap']['VID'];
                $cid = $val['fieldMap']['ID'];
                $sid = $val['fieldMap']['ID'];
                $wee = "0201101";
                echo "抓取详情数据--------" . $key . "----\r\n";
                self::getDetailData($nrdAn, $cid, $sid, $wee);

            }
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    /**
     * 获取详情数据
     * @param $nrdAn
     * @param $cid
     * @param $sid
     * @param $wee
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/3/21-19:09
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getDetailData($nrdAn, $cid, $sid, $wee)
    {
       // sleep(mt_rand(15, 20));
        var_dump($nrdAn . '---' . $cid . '----' . $sid . '-----' . $wee);


//        try {
        $cache_path = __DIR__ . '/temp/';
        // 发送post请求
        $ql = QueryList::post(self::$detailUrl, [
            "nrdAn" => $nrdAn,
            "cid" => $cid,
            "sid" => $sid,
            "wee.bizlog.modulelevel" => $wee
        ], [
            'cache' => $cache_path,
            //'proxy' =>  proxy(),
            'timeout' => 30,
            'headers' => array_merge(self::$header,[
                "Host"=>"www.pss-system.gov.cn",
                "Origin"=>"http://www.pss-system.gov.cn",
                "Referer"=>"http://www.pss-system.gov.cn/sipopublicsearch/patentsearch/showViewList-jumpToView.shtml"
            ])
        ]);
        $dataArr = json_decode($ql->getHtml(), true);

        var_dump($dataArr);
        $data = [
            'sid' => $dataArr['sid'],//数据id
            'sign' => md5($dataArr['sid']),//唯一标识
            'title' => $dataArr['abstractInfoDTO']['tioIndex']['value'],//名称
            'apply_num' => $dataArr['abstractInfoDTO']['abstractItemList'][0]['value'],//申请号
            'apply_date' => $dataArr['abstractInfoDTO']['abstractItemList'][1]['value'],//申请日
            'public_num' => $dataArr['abstractInfoDTO']['abstractItemList'][2]['value'],//公开号
            'public_date' => $dataArr['abstractInfoDTO']['abstractItemList'][3]['value'],//公开日
            'ipc_cate' => (explode(";", $dataArr['abstractInfoDTO']['abstractItemList'][4]['value']))[0],//IPC主分类号
            'ipc_cate_other' => $dataArr['abstractInfoDTO']['abstractItemList'][4]['value'],//IPC分类号
            'applicant' => $dataArr['abstractInfoDTO']['abstractItemList'][5]['value'],//申请人
            'inventor' => $dataArr['abstractInfoDTO']['abstractItemList'][6]['value'],//发明人
            'agent' => '',//代理人
            'agency' => '',//代理机构
            'digest' => addslashes(json_encode($dataArr['abstractInfoDTO']['abIndexList'], JSON_UNESCAPED_UNICODE)),//摘要
            'applicant_address' => $dataArr['abstractInfoDTO']['abstractItemList'][9]['value'],//申请人地址
            'applicant_zip' => $dataArr['abstractInfoDTO']['abstractItemList'][10]['value'],//申请人邮编
            'cpc_cate' => $dataArr['abstractInfoDTO']['abstractItemList'][11]['value'],//cpc分类号
            'low_status' => self::getLowStatus($dataArr['abstractInfoDTO']['nrdAn'], $dataArr['abstractInfoDTO']['pn']),//审核状态--法律状态
            'createtime' => $_SERVER['REQUEST_TIME'],//创建时间
            'updatetime' => $_SERVER['REQUEST_TIME']//申请时间
        ];
        var_dump($data);
        self::writeSql($data);
//            return true ;
//        } catch (Exception $e) {
//            return false;
//        }
    }

    /**
     * 获取法律状态
     * @param $nrdAn
     * @param $nrdPn
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/3/21-15:46
     * @return string
     * @throws Exception
     */
    private static function getLowStatus($nrdAn, $nrdPn)
    {
       // sleep(mt_rand(15, 20));
        echo "抓取法律数据--------\r\n";
        // try {
        $cache_path = __DIR__ . '/temp/';
        // 发送post请求
        $ql = QueryList::post(self::$lowStatusUrl, [
            "literaInfo.nrdAn" => $nrdAn,
            "literaInfo.nrdPn" => $nrdPn,
            "literaInfo.fn" => ""
        ], [
            'cache' => $cache_path,
//            'proxy' =>  proxy(),
            'timeout' => 30,
            'headers' => self::$header
        ]);
        $dataArr = json_decode($ql->getHtml(), true);
        var_dump(json_encode($dataArr));
        return addslashes(json_encode($dataArr['lawStateList'], JSON_UNESCAPED_UNICODE));
//        } catch (Exception $e) {
//            throw new Exception("获取详情出问题" + json_encode($e));
//        }
    }


    /**
     * 写入数据库
     * @param $data
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/3/21-15:40
     */
    private static function writeSql($data)
    {
        if (!db(self::$config, "block_chain")->where(['sign' => $data['sign']])->find()) {
            db(self::$config, "block_chain")->add($data);
        }
    }

    /**
     * 英文
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/3/21-13:08
     */
//    public static function getEnListData($page)
//    {
//        $cache_path = __DIR__ . '/temp/';
//        try {
//            // 发送post请求
//            $ql = QueryList::post(self::$listUrl, [
//                'resultPagination.limit' => 12,
//                'resultPagination.sumLimit' => 10,
//                'resultPagination.start' => $page,
//                'resultPagination.totalCount' => 6261,
//                'searchCondition.sortFields' => '-APD,+PD',
//                'searchCondition.searchType' => 'Sino_foreign',
//                'searchCondition.originalLanguage' => '',
//                "searchCondition.extendInfo['MODE']" => 'MODE_SMART',
//                "searchCondition.extendInfo['STRATEGY']" => '',
//                'searchCondition.searchExp' => "复合文本=(区块链)",
//                "searchCondition.executableSearchExp" => "VDB:(TBI='区块链')",
//                "searchCondition.dbId" => "",
//                "searchCondition.literatureSF" => "复合文本=(区块链)",
//                "searchCondition.targetLanguage" => "",
//                "searchCondition.resultMode" => "undefined",
//                "searchCondition.strategy" => "",
//                "searchCondition.searchKeywords" => "[区][ ]{0,}[块][ ]{0,}[链][ ]{0,}"
//            ], [
//                'cache' => $cache_path,
//                //  'proxy' => 'http://222.141.11.17:8118',
//                'timeout' => 30,
//                'headers' => [
//                    'Cookie' => 'WEE_SID=DwWeIfvOd_6qSY2Y_JO8D1ABOVX17nLIlgZzQ5_aM3XrZiO9Folf!-529919397!1193910941!1553136221134; IS_LOGIN=true; JSESSIONID=DwWeIfvOd_6qSY2Y_JO8D1ABOVX17nLIlgZzQ5_aM3XrZiO9Folf!-529919397!1193910941',
//                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36',
//                    'X-Requested-With' => 'XMLHttpRequest'
//                ]
//            ]);
//            $dataArr = json_decode($ql->getHtml(), true);
//        } catch (Exception $e) {
//            throw new Exception("获取列表出问题" + json_encode($e));
//        }
//
//        foreach ($dataArr['searchResultDTO']['searchResultRecord'] as $key => $val) {
//            $nrdAn = $val['fieldMap']['VID'];
//            $cid = $val['fieldMap']['ID'];
//            $sid = $val['fieldMap']['ID'];
//            $wee = "0201101";
//            self::getDetailData($nrdAn, $cid, $sid, $wee);
//            sleep(mt_rand(5, 10));
//        }
//    }

}

$startPage = 0;
$endPage = 6252;
BlockChainSpider::start($startPage, $endPage);