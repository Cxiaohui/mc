/**
 * Created by chenxh on 2018/7/1.
 */
//编辑器
if($('#editor').length>0){
    ue = UE.getEditor('editor', {toolbars: [myueditorconfig],serverUrl:ed_url});
}

$(function(){

    $('#subbtn').on('click',function(){

        var title = $('input[name="title"]'),
            summary = $('textarea[name="summary"]'),
            content = $('textarea[name="content"]');

        if(!checkInputEmpty(title)){
            formValid.showErr(title,'请填写文章标题');
            return false;
        }else{
            formValid.showSuccess(title);
        }

        if(summary.val()){
            if(summary.val().length>150){
                formValid.showErr(summary,'文章摘要不超过150字');
                return false;
            }else{
                formValid.showSuccess(summary);
            }
        }

        if(!ue.hasContents()){
            formValid.showErr(content,'请填写文章内容');
            return false;
        }else{
            formValid.showSuccess(content);
        }

        $('#postform').submit();
    });

});