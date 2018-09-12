/**
 * Created by xiaohui on 2015/8/13.
 */
var ue;
$(function(){

    //编辑器
    if($('#editor').length>0){
        ue = UE.getEditor('editor', {toolbars: [myueditorconfig]});
    }
    valid.init();
});

var valid = {
    'form':'#aboutform',
    'subbtn':'#subbtn',

    init:function(){
        this._bindEvent();
    },
    _bindEvent: function () {
        //保存
        $(this.subbtn).on('click',function(){
            if(!valid._checkInput()){
                return false;
            }
            valid._post();
        });

    },
    _checkInput:function(){
        var name= $('input[name="name"]'),
            ctel = $('input[name="ctel"]'),
            /*sertel = $('input[name="sertel"]'),
            serqq = $('input[name="serqq"]'),
            wb = $('input[name="wb"]'),
            wx = $('input[name="wx"]'),*/
            address = $('input[name="address"]'),
            about = $('textarea[name="about"]');

        if(!checkInputEmpty(name)){
            formValid.showErr(name,'请填写公司名称');
            return false;
        }else{
            formValid.showSuccess(name);
        }
        if(!checkInputEmpty(ctel)){
            formValid.showErr(ctel,'请填写公司电话');
            return false;
        }else{
            formValid.showSuccess(ctel);
        }
        if(!checkInputEmpty(address)){
            formValid.showErr(address,'请填写公司地址');
            return false;
        }else{
            formValid.showSuccess(address);
        }

        if(!ue.hasContents()){
            formValid.showErr(about,'请填写公司简介');
            return false;
        }else{
            formValid.showSuccess(about);
        }
        return true;
    },

    _post:function(){
        $(this.form).submit();
    }
};
