<?php
/**
 * 火星坐标系 (GCJ-02) 与百度坐标系 (BD-09) 的转换算法
 * 标准地球坐标即GPS设备获得的坐标，该坐标需要经过国家测绘局进行加密后形成火星坐标（WGS-84 ），
 * 我们用的google坐标和高德地图坐标也就是火星坐标
 * 百度地图，在火星坐标的基础上再进行一次加密，形成了百度地图上的坐标，
 * 因此，直接将标准地球坐标显示在百度地图上是会有几百米的偏差的。
 * 按照此原理，标准GPS坐标经过两步的转换可得到百度坐标
 * User: xiaohui
 * Date: 2015/8/7
 * Time: 11:12
 */

namespace extend;

class Coor {
    const PI = 3.14159265358979324;
    const A = 6378245.0;
    const EE = 0.00669342162296594323;
    const XPI = 3.14159265358979324 * 3000.0 / 180.0;


    /**
     * 根据经纬度计算距离 其中A($lat1,$lng1)、B($lat2,$lng2)
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @return float 单位为米
     */
    public static function getDistance($lat1,$lng1,$lat2,$lng2) {
        //地球半径(米)
        $R = 6378137;

        //将角度转为狐度
        $radLat1 = deg2rad($lat1);
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);

        //结果
        $s = 2*asin(sqrt(pow(sin(($radLat1-$radLat2)/2),2)+cos($radLat1)*cos($radLat2)*pow(sin(($radLng1-$radLng2)/2),2)))*$R;

        //精度
        $s = round($s* 10000)/10000;

        return  round($s);
    }

    /**
     * 火星坐标 转 百度坐标
     * @param $lng  经度
     * @param $lat  纬度
     * @return array
     */
    static public function GCJ2BD($lng,$lat){
        $z = sqrt($lng*$lng + $lat*$lat) + 0.00002 * sin($lat * self::XPI);
        $theta = atan2($lat, $lng) + 0.000003 * cos($lng * self::XPI);

        return array(
            'lng'=>self::setRes($z * cos($theta) + 0.0065),
            'lat'=>self::setRes($z * sin($theta) + 0.006)
        );
    }
    /**
     * 地球坐标 转 百度坐标
     * @param $lng  经度
     * @param $lat  纬度
     * @return array
     */
    static public function E2BD($lng,$lat){
        $coor = self::E2GCJ($lng,$lat);
        return self::GCJ2BD($coor['lng'],$coor['lat']);
    }
    /**
     * 百度坐标 转 火星坐标
     * @param $lng  经度
     * @param $lat  纬度
     * @return array
     */
    static public function BD2GCJ($lng,$lat){
        $x = $lng - 0.0065;
        $y = $lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * self::XPI);
        $theta = atan2($y, $x) - 0.000003 * cos($x * self::XPI);

        return array(
            'lng'=>self::setRes($z * cos($theta)),
            'lat'=>self::setRes($z * sin($theta))
        );
    }

    /**
     * 中国范围判断
     * @param $lng
     * @param $lat
     * @return bool
     */
    static public function outOfChina($lng,$lat){
        if ($lng < 72.004 || $lng > 137.8347)
            return true;
        if ($lat < 0.8293 || $lat > 55.8271)
            return true;
        return false;
    }


    /**
     * 纬度调整
     * @param $x
     * @param $y
     * @return float
     */
    static public function transformLat($x,$y){
        $ret = -100.0 + 2.0 * $x + 3.0 * $y + 0.2 * $y * $y + 0.1 * $x * $y + 0.2 * sqrt(abs($x));
        $ret += (20.0 * sin(6.0 * $x * self::PI) + 20.0 * sin(2.0 * $x * self::PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($y * self::PI) + 40.0 * sin($y / 3.0 * self::PI)) * 2.0 / 3.0;
        $ret += (160.0 * sin($y / 12.0 * self::PI) + 320 * sin($y * self::PI / 30.0)) * 2.0 / 3.0;
        return $ret;
    }

    /**
     * 经度调整
     * @param $x
     * @param $y
     * @return float
     */
    static public function transformLng($x,$y){
        $ret = 300.0 + $x + 2.0 * $y + 0.1 * $x * $x + 0.1 * $x * $y + 0.1 * sqrt(abs($x));
        $ret += (20.0 * sin(6.0 * $x * self::PI) + 20.0 * sin(2.0 * $x * self::PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($x * self::PI) + 40.0 * sin($x / 3.0 * self::PI)) * 2.0 / 3.0;
        $ret += (150.0 * sin($x / 12.0 * self::PI) + 300.0 * sin($x / 30.0 * self::PI)) * 2.0 / 3.0;
        return $ret;
    }

    /**
     * 地球坐标转火星坐标
     * @param $lng
     * @param $lat
     * @return array
     */
    static public function E2GCJ($lng,$lat){
        if (self::outOfChina($lng, $lat))
        {
            return array('lng'=>$lng,'lat'=>$lat);
        }
        $dlat = self::transformLat($lng-105.0,$lat-35.0);
        $dlng = self::transformLng($lng-105.0,$lat-35.0);
        $radlat = $lat / 180.0 * self::PI;
        $magic = sin($radlat);
        $magic = 1 - self::EE * $magic * $magic;
        $sqrtMagic = sqrt($magic);
        $dlat = ($dlat * 180.0) / ((self::A * (1 - self::EE)) / ($magic * $sqrtMagic) * self::PI);
        $dlng = ($dlng * 180.0) / (self::A / $sqrtMagic * cos($radlat) * self::PI);

        return array('lng'=>$lat+$dlat,'lat'=>$lng+$dlng);
    }

    /**
     * 结果 处理
     * @param $n
     * @return string
     */
    static public function setRes($n){
        return sprintf("%.6f",$n);
    }

}