/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/19
 * Time: 19:03
 */
var uploadeds = [];
var mc_qiniu = {
    'config':{
        'base_config':{
            useCdnDomain: true,//表示是否使用 cdn 加速域名
            region: null//选择上传域名区域
        },
        'putExtra':{
            fname: "",//文件原文件名
            params: {},//用来放置自定义变量
            mimeType: null//["image/png", "image/jpeg", "image/gif"]
        },
        'uptoken':'',
        'select':'.filepick'
    },
    init:function(uptoken,select,after_upload){
        if(typeof md5 == 'undefined'){
            throw new Error('需要引入md5.js');
        }
        this.config.uptoken = uptoken;
        this.config.select = select;
        this.after_upload = after_upload;
        this.init_upload_event();

    },
    init_upload_event:function(){
        var _this = this;
        $(document).on('change',_this.config.select,function(){
            //$(_this.config.select).change(function(){

            var file = this.files[0];
            var nameinfo = _this.do_filename(file.name);
            var file_dom = $(this),
                press = file_dom.closest('.mcdoc_upload_box').find('.mcdoc_press');

            var observer = {
                next:function(res){
                    //console.log(res);
                    press.css('width',res.total.percent+'%').html(res.total.percent+'%');
                },
                error:function(err){
                    if(err.message.indexOf('file type doesn\'t match')>-1){
                        layer.alert('上传的文件类型有误，请确认后再上传');
                    }else if(err.code && qnErrors[err.code]){

                        //目标资源已存在
                        if(err.code == 614){
                            var res = {
                                "filename":nameinfo['filename'],
                                "key":nameinfo['q_key'],
                                "hash":md5(nameinfo['q_key'])
                            };

                            _this.after_upload && _this.after_upload(file_dom,res);
                            return false;
                        }

                        layer.alert(qnErrors[err.code]);
                    }else{
                        layer.alert(err.message);
                    }
                    console.log(err);
                },
                complete:function(res){
                    //console.log(res);
                    //console.log(nameinfo);
                    res['filename'] = nameinfo['filename'];
                    _this.after_upload && _this.after_upload(file_dom,res);
                }
            };

            var subscription;
            // 调用sdk上传接口获得相应的observable，控制上传和暂停
            var observable = qiniu.upload(file, nameinfo['q_key'], _this.config.uptoken, _this.config.putExtra, _this.config.base_config);
            observable.subscribe(observer)
        });
    },
    do_filename:function(filename){

        var ext = filename.toLowerCase().split('.').splice(-1)[0];
        //console.log(dtx);
        return{
            'q_key':'projects/mcdocs-'+md5(encodeURIComponent(filename)+'-'+(new Date().getTime()))+'.'+ext,
            'filename':filename
        } ;
    },
    after_upload:null
};

mc_qiniu.init(uptoken,'.select',function(obj,res){
    obj.next('.mcdoc_ppress').remove();
    //console.log(obj);
    //console.log(res);
    //{hash: "Fjr8PuxDHsEimocNdGAr69SPvSeM", key: "mcdocs-cd39da27b1ab50e4370b741f9caf38b4.jpg", filename: "3f5d0349454ce55e56d8bbde67e17797.jpg"}
    var ext = (res['filename'].split('.').splice(-1))[0].toLowerCase();
    obj.after('文件名：<input class="p_doc" value="'+res['filename']+'" data-ext="'+ext+'" data-key="'+res['key']+'" data-hash="'+res['hash']+'">');
    if(ext=='jpg' || ext=='png' || ext=='jpeg' || ext=='gif'){
        obj.after('<p><img src="'+qu_host+res['key']+'" style="height: 120px;"/> </p>');
    }
    obj.after('<p><a href="javascript:;" class="text-error del_qnfile"><i class="icon-trash"></i> 删除</a></p>');
    obj.remove();

    //uploadeds.push(res);
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