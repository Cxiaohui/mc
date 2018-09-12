/**
 * Created by xiaohui on 2015/7/27.
 */

$(function(){

    valid.init();
});

var valid = {
    'subbtn':'#subbtn',
    'form':'#postform',
    init:function(){
        this._bindEvent();
    },
    _bindEvent:function(){
        $(this.subbtn).on('click',function(){
            valid._checkdata();
        });
    },
    _checkdata:function(){
        var sid = $('input[name="sid"]').val();
        if(!sid){
            return false;
        }
        var time1 = $('input[name="time1"]'),
            time2 = $('input[name="time2"]'),
            uname = $('input[name="uname"]'),
            uphone = $('input[name="uphone"]'),
            chepai = $('input[name="chepai"]'),
            descn = $('textarea[name="descn"]');

        if(!checkInputEmpty(time1)){
            pageMesg.show('请填写预约时间',0);
            time1.focus();
            return false;
        }
        if(!checkInputEmpty(time2)){
            pageMesg.show('请填写预约时间',0);
            time2.focus();
            return false;
        }

        if(!checkInputEmpty(uname)){
            pageMesg.show('请填写车主姓名',0);
            uname.focus();
            return false;
        }
        if(!checkInputEmpty(uphone) || !isPhone(uphone)){
            pageMesg.show('请填写车主手机号码,并确认是否正确',0);
            uphone.focus();
            return false;
        }
        if(!checkInputEmpty(chepai)){
            pageMesg.show('请填写车牌号码',0);
            chepai.focus();
            return false;
        }
        if(!checkInputEmpty(descn)){
            pageMesg.show('请填写问题故障',0);
            descn.focus();
            return false;
        }

        pageMesg.hide();
        $(this.form).submit();

    }
};