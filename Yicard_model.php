<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * Created by PhpStorm.
 * User: XIN
 * Date: 17/01/10
 * Time: 22:36
 */
 

class Yicard_model extends Online_api_model{
    function __construct()
    {
        parent::__construct();
        $this->init_db();
    }

    function get_all_info($url,$order_num,$s_amount,$bank,$pay_id,$pay_key){
        $user_ip = "";
        if(isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $user_ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $user_ip = $_SERVER['REMOTE_ADDR'];
        }
        $req_url = 'http://'.$url.'/index.php/pay/payfor';//跳轉地址
        $ServerUrl = 'http://'.$url.'/index.php/pay/yicard_callback';//商戶後臺通知地址
        $hrefbackurl = 'http://'.$url.'/index.php/pay/return_url';//支付成功后跳转地址
        $form_url = 'http://www.23card.net/chargebank.aspx';//第三方地址
        $conf = array();
        $conf['parter'] = $pay_id;//商戶id
        $conf['type'] = $bank;//銀行類型
        $conf['value'] = $s_amount;//金額
        $conf['orderid'] = $order_num;//商戶訂單號
        $conf['callbackurl'] = $ServerUrl;//下行異步通知地址
        $conf['hrefbackurl'] = $hrefbackurl;//下行同步通知地址(充值成功后的跳轉地址，不填則不跳轉）
        $conf['payerIp'] = $user_ip;//用戶IP
        $conf['attach'] = '';//備註消息
        $conf['pay_key'] = $pay_key;//MD5 key
        $md5 = 'parter='.$conf['parter'].'&type='.$conf['type'].'&value='.$conf['value'].'&orderid='.$conf['orderid'].'&callbackurl='.$conf['callbackurl'].$conf['pay_key'];
        $sign = md5($md5);
        $conf['sign'] = $sign;//MD5 簽名值
        $conf['agent'] = '';//代理id
        $conf['req_url'] = $req_url;
        $conf['form_url'] = $form_url;
        $conf['payMethod'] = "MD5";
        $conf['act'] = "Yicard";//对应在Pay.php 中调用的方法名

        return $conf;
    }
    
}