/**
 * Created by chenxh on 2018/9/27.
 */
$(function(){


    $('input[name="run_type"]').on("change",function(){
        
        var val = $(this).val();
        //console.log(val);
        if(val==2){
            $('#cron_time').show();
        }else{
            $('#cron_time').hide();
        }
        
    });


    $('#subform').on('click',function(){

        var run_type = $('input[name="run_type"]:checked'),
            run_time = $('input[name="run_time"]'),
            geter = $('input[name="geter"]:checked');

        //console.log(run_type.val());
        if(!checkInputEmpty(run_type)){
            formValid.showErr(run_type,'请选择推送类型');
            return false;
        }else{
            formValid.showSuccess(run_type);
        }
        if(run_type.val()==2){

            if(!checkInputEmpty(run_time)){
                formValid.showErr(run_time,'请选择推送时间');
                return false;
            }else{
                formValid.showSuccess(run_time);
            }

        }

        if(!checkInputEmpty(geter)){
            formValid.showErr(geter,'请选择推送类型');
            return false;
        }else{
            formValid.showSuccess(geter);
        }

        $('#postform').submit();
    });

});