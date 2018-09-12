/**
 * Created by chenxh on 2017/7/6.
 */
$(function(){
    //封面图上传
    if($('#filePicker').length>0){
        var confg = {
            'btntext': '<i class="icon-upload-alt"></i> 上传图片',
            'server': up_url,
            'progress': $('#progressbar'),
            'statbar': $('.statusBar'),
            'viewimg': '',
            'inneralert':false,
            'isiframe':false,
            'formData': {'uptype':'indexmdu','typeid':0},
            'afterSuccess': function (file, response) {
                //console.log(response);
                var timenow = new Date().getTime();
                $('#logoimg').attr('src', response['data'] + '?' + timenow);
                $('#logoinput').val(response['info']);
            }

        };
        //插件初始化
        sUpd.init(confg);
    }

    $('#subbtn').on('click',function(){
        var title = $('input[name="title"]'),
            plc = $('input[name="plc"]');


        if(!checkInputEmpty(title)){
            formValid.showErr(title,'请填写标题');
            return false;
        }else{
            formValid.showSuccess(title);
        }
        if(!checkInputEmpty(plc)){
            formValid.showErr(plc,'请上传图片');
            return false;
        }else{
            formValid.showSuccess(plc);
        }
        $('#postform').submit();

    });

});