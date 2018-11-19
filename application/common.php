<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function get_ref($base64=false){
    if(!isset($_SERVER['HTTP_REFERER'])){
        return '';
    }
    return $base64 ? base64_encode($_SERVER['HTTP_REFERER']) : $_SERVER['HTTP_REFERER'];
}

/**
 * 获取当前的完整url
 */
function get_full_url($base64=false) {
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $base64 ? base64_encode($url) : $url;
}

function quimg($path1,$path2,$q_host=''){
    $src = $path1?:$path2;

    //
    //$fop = '';
    /*$ext = pathinfo($src,PATHINFO_EXTENSION);
    if(in_array($ext,config('img_ext'))){
        $fop = '?imageslim';
        $fop = '?imageMogr2/strip/format/webp/interlace/1/quality/80/';
        $fop = '?imageView2/2/w/2048/';
    }*/

    if(strpos($src,'http://')!==false){
        return $src;
    }else{
        return ($q_host?:config('qiniu.host')).$src;
    }

}

function get_qn_img_slm($src){
    $ext = pathinfo($src,PATHINFO_EXTENSION);
    $newsrc = str_replace([config('qiniu.host'),'.'.$ext],'',$src).'_2048.'.$ext;

    return $newsrc;
}

/**
 * 将二维数组转为一维数组
 */
function array2to1($arr, $key = '', $str = false) {
    $data = array();
    if (empty($arr)) {
        return false;
    }
    foreach ($arr as $val) {
        if ($key) {
            $data[] = trim($val[$key]);
        } else {
            $data[] = $val;
        }
    }
    if ($str) {
        return implode(',', array_unique($data));
    } else {
        return array_unique($data);
    }
}
//计算文件大小
function get_data_size($size) {
    $units = array(' B', ' KB', ' MB', ' GB', ' TB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++)
        $size /= 1024;
    return round($size, 2) . $units[$i];
}

function create_pwd($pwd,$stat){
    return md5(md5($pwd).$stat);
}

/**
 * 创建无限分类树
 * @staticvar array $tree 保存树结构的数组
 * @param type $data 节点数组
 * @param type $pid 父节点，即从该节点往下找
 */
function create_tree($data, $pid = 0) {

    static $tree = array();

    foreach ($data as $key => $val) {

        if ($val['pid'] == $pid) {
            $tree[] = $val;
            unset($data[$key]);
            create_tree($data, $val['id']);
        }
    }
    return $tree;
}
function create_tree2($data, $pid = 0,&$tree=[]) {

    //static $tree = array();

    foreach ($data as $key => $val) {

        if ($val['pid'] == $pid) {
            $tree[] = $val;
            unset($data[$key]);
            create_tree2($data, $val['id'],$tree);
        }
    }
    //return $tree;
}

/**
 * 创建有层次的分类树
 * @param type $data 节点数组
 * @param type $pid 父节点，即从该节点往下找
 * @return type
 */
function create_level_tree($data, $pid = 0) {
    $tree = array();
    $i = 0;
    foreach ($data as $key => $val) {

        if ($val['pid'] == $pid) {
            $tree[$i] = $val;
            unset($data[$key]);
            $tmp = create_level_tree($data, $val['id']);
            if (!empty($tmp)) {
                $tree[$i]['sub'] = $tmp;
            }else{
                $tree[$i]['sub'] = [];
            }
            unset($tmp);
            $i++;
        }
    }
    return $tree;
}

/**
 * 获取任间尺寸的图片
 * $file 图片地址
 * $key 目录的对应表 key
 *          0-图片目录
 *          1-会员头像
 *          2-商品
 *          3-商铺
 * $w 图片宽度
 * $h 图片高度
 */
function c_img($file, $key = 0, $w = 0, $h = 0) {
    return \app\common\library\Anysizeimg::thumb($file, $key, $w, $h);
}


function cut_content($cont,$start=0,$len=120){
    //去掉无意义的符号
    $htmlcontent = html_entity_decode($cont); //还原html标签
    //去掉摘要中的换行，引号
    $nohtmlcontent = str_replace(array("\r\n","\r","\n","'",'"','-', ' ', ' ', '&nbsp;'), '', strip_tags($htmlcontent));
    unset($htmlcontent);
    $digest = think\helper\Str::substr($nohtmlcontent,$start,$len);
    unset($nohtmlcontent);
    return $digest;
}

/**
 * 验证邮箱格式
 */
function is_mail($mail) {
    $rule = '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/';
    return preg_match($rule, $mail) === 1;
}

/**
 * 验证手机号码（适用于中国大陆地区）
 */
function is_phone($phone) {
    return preg_match('/^1[3-9]{1}[0-9]{9}$/', $phone) === 1;
}

/**
 * 生成新的订单号
 * @param $prefix,订单号前缀，商品订单前缀：CG，预约订单前缀：CB,退款CR
 * @return string,17位的订单号
 */
function build_order_no($prefix='CB',$date=''){
    $pres = ['CB','CR','CG','CGR'];
    if(!in_array($prefix,$pres)){
        return false;
    }
    /* 选择一个随机的方案 */
    mt_srand((double) microtime() * 1000000);
    $date = $date?$date:date('Ymd');
    return $prefix.$date . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

function cnnum2int($str){
    if(strpos($str,'万')!==false){
        return str_replace('万','',$str)*10000;
    }else{
        return (int)$str;
    }
}

function cntime2time($str){
    if(strpos($str,'小时')!==false){
        $str = str_replace('小时前','',$str);
        return date('Y-m-d H:i',strtotime ("-$str hours"));
    }else if(strpos($str,'分钟')!==false){
        $str = str_replace('分钟前','',$str);
        return date('Y-m-d H:i',strtotime ("-$str minutes"));
    }else{
        return $str;
    }
}

function create_kv($data,$k,$v){

    $return = [];
    foreach($data as $va){
        $text = '';
        if(is_array($v)){
            $tmp = [];
            foreach($v as $txt){
                $tmp[] = $va[$txt];
            }
            $text = implode('-',$tmp);
        }else{
            $text = $va[$v];
        }
        $return[$va[$k]] = $text;
    }
    return $return;
}

function create_select($data,$value,$texts,$selected=''){
    $tpl = '';
    foreach($data as $da){
        $text = '';
        if(is_array($texts)){
            $tmp = [];
            foreach($texts as $txt){
                $tmp[] = $da[$txt];
            }
            $text = implode('-',$tmp);
        }else{
            $text = $da[$texts];
        }
        $seled = '';
        if($selected==$da[$value]){
            $seled = ' selected';
        }
        $tpl .= '<option value="'.$da[$value].'"'.$seled.'>'.$text.'</option>';
    }
    return $tpl;
}

function tostring($data){
    if(empty($data)){
        return $data;
    }
    if(is_string($data)){
        return $data;
    }
    foreach($data as $k=>$da){
        if(is_array($da)){
            $data[$k] = tostring($da);
        }else if(is_object($da)){
            $data[$k] = $da;
        }else if(!is_string($da)){
            $data[$k] = (string) $da;
        }
    }
    return $data;
}

function getimgtag($html){
    $pattern = "/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i";
    preg_match_all($pattern,html_entity_decode($html),$match);
    $imgs = [];
    if(!empty($match[1])){
        foreach($match[1] as $p){
            //$imgs[] = str_replace('http://'.DOMAIN,'',$p);
            $imgs[] = $p;
        }
    }
    return $imgs;

}

function get_user_type_str($k){
    $strs = [
        1=>'b',
        2=>'c'
    ];
    return isset($strs[$k])?  $strs[$k]: $strs;
}

//人性化时间显示
function formatTime($time){
    $rtime = date("m-d H:i",$time);
    //$htime = date("H:i",$time);
    $time = time() - $time;
    if ($time < 60){
        $str = '刚刚';
    }elseif($time < 60 * 60){
        $min = floor($time/60);
        $str = $min.'分钟前';
    }elseif($time < 60 * 60 * 24){
        $h = floor($time/(60*60));
        $str = $h.'小时前 ';
    }elseif($time < 60 * 60 * 24 * 3){
        $d = floor($time/(60*60*24));
        if($d==1){
            $str = '昨天 '.$rtime;
        }else{
            $str = '前天 '.$rtime;
        }
    }else{
        $str = $rtime;
    }
    return $str;
}

function gender_txt($k=null){
    $genders =  [
        '未知','男','女'
    ];
    return $k?$genders[$k]:$genders;
}