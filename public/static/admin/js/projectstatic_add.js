/**
 * Created by chenxh on 2018/7/8.
 */
var uploadeds = [];
var mime_type = [
    "",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    "application/wps-writer",
    "application/pdf"
];
if(type==2){
    mime_type = ["image/png", "image/jpeg", "image/gif"];
}
mc_qiniu.init({
    'uptoken':uptoken,
    'upselect':'.select',
    'file_prefix':'projectstatics',
    'after_upload':false,
    'mime_type':mime_type
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
        var name = $('#name');
        if(!checkInputEmpty(name)){
            formValid.showErr(name,'请填写名称');
            return false;
        }else{
            formValid.showSuccess(name);
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
        if(pid==0){
            if(uploadeds.length<=0){
                formValid.showErr($('.up_box'),'请上传文档');
                return false;
            }else{
                formValid.showSuccess($('.up_box'));
            }
        }

        is_click = true;
        $.post(location.href,{'type':$('#type').val(),'name':name.val(),'remark':$('#remark').val(),'upfiles':uploadeds},function(d){
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