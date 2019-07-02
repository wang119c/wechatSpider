<?php
/**
 * 对emoji表情加密
 */
function emoji_encode($str)
{
    $strEncode = '';
    $length = mb_strlen($str, 'utf-8');
    for ($i = 0; $i < $length; $i++) {
        $_tmpStr = mb_substr($str, $i, 1, 'utf-8');
        if (strlen($_tmpStr) >= 4) {
            $strEncode .= '[[EMOJI:' . rawurlencode($_tmpStr) . ']]';
        } else {
            $strEncode .= $_tmpStr;
        }
    }
    return $strEncode;
}
/**
 * 对emoji表情解密
 */
function emoji_decode($str)
{
    $strDecode = preg_replace_callback('/\[\[EMOJI:(.*?)\]\]/', function ($matchs) {
        return rawurldecode($matchs[1]);
    }, $str);
    return $strDecode;
}