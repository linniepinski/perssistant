(function($, Models, Collections, Views) {
    $(document).ready(function() {
        Models.Message = Backbone.Model.extend({
            action: 'ae-sync-message',
            initialize: function() {}
        });
        Collections.Messages = Backbone.Collection.extend({
            model: Models.Message,
            action: 'ae-fetch-messages',
            initialize: function() {
                this.paged = 1;
            },
            comparator: function(m) {
                // console.log(m);
                var jobDate = new Date(m.get('comment_date'));
                return -jobDate.getTime();
            }
        });
        MessageItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'message-item',
            template: _.template($('#ae-message-loop').html()),
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                // after render view
            }
        });
        ListMessage = Views.ListPost.extend({
            tagName: 'li',
            itemView: MessageItem,
            itemClass: 'message-item',
            appendHtml: function(cv, iv, index) {
                var post = index,
                    $existingItems = cv.$('li.message-item'),
                    index = (index) ? index : $existingItems.length,
                    position = $existingItems.eq(index - 1),
                    $itemView = $(iv.el);
                if (!post || position.length === 0) {
                    cv.$el.prepend(iv.el);
                } else {
                    $itemView.insertAfter(position);
                }
            }
        });
        // view control file upload
        Views.FileUploader = Backbone.View.extend({
            events: {
                'click .removeFile': 'removeFile',
            },
            fileIDs : [],
            docs_uploader : {},
            initialize: function(options) {
                _.bindAll(this, 'refresh');
                var view = this,
                    $apply_docs = this.$el,
                    uploaderID = options.uploaderID;
                view.blockUi = new Views.BlockUi();
                // this.fileIDs = options.fileIDs;
                // this.docs_uploader = options.docs_uploader;
                this.docs_uploader = new AE.Views.File_Uploader({
                    el: $apply_docs,
                    uploaderID: uploaderID,
                    multi_selection: true,
                    unique_names: true,
                    upload_later: true,
                    filters: [{
                        title: "Compressed Files",
                        extensions: 'zip,rar'
                    }, {
                        title: 'Documents',
                        extensions: 'pdf,doc,docx,png,jpg,gif'
                    }],
                    multipart_params: {
                        _ajax_nonce: $apply_docs.find('.et_ajaxnonce').attr('id'),
                        action: 'ae_upload_files', 
                        imgType : 'file'
                    },
                    cbAdded: function(up, files) {
                        var $file_list = view.$('.apply_docs_file_list'),
                            i;
                        // Check if the size of the queue is over MAX_FILE_COUNT
                        if (up.files.length > view.docs_uploader.MAX_FILE_COUNT) {
                            // Removing the extra files
                            while (up.files.length > view.docs_uploader.MAX_FILE_COUNT) {
                                up.removeFile(up.files[up.files.length - 1]);
                            }
                        }
                        // render the file list again
                        $file_list.empty();
                        for (i = 0; i < up.files.length; i++) {
                            $(view.fileTemplate({
                                id: up.files[i].id,
                                filename: up.files[i].name,
                                filesize: plupload.formatSize(up.files[i].size),
                                percent: up.files[i].percent
                            })).appendTo($file_list);
                        }
                    },
                    cbRemoved: function(up, files) {
                        for (var i = 0; i < files.length; i++) {
                            view.$('#' + files[i].id).remove();
                        }
                    },
                    onProgress: function(up, file) {
                        view.$('#' + file.id + " .percent").html(file.percent + "%");
                    },
                    cbUploaded: function(up, file, res) {
                        if (res.success) {
                            view.fileIDs.push(res.data);
                        } else {
                            // assign a flag to know that we are having errors
                            view.hasUploadError = true;
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    },
                    onError: function(up, err) {
                        AE.pubsub.trigger('ae:notification', {
                            msg: err.message,
                            notice_type: 'error'
                        });
                    },
                    beforeSend: function() {
                        view.blockUi.block($apply_docs);
                    },
                    success: function() {
                        view.blockUi.unblock();
                    }
                });

                // setup the maximum files allowed to attach in an application
                this.docs_uploader.MAX_FILE_COUNT = 3;
            },
            fileTemplate: _.template('<li id="{{=id}}"><span class="file-name" >{{=filename }}</span><a href="#"><i class="fa fa-times removeFile"></i></a></li>'),
            removeFile: function(e) {
                e.preventDefault();
                var fileID = $(e.currentTarget).closest('li').attr("id");
                for (i = 0; i < this.docs_uploader.controller.files.length; i++) {
                    if (this.docs_uploader.controller.files[i].id === fileID) {
                        this.docs_uploader.controller.removeFile(this.docs_uploader.controller.files[i]);
                    }
                }
            }, 
            removeAllFile : function(){
                var view = this;
                $.each(view.docs_uploader.controller.files, function (i, file) {
                    view.docs_uploader.controller.removeFile(file);
                });
            }, 
            refresh : function(){
                this.$('.apply_docs_file_list').html('');
                this.fileIDs = [];
            }
        });
        /**
         * project workspace control
         * @since 1.3
         * @author Dakachi
         */
        Views.WorkPlaces = Backbone.View.extend({
            events: {
                'submit form.form-message': 'submitAttach'
            },
            initialize: function(options) {
                var view = this;
                view.blockUi = new Views.BlockUi();
                if ($('.message-container').find('.postdata').length > 0) {
                    var postsdata = JSON.parse($('.message-container').find('.postdata').html());
                    view.messages = new Collections.Messages(postsdata);
                } else {
                    view.messages = new Collections.Messages();
                }
                /**
                 * init list blog view
                 */
                new ListMessage({
                    itemView: MessageItem,
                    collection: view.messages,
                    el: $('.message-container').find('.list-chat-work-place')
                });
                /**
                 * init block control list blog
                 */
                new Views.BlockControl({
                    collection: view.messages,
                    el: $('.message-container')
                });
                // init upload file control
                this.docs_uploader = {};
                this.filecontroller = new Views.FileUploader({
                    el: $('#apply_docs_container'),
                    uploaderID: 'apply_docs', 
                    fileIDs : []
                });
                this.docs_uploader = this.filecontroller.docs_uploader;
            },
            submitAttach: function(e) {
                var self = this;
                var uploaded = false,
                    $target = $(e.currentTarget);
                e.preventDefault();
                if (this.docs_uploader.controller.files.length > 0) {
                    this.docs_uploader.controller.bind('StateChanged', function(up) {
                        if (up.files.length === up.total.uploaded) {
                            // if no errors, post the form
                            if (!self.hasUploadError && !uploaded) {
                                self.sendMessage($target);
                                uploaded = true;
                            }
                        }
                    });
                    this.hasUploadError = false; // reset the flag before re-upload
                    this.docs_uploader.controller.start();
                } else {
                    this.sendMessage($target);
                }
            },
            sendMessage: function(target) {
                var message = new Models.Message(),
                    view = this, 
                    $target = target;
                $target.find('textarea, input, select').each(function() {
                    message.set($(this).attr('name'), $(this).val());
                });
                message.set('fileID' , this.filecontroller.fileIDs);                
                
                this.filecontroller.fileIDs = [];
                message.save('', '', {
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(result, res, xhr) {
                        view.blockUi.unblock();
                        view.$('textarea').val('');
                        view.docs_uploader.controller.splice();
                        view.docs_uploader.controller.refresh();
                        if (res.success) {
                            view.messages.add(message);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            }
        });
        new Views.WorkPlaces({
            el: 'div.workplace-details'
        });
    })
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);
/**
 * report control view
 * @author Dakachi
 */
(function($, Models, Collections, Views) {
    $(document).ready(function() {
        Models.Report = Backbone.Model.extend({
            action: 'ae-sync-report',
            initialize: function() {}
        });
        Collections.Reports = Backbone.Collection.extend({
            model: Models.Message,
            action: 'ae-fetch-reports',
            initialize: function() {
                this.paged = 1;
            },
            comparator: function(m) {
                // console.log(m);
                var jobDate = new Date(m.get('comment_date'));
                return -jobDate.getTime();
            }
        });
        ReportItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'message-item',
            template: _.template($('#ae-report-loop').html()),
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                // after render view
            }
        });
        ListReport = Views.ListPost.extend({
            tagName: 'li',
            itemView: MessageItem,
            itemClass: 'message-item',
            appendHtml: function(cv, iv, index) {
                var post = index,
                    $existingItems = cv.$('li.message-item'),
                    index = (index) ? index : $existingItems.length,
                    position = $existingItems.eq(index - 1),
                    $itemView = $(iv.el);
                if (!post || position.length === 0) {
                    cv.$el.prepend(iv.el);
                } else {
                    $itemView.insertAfter(position);
                }
            }
        });

        Views.ReportPlaces = Backbone.View.extend({
            events: {
                'submit form.form-report': 'submitAttach'
            },
            initialize: function(options) {
                var view = this;
                view.blockUi = new Views.BlockUi();
                if ($('.report-container').find('.postdata').length > 0) {
                    var postsdata = JSON.parse($('.report-container').find('.postdata').html());
                    view.messages = new Collections.Messages(postsdata);
                } else {
                    view.messages = new Collections.Messages();
                }
                /**
                 * init list blog view
                 */
                new ListMessage({
                    itemView: ReportItem,
                    collection: view.messages,
                    el: $('.report-container').find('.list-chat-work-place')
                });
                /**
                 * init block control list blog
                 */
                new Views.BlockControl({
                    collection: view.messages,
                    el: $('.report-container')
                });

                // init upload file control
                this.docs_uploader = {};
                this.filecontroller = new Views.FileUploader({
                    el: $('#report_docs_container'),
                    uploaderID: 'report_docs', 
                    fileIDs : []
                });
                this.docs_uploader = this.filecontroller.docs_uploader;

            },
            submitAttach: function(e) {
                var self = this;
                var uploaded = false,
                    $target = $(e.currentTarget);
                e.preventDefault();
                if (this.docs_uploader.controller.files.length > 0) {
                    this.docs_uploader.controller.bind('StateChanged', function(up) {
                        if (up.files.length === up.total.uploaded) {
                            // if no errors, post the form
                            if (!self.hasUploadError && !uploaded) {
                                self.sendMessage($target);
                                uploaded = true;
                            }
                        }
                    });
                    this.hasUploadError = false; // reset the flag before re-upload
                    this.docs_uploader.controller.start();
                } else {
                    this.sendMessage($target);
                }
            },
            sendMessage: function(target) {
                var message = new Models.Report(),
                    view = this, 
                    $target = target;
                $target.find('textarea, input, select').each(function() {
                    message.set($(this).attr('name'), $(this).val());
                });
                message.set('fileID' , this.filecontroller.fileIDs);                
                this.filecontroller.fileIDs = [];
                message.save('', '', {
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(result, res, xhr) {
                        view.blockUi.unblock();
                        view.$('textarea').val('');
                        view.docs_uploader.controller.splice();
                        view.docs_uploader.controller.refresh();
                        if (res.success) {
                            view.messages.add(message);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            }
        });
        new Views.ReportPlaces({
            el: 'div.report-details'
        });
    })
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);