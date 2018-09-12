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
            $('.setting2').hide();
            $('.setting3').hide();
            subform.html('立即发送提醒');
        }else{
            $('.setting'+val).show();
            $('.setting'+(val=='2'?3:2)).hide();
            subform.html('保存设置');
        }
    });

    subform.on('click',function(){
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