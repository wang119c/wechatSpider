<?php
/**
 * Created by PhpStorm.
 * User: huizi
 * Date: 2019/5/14
 * Time: 19:39
 */

use QL\QueryList;

require "./vendor/autoload.php";





$header = [
    'Cookie' => 'noticeLoginFlag=1; ua_id=dDyvniObJ0zSYnMjAAAAAKA8hgSy7LdpxoicsFTjvRc=; pgv_pvi=7205114880; pgv_si=s843882496; cert=wUrELdOtHcoI9cQEDT1HWakZOfn7t0Gn; mm_lang=zh_CN; rewardsn=; wxtokenkey=777; pgv_info=ssid=s1184832762; pgv_pvid=334576536; pac_uid=0_5cda2438b42fb; sig=h01de48fd4b058e3bc00dd2f63e9335a541d2982b8af72148bee4149556185a77c69959dd8b26a72fd6; ticket_id=gh_5cd8b6adaf72; noticeLoginFlag=1; uuid=115bda16f1a931256b36d7d3854cbb4a; ticket=d496acb38fbe0169bcd28cfede6f002f7632d98d; data_bizuin=3088390918; bizuin=3093392608; data_ticket=X4dlgHMQTht+9L2Ol2Y71Dq73XS0U4ht2T8lQLWqr0fab10izUrWCpn9aHsa7qem; slave_sid=c18zWkhCTzJfVXNfUVNmQVhRRm55d0d0THpHSzlZUGhTZE9VQkFLYmxibktvSzNYWUd1UVNvVzdSbVVrd0ZRcmdaVklsZnJfcUo4cGk3NHF1UGh5dFdDNjBPTkYzeEMwTEUyREhDZDBYVmtYeGxXWHpSNDl0QmtPWTVKNHVqWktFSEtRdlFaUmk5WGlvYThM; slave_user=gh_5cd8b6adaf72; xid=14912bdc360b914f2df8f29fe9db985f; openid2ticket_oBhsVuLsmr2QXsXqAvGYU_kwbtjw=oJfwk8V4OydO+1Au8OufDR4EOzfDKabhxNBokviRCUQ=',
    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36',
    'Accept' => 'application/json, text/javascript, */*; q=0.01'
];


function _randFloat($min = 0, $max = 1)
{
    return $min + mt_rand() / mt_getrandmax() * ($max - $min);
}
$ql = QueryList::get('https://mp.weixin.qq.com/cgi-bin/appmsg', [
    'token' => 2136755505,
    'lang' => 'zh_CN',
    'f' => 'json',
    'ajax' => 1,
    'random' => _randFloat(),
    'action' => 'list_ex',
    'begin' => 0,
    'count' => 5,
    'query' => '',
    'fakeid' => "MzI1Mzk4ODIwOA==",
    'type' => 9
], [
    'headers' => $header
]);
$html = $ql->getHtml();


$articleRes = \GuzzleHttp\json_decode($html, true);
var_dump($articleRes);die;













//$url = "https://duoke.heilluo.com/kuairushou/v2/Goods/search";
//$data = $ql->myHttp($url)->getHtml();
//$res = \GuzzleHttp\json_decode($data,true) ;
//print_r(  $res['data'] );

//$ql = QueryList::get('https://duoke.heilluo.com/kuairushou/v2/Goods/search', [
////    'action' => 'search_biz',
////    'token' => '1326854404',
////    'lang' => 'zh_CN',
////    'f' => 'json',
////    'ajax' => '1',
////    'random' => self::randFloat(),
////    'query' => $wechatNum,
////    'begin' => 0,
////    'count' => 5
//], [
////'headers' => self::$header
//])->getHtml();


$res = \GuzzleHttp\json_decode($ql,true) ;
print_r(  $res['code'] );




/*
//采集开发者头条
$ql = QueryList::getInstance();
//注册一个myHttp方法到QueryList对象
$ql->bind('myHttp',function ($url){
    $html = file_get_contents($url);
    $this->setHtml($html);
    return $this;
});
//然后就可以通过注册的名字来调用
$url = 'https://mp.weixin.qq.com/s?src=11&timestamp=1557885601&ver=1607&signature=CuE-2qFbvuFSHk3IWUc7et9RPCiJJfl0Rb3f4RPnWH5ayYgfmGJIhg6y6szhJrGvOk5JmQWzhr3m8efRUkMMFbJSR9Ydw6lToGCRaw9BiBh-LF-7ilnNJTXLZ1r2q6IQ&new=1' ;

//$url = "https://duoke.heilluo.com/kuairushou/v2/Goods/search";
//获取微信文章的内容
$content = $ql->myHttp($url)->find("#js_content.rich_media_content")->html();
*/

//分析url
//$url = "https://mp.weixin.qq.com/s?__biz=MzI1Mzk4ODIwOA==&mid=2247488212&idx=1&sn=7bc1a567bec54eaa8ed6b913c3ffbd43&chksm=e9cd4ecbdebac7ddf3351b4753723dfbc8824e67480e41723bf77c7fa51d0676589dae17e9ec&scene=21&token=2136755505&lang=zh_CN#wechat_redirect";
//function midBuildMd5($url){
//    $urlArr = parse_url($url);
//    parse_str($urlArr['query'],$queryArr) ;
//    return  md5($queryArr['mid']);
//}
//
//
//var_dump(midBuildMd5($url));




//$res = \GuzzleHttp\json_decode($data,true) ;
//print_r(  $res['data'] );
//或者这样用
//$data = $ql->rules([
//    'title' => ['h3 a','text'],
//    'link' => ['h3 a','href']
//])->myHttp('https://toutiao.io')->query()->getData();
//print_r($data->all());



//$client = new GuzzleHttp\Client();
//$jar = new \GuzzleHttp\Cookie\CookieJar;
//$r = $client->request('GET', 'http://httpbin.org/cookies', [
//    'cookies' => $jar
//]);


//$wechatUrl = "https://mp.weixin.qq.com/s?src=11&timestamp=1557885601&ver=1607&signature=CuE-2qFbvuFSHk3IWUc7et9RPCiJJfl0Rb3f4RPnWH5ayYgfmGJIhg6y6szhJrGvOk5JmQWzhr3m8efRUkMMFbJSR9Ydw6lToGCRaw9BiBh-LF-7ilnNJTXLZ1r2q6IQ&new=1";
//$html = \QL\QueryList::getInstance()->get($wechatUrl)->getHtml();
//
//
////获取当前页面的内容
//$content = QueryList::html($html)->find('#js_content.rich_media_content')->html();
////获取当前页面的uuid
//# 新增,以后进行更新的时候进行新的添加
//
//$scriptLists = QueryList::html($html)->find("script")->attr("type","text/javascript")->html();
//
//var_dump(count($scriptLists));

//script_lists = html_obj.findAll('script', {'type': 'text/javascript'})
//        mid = ""
//        for script_list in script_lists:
//            content_ids = re.findall(r"var(.*)mid(.*)=(.*)\";", str(script_list))
//            if content_ids != []:
//                content_id = re.findall(r"\d+", content_ids[0][2])
//                if (content_id != []):
//                    mid = content_id[0]
//                    break












//$str = '{"base_resp":{"ret":0,"err_msg":"ok"},"list":[{"fakeid":"MzI1Mzk4ODIwOA==","nickname":"閾鹃椈ChainNews","alias":"chainnewscom","round_head_img":"http:\/\/mmbiz.qpic.cn\/mmbiz_png\/jNibHMrCMW
//ab3VWxRbcEqkMAXykAsq7k0UNgHRQduLJjjSwjDwcRaGpemHW2P195s6xf2BBBmrzc3smwZHMSTkQ\/0?wx_fmt=png","service_type":1}],"total":1}';
//
//var_dump(json_decode($str,true));

///**
// * 获取公众号文章的列表
// * Created by PhpStorm.
// * Author:huizi
// * Date: 2019/5/15-11:35
// */
//public function getWechatArticleList111($wechatInfoArr)
//{
//    //sleep(mt_rand(10, 12));
//    echo "抓取文章列表...";
//    //$url = "https://mp.weixin.qq.com/cgi-bin/appmsg?token=2136755505&lang=zh_CN&f=json&ajax=1&random=0.7231723217024018&action=list_ex&begin=0&count=5&query=&fakeid=MzUxMTcwNDQzMA%3D%3D&type=9";
//    $ql = QueryList::get('https://mp.weixin.qq.com/cgi-bin/appmsg', [
//        'token' => $this->_token,
//        'lang' => 'zh_CN',
//        'f' => 'json',
//        'ajax' => 1,
//        'random' => $this->_randFloat(),
//        'action' => 'list_ex',
//        'begin' => 0,
//        'count' => 5,
//        'query' => '',
//        'fakeid' => $wechatInfoArr['fakeid'],
//        'type' => 9
//    ], [
//        'headers' => $this->_header
//    ]);
//    $html = $ql->getHtml();
//    $articleRes = \GuzzleHttp\json_decode($html, true);
//    if (isset($articleRes['base_resp']['err_msg']) && $articleRes['base_resp']['err_msg'] == "ok") {
//        foreach ($articleRes['app_msg_list'] as $key => $val) {
////                "aid": "2247491878_1",
////            "appmsgid": 2247491878,
////            "cover": "https://mmbiz.qlogo.cn/mmbiz_jpg/5fYayYXZHofJKSB8FvReIR6Kib5ia7VIue8yJePLLoGYuKn4vge5jf9WYcNWQ3AbaQRcCdk43cRps1bXichTCqwtg/0?wx_fmt=jpeg",
////            "digest": "作为一种有效的融资方式，就像ICO一样，IEO不会很快消亡。",
////            "item_show_type": 0,
////            "itemidx": 1,
////            "link": "http://mp.weixin.qq.com/s?__biz=MzUxMTcwNDQzMA==&mid=2247491878&idx=1&sn=680aa8ada265b9b2a7118da2336197ac&chksm=f96d0d27ce1a8431443cb5e996999352ded8b29988a06ce2ccceeedb98858ce043e16a79e188#rd",
////            "title": "项目屡屡破发，平台币遭遇价格腰斩：IEO的转折点已经到来",
////            "update_time": 1557758774
//            $data = [
//                'title' => $val['title'],
//                'cover' => $val['cover'],
//                'source' => $wechatInfoArr['nickname'],
//                'message' => $this->getContent($val['link']),
//                'url' => $val['link'],
//                'summary' => $val['digest'],
//                'uuid' => md5($val['appmsgid'])
//            ];
//            var_dump($data);
//            //$this->writeSql($data);
//        }
//    } else {
//        var_dump($articleRes);
//    }
//    //self::writeSql($data);
//}