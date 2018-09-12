/**
 * Created by chenxh on 2017/7/4.
 */
$(function () {

    $('#so_btn').on('click',function(){

        var sok = $('select[name="sok"] option:selected').val(),
            sov = $('input[name="sov"]').val();
        if(!sok || !sov){
            return false;
        }
        $.post(oper_url,{'act':'so_h','sok':sok,'sov':sov},function(d){
            if(d.err){
                mlayer.alert(d.mesg);
            }else{
                so_result.fill_tpl(d.data);
            }
        },'json');

    });

    $('#subbtn').on('click',function(){
        var id = $('input[name="hid"]').val(),
            d_name = $('input[name="display_name"]'),
            lgacc = $('input[name="lgacc"]'),
            pwd = $('input[name="pwd"]');

        if(!id){
            formValid.showErr(d_name,'请选择相关机构');
            return false;
        }else{
            formValid.showSuccess(d_name);
        }
        if(!checkInputEmpty(d_name)){
            formValid.showErr(d_name,'显示名称也写一个吧');
            return false;
        }else{
            formValid.showSuccess(d_name);
        }
        if(!checkInputEmpty(lgacc)){
            formValid.showErr(lgacc,'请填写账号');
            return false;
        }else{
            formValid.showSuccess(lgacc);
        }
        var lgacc_v = $.trim(lgacc.val());
        if(!/^[0-9a-zA-Z]+$/.test(lgacc_v)){
            formValid.showErr(lgacc,'账号仅允许字母与数字');
            return false;
        }
        if(lgacc_v.length<4){
            formValid.showErr(lgacc,'账号最少4位');
            return false;
        }
        if(pwd.length>0){
            if(!checkInputEmpty(pwd)){
                formValid.showErr(pwd,'请填写登录密码');
                return false;
            }else{
                formValid.showSuccess(pwd);
            }
        }


        $('#postform').submit();

    });

});
var so_result = {

    'boxid':'#so_result',
    fill_tpl:function(data){

        var len = data.length,i=0,tpl='';
        for(;i<len;i++){
            tpl += '<a href="javascript:;" data-id="'+data[i]['id']+'" onclick="so_result.select(this);">'+data[i]['name']+'</a><br/>';
        }
        //console.log(tpl);
        $(this.boxid).html(tpl)
    },
    select:function(obj){
        var _this = $(obj),
            _id = _this.attr('data-id'),
            _name = _this.text();
        $('input[name="hid"]').val(_id);
        $('input[name="display_name"]').val(_name);
    }


};