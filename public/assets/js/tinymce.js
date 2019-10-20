(function( $ ){
    var methods = {
        init : function(params) {
            $(this).each(function() {
                tinymce.init({
                    target: $(this).get(0),
                    height: 500,
                    plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen code",
                        "insertdatetime media table paste imagetools wordcount"
                    ],
                    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code",
                    images_upload_url: 'tinyMceImage',
                    content_css: ['//fonts.googleapis.com/css?family=Lobster&display=swap'],
                    font_formats: 'Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Lobster=Lobster, cursive; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats',
                    setup: function (editor) {
                        editor.on('init', function () {
                            $('#' + this.id).wrap('<div style="width:0;height:0;overflow:hidden"></div>')
                            $('#' + this.id).show();
                        });
                        editor.on('change', function () {
                            editor.save();
                        });
                    }
                });
            });

            return this;
        },
        preview: function(event) {
            event.preventDefault();
            var $form = $('form', $(this));
            var data = $form.serializeArray();
            data.push({name: "preview", value: true});
            $.ajax({
                url: $form.attr('action'),
                type: 'post',
                dataType: 'json',
                data: $.param(data),
                success: function(data) {
                    var body = '';
                    for (var i in data) {
                        body += '' +
                            '<div class="table">' +
                            '    <div class="row">' +
                            '        <div class="cell">De : </div>' +
                            '       <div class="cell">' + Object.keys(data[i].from).join(', ') + '</div>' +
                            '   </div>' +
                            '   <div class="row">' +
                            '        <div class="cell">À: </div>' +
                            '       <div class="cell">' + Object.keys(data[i].to).join(', ') + '</div>' +
                            '   </div>' +
                            '   <div class="row">' +
                            '        <div class="cell">Sujet: </div>' +
                            '       <div class="cell">' + data[i].subject + '</div>' +
                            '   </div>' +
                            '</div>' +
                            '<div class="body">' + data[i].body + '</div>';
                    }
                    var $modal = $('' +
                        '<div class="modal" role="dialog" aria-labelledby="dataExtraLabel" aria-hidden="true">' +
                            '<div class="modal-dialog modal-lg">' +
                                '<div class="modal-content">' +
                                    '<div class="modal-body">' +
                                        body +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>'
                    );
                    $('body').append($modal);
                    $modal.on('hidden.bs.modal', function (e) {
                        $(this).remove();
                    });
                    $modal.modal({show: true});
                }
            });
        },
    };

    $.fn.addTinymce = function(methodOrOptions) {
        var currentArguments = arguments;
        var result = [];
        this.each(function() {
            if ( methods[methodOrOptions] ) {
                result.push(methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( currentArguments, 1 )));
            } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
                result.push(methods.init.apply( this, currentArguments ));
            } else {
                $.error( 'Method ' +  methodOrOptions + ' does not exist on jQuery.slot' );
            }
        });

        return this.length == 1 && result.length == 1 ? result[0] : result;
    };
})( jQuery );
