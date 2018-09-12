<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/26
 * Time: 21:56
 */
namespace extend;

class Imgjoin{

    public $pic_list = [];
    public $bg_w = 150;
    public $bg_h = 150;
    public $save_path = '';

    public function __construct($pic_list,$save_path)
    {
        $this->pic_list = array_slice($pic_list, 0, 9); // 只操作前9个图片
        $this->save_path = $save_path;
    }

    public function do_work(){
        $background = imagecreatetruecolor($this->bg_w,$this->bg_h); // 背景图片
        $color   = imagecolorallocate($background, 202, 201, 201); // 为真彩色画布创建白色背景，再设置为透明
        imagefill($background, 0, 0, $color);
        imageColorTransparent($background, $color);

        $pic_count  = count($this->pic_list);
        $lineArr    = array();  // 需要换行的位置
        $space_x    = 3;
        $space_y    = 3;
        $line_x  = 0;
        switch($pic_count) {
            case 1: // 正中间
                $start_x    = intval($this->bg_w/4);  // 开始位置X
                $start_y    = intval($this->bg_h/4);  // 开始位置Y
                $pic_w   = intval($this->bg_w/2); // 宽度
                $pic_h   = intval($this->bg_h/2); // 高度
                break;
            case 2: // 中间位置并排
                $start_x    = 2;
                $start_y    = intval($this->bg_h/4) + 3;
                $pic_w   = intval($this->bg_w/2) - 5;
                $pic_h   = intval($this->bg_h/2) - 5;
                $space_x    = 5;
                break;
            case 3:
                $start_x    = 40;   // 开始位置X
                $start_y    = 5;    // 开始位置Y
                $pic_w   = intval($this->bg_w/2) - 5; // 宽度
                $pic_h   = intval($this->bg_h/2) - 5; // 高度
                $lineArr    = array(2);
                $line_x  = 4;
                break;
            case 4:
                $start_x    = 4;    // 开始位置X
                $start_y    = 5;    // 开始位置Y
                $pic_w   = intval($this->bg_w/2) - 5; // 宽度
                $pic_h   = intval($this->bg_h/2) - 5; // 高度
                $lineArr    = array(3);
                $line_x  = 4;
                break;
            case 5:
                $start_x    = 30;   // 开始位置X
                $start_y    = 30;   // 开始位置Y
                $pic_w   = intval($this->bg_w/3) - 5; // 宽度
                $pic_h   = intval($this->bg_h/3) - 5; // 高度
                $lineArr    = array(3);
                $line_x  = 5;
                break;
            case 6:
                $start_x    = 5;    // 开始位置X
                $start_y    = 30;   // 开始位置Y
                $pic_w   = intval($this->bg_w/3) - 5; // 宽度
                $pic_h   = intval($this->bg_h/3) - 5; // 高度
                $lineArr    = array(4);
                $line_x  = 5;
                break;
            case 7:
                $start_x    = 53;   // 开始位置X
                $start_y    = 5;    // 开始位置Y
                $pic_w   = intval($this->bg_w/3) - 5; // 宽度
                $pic_h   = intval($this->bg_h/3) - 5; // 高度
                $lineArr    = array(2,5);
                $line_x  = 5;
                break;
            case 8:
                $start_x    = 30;   // 开始位置X
                $start_y    = 5;    // 开始位置Y
                $pic_w   = intval($this->bg_w/3) - 5; // 宽度
                $pic_h   = intval($this->bg_h/3) - 5; // 高度
                $lineArr    = array(3,6);
                $line_x  = 5;
                break;
            case 9:
                $start_x    = 5;    // 开始位置X
                $start_y    = 5;    // 开始位置Y
                $pic_w   = intval($this->bg_w/3) - 5; // 宽度
                $pic_h   = intval($this->bg_h/3) - 5; // 高度
                $lineArr    = array(4,7);
                $line_x  = 5;
                break;
        }
        foreach( $this->pic_list as $k=>$pic_path ) {
            $kk = $k + 1;
            if ( in_array($kk, $lineArr) ) {
                $start_x    = $line_x;
                $start_y    = $start_y + $pic_h + $space_y;
            }
            //$pathInfo    = pathinfo($pic_path);
            $size_info = getimagesize($pic_path);
            //print_r($size_info);
            $mine = $size_info['mime'];
            //switch( strtolower($pathInfo['extension']) ) {
            switch( strtolower($mine) ) {
                case 'image/jpeg':
                case 'image/jpg':
                    $imagecreatefromjpeg    = 'imagecreatefromjpeg';
                    break;
                case 'image/png':
                    $imagecreatefromjpeg    = 'imagecreatefrompng';
                    break;
                case 'image/gif':
                default:
                    $imagecreatefromjpeg    = 'imagecreatefromstring';
                    $pic_path    = file_get_contents($pic_path);
                    break;
            }
            $resource   = $imagecreatefromjpeg($pic_path);
            // $start_x,$start_y copy图片在背景中的位置
            // 0,0 被copy图片的位置
            // $pic_w,$pic_h copy后的高度和宽度
            imagecopyresized($background,$resource,$start_x,$start_y,0,0,$pic_w,$pic_h,imagesx($resource),imagesy($resource)); // 最后两个参数为原始图片宽度和高度，倒数两个参数为copy时的图片宽度和高度
            $start_x    = $start_x + $pic_w + $space_x;
        }

        //header("Content-type: image/jpg");
        return imagejpeg($background,$this->save_path,95);
    }

}