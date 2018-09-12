/**
 * Created by chenxh on 2017/6/29.
 */
var ue;
$(function(){
    //编辑器
    if($('#editor').length>0){
        ue = UE.getEditor('editor', {toolbars: [myueditorconfig],serverUrl:ed_url});
    }
    var type_radio = $('input[name="type"]'),
        t_val = $('input[name="type"]:checked').val();

    box_toggel(t_val);

    type_radio.on('change',function(){
        var _val = $(this).val();
        console.log(_val);
        box_toggel(_val);
    });


    function box_toggel(_val) {
        if(_val==0){
            $('#txt_box').show();
            $('#html_box').hide();
        }else{
            $('#html_box').show();
            $('#txt_box').hide();
        }
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
        var title = $('input[name="title"]'),
            //pkey = $('input[name="pkey"]'),
            type = $('input[name="type"]:checked').val(),
            txt_cont = $('textarea[name="txt_cont"]'),
            html_cont = $('textarea[name="html_cont"]');


        if(!checkInputEmpty(title)){
            formValid.showErr(title,'请填写页面标题');
            return false;
        }else{
            formValid.showSuccess(title);
        }


        /*if(!checkInputEmpty(pkey)){
            formValid.showErr(pkey,'请填写页面唯一标识');
            return false;
        }else{
            formValid.showSuccess(pkey);
        }*/
        console.log(type);


        if(type==0){
            if(!checkInputEmpty(txt_cont)){
                formValid.showErr(txt_cont,'请填写纯文本内容');
                return false;
            }else{
                formValid.showSuccess(txt_cont);
            }
        }else{
            if(!ue.hasContents()){
                //console.log('dsfsdfds');
                formValid.showErr(html_cont,'请填写页面html内容');
                return false;
            }else{
                //console.log('d000000000');
                formValid.showSuccess(html_cont);
            }
        }

        return true;
    },

    _post:function(){
        $(this.form).submit();
    }
};