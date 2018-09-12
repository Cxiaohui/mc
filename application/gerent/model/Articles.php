<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-20
 * Time: 9:57
 */

namespace app\gerent\model;
use app\common\model\Base;

class Articles extends  Base
{
    protected $article_table = 'articles';
    protected $article_cont_table = 'article_conts';
    //protected $article_comment_table = 'article_comment';
    protected $article_cate_table = 'article_cate';

    //===========================================articles

    public function get_article_list($where,$field='*',$limit=15){
        return $this->_get_list($this->article_table,$where,['id'=>'desc'],$field,$limit);
    }

    public function get_article_count($where){
        return $this->_get_count($this->article_table,$where);
    }

    public function get_article_info($where,$field='*'){
        return $this->_get_info($this->article_table,$where,$field);
    }

    public function save_article_data($data){
        if(isset($data['id']) && $data['id']>0){
            $this->_update_data($this->article_table,['id'=>$data['id']],$data);
            return $data['id'];
        }else{
            $data['addtime'] = date('Y-m-d H:i:s');
            return $this->_insert_data($this->article_table,$data,true);
        }
    }

    public function update_article_data($where,$update){
        return $this->_update_data($this->article_table,$where,$update);
    }

    public function del_art($where){
        return $this->_update_data($this->article_table,$where,['isdel'=>1]);
        //return $this->_del_row($this->article_table,$where);
    }

    //===========================================article_conts

    public function get_article_cont_info($where,$field='*'){
        return $this->_get_info($this->article_cont_table,$where,$field);
    }

    public function save_article_cont_data($artid,$cont){
        $cont = gzcompress($cont,8);
        $count = $this->_get_count($this->article_cont_table,['artid'=>$artid]);
        if($count>0){
            return $this->_update_data($this->article_cont_table,['artid'=>$artid],['content'=>$cont]);
        }else{
            return $this->_insert_data($this->article_cont_table,['artid'=>$artid,'content'=>$cont]);
        }
    }

    public function del_cont($where){
        return $this->_del_row($this->article_cont_table,$where);
    }

    //=====================================cate_article

    public function get_cate_keydata($add0=false){
        $data = $this->get_cate_list([],'id,name');
        $return = [];
        $add0 && $return[0] = '未分类';
        foreach($data as $da){
            $return[$da['id']] = $da['name'];
        }
        return $return;
    }

    public function get_cate_list($where,$field='*',$limit=0){
        return $this->_get_list($this->article_cate_table,$where,['id'=>'asc'],$field,$limit);
    }

    public function get_cate_count($where){
        return $this->_get_count($this->article_cate_table,$where);
    }

    public function get_cate_info($where,$field='*'){
        return $this->_get_info($this->article_cate_table,$where,$field);
    }

    public function save_cate_data($data){
        if(isset($data['id']) && $data['id']>0){
            return $this->_update_data($this->article_cate_table,['id'=>$data['id']],$data);
        }else{
            return $this->_insert_data($this->article_cate_table,$data);
        }
    }

    public function del_cate($where){
        return $this->_del_row($this->article_cate_table,$where);
    }

}