<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/1
 * Time: 14:42
 */
namespace app\gerent\validate;
use think\Validate;

class Pcase extends Validate{
    protected $rule = [
        'name'  =>  'require',
        'huxing'  =>  'require',
        'mianji'  =>  'require',
        'fengge'  =>  'require',
        'seijishi'  =>  'require',
        'jingli'  =>  'require',
        'jianli'  =>  'require',
        'step_json'  =>  'require',
        'coverimg'  =>  'require',

    ];

    protected $message = [
        'name.require'  =>  '请填写案例名称',
        'huxing.require'  =>  '请填写案例户型信息',
        'mianji.require'  =>  '请填写案例面积信息',
        'fengge.require'  =>  '请填写案例风格信息',
        'seijishi.require'  =>  '请填写案例设计师信息',
        'jingli.require'  =>  '请填写案例项目经理信息',
        'jianli.require'  =>  '请填写案例项目监理信息',
        'step_json.require'  =>  '请完成阶段的信息',
        //'coverimg.require'  =>  '请上传案例封面',
    ];

    protected $scene = ['edit','add'];
}