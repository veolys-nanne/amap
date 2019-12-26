(function( $ ){
    var methods = {
        init : function(params) {
            var $context = $(this);
            $(this).on('click', function(event) {
                if ($(this).parents('.modal').length == 0) {
                    event.preventDefault();
                    $context.addFormPopin('openModal', $(this).data('action') ? $($(this).data('action')) : $(this), $($(this).data('target')), $(this).data('values'), $(this).data('text') || '');
                }
            });

            return this;
        },
        openModal : function($button, $target, formValues, text) {
            var $context = $(this);
            var $modal = $('' +
                '<div class="modal" role="dialog" aria-labelledby="dataConfirmLabel" aria-hidden="true">' +
                    '<div class="modal-dialog modal-lg">' +
                        '<div class="modal-content">' +
                            '<div class="modal-body">' +
                                '<div class="modal-text">' + text + '</div>' +
                                '<div class="modal-form"></div>' +
                            '</div>' +
                            '<div class="modal-footer"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
            var $container = $target.parent();
            $target.detachTemp().appendTo($('.modal-body .modal-form', $modal)).toggleClass('hidden');
            $button
                .data('hidden', $button.hasClass('hidden'))
                .detachTemp().appendTo($('.modal-footer', $modal)).removeClass('hidden');
            if (formValues) {
                for (var i in formValues) {
                    $('[name="' + i + '"]', $target)
                        .val(formValues[i])
                        .parents('.form-group')
                        .toggleClass('hidden', null == formValues[i]);
                }
            }
            $target.closest('form').append($modal);
            $modal.modal({show: true});
            $modal.on('hidden.bs.modal', function (e) {
                $target.reattach().toggleClass('hidden');
                $button.reattach().toggleClass('hidden', $button.data('hidden'));
                $(this).remove();
            });
            $('.modal-footer button', $modal).on('click', function(event) {
                $modal.modal('hide');
            });

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
