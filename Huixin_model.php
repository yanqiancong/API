<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/27
 * Time: 18:06
 */




class Huixin_model extends Online_api_model{
    function __construct()
    {
        parent::__construct();
        $this->init_db();
    }

    function get_channel($pay_id,$pay_key){

        $svcName = 'paygate.tranChannelQry';//服务名 固定值
        $merId = $pay_id;//商户编号
        $payType = 1;//支付方式
        $tranTime = date('Ymd h:i:s',time());//交易日期
        $md5value = strtoupper(md5($svcName.$merId.$payType.$tranTime.$pay_key));//MD5 校验码

        $data = array();
        $data['svcName'] = $svcName;//服务名称
        $data['merId'] = $merId;//商户编号
        $data['payType'] = $payType;//订单编号
        $data['tranTime'] = $tranTime;//币种
        $data['md5value'] = $md5value;//交易时间

        $message = json_encode($data);
        $ch = curl_init('http://pay.ronghexx.com/api/');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$message);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($message)
        ));

        $result = curl_exec($ch);
        if($result){
            $content = curl_getinfo($ch);//获取内容
            $info = json_decode($content,true);//输出获取的内容
            curl_close($ch);
            return $info;
        }
        return false;
    }




    function get_all_info($url,$order_num,$s_amount,$bank,$pay_id,$pay_key,$info){

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



        $merchUrl = 'http://'.$url.'/index.php/pay/Huixin_callback';//商戶後臺通知地址
        $hrefbackurl = 'http://'.$url.'/index.php/pay/return_url';//支付成功后跳转地址


        $svcName = 'paygate.directgatewaypay';//服务名称
        $merId = $pay_id;//商户编号
        $merchOrderId = $order_num;//订单编号
        $amt = $s_amount;//订单编号
        $ccy = '';//币种 默认：CNY-人民币
        $tranTime = data('Ymd h:i:s',time());//交易时间
        $tranChannel = $info['channelCode'];//交易渠道 具体传channelCode 还是channelName需要更多信息
        $merUrl = $merchUrl;//返回商户URL
        $retUrl = $hrefbackurl;//结果通知URL
        $merData = '';//商户自定义数据 商户可自定义数据上传，汇鑫会原值返回。
        $pName = '';//商品名称
        $pCat = 7;//商品种类 固定值：7-实物电商
        $pDesc = '';//商品描述

        $data = array();
        $data['svcName'] = $svcName;//服务名称
        $data['merId'] = $merId;//商户编号
        $data['merchOrderId'] = $merchOrderId;//订单编号
        $data['ccy'] = $ccy;//币种
        $data['tranTime'] = $tranTime;//交易时间
        $data['tranChannel'] = isset($tranChannel)?$tranChannel:1;//交易渠道
        $data['merUrl'] = $merUrl;//返回商户URL
        $data['retUrl'] = $retUrl;//结果通知URL
        $data['merData'] = $merData;//商户自定义数据
        $data['pName'] = $pName;//商品名称
        $data['pCat'] = $pCat;//商品种类
        $data['pDesc'] = $pDesc;//商品种类

        if($bank == 'winxin'){

            $tranType = $info['payTpey'];//  JSAPI（公众号支付）NATIVE(原生扫码支付)E WEIXIN_NATIVE 原生扫码支付)APP（app 支付）MICROPAY（刷卡支付）CLOUD(云付)CLOUDUNION(建行银联)ALIPAYSCAN(支付宝扫码支付)ALIPAYMOBILE (支付宝移动支付) ALIPAYWAP(支付宝手机网站支付)
            $mer_cust_id = '';//商户用户 ID 快捷支付必输
            $terminalType = 'OTHER';//终端标识类型 0 ：IMEI(手机)1 ：MAC(pc)2 ：UUID（ 针对IOS 系统）3：OTHER
            $merUserId = $_SESSION['uid'];//商户用户 ID
            $terminalId = 'pc';//终端标识
            $productType = 3;//产品形态1-android客户端；2-IOS客户端；3-PC端（web页面）；4-手机端（wap或html5等页面）；
            $userIp = $user_ip;//用户 IP
            $rcvName = '';//收货人姓名
            $rcvMobile = '';//收货人手机号
            $rcvAdress = '';//送货地址
            $regMail = '';//注册 email
            $regTime = '';//用户注册时间
            $data['tranType'] = $tranType;
            $data['mer_cust_id'] = $mer_cust_id;//商户用户 ID
            $data['terminalType'] = $terminalType;//终端标识类型
            $data['merUserId'] = $merUserId;//商户用户 ID
            $data['terminalId'] = $terminalId;//终端标识
            $data['productType'] = $productType;//产品形态
            $data['userIp'] = $userIp;//用户 IP
            $data['rcvName'] = $rcvName;//收货人姓名
            $data['rcvMobile'] = $rcvMobile;//收货人手机号
            $data['rcvAdress'] = $rcvAdress;//送货地址
            $data['regMail'] = $regMail;//注册 email
            $data['regTime'] = $regTime;//用户注册时间

            $md5value=strtoupper(MD5($svcName.$merId.$merchOrderId.$amt.$ccy.$tranTime.$tranChannel.$retUrl.$merUserId.$tranType.$terminalType.$terminalId.$productType.$userIp.$pay_key));




        }elseif ($bank == 'zhifubao'){
            $tranType = $info['payTpey'];//
            $sourceType = '';//商户用户 ID
            $merUserId = $_SESSION['uid'];//商户用户 ID
            $terminalType = 'C';//收银台支付请求必须上送：C
            $terminalId = '';//终端标识
            $productType = 'pc';//产品形态
            $userIp = $user_ip;//用户 IP

            $data['tranType'] = $tranType;
            $data['sourceType'] = $sourceType;//请求类型
            $data['merUserId'] = $merUserId;//终端标识类型
            $data['terminalType'] = $terminalType;//商户用户 ID
            $data['terminalId'] = $terminalId;//产品形态
            $data['productType'] = $productType;//用户IP
            $data['$userIp'] = $userIp;//MD5 校验码

            $md5value=strtoupper(MD5($svcName.$merId.$merchOrderId.$amt.$ccy.$tranTime.$tranChannel.$retUrl.$merUserId.$tranType.$terminalType.$terminalId.$productType.$userIp.$pay_key));
        }else{

            $md5value=strtoupper(MD5($svcName.$merId.$merchOrderId.$amt.$ccy.$tranTime.$tranChannel.$merUrl.$pay_key));
        }



        $data['md5value'] = $md5value;//MD5校验码


        $form_url = 'http://pay.ronghexx.com/api/';//第三方支付地址 测试
//        $form_url = 'http://pay.ronghexx.com/api/';//第三方支付地址 正式

        $message = json_encode($data);
        $ch = curl_init($form_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$message);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($message)
        ));

        $result = curl_exec($ch);
        if($result['retCode'] !== 000000){
            echo "<script>alert('提交失败，请重新再试');window.close()";
        }
        curl_close($ch);

//        echo "<script>window.location.href=".$url.";</script>";
    }

}