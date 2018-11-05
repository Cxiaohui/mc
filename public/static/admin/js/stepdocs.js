/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/19
 * Time: 19:03
 */
var uploadeds = [];
mc_qiniu.init({
    'uptoken':uptoken,
    'upselect':'.select',
    'file_prefix':'projects',
    'after_upload':false,
    'mime_type':null
});

var is_click = false;
$(function(){

    $('#add_more_ups').on('click',function(){
        var tpl = '<div class="mcdoc_upload_box">'
            +'<input type="file" class="select"/>'
            +'<div class="mcdoc_ppress">'
            +'<div class="mcdoc_press">0%</div></div></div>';

        $(this).before(tpl);
    });


    $('#subbtn').on('click',function(){
        if(is_click){
            return false;
        }
        var usermesg = $('#usermesg');
        if(!checkInputEmpty(usermesg)){
            formValid.showErr(usermesg,'请填写操作说明');
            return false;
        }else{
            formValid.showSuccess(usermesg);
        }
        $('.p_doc').each(function(i){
            var _this = $(this);
            uploadeds.push({
                "filename":_this.val(),
                "key":_this.attr('data-key'),
                "hash":_this.attr('data-hash'),
                'ext':_this.attr('data-ext')
            });
        });

        if(uploadeds.length<=0){
            formValid.showErr($('.up_box'),'请上传文档');
            return false;
        }else{
            formValid.showSuccess($('.up_box'));
        }
        is_click = true;
        $.post(location.href,{'usermesg':usermesg.val(),'upfiles':uploadeds},function(d){
            console.log(d);
            if(d.err==0){
                layermsg('保存成功',1);
                setTimeout(function(){
                    //history.back();
                    location.href = d.url;
                },1500);
            }else{
                is_click = false;
                layeralert(d.mesg,4,'提示');
            }
        },'json');
    });

});