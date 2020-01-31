(function( $ ){
    var methods = {
        init : function(params) {
            var $record = $('button.record', $(this));
            var $table = $('.table', $(this));
            if ($('.mail-log-check', $table).length == 0) {
                var $record = $('<button class="btn btn-block btn-success record">Enregistrer</button>');
                $('thead tr', $table).append($('<th>', {class: 'mail-log-check'}).append('Payé'));
                $('tbody tr', $table).append($('<td>').append($('<i>', {class: 'far fa-square text-dark'}).css({cursor : 'pointer'})));
                $table.after($record);
            }
            $('.far', $table).on('click', function() {
                $(this).toggleClass('fa-square').toggleClass('fa-check-square');
            });
            $record.on('click', function() {
                var $wrapper = $(this).closest('.mail-log');
                $.ajax({
                    url : $wrapper.data('url'),
                    method: 'post',
                    data: {id: $wrapper.data('id'), content: $wrapper.html()},
                    success: function(data) {
                    }
                });
            });

            return this;
        },
    };

    $.fn.addMailForm = function(methodOrOptions) {
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
