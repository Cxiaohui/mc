$(function(){

    //编辑器
    if($('#editor').length>0){
        ue = UE.getEditor('editor', {toolbars: [myueditorconfig],serverUrl:ed_url});
    }

    if($('.uploadimgbox').length>0){

        mc_qiniu.init({
            'uptoken':uptoken,
            'upselect':'.uploadfile',
            'file_prefix':'goods',
            'mime_type':["image/png", "image/jpeg", "image/gif"],
            'after_upload':function(res){
                console.log(res);
                console.log(res.thisdom);
                var timenow = new Date().getTime();
                res.thisdom.next('img.defimg').attr('src', qu_host+res['key'] + '?' + timenow);
                //$('#rt_'+file.source.getRuid()).closest('.uploadimgbox').find('img.defimg').attr('src', response['data'] + '?' + timenow);
                //$('#logoinput').val(res['key']);
                //$('#logoimg').attr('src',qu_host+res['key']);
                /*var ext = (res['filename'].split('.').splice(-1))[0].toLowerCase();

                var html = '<div class="qiniu_doc_line">';
                if(ext=='jpg' || ext=='png' || ext=='jpeg' || ext=='gif'){
                    html += '<p><img src="'+qu_host+res['key']+'" style="height: 120px;"/> </p>';
                }
                html += '<input type="hidden" name="head_pic" class="p_doc" value="'+res['key']+'" data-ext="'+ext+'" data-key="'+res['key']+'" data-hash="'+res['hash']+'">'
                    +'<p><a href="javascript:;" class="text-error del_qnfile"><i class="icon-trash"></i> 删除</a></p>'
                    +'</div>';
                $('.mcdoc_upload_box').append(html);*/
            }

        });


        /*var confg = {
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
        sUpd.init(confg);*/
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
            //price_original = $('input[name="price_original"]'),
            //price_now = $('input[name="price_now"]'),
            cate_id = $('select[name="cate_id"]'),
            //hids = $('select[name="hids[]"]'),
            //tags = $('input[name="tags"]'),
            //open_time = $('input[name="open_time"]'),
            //tel = $('input[name="tel"]'),
            //localinput = $('input[name="localinput"]'),
            content = $('textarea[name="content"]');
            //logo = $('input[name="logo"]');

        if(!checkInputEmpty(name)){
            formValid.showErr(name,'请填写商品名称');
            return false;
        }else{
            formValid.showSuccess(name);
        }
        /*if(!checkInputEmpty(price_original)){
            formValid.showErr(price_original,'请填写商品原价');
            return false;
        }else{
            formValid.showSuccess(price_original);
        }
        if(!isNum(price_original) || price_original.val()<=0){
            formValid.showErr(price_original,'价格必须为大于0的数字');
            return false;
        }else{
            formValid.showSuccess(price_original);
        }
        if(!checkInputEmpty(price_now)){
            formValid.showErr(price_now,'请填写商品现价');
            return false;
        }else{
            formValid.showSuccess(price_now);
        }
        if(!isNum(price_now) || price_now.val()<=0){
            formValid.showErr(price_now,'价格必须为大于0的数字');
            return false;
        }else{
            formValid.showSuccess(price_now);
        }

        if(!checkSelectEmpty(hids)){
            formValid.showErr(hids,'请选择相关机构');
            return false;
        }else{
            formValid.showSuccess(hids);
        }

        if(!checkInputEmpty(tags)){
            formValid.showErr(tags,'请填写商品标签');
            return false;
        }else{
            formValid.showSuccess(tags);
        }*/

        var imginput = $('input[name="imgs"]'),
            imgs = [];
        $('.defimg').each(function(i){
            var src = $(this).attr('src');
            if(src.indexOf('imgadd.png')<=-1){
                imgs.push(src.split('?')[0]);
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

        /*var is_time = $('input[name="is_time"]:checked').val();
        if(is_time==1){
            var price_time = $('input[name="price_time"]'),
                be_time = $('input[name="be_time"]'),
                end_time = $('input[name="end_time"]');
            if(!checkInputEmpty(price_time)){
                formValid.showErr(price_time,'请填写限时抢购价格');
                return false;
            }else{
                formValid.showSuccess(price_time);
            }

            if(!isNum(price_time) || price_time.val()<=0){
                formValid.showErr(price_time,'价格必须为大于0的数字');
                return false;
            }else{
                formValid.showSuccess(price_time);
            }

            if(!checkInputEmpty(be_time) || !checkInputEmpty(end_time)){
                formValid.showErr(be_time,'请填写限时抢购时间');
                return false;
            }else{
                formValid.showSuccess(be_time);
            }
        }*/

        $(this.form).submit();
    }

};