/**
 * Created by xiaohui on 2015/8/18.
 */
$(function(){

    //编辑器
    if($('#editor').length>0){
        ue = UE.getEditor('editor', {toolbars: [myueditorconfig]});
    }

    valid.init();

});
var valid = {
    'form':'#postform',
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
        var conts = $('textarea[name="conts"]');

        if(!ue.hasContents()){
            formValid.showErr(conts,'请填写文章内容');
            return false;
        }else{
            formValid.showSuccess(conts);
        }
        return true;
    },

    _post:function(){
        $(this.form).submit();
    }
};
