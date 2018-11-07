<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/24
 * Time: 21:24
 */
namespace app\common\library;
use think\Db;
class Recomd {
    /**
     * 获取前端显示的推荐数据
     * @param $dev 设备
     * @return array
     */
    public function get_show_data($dev){

        $cachekey = 'indexsll:'.$dev;
        $data = cache($cachekey);

        /*if($data){
            return $data;
        }*/


        $w = '';
        $now = date('Y-m-d');

        $w = " betime<='{$now}' and (entime=0 or entime>='{$now}') and isdel=0";

        //echo $w;
        $list = Db::name('recommend')->field('retype,stable,sid,pic,url')->where($w)->order('sort','asc')->select();
        //print_r($list);
        $data = array();
        foreach($list as $lt){
            $tmp = $this->get_recmd_info($lt,$dev);
            if(!isset($tmp['pic']) || $tmp['pic']==''){
                continue;
            }
            $data[] = $tmp;
        }
        //cache($cachekey,$data,120);
        return $data;
    }

    public function get_recmd_info($row,$dev='table',$isaded=false){
        //print_r($row);
        $info = [];
        if(empty($row)){
            return $info;
        }
        $imgkeys = [
            'articles'=>4,
            'self'=>5
        ];
        $imgkey = $imgkeys[$row['stable']];
        /*if(strpos($row['pic'],'data/')===false){
            $imgkey = $imgkeys[$row['stable']];
        }else{
            $imgkey = -1;
        }*/
        //echo $imgkey;
        //跳转链接
        $url = '';
        if($row['retype']==1){
            if($isaded){
                $url = '';
            }else{
                $urls = [];
                switch($row['stable']){
                    case 'articles':
                        $urls = [
                            'table'=>url('Article/artedit',['id'=>$row['sid']]),
                            //'wx'=>
                            'app'=>url('Articles/info',['id'=>$row['sid']]),
                            //'pc'=>''
                        ];
                        break;
                }
                $url = $urls[$dev];
            }

        }else{
            $url = $row['url'];
        }
        $size = $this->getImgSize($dev);
        return [
            //'title'=>$row['title'],
            //'pic_url'=>c_img($row['pic'],$imgkey,$size['w'],$size['h']),
            'pic'=>c_img($row['pic'],$imgkey,$size['w'],$size['h']),
            //'info_url'=>$url,
            'url'=>$url
        ];

    }

    public function get_recmd_info_add($row,$dev='table'){
        $info = array();
        if(empty($row)){
            return $info;
        }
        switch($row['stable']) {

            //文章推荐
            case 'articles':
                $m = 'articles';
                $fields = array(
                    '1' => 'id as sid,coverimg as pic',
                    '2' => 'id as sid,title,coverimg as pic',
                    '3' => 'id as sid,title'
                );
                $imgkey = 4;
                $w = ['id'=>$row['sid']];
                break;
            default:
                return $info;
        }

        $info = Db::name($m)->field($fields[$row['type']])->where($w)->find();
        //图片
        if(isset($info['pic'])){
            $size = $this->getImgSize($dev);
            $info['pic'] = c_img($info['pic'],$imgkey,$size['w'],$size['h']);
        }else{
            $info['pic'] = '';
        }
        $info['url'] = '';

        return $info;
    }



    /**
     * 不同设备上的图片尺寸
     * @param $dev
     * @return array
     */
    protected function getImgSize($dev){

        $sizes = array(
            'table'=>array('w'=>0,'h'=>0),//后台
            'app'=>array('w'=>750,'h'=>300),//app
            'pc'=>array('w'=>0,'h'=>0),//pc网页
            'wx'=>array('w'=>750,'h'=>300)//h5
        );
        return isset($sizes[$dev])?$sizes[$dev]:array('w'=>0,'h'=>0);
    }
}