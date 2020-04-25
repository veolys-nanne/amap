(function( $ ) {
    var methods = {
        init : function(params) {
            $('textarea:not(.form-popin)', $(this)).addTinymce();
            $('.slideshow', $(this)).addSlideShow();
            $('input.date-picker', $(this)).datepicker($.datepicker.regional['fr']);
            $('a[data-confirm]', $(this)).addConfirm();
            $(this).addFormHelper();
            $('select[multiple="multiple"]:visible', $(this)).select2();
            if ($('.collection').length) {
                $('.collection').collection({
                    allow_up : false,
                    allow_down : false,
                    add: '<a href="#"><i class="far fa-plus-square"></i></a>',
                    remove: '<a href="#"><i class="far fa-minus-square"></i></a>',
                    drag_drop: false,
                    after_add: function (collection, element) {
                        $(element).initCommon();
                        return true;
                    },
                });
            }
            $(this).addProductListing();
            $('a', $(this)).initCommon('ajax');
            $('form', $(this)).initCommon('ajax');
            $("input[type=file]", $(this)).change(function () {
                $(this).next(".custom-file-label").attr('data-content', this.files[0].name);
                $(this).next(".custom-file-label").text(this.files[0].name);
            });

            return this;
        },
        ajax : function() {
            $.ajaxSetup({ cache: false });
            if (!$(this).hasClass('download-file')) {
                var url = $(this).attr('href') || $(this).data('url');
                if ($(this).is('form')) {
                    url = $(this).attr('action') || window.location.href;
                    var $form = $(this);
                    $(':submit', $form).bind('click keypress', function () {
                        $form.data('submit', this.id);
                    });
                }
                if (undefined == $._data($(this)[0], 'events') && url != '#' && url) {
                    $(this).data('url', url);
                    $(this).on($(this).is('form') ? 'submit' : 'click', function(event) {
                        var $submit = $('#' + $(this).data("submit"));
                        if ($submit.hasClass('download-file')) {
                            return;
                        } else {
                            data = null;
                            if ($(this).is('form')) {
                                var data = new FormData($(this)[0]);
                                data.append($submit.attr('name'), null);
                            }
                            event.preventDefault();
                            $.ajax({
                                url : $(this).data('url'),
                                method: $(this).is('form') ? 'post' : 'get',
                                data: data,
                                dataType: 'json',
                                processData: false,
                                contentType: false,
                                success: function(data) {
                                    document.title = data.header;
                                    window.history.pushState({url: data.url, title: data.header}, data.header, data.url);
                                    $.refreshFromAjax(data);
                                }
                            });
                        }
                    });
                }
            }
        },
    };
    $.fn.initCommon = function(methodOrOptions) {
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
