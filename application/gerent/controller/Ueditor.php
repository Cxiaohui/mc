<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-18
 * Time: 17:33
 */
namespace app\gerent\controller;

class Ueditor{
    public function index(){
        \app\common\library\Uedit::img();
    }
}