<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/13
 * Time: 19:01
 */
class Cmpp {
    // 设置项
    public $host = "";   //服务商ip
    public $port = "17890";           //端口号
    public $Source_Addr = "";           //企业id  企业代码
    public $Shared_secret = '';         //网关登录密码
    public $Dest_Id = "";      //短信接入码 短信端口号
    public $SP_ID = "";
    public $SP_CODE = "";
    public $Service_Id = "";
    public $deliver;
    private $socket;
    private $Sequence_Id = 1;
    private $bodyData;
    private $AuthenticatorSource;
    public $CMPP_CONNECT = 0x00000001; // 请求连接
    public $CMPP_CONNECT_RESP = 0x80000001; // 请求连接
    public $CMPP_DELIVER = 0x00000005; // 短信下发
    public $CMPP_DELIVER_RESP = 0x80000005; // 下发短信应答
    public $CMPP_ACTIVE_TEST = 0x00000008; // 激活测试
    public $CMPP_ACTIVE_TEST_RESP = 0x80000008; // 激活测试应答
    public $CMPP_SUBMIT = 0x00000004; // 短信发送
    public $CMPP_SUBMIT_RESP = 0x80000004; // 发送短信应答
    public static $msgid = 1;
    public function createSocket(){
        $this->socket =socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
        socket_connect($this->socket,$this->host, $this->port);
    }
    public function CMPP_CONNECT(){
        date_default_timezone_set('PRC');
        $Source_Addr = $this->Source_Addr;
        $Version = 0x30;
        $Timestamp = date('mdHis');
        //echo $Timestamp;
        $AuthenticatorSource = $this->createAS($Timestamp);
        $bodyData = pack("a6a16CN", $Source_Addr, $AuthenticatorSource, $Version, $Timestamp);
        $this->AuthenticatorSource = $AuthenticatorSource;
        $this->send($bodyData, "CMPP_CONNECT");
    }
    public function CMPP_CONNECT_RESP(){
        echo "connect success";
        $body = unpack("CStatus/a16AuthenticatorISMG/CVersion", $this->bodyData);
    }
    public function send($bodyData, $Command,$Sequence=0){
        $Command_Id=0x00000001;
        if($Command =="CMPP_CONNECT"){
            $Command_Id = 0x00000001;
        }elseif($Command =="CMPP_DELIVER_RESP"){
            $Command_Id = 0x80000005;
        }elseif($Command =="CMPP_ACTIVE_TEST_RESP"){
            $Command_Id = 0x80000008;
        }elseif($Command =="CMPP_SUBMIT"){
            $Command_Id = 0x00000004;
        }
        $Total_Length = strlen($bodyData) + 12;
        if($Sequence==0){
            if($this->Sequence_Id <10){
                $Sequence_Id = $this->Sequence_Id;
            }else{
                $Sequence_Id =1;
                $this->Sequence_Id=1;
            }
            $this->Sequence_Id = $this->Sequence_Id+1;
        }else{
            $Sequence_Id = $Sequence;
        }
        $headData = pack("NNN", $Total_Length, $Command_Id, $Sequence_Id);
        // 发送消息
        $this->log("send $Command_Id");
        socket_write($this->socket, $headData.$bodyData, $Total_Length);
        // $this->listen($Sequence_Id);
        $i=1;
        do{
            $this->listen($Sequence_Id);
            //$i = $i-1;
            sleep(15);//等待时间，进行下一次操作
        }while($i>0);
    }
    public function listen($Sequence_Id){
        // 处理头
        $headData = socket_read($this->socket, 12);
        if(empty($headData)){
            $this->log("0000");
            return;
        }
        $head = unpack("NTotal_Length/NCommand_Id/NSequence_Id", $headData);
        $this->log("get ".($head['Command_Id'] & 0x0fffffff));
        $Sequence_Id = $head['Sequence_Id'];
        // 处理body
        $this->bodyData = socket_read($this->socket,$head['Total_Length'] - 12);
        //var_dump($this->bodyData);
        switch ( $head['Command_Id'] & 0x0fffffff ) {
            case 0x00000001:
                $this->CMPP_CONNECT_RESP();
                break;
            case 0x00000005:
                $this->CMPP_DELIVER($head['Total_Length'],$Sequence_Id);
                break;
            case 0x80000005:
                $this->CMPP_DELIVER($head['Total_Length'],$Sequence_Id);
                break;
            case 0x00000008:
                $bodyData=pack("C",1);                   //数据联络包返回
                $this->send($bodyData, "CMPP_ACTIVE_TEST_RESP",$Sequence_Id);
                break;
            default:
                $bodyData=pack("C",1);
                $this->send($bodyData, "CMPP_ACTIVE_TEST_RESP",$Sequence_Id);
                break;
        }
    }
    public function CMPP_DELIVER($Total_Length,$Sequence_Id){    //Msg_Id直接用N解析不行,N只有4位
        $contentlen = $Total_Length-109;
        $body = unpack("N2Msg_Id/a21Dest_Id/a10Service_Id/CTP_pid/CTP_udhi/CMsg_Fmt/a32Src_terminal_Id/CSrc_terminal_type/CRegistered_Delivery/CMsg_Length/a".$contentlen."Msg_Content/a20LinkID",$this->bodyData);
        var_dump($body);
        if($body['Msg_Length']>0){
            $data = $body['Msg_Content'];
            //$Msg_Id = $body['Msg_Id'];
            $Msg_Id = ($body['Msg_Id1']& 0x0fffffff);
            $Msg_Idfu = $body['Msg_Id2'];
            $msgidz = unpack("N",substr($this->bodyData,0,8));
            $msgidzz = '0000' .$msgidz[1];
            /*mysql_connect('localhost','root','root123');
            mysql_select_db('trace');
            mysql_query('set names utf8');
            $data = trim($data);
            $sql1 = "select id from socket_yd_msg where msgid='".$Msg_Id."'";
            $chongfu = mysql_query($sql1);
            $arrs =array();
            while($arr= mysql_fetch_assoc($chongfu) ){
                $arrs[] = $arr;
            }
            if( $arrs==array() || $arrs[0] == null ){
                $sql = "insert into socket_yd_msg set msgid='".$Msg_Id."', content='".addslashes($data)."', add_time='".date('Y-m-d H:i:s')."'";
                mysql_query($sql);
            }
            mysql_close();*/
            //echo $Msg_Id."\n";
            echo $data."\n";
            echo $msgidzz."\n";
            echo $Sequence_Id."\n";
            $this->CMPP_DELIVER_RESP($msgidzz,$Msg_Idfu,$Sequence_Id);
        }
    }
    // N打包只有4位
    public function CMPP_DELIVER_RESP($Msg_Id,$Msg_Idfu,$Sequence_Id){
        $sendda2 = 0x00;
        $bodyData = pack("N", $Msg_Id).pack("N", $Msg_Idfu).pack("N",$sendda2);
        $this->send($bodyData, "CMPP_DELIVER_RESP",$Sequence_Id);
    }
    /**AuthenticatorSource = MD5(Source_Addr+9 字节的0 +shared secret+timestamp) */
    public function createAS($Timestamp){
        $temp = $this->Source_Addr . pack("a9","") . $this->Shared_secret . $Timestamp;
        return md5($temp, true);
    }
    /*** AuthenticatorISMG =MD5(Status + AuthenticatorSource + shared secret) */
    public function cheakAISMG($Status, $AuthenticatorISMG){
        $temp = $Status . $this->AuthenticatorSource . $this->Shared_secret;
        $this->debug($temp.pack("a",""), 1, 1);
        $this->debug($AuthenticatorISMG.pack("a",""), 2, 1);
        if($AuthenticatorISMG != md5($temp, true)){
            $this->throwErr("ISMG can't pass check .", __LINE__);
        }
    }
    public function log($data, $line = null){
        if($line){
            $data = $line . " : ".$data;
        }
        file_put_contents("./cmpp.log", print_r($data, true).PHP_EOL, FILE_APPEND);
    }
    public function debug($data, $fileName, $noExit = false){
        file_put_contents("./$fileName.debug", print_r($data, true));
        if(!$noExit) exit;
    }
    public function throwErr($info, $line){
        die("info: $info in line :$line");
    }

}

//@unlink("./cmpp.log");
//$cmpp = new Cmpp;
//$cmpp->createSocket();
//$cmpp->CMPP_CONNECT();