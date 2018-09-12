<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/2
 * Time: 18:40
 */
namespace app\gerent\controller;
use app\common\model\TestQA as mTqa;


class Testqa extends Common
{
    /**
     * @var mTqa
     */
    private $m;
    public function _initialize($check_login = true)
    {
        parent::_initialize($check_login);
        $this->m = new mTqa();
    }

    public function index()
    {
        $sop = $this->dosearch();

        $count = $this->m->get_count($sop['w']);
        $data = $page = [];
        if($count>0){
            $page = $this->_pagenav($count);
            $field = '*';
            $data = $this->m->get_list($sop['w'],$field,$page['offset'].','.$page['limit']);
        }

        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        $this->assign('sov',$sop['p']['sov']);
        $this->assign('is_so',$sop['is_so']);
        $this->assign('fselect',$this->filterSelect($sop['p']['sok'],['mobile'=>'手机号码','uname'=>'姓名']));
        $this->assign('answers',$this->answers());
        return $this->fetch('index');

    }

    protected function dosearch(){

        $is_so = false;
        $w = [];

        $soks = ['uname','mobile'];
        $p['sok'] = input('get.sok','');
        $p['sov'] = input('get.sov','');
        if($p['sov'] && $p['sok'] && in_array($p['sok'],$soks)){

            $w[$p['sok']] = ['like','%'.$p['sov'].'%'];

            $is_so = true;
        }

        return ['w'=>$w,'p'=>$p,'is_so'=>$is_so];
    }


    protected function answers(){
        return [
            '1'=>[
                'A'=>'一室',
                'B'=>'二室',
                'C'=>'三室',
                'D'=>'四室',
            ],
            '2'=>[
                'A'=>'男',
                'B'=>'女'
            ],
            '3'=>[
                'A'=>'35岁以下',
                'B'=>'35岁-45岁',
                'C'=>'45岁以上'
            ],
            '4'=>[
                'A'=>'老虎',
                'B'=>'猫头鹰',
                'C'=>'考拉',
                'D'=>'孔雀',
            ],
            '5'=>[
                'A'=>'A.清新北欧风格',
                'B'=>'B.简约不简单现代风格',
                'C'=>'C.禅意韵味的中式风格',
                'D'=>'D.清新北欧风格',
            ],
        ];
    }
}