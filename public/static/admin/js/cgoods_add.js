/**
 * Created by chenxh on 2017/11/12.
 */
$(function(){
    //编辑器
    if($('#editor').length>0){
        ue = UE.getEditor('editor', {toolbars: [myueditorconfig],serverUrl:ed_url});
    }

    //封面图上传
    //if($('#filePicker').length>0){
    if($('.uploadimgbox').length>0){
        var confg = {
            'ubtn': '.uploadimgbox',
            'btntext': '',
            //'btntext': '<i class="icon-upload-alt"></i> 上传封面图片',
            'server': up_url,
            'progress': $('#progressbar'),
            'statbar': $('.statusBar'),
            'viewimg': '',
            //'multiple':true,
            'fileNumLimit':5,
            'fileSizeLimit':8*1024*1024,
            'inneralert':false,
            'isiframe':false,
            'formData': {'uptype':'goodsimg','typeid':0},
            'afterSuccess': function (file, response) {

                //console.log($(this));
                //console.log(file);
                //console.log(file.source);
                var timenow = new Date().getTime();
                console.log(file.source.getRuid());
                $('#rt_'+file.source.getRuid()).closest('.uploadimgbox').find('img.defimg').attr('src', response['data'] + '?' + timenow);


                //console.log(response);
                //var timenow = new Date().getTime();
                //$('#logoimg').attr('src', response['data'] + '?' + timenow);
                //$('#logoinput').val(response['info']);
            }

        };
        //插件初始化
        sUpd.init(confg);
    }
    $('.goodsimg').dragsort({
        //容器拖动手柄
        dragSelector: "a.move_btn",
        //执行之后的回调函数
        dragEnd:function(){},
        placeHolderTemplate:'<li class="uploadimgbox"></li>',
        //指定不会执行动作的元素
        dragSelectorExclude : "img"
    });


    $('.del_img').on('click',function(){
        var _this = $(this);
        layerconfirm('确定要删除吗？', 2, '确认', function () {
            var _img = _this.closest('.uploadimgbox').find('img'),
                src = _img.attr('src');
            if(src.indexOf('imgadd.png')>-1){
                closelayer();
                return false;
            }
            $.post(del_url,{'path':src},function(d){
                if(d.err==0){
                    layermsg(d.msg,1);
                    _img.attr('src','/data/image/imgadd.png');
                }else{
                    layermsg(d.msg,4);
                }
                closelayer();
                return false;
            },'json');
        }, function () {
            //layer.msg('取消', 2, {tcolor: 4});
            closelayer();
        });
    });

    valid.init();
});

//数据验证
var valid = {
    'form':'#postform',
    'subbtn':'#subbtn',
    init:function(){
        $(this.form).attr({'action':location.href});
        $(this.subbtn).on('click',function(){
            valid._checkVaild();
        });
    },
    _checkVaild:function(){

        var name = $('input[name="name"]'),
            credits = $('input[name="credits"]'),
            relief_amount = $('input[name="relief_amount"]'),
            //hids = $('select[name="hids[]"]'),
            //tags = $('input[name="tags"]'),
            //open_time = $('input[name="open_time"]'),
            //tel = $('input[name="tel"]'),
            //localinput = $('input[name="localinput"]'),
            content = $('textarea[name="content"]');

        if(!checkInputEmpty(name)){
            formValid.showErr(name,'请填写商品名称');
            return false;
        }else{
            formValid.showSuccess(name);
        }
        if(!checkInputEmpty(credits)){
            formValid.showErr(credits,'请填写商品兑换积分');
            return false;
        }else{
            formValid.showSuccess(credits);
        }
        if(!isNum(credits) || credits.val()<=0){
            formValid.showErr(credits,'兑换积分必须为大于0的数字');
            return false;
        }else{
            formValid.showSuccess(credits);
        }
        if(!checkInputEmpty(relief_amount)){
            formValid.showErr(relief_amount,'请填写商品可减免金额');
            return false;
        }else{
            formValid.showSuccess(relief_amount);
        }
        if(!isNum(relief_amount) || relief_amount.val()<0){
            formValid.showErr(relief_amount,'可减免金额不能为负数');
            return false;
        }else{
            formValid.showSuccess(relief_amount);
        }

        var imginput = $('input[name="imgs"]'),
            imgs = [];
        $('.defimg').each(function(i){
            var src = $(this).attr('src');
            if(src.indexOf('imgadd.png')<=-1){
                imgs.push(src);
            }
        });

        if(imgs.length<=0){
            formValid.showErr(imginput,'请上传商品图片');
            return false;
        }else{
            imginput.val(imgs.join('|'));
            formValid.showSuccess(imginput);
        }

        if(!checkInputEmpty(content)){
            formValid.showErr(content,'请填写商品详情');
            return false;
        }else{
            formValid.showSuccess(content);
        }

        $(this.form).submit();
    }

};