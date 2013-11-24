/**
 * FancyUpload - Flash meets Ajax for powerful and elegant uploads.
 *
 * Updated to latest 3.0 API. Hopefully 100% compat!
 *
 * @version		3.0
 *
 * @license		MIT License
 *
 * @author		Harald Kirschner <http://digitarald.de>
 * @copyright	Authors
 */

var SPGallUpload = new Class({

    Extends: Swiff.Uploader,

    options: {
        queued: 1,
        // compat
        limitSize: 0,
        limitFiles: 0,
        validateFile: $lambda(true)
    },

    initialize: function(fieldName, options) {
        this.fieldName
        = options['fieldName']
        = fieldName;
        this.list = $(fieldName + '_images');
        this.text = $(fieldName + '_text');
        progress = $(fieldName + '_progress');
        this.progress = new Fx.ProgressBar(progress, {
            text: new Element('span', {
                'class': 'progress-text'
            }).inject(progress, 'after')
        });

        i = 0;
        this.list.getElements('.sgimage').each(function(el){
            if( i % 4 == 0 ){
                el.addClass('left');
            }else if(i % 4 == 3){
                el.addClass('right');
            }
            i++;

            var img = el.getElement('img');
            var checkbox = el.getElement('input[type=checkbox]');
            if(checkbox.checked){
                img.removeClass('delete');
            }else{
                img.addClass('delete');
            }
            checkbox.addEvent('click', function(){
                if(this.checked){
                    img.removeClass('delete');
                }else{
                    img.addClass('delete');
                }
            });
        });

        // compat
        options.fileClass = options.fileClass || SPGallUpload.File;
        options.fileSizeMax = options.limitSize || options.fileSizeMax;
        options.fileListMax = options.limitFiles || options.fileListMax;
        options.url = options.url + '&format=json';

        this.parent(options);

        this.addEvents({
            'queue': this.onQueue,
            'complete': this.onComplete,
            'fileSuccess': this.onFileSuccess,
            'selectFail': this.onSelectFail
        });
    },

    onQueue: function() {
        this.text.set('html', '');
    },

    onComplete: function() {
        if (!this.size) {
            this.progress.set(0);
        }
    },

    onSelectFail: function(files){
        alert(Joomla.JText._('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_FILELISTMAX'));
    },
    /**
	 * compat
	 */
    upload: function() {
        this.start();
    },

    removeFile: function() {
        return this.remove();
    },

    reposition: function(coords){
        p = document.id(this.box.offsetParent);
        coords = coords || ((this.target && this.target.offsetHeight)
            ? this.target.getCoordinates()
            : {
                top: window.getScrollTop(),
                left: 0,
                width: 40,
                height: 40
            });
        this.box.setStyles(coords);
        this.fireEvent('reposition', [coords, this.box, this.target]);
    },

    onFileSuccess: function(file, response){
        var json = new Hash(JSON.decode(response, true) || {});

        if (json.get('status') == '1') {
            this.text.set('html', '<strong>' + Joomla.JText._('JLIB_HTML_BEHAVIOR_UPLOADER_FILE_SUCCESSFULLY_UPLOADED') + '</strong>');
            var newel = new Element('div', {
                'class': 'sgimage',
                'style': 'float:left;'
            });
            var checkbox = new Element('input', {
                type:'checkbox',
                name:this.fieldName + '[]',
                value:json.images.original,
                checked: true,
                'class': 'checkbox'
            });
            var image = new Element('img', {
                style: 'valign: middle',
                src: json.images.baseurl + json.images.thumb
            });

            newel.adopt(checkbox, image).inject(this.list);
            if(this.list.getElements('sgimage').size % 4 == 0){
                newel.addClass('left');
            }else if(this.list.getElements('sgimage').size % 4 == 3){
                newel.addClass('right');
            }
            window[this.fieldName + '_sortables'].addItems(newel);

            checkbox.addEvent('click', function(){
                if(this.checked){
                    image.removeClass('delete');
                }else{
                    image.addClass('delete');
                }
            });
        } else {
            var error = Joomla.JText._('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_OCCURRED',
                    'An Error Occurred').substitute({
                    error: json.get('error')
                });
            this.text.set('html', '<strong>' +
                error + '</strong>');
            alert(error);
        }
    }
});

SPGallUpload.File = new Class({

    Extends: Swiff.Uploader.File,

    render: function() {
        if (this.invalid) {
            if (this.validationError) {
                var msg = Joomla.JText._('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_'+this.validationError, this.validationError);
                this.validationErrorMessage = msg.substitute({
                    name: this.name,
                    size: Swiff.Uploader.formatUnit(this.size, 'b'),
                    fileSizeMin: Swiff.Uploader.formatUnit(this.base.options.fileSizeMin || 0, 'b'),
                    fileSizeMax: Swiff.Uploader.formatUnit(this.base.options.fileSizeMax || 0, 'b'),
                    fileListMax: this.base.options.fileListMax || 0,
                    fileListSizeMax: Swiff.Uploader.formatUnit(this.base.options.fileListSizeMax || 0, 'b')
                });
            }
            this.remove();
            return;
        }

        this.addEvents({
            'start': this.onStart,
            'progress': this.onProgress,
            'complete': this.onComplete,
            'error': this.onError
        });
    },

    validate: function() {
        return (this.parent() && this.base.options.validateFile(this));
    },

    onStart: function() {
        this.base.progress.cancel().set(0);
    },

    onProgress: function() {
        this.base.text.set('html', Joomla.JText._('JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_PROGRESS', 'Current Progress').substitute({
            rate: (this.progress.rate) ? Swiff.Uploader.formatUnit(this.progress.rate, 'bps') : '- B',
            bytesLoaded: Swiff.Uploader.formatUnit(this.progress.bytesLoaded, 'b'),
            timeRemaining: (this.progress.timeRemaining) ? Swiff.Uploader.formatUnit(this.progress.timeRemaining, 's') : '-'
        }));
        this.base.progress.start(this.progress.percentLoaded);
    },

    onComplete: function() {
        this.base.text.set('html', Joomla.JText._('JLIB_HTML_BEHAVIOR_UPLOADER_UPLOAD_COMPLETED', 'Upload Completed'));
        this.base.progress.start(100);

        if (this.response.error) {
            var msg = this.response.error;
            this.errorMessage = msg;
            var args = [this, this.errorMessage, this.response];

            this.fireEvent('error', args).base.fireEvent('fileError', args);
        } else {
            this.base.fireEvent('fileSuccess', [this, this.response.text || '']);
        }
    },

    onError: function() {
        var error = Joomla.JText._('JLIB_HTML_BEHAVIOR_UPLOADER_FILE_ERROR', 'File Error').substitute(this);
        //this.base.text.set('html', '<strong>' + error + ':</strong> ' + this.errorMessage);
        alert(error);
    }
});
