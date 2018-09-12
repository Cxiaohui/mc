/* 
 * 单文件上传
 */


var sUpd = {
    options: {
        'ubtn': '#filePicker',
        'btntext': '<i class="icon-upload-alt"></i> 点击选择图片',
        'ext': 'jpg,bmp,png',
        'minitype':'image/*',
        'server': '',
        'fileVal': 'Filedata',
        'formData': {},
        'fileSizeLimit': 1.5*1024 * 1024,
        'auto': true,
        'progress': '',
        'statbar':'',
        'viewimg':'',
        'mesg':'',
        'okclass':'',
        'errclass':'',
        'inneralert':true,
        'isiframe':true,
        'innermesg':true,
        'afterSuccess':function(file, response){}
    },
    // 所有文件的进度信息，key为file id
    percentages: {},
    uploader: null,
    init: function (confg) {
        this.checkSupp();
        this.options = $.extend({}, this.options, confg);
        this.initPluges();
        this.listenEvent();
    },
    //检查是否支持
    checkSupp: function () {
        if (!WebUploader.Uploader.support()) {
            alert('Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
            throw new Error('WebUploader does not support the browser you are using.');
        }
    },
    //初始化插件
    initPluges: function () {
        this.uploader = WebUploader.create({
            //指定选择文件的按钮容器，不指定则不创建按钮
            pick: {
                //注意 这里虽然写的是 id, 但是不是只支持 id, 还支持 class, 或者 dom 节点。
                id: this.options['ubtn'],
                label: this.options['btntext'],
                multiple: false//是否开起同时选择多个文件能力。
            },
            //指定Drag And Drop拖拽的容器，如果不指定，则不启动	
            //dnd: '#uploader .queueList',
            //指定监听paste事件的容器，如果不指定，不启用此功能。此功能为通过粘贴来添加截屏的图片。建议设置为document.body.	
            //paste: document.body,
            //指定接受哪些类型的文件。 由于目前还有ext转mimeType表，所以这里需要分开指定。
            accept: {
                title: 'Images',
                extensions: this.options['ext'],
//                mimeTypes: 'application/excel'//application/excel
            },
            // swf文件路径
            swf: '/static/plugin/webuper/js/Uploader.swf',
            //是否禁掉整个页面的拖拽功能，如果不禁用，图片拖进来的时候会默认被浏览器打开。
            disableGlobalDnd: true,
            //是否要分片处理大文件上传。
            chunked: true,
            server: this.options['server'],
            //验证文件总数量, 超出则不允许加入队列
            fileNumLimit: 1,
            //验证文件总大小是否超出限制, 超出则不允许加入队列
            fileSizeLimit: this.options['fileSizeLimit'],
            //验证单个文件大小是否超出限制, 超出则不允许加入队列。
            fileSingleSizeLimit: this.options['fileSizeLimit'],
            //method 
            method: 'POST',
            //文件上传请求的参数表，每次发送都会发送此对象中的参数。
            formData: this.options['formData'],
            //设置文件上传域的name
            fileVal: 'Filedata',
            //设置为 true 后，不需要手动调用上传，有文件选择即开始上传
            auto: this.options['auto']
        });
    },
    //监听事件
    listenEvent: function () {
        //上传过程中触发，携带上传进度
        this.uploader.onUploadProgress = function (file, percentage) {
            sUpd.uploadProgress(file, percentage);
        };
        //当文件被加入队列以后触发
        this.uploader.onFileQueued = function (file) {
            sUpd.fileQueued(file);
        };
        //上传成功
        this.uploader.onUploadSuccess = function (file, response) {
            sUpd.uploadSuccess(file, response);
        };
        //错误处理
        this.uploader.onError = function( code ) {
            sUpd.error(code);
        };
    },
    //更新进度
    updateTotalProgress: function () {
        var loaded = 0,
                total = 0,
                spans = this.options['progress'].children('span'),
                percent;
        $.each(this.percentages, function (k, v) {
            total += v[ 0 ];
            loaded += v[ 0 ] * v[ 1 ];
        });
        percent = total ? loaded / total : 0;
        spans.eq(0).text(Math.round(percent * 100) + '%').css('width', Math.round(percent * 100) + '%');
        //updateStatus();
    },
    //上传过程中触发，携带上传进度
    uploadProgress: function (file, percentage) {
        var $percent = this.options['progress'].children('span');
        $percent.css('width', percentage * 100 + '%');
        this.percentages[ file.id ][ 1 ] = percentage;
        this.updateTotalProgress();
    },
    //当文件被加入队列以后触发
    fileQueued: function (file) {
        this.percentages[ file.id ] = [file.size, 0];
        this.options['progress'].show();
    },
    //上传成功
    uploadSuccess: function (file, response) {
        //console.log(response);
        if (response.status == '0') {
            this.showMesg(response.info,0);  
            this.uploader.reset();
            this.options['progress'].fadeOut();
        } else {
            //this.options['viewimg'] && this.options['viewimg'].attr('src', response.data);
            this.uploader.reset();
            this.options['innermesg'] && this.showMesg('上传成功',1);
            this.options['progress'].fadeOut();
            this.options['afterSuccess'] && this.options['afterSuccess'](file, response);
        }
        return false;
    },
    //错误
    error: function (code) {
        var txt = '';
        if (code === 'Q_TYPE_DENIED') {
            txt = '文件类型不符合';
        } else if (code === 'Q_EXCEED_NUM_LIMIT') {
            txt = '添加的文件数量超出';
        } else if (code === 'Q_EXCEED_SIZE_LIMIT') {
            txt = '添加的文件总大小超出';
        }
        this.showMesg(txt,0);
    },
    //显示操作提示
    showMesg:function(txt,type){    
        //使用外部的提示
       if(!this.options['inneralert']){
           if(!this.options['isiframe']){
               layermsgpos(txt,2,(type===0?4:3));
           }else{
               pageMesg.show(txt, type);
           }
           
           return false;
       }
        //错误
        if(type===0){
            this.options['mesg'].removeClass(this.options['okclass']).addClass(this.options['errclass']);
        }else{
            this.options['mesg'].removeClass(this.options['errclass']).addClass(this.options['okclass']);
        }
        
        this.options['mesg'].html(txt).show();
    },
    
    hideMesg:function(){
        this.options['mesg'].hide();
    }  
};

