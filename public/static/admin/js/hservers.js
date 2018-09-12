/**
 * Created by chenxh on 2017/6/30.
 */
$(function(){




    valid.init();

});

var valid = {
    'form':'#postform',
    'subbtn':'#subbtn',
    'review':'#review',

    init:function(){
        this._bindEvent();
    },
    _bindEvent: function () {
        //保存
        $(this.subbtn).on('click',function(){
            if(!valid._checkInput(0)){
                return false;
            }
            valid._post();
        });
        //
        $(this.review).on('click',function(){
            var data = valid._checkInput(1);
            if(!data){
                return false;
            }
            $.post(opers_url,data,function(d){
                if(d.err){
                    layeralert(d.mesg);
                    return false;
                }else{
                    $('#review_box').html(d.data);
                }
                return false;
            },'json');
        });
    },
    _checkInput:function(n){
        var hid = $('input[name="hid"]'),
            user_id = $('input[name="user_id"]');


        if(!checkInputEmpty(hid)){
            formValid.showErr(hid,'请填写机构ID');
            return false;
        }else{
            formValid.showSuccess(hid);
        }

        if(!checkInputEmpty(user_id)){
            formValid.showErr(user_id,'请填写联系人ID');
            return false;
        }else{
            formValid.showSuccess(user_id);
        }
        if(n==0){
            return true;
        }else{
            return {'act':'review','hid':hid.val(),'uid':user_id.val()}
        }

    },

    _post:function(){
        $(this.form).submit();
    }
};