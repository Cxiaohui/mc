/**
 * Created by chenxh on 2018/6/24.
 */
$(function(){
    $('#save_btn').on('click',function(){

        var paied = $('input[name="paied"]'),
            paied_time = $('input[name="paied_time"]'),
            remark = $('input[name="remark"]');
        if(!checkInputEmpty(paied)){
            formValid.showErr(paied,'请填写实付金额');
            return false;
        }else{
            formValid.showSuccess(paied);
        }
        if(!checkInputEmpty(paied_time)){
            formValid.showErr(paied_time,'请填写实付时间');
            return false;
        }else{
            formValid.showSuccess(paied_time);
        }

        return true;
    });
});