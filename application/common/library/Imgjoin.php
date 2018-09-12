<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/26
 * Time: 22:23
 */
namespace app\common\library;
use app\common\model\Buser,
    app\common\model\Cuser,
    app\common\model\IM,
    extend\Imgjoin as Jimg;
class Imgjoin{


    static public function create_group_icon($buserids,$cuserids,$tid){
        
        $buserids = array_unique($buserids);
        $cuserids = array_unique($cuserids);
        $busers = (new Buser())->get_list(['id'=>['in',$buserids]],'id,head_pic');
        $cusers = (new Cuser())->get_list(['id'=>['in',$cuserids]],'id,head_pic');

        $pic_list = [];
        foreach($busers as $bu){
            $pic_list[] = c_img($bu['head_pic'],3);
        }

        foreach($cusers as $cu){
            $pic_list[] = c_img($cu['head_pic'],2);
        }
        //print_r($pic_list);
        $save_path = './data/image/'.config('image_dirs.6');

        if(!is_dir($save_path)){
            mkdir($save_path);
        }
        $save_name = $save_path.md5($tid.'-'.time()).'.jpg';

        //exit;
        $jimg = new Jimg($pic_list,$save_name);
        if($jimg->do_work()){
            (new IM())->update_data(['im_type'=>2,'target_tag'=>$tid],['target_icon'=>ltrim($save_path,'.')]);
            return ltrim($save_path,'.');
        }
        //echo $save_name;
        return '';
    }

}