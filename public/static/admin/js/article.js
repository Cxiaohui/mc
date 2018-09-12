/**
 * Created by xiaohui on 2015/7/31.
 */

var ue;
$(function(){
    if($("#areabox").length > 0){
        $("#areabox").cateSelect({
            url:'area.js',
            nodata:"none"
        });
    }

    //推荐
    if($('a.openlayerwin').length>0){
        $('a.openlayerwin').on('click',function(){
            var url = $(this).attr('data-url');
            if(!url){
                return false;
            }
            layeriframe(url, '推荐设置', 600, 550);
        });

    }

    //编辑器
    if($('#editor').length>0){
        ue = UE.getEditor('editor', {toolbars: [myueditorconfig],serverUrl:ed_url});
    }
    //封面图上传
    if($('#filePicker').length>0){
        var confg = {
            'btntext': '<i class="icon-upload-alt"></i> 上传封面图片',
            'server': up_url,
            'progress': $('#progressbar'),
            'statbar': $('.statusBar'),
            'viewimg': '',
            'inneralert':false,
            'isiframe':false,
            'formData': {'uptype':'artcover','typeid':0},
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
    //delFile.init();
    valid.init();
    });
var valid = {
    'form':'#postform',
    'subbtn1':'#subbtn1',
    'subbtn2':'#subbtn2',

    init:function(){
        this._bindEvent();
    },
    _bindEvent: function () {
        //发布
        $(this.subbtn1).on('click',function(){
            if(!valid._checkInput()){
                return false;
            }

            valid._post(1);
        });
        //保存
        $(this.subbtn2).on('click',function(){
            if(!valid._checkInput()){
                return false;
            }
            valid._post(0);
        });
    },
    _checkInput:function(){
        var title = $('input[name="title"]'),
            acid = $('select[name="acid"]'),
            coverimg = $('input[name="coverimg"]'),
            //author = $('select[name="author"]'),
            summary = $('textarea[name="summary"]'),
            content = $('textarea[name="content"]');


        if(!checkInputEmpty(title)){
            formValid.showErr(title,'请填写文章标题');
            return false;
        }else{
            formValid.showSuccess(title);
        }

        if(!checkSelectEmpty(acid)){
            formValid.showErr(acid,'请选择文章分类');
            return false;
        }else{
            formValid.showSuccess(acid);
        }
        /*if(!checkSelectEmpty(author)){
            formValid.showErr(author,'请选择文章作者');
            return false;
        }else{
            formValid.showSuccess(author);
        }*/
        //return true;
        if(!checkInputEmpty(coverimg)){
            formValid.showErr(coverimg,'请上传文章封面');
            return false;
        }else{
            formValid.showSuccess(coverimg);
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
        return true;
    },

    _post:function(stat){
        $('input[name="status"]').val(stat);
        $(this.form).submit();
    }
};
