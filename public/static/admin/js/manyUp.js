/**
 * Created by xiaohui on 2015/7/24.
 */
var mUpd = {
    options: {
        'wrap': '#uploader',
        'ubtn': '#manyFilePicker',
        'btntext': '<i class="icon-upload-alt"></i> 选择图片',
        'addbtn': '#addmorefilebtn',
        'addbtntxt': '继续添加',
        'ext': 'jpg,bmp,png',
        'minitype': 'image/*',
        'server': '',
        'fileVal': 'Filedata',
        'formData': {},
        'fileNumLimit': 12,
        'fileSizeLimit': 12 * 1024 * 1024,
        'fileSingleSizeLimit': 12 * 1024 * 1024,
        'dnd': '#uploader .queueList',
        'thumbW': 110,
        'thumbH': 110,
        'mesg': '',
        'okclass': '',
        'errclass': '',
        'inneralert': true,
        'isiframe': true,
        'innermesg': true,
        'afterSuccess': function (file, response) {
        }
    },
    // 所有文件的进度信息，key为file id
    percentages: {},
    uploader: null,
    wrap: '',
    queue: '',
    statusBar: '',
    info: '',
    upload: '',
    placeHolder: '',
    progress: '',
    fileCount: 0,//添加的文件数量
    fileSize: 0,//添加的文件总大小
    ratio: 1,
    thumbnailWidth: 0,
    thumbnailHeight: 0,
    supportTransition: null,
    state: 'pedding',// 可能有pedding, ready, uploading, confirm, done.
    init: function (confg) {
        this._checkSupp();
        this.options = $.extend({}, this.options, confg);
        this._createWrap();
        this._initVar();
        this._initUpper();
        this._listenEvent();
        this.upload.addClass('state-' + this.state);
        this.updateTotalProgress();
    },
    //检查是否支持
    _checkSupp: function () {
        if (!WebUploader.Uploader.support()) {
            alert('Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
            throw new Error('WebUploader does not support the browser you are using.');
        }
    },
    _createWrap: function () {
        this.wrap = $(this.options.wrap);
        // 图片容器
        this.queue = $('<ul class="filelist"></ul>')
            .appendTo(this.wrap.find('.queueList'));
        // 状态栏，包括进度和控制按钮
        this.statusBar = this.wrap.find('.statusBar');
        // 文件总体选择信息。
        this.info = this.statusBar.find('.info');
        // 上传按钮
        this.upload = this.wrap.find('.uploadBtn');
        // 没选择文件之前的内容。
        this.placeHolder = this.wrap.find('.placeholder');
        // 总体进度条
        this.progress = this.statusBar.find('.progress').hide();
    },
    //参数调整
    _initVar: function () {
        // 优化retina, 在retina下这个值是2
        this.ratio = window.devicePixelRatio || 1;
        // 缩略图大小
        this.thumbnailWidth = this.options.thumbW * this.ratio;
        this.thumbnailHeight = this.options.thumbH * this.ratio;
        this.supportTransition = (function () {
            var s = document.createElement('p').style,
                r = 'transition' in s ||
                    'WebkitTransition' in s ||
                    'MozTransition' in s ||
                    'msTransition' in s ||
                    'OTransition' in s;
            s = null;
            return r;
        })();
    },
    //实例化
    _initUpper: function () {
        this.uploader = WebUploader.create({
            pick: {
                id: this.options.ubtn,
                label: this.options.btntext
            },
            dnd: this.options.dnd,
            paste: document.body,
            accept: {
                title: 'Images',
                extensions: this.options.ext,
                mimeTypes: this.options.minitype
            },
            // swf文件路径
            swf: '/static/plugin/webuper/js/Uploader.swf',
            disableGlobalDnd: true,
            formData: this.options.formData,
            chunked: true,
            server: this.options.server,
            fileNumLimit: this.options.fileNumLimit,
            fileSizeLimit: this.options.fileSizeLimit,
            fileSingleSizeLimit: this.options.fileSingleSizeLimit
        });
        // 添加“添加文件”的按钮，
        this.uploader.addButton({
            id: this.options.addbtn,
            label: this.options.addbtntxt
        });
    },
    // 当有文件添加进来时执行，负责view的创建
    addFile: function (file) {
        var $li = $('<li id="' + file.id + '">' +
                '<p class="title">' + file.name + '</p>' +
                '<p class="imgWrap"></p>' +
                '<p class="progress"><span></span></p>' +
                '</li>'),

            $btns = $('<div class="file-panel">' +
                '<span class="cancel">删除</span>' +
                '<span class="rotateRight">向右旋转</span>' +
                '<span class="rotateLeft">向左旋转</span></div>').appendTo($li),
            $prgress = $li.find('p.progress span'),
            $wrap = $li.find('p.imgWrap'),
            $info = $('<p class="error"></p>'),

            showError = function (code) {
                switch (code) {
                    case 'exceed_size':
                        text = '文件大小超出';
                        break;

                    case 'interrupt':
                        text = '上传暂停';
                        break;
                    default:
                        text = '上传失败，请重试';
                        break;
                }

                $info.text(text).appendTo($li);
            };

        if (file.getStatus() === 'invalid') {
            showError(file.statusText);
        } else {

            $wrap.text('预览中');
            this.uploader.makeThumb(file, function (error, src) {
                if (error) {
                    $wrap.text('不能预览');
                    return;
                }
                var img = $('<img src="' + src + '">');
                $wrap.empty().append(img);
            }, this.thumbnailWidth, this.thumbnailHeight);

            this.percentages[file.id] = [file.size, 0];
            file.rotation = 0;
        }

        file.on('statuschange', function (cur, prev) {
            if (prev === 'progress') {
                $prgress.hide().width(0);
            } else if (prev === 'queued') {
                $li.off('mouseenter mouseleave');
                $btns.remove();
            }

            // 成功
            if (cur === 'error' || cur === 'invalid') {
                console.log(file.statusText);
                showError(file.statusText);
                mUpd.percentages[file.id][1] = 1;
            } else if (cur === 'interrupt') {
                showError('interrupt');
            } else if (cur === 'queued') {
                mUpd.percentages[file.id][1] = 0;
            } else if (cur === 'progress') {
                $info.remove();
                $prgress.css('display', 'block');
            } else if (cur === 'complete') {
                $li.append('<span class="success"></span>');
            }

            $li.removeClass('state-' + prev).addClass('state-' + cur);
        });

        $li.on('mouseenter', function () {
            $btns.stop().animate({height: 30});
        });

        $li.on('mouseleave', function () {
            $btns.stop().animate({height: 0});
        });

        $btns.on('click', 'span', function () {
            var index = $(this).index(),
                deg;

            switch (index) {
                case 0:
                    mUpd.uploader.removeFile(file);
                    return;
                case 1:
                    file.rotation += 90;
                    break;
                case 2:
                    file.rotation -= 90;
                    break;
            }

            if (mUpd.supportTransition) {
                deg = 'rotate(' + file.rotation + 'deg)';
                $wrap.css({
                    '-webkit-transform': deg,
                    '-mos-transform': deg,
                    '-o-transform': deg,
                    'transform': deg
                });
            } else {
                $wrap.css('filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation=' + (~~((file.rotation / 90) % 4 + 4) % 4) + ')');
            }
        });

        $li.appendTo(this.queue);
    },
    // 负责view的销毁
    removeFile: function (file) {
        var $li = $('#' + file.id);
        delete this.percentages[file.id];
        this.updateTotalProgress();
        $li.off().find('.file-panel').off().end().remove();
    },
    //更新进度
    updateTotalProgress: function () {
        var loaded = 0,
            total = 0,
            spans = this.progress.children(),
            percent;

        $.each(this.percentages, function (k, v) {
            total += v[0];
            loaded += v[0] * v[1];
        });

        percent = total ? loaded / total : 0;

        spans.eq(0).text(Math.round(percent * 100) + '%');
        spans.eq(1).css('width', Math.round(percent * 100) + '%');
        this.updateStatus();
    },
    //理新状态
    updateStatus: function () {
        var text = '', stats;

        if (this.state === 'ready') {
            text = '选中' + this.fileCount + '张图片，共' +
                WebUploader.formatSize(this.fileSize) + '。';
        } else if (this.state === 'confirm') {
            stats = this.uploader.getStats();
            if (stats.uploadFailNum) {
                text = '已成功上传' + stats.successNum + '张图片，' +
                    stats.uploadFailNum + '张照片上传失败';
                //，<a class="retry" href="javascript:;">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>
            }

        } else {
            stats = this.uploader.getStats();
            text = '共' + this.fileCount + '张（' +
                WebUploader.formatSize(this.fileSize) +
                '），已上传' + stats.successNum + '张';

            if (stats.uploadFailNum) {
                text += '，失败' + stats.uploadFailNum + '张';
            }
        }

        this.info.html(text);
    },
    //设置状态
    setState: function (val) {
        var file, stats;

        if (val === this.state) {
            return;
        }

        this.upload.removeClass('state-' + this.state);
        this.upload.addClass('state-' + val);
        this.state = val;

        switch (this.state) {
            case 'pedding':
                this.placeHolder.removeClass('element-invisible');
                this.queue.parent().removeClass('filled');
                this.queue.hide();
                this.statusBar.addClass('element-invisible');
                this.uploader.refresh();
                break;

            case 'ready':
                this.placeHolder.addClass('element-invisible');
                $('#addmorefilebtn').removeClass('element-invisible');
                this.queue.parent().addClass('filled');
                this.queue.show();
                this.statusBar.removeClass('element-invisible');
                this.uploader.refresh();
                break;

            case 'uploading':
                $(this.options.addbtn).addClass('element-invisible');
                this.progress.show();
                this.upload.text('暂停上传');
                break;

            case 'paused':
                this.progress.show();
                this.upload.text('继续上传');
                break;

            case 'confirm':
                this.progress.hide();
                this.upload.text('开始上传').addClass('disabled');

                stats = this.uploader.getStats();
                if (stats.successNum && !stats.uploadFailNum) {
                    this.setState('finish');
                    return;
                }
                break;
            case 'finish':
                stats = this.uploader.getStats();
                if (stats.successNum) {
                    mUpd.showMesg('上传成功',1);
                } else {
                    // 没有成功的图片，重设
                    this.state = 'done';
                    location.reload();
                }
                break;
        }

        this.updateStatus();
    },
    //监听
    _listenEvent: function () {
        this.uploader.onUploadProgress = function (file, percentage) {
            var $li = $('#' + file.id),
                $percent = $li.find('.progress span');

            $percent.css('width', percentage * 100 + '%');
            mUpd.percentages[file.id][1] = percentage;
            mUpd.updateTotalProgress();
        };
        //上传成功
        this.uploader.onUploadSuccess = function (file, response) {
            mUpd.uploadSuccess(file, response);
        };

        this.uploader.onFileQueued = function (file) {
            mUpd.fileCount++;
            mUpd.fileSize += file.size;

            if (mUpd.fileCount === 1) {
                mUpd.placeHolder.addClass('element-invisible');
                mUpd.statusBar.show();
            }

            mUpd.addFile(file);
            mUpd.setState('ready');
            mUpd.updateTotalProgress();
        };

        this.uploader.onFileDequeued = function (file) {
            mUpd.fileCount--;
            mUpd.fileSize -= file.size;

            if (!mUpd.fileCount) {
                mUpd.setState('pedding');
            }

            mUpd.removeFile(file);
            mUpd.updateTotalProgress();

        };

        this.uploader.on('all', function (type) {
            var stats;
            switch (type) {
                case 'uploadFinished':
                    mUpd.setState('confirm');
                    break;

                case 'startUpload':
                    mUpd.setState('uploading');
                    break;

                case 'stopUpload':
                    mUpd.setState('paused');
                    break;

            }
        });

        this.uploader.onError = function (code) {
            this.error(code);
        };

        this.upload.on('click', function () {
            if ($(this).hasClass('disabled')) {
                return false;
            }

            if (mUpd.state === 'ready') {
                mUpd.uploader.upload();
            } else if (mUpd.state === 'paused') {
                mUpd.uploader.upload();
            } else if (mUpd.state === 'uploading') {
                mUpd.uploader.stop();
            }
        });

        this.info.on('click', '.retry', function () {
            mUpd.uploader.retry();
        });

        this.info.on('click', '.ignore', function () {
            mUpd.showMesg('todi',0);
        });
    },
    //上传成功
    uploadSuccess: function (file, response) {
        //console.log(response);
        this.options['afterSuccess'] && this.options['afterSuccess'](file, response);
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
        this.showMesg(txt, 0);
    },
    //显示操作提示
    showMesg: function (txt, type) {
        //使用外部的提示
        if (!this.options['inneralert']) {
            if (!this.options['isiframe']) {
                layermsgpos(txt, 2, (type === 0 ? 4 : 3));
            } else {
                pageMesg.show(txt, type);
            }

            return false;
        }
        //错误
        if (type === 0) {
            this.options['mesg'].removeClass(this.options['okclass']).addClass(this.options['errclass']);
        } else {
            this.options['mesg'].removeClass(this.options['errclass']).addClass(this.options['okclass']);
        }

        this.options['mesg'].html(txt).show();
    },

    hideMesg: function () {
        this.options['mesg'].hide();
    }

};