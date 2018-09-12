<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-13
 * Time: 19:47
 */
namespace app\gerent\validate;
use think\Validate;

class Admin extends Validate{

    protected $rule = [
        'log'  =>  'require|alphaDash|unique:admin',//|unique:admin
        'pwd' =>  'require',
        'name' =>  'require',
        'mobile'=>'require|unique:admin'
    ];

    protected $message = [
        'log.require'  =>  '用户名必须',
        'log.alphaDash'  =>  '用户名只能为字母和数字，下划线_及破折号-',
        'log.unique'  =>  '该用户名已使用',
        'pwd.require' =>  '请设置密码',
        'name.require' =>  '请填写姓名',
        'mobile.require' =>  '请填写手机号码',
        'mobile.unique' =>  '该手机号码已存在',
    ];

    protected $scene = [
        'add'=>['log','pwd','name','mobile'],
        'edit'  =>  ['log','name','mobile'],
    ];
}
