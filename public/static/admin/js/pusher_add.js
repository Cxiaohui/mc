/**
 * Created by chenxh on 2018/9/9.
 */
$(function(){
    var geter_box = $('#geter_box'),
        subform = $('#subform'),
        geter_list = $('#geter_list');

    $('#pject_select').on('change',function(){
        var _this = $(this),
            pject_id = _this.val();
        //console.log(p_id);

        get_project_info(pject_id);
    });
    if(p_id>0){
        get_project_info(p_id);
    }
    
    $('input[name="run_type"]').on('change',function(){
        var val = $(this).val();
        //console.log(val);
        if(val == '1'){
            $('.setting1').show();
            $('.setting2,.setting3').hide();
            //subform.html('立即发送提醒');
        }else if(val == '2'){
            $('.setting2').show();
            $('.setting1,.setting3').hide();
        }else if(val == '3'){
            $('.setting3').show();
            $('.setting1,.setting2').hide();
        }
    });

    $('#subform1,#subform2,#subform3').on('click',function(){

        var pject_select = $("#pject_select"),
            geterids = $('input[name="geterid[]"]:checked'),
            title = $('input[name="title"]'),
            message = $('textarea[name="message"]'),
            run_type = $('input[name="run_type"]:checked');

        //console.log(run_type);

        if(!checkSelectEmpty(pject_select)){
            formValid.showErr(pject_select,'请选择相关项目');
            return false;
        }else{
            formValid.showSuccess(pject_select);
        }

        //console.log(geterids.length);
        if(geterids.length<=0){
            formValid.showErr($('#geter_box'),'请选择接收者');
            return false;
        }else{
            formValid.showSuccess($('#geter_box'));
        }


        if(!checkInputEmpty(title)){
            formValid.showErr(title,'请填写提醒标题');
            return false;
        }else{
            formValid.showSuccess(title);
        }

        if(!checkInputEmpty(message)){
            formValid.showErr(message,'请填写提醒文案');
            return false;
        }else{
            formValid.showSuccess(message);
        }

        if(!run_type.val()){
            formValid.showErr(run_type,'请选择消息运行方式');
            return false;
        }else{
            formValid.showSuccess(run_type);
        }
        //多次
        if(run_type.val()==3){
            var begin_time = $('input[name="begin_time"]'),
                end_time = $('input[name="end_time"]'),
                run_rate_day = $('input[name="run_rate_day"]'),
                run_rate_time = $('select[name="run_rate_time"]');

            if(!checkInputEmpty(begin_time)){
                formValid.showErr(begin_time,'请填写时间范围');
                return false;
            }else{
                formValid.showSuccess(begin_time);
            }
            if(!checkInputEmpty(end_time)){
                formValid.showErr(end_time,'请填写时间范围');
                return false;
            }else{
                formValid.showSuccess(end_time);
            }
            if(!checkInputEmpty(run_rate_day)){
                formValid.showErr(run_rate_day,'请填写运行频率');
                return false;
            }else{
                formValid.showSuccess(run_rate_day);
            }

            if(!checkSelectEmpty(run_rate_time)){
                formValid.showErr(run_rate_time,'请填写运行频率');
                return false;
            }else{
                formValid.showSuccess(run_rate_time);
            }
        }

        //return false;
        $('#postform').submit();
    });



    function get_project_info(p_id){
        $.get(get_member_url,{'p_id':p_id,'pushid':pushid},function(d){
            //console.log(d);
            if(d.err=='0'){

                var tpl = '',len=d.data.length;
                for(var i=0;i<len;i++){

                    var checked = (d.data[i]['checked']==1)?' checked':'';

                    tpl += '<label class="label"><input type="checkbox" name="geterid[]" ' +
                        'value="'+d.data[i]['id']+'|'+d.data[i]['role']+'-'+d.data[i]['name']+'" '+checked+'/>'+
                        d.data[i]['role']+'-'+d.data[i]['name']+' </label><br/>';
                }
                geter_box.show();
                geter_list.html(tpl);
            }else{
                layeralert(d.msg,4,'提示');
            }
        },'json');
    }
});