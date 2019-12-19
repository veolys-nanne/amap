(function( $ ){
    var language = null;
    var methods = {
        init : function(params) {
            var $context = $(this);
            $(this).on('click', function(event) {
                $context.addFormPopin('openModal', event, $(this), $(this).data('button') || 'Envoyer', $($(this).data('target')), $(this).data('values'), $(this).data('text') || '');
                if ($(this).data('action')) {
                    var $button = $($(this).data('action'));
                    $button.on('click', function(event) {
                        event.preventDefault();
                    });
                    $button.click();
                }
            });

            return this;
        },
        openModal : function(event, $button, buttonText, $target, formValues, text) {
            event.preventDefault();
            var $context = $(this);
            var $modal = $('' +
                '<div class="modal" role="dialog" aria-labelledby="dataConfirmLabel" aria-hidden="true">' +
                    '<div class="modal-dialog modal-lg">' +
                        '<div class="modal-content">' +
                            '<div class="modal-body">' +
                                '<div class="modal-text">' + text + '</div>' +
                                '<div class="modal-form"></div>' +
                            '</div>' +
                            '<div class="modal-footer">' +
                                '<button class="btn btn-success">' + buttonText + '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
            var $container = $target.parent();
            $target.detach().appendTo($('.modal-body .modal-form', $modal)).toggleClass('hidden');
            if (formValues) {
                for (var i in formValues) {
                    $('[name="' + i + '"]', $target)
                        .val(formValues[i])
                        .parents('.form-group')
                        .toggleClass('hidden', null == formValues[i]);
                }
            }
            $('body').append($modal);
            $modal.modal({show: true});
            (function ($context, $container, $target, $button) {
                $modal.on('hidden.bs.modal', function (e) {
                    $(this).remove();
                    $target.detach().appendTo($container).toggleClass('hidden');
                });
                $('button', $modal).on('click', function(event) {
                    $modal.modal('hide');
                    $($button.data('target')).parents('form').submit();
                });
            })($context, $container, $target, $button, buttonText);

            return this;
        },
    };

    $.fn.addFormPopin = function(methodOrOptions) {
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
