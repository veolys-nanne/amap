(function( $ ){
    var methods = {
        init : function(params) {
            $(this).on('click', function (event) {
                event.preventDefault();
                var $modal = $('' +
                    '<div class="modal" role="dialog" aria-labelledby="dataConfirmLabel" aria-hidden="true">' +
                        '<div class="modal-dialog">' +
                            '<div class="modal-content">' +
                                '<div class="modal-header">' +
                                    '<h3 id="dataConfirmLabel">Merci de confirmer</h3>' +
                                '</div>' +
                                '<div class="modal-body">' + $(this).data('confirm') + '</div>' +
                                '<div class="modal-footer">' +
                                    '<button class="btn" data-dismiss="modal" aria-hidden="true">Non</button>' +
                                    '<a href="' + $(this).attr('href') + '" class="btn btn-danger">Oui</a>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
                $('body').append($modal);
                $modal.modal({show: true});
                $modal.on('hidden.bs.modal', function (e) {
                    $(this).remove();
                });
            });
            return this;
        },
    };

    $.fn.addConfirm = function(methodOrOptions) {
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
