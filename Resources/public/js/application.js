(function($) {

    $.fn.Uploader = function (settings) {
        return new Uploader(this, settings || {});
    }

    /**
     * Constructor for uploader object instances
     *
     * @param object selector jQuery selector
     * @param object settings Object literal with the following properties:
     *                        maxSize     File max size
     *                        mimeType    Allowed file types
     *                        path        Full path to upload folder
     *                        urlUpload   URL of the server-side upload script
     *                        urlDownload URL of the server-side download file script
     *                        urlremove   URL of the server-side remove file script
     *
     * @return Uploader instance
     */
    var Uploader = function(selector, settings) {
        this.selector = $(selector);

        this.divObj = {
            loader : '.loader',
            success: '.success',
            error  : '.error',
            name   : '.filename',
            remove : '.remove',
            message: '.message'
        };

        this.divState = {
            hide: {
                display: 'none'
            },
            show: {
                display: 'inline-block'
            }
        };

        settings = settings || {};
        $.each({
            'max-size'     : '1024k',
            'mime-types'   : [],
            'folder'       : '',
            'url-upload'   : '/_uploader/file_upload',
            'url-remove'   : '/_uploader/file_remove',
            'url-download' : '/_uploader/file_download'
        }, function (key, value) {
            if (typeof settings[key] == 'undefined') {
                settings[key] = $(selector).attr('data-' + key)
                    ? $(selector).data(key)
                    : value
                ;
            }
        });

        this.url = {
            upload   : settings['url-upload'],
            remove   : settings['url-remove'],
            download : settings['url-download']
        };

        this.data = {
            maxSize   : settings['max-size'],
            mimeTypes : settings['mime-types'],
            folder    : settings['folder']
        };

        this.init();

        // bindings
        var instance = this;

        this.getElement('.filename a').bind('click', function () {
            if ($(this).attr('href') == '#' && instance.url.download) {
                $(this).attr('href', instance.url.download + '?' + $.param({
                    filename: $('#' + instance.selector.attr('id').substring(9)).val(),
                    folder: instance.data.folder
                }));

                return true;
            }

            return false;
        });

        this.getElement('.remove').bind('click', function () {
            instance.loader();
            instance.getElement(instance.divObj.message).html('');

            $.ajax({
                url: instance.url.remove,
                type: 'POST',
                dataType: 'json',
                data: {
                    filename: $('#' + instance.selector.attr('id').substring(9)).val(),
                    folder: instance.data.folder
                },
                complete: function (xhr, status) {
                    var json;

                    // this is here to catch non-empty, non-JSON responses
                    try {
                        json = $.parseJSON(xhr.responseText);
                        if (null !== json) {
                            instance.selector.trigger(json.event, [json.data]);

                            if (json.event == 'uploader:error') {
                                throw json.data.message;
                            }

                            instance.success();
                            instance.getElement(instance.divObj.name).html('');
                            instance.getElement(instance.divObj.remove).hide();

                            // set original form value with filename
                            $('#' + instance.selector.attr('id').substring(9)).val('');
                        }

                        // this catches empty or otherwise non-JSON responses
                        else {
                            throw null;
                        }
                    } catch (e) {
                        instance.error();
                        instance.getElement(instance.divObj.message).html(e || 'An error occurred while removing file.');
                        instance.selector.trigger('uploader:error');
                    }
                }
            });
        });

        return this;
    };

    $.extend(Uploader.prototype = {
        /**
         * Initialize AjaxUpload object instance.
         */
        init: function () {
            var instance = this;

            new AjaxUpload(this.selector.attr('id'), {
                action: instance.url.upload,
                name: 'file',
                data: instance.data,
                onSubmit: function () {
                    instance.loader();
                    instance.getElement(instance.divObj.message).html('');
                },
                onComplete: function (file, response) {
                    var json;

                    // this is here to catch non-empty, non-JSON responses
                    try {
                        json = $.parseJSON(response);
                        if (null !== json) {
                            instance.selector.trigger(json.event, [json.data]);

                            if (json.event == 'uploader:error') {
                                throw json.data.message;
                            }

                            if (json.data.filename) {
                                instance.success();
                                instance.getElement(instance.divObj.name).html(
	                                instance.url.download
	                                ? '<a href="' + instance.url.download + '?' + $.param({filename: json.data.filename, folder: instance.data.folder}) + '" target="_blank">' + json.data.filename + '</a>'
	                                : json.data.filename
                                );
                                instance.getElement(instance.divObj.remove).show();

                                // set original form value with filename
                                $('#' + instance.selector.attr('id').substring(9)).val(json.data.filename);
                            }
                        }

                        // this catches empty or otherwise non-JSON responses
                        else {
                            throw null;
                        }
                    } catch (e) {
                        instance.error();
                        instance.getElement(instance.divObj.message).html(e || 'File was not uploaded.');
                        instance.selector.trigger('uploader:error');
                    }
                }
            });
        },

        getElement: function (key) {
            return this.selector.parent().find(key);
        },

        /**
         * Show loading icon.
         */
        loader: function () {
            this.getElement(this.divObj.loader).css(this.divState.show);
            this.getElement(this.divObj.success).css(this.divState.hide);
            this.getElement(this.divObj.error).css(this.divState.hide);
        },

        /**
         * Show success icon.
         */
        success: function() {
            this.getElement(this.divObj.loader).css(this.divState.hide);
            this.getElement(this.divObj.success).css(this.divState.show);
            this.getElement(this.divObj.error).css(this.divState.hide);
        },

        /**
         * Show error icon.
         */
        error: function () {
            this.getElement(this.divObj.loader).css(this.divState.hide);
            this.getElement(this.divObj.success).css(this.divState.hide);
            this.getElement(this.divObj.error).css(this.divState.show);
        }
    });

})(jQuery);
