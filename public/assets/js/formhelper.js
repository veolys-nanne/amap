(function( $ ){
    var methods = {
        init : function(params) {
            var $context = $(this);
            $('.form-popin', $(this)).closest('.form-group').addClass('form-popin');
            $('[data-form][data-form-values]', $(this)).on('click', function(event) {
                var $form = $($(this).data('form'));
                var formValues = $(this).data('formValues');
                for (var i in formValues) {
                    $('[name="' + i + '"]', $form).val(formValues[i]);
                }
            });
            $('[data-sub-form]', $(this)).on('click', function(event) {
                if ($(this).parents('.modal').length == 0) {
                    event.preventDefault();
                    $context.addFormHelper('openModal',
                        $(this).data('button') ? $($(this).data('button')) : $(this),
                        $(this).data('subForm') ? $($(this).data('subForm')).closest('.form-group') : null,
                        $(this).data('form') ? $($(this).data('form')) : $($(this).data('subForm')).closest('form'),
                        $(this).data('text') || '');
                }
            });

            return this;
        },
        openModal : function($button, $subForm, $form, text) {
            var $modal = $('' +
                '<div class="modal sub-form" role="dialog" aria-hidden="true">' +
                    '<div class="modal-dialog modal-lg">' +
                        '<div class="modal-content">' +
                            '<div class="modal-body">' +
                                '<div class="modal-text"><strong>' + text + '</strong></div>' +
                                '<div class="modal-form"></div>' +
                            '</div>' +
                            '<div class="modal-footer"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
            if ($subForm) {
                $subForm.detachTemp().appendTo($('.modal-form', $modal));
            }
            $button.each(function() {
                $(this).detachTemp().appendTo($('.modal-footer', $modal));
            });
            $form.append($modal);
            $modal.modal({show: true});
            $modal.on('hidden.bs.modal', function (e) {
                if ($subForm) {
                    $subForm.reattach();
                }
                $button.each(function() {
                    $(this).reattach();
                });
                $modal.remove();
            });
            $('.modal-footer button', $modal).on('click', function(event) {
                $modal.modal('hide');
            });

            return this;
        },
    };

    $.fn.addFormHelper = function(methodOrOptions) {
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
