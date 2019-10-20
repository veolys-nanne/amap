(function( $ ){
    var methods = {
        init : function(params) {
            var $select = $('.model select', $(this));
            var values = $select.val();
            $(this).drawMultiSelect('addColumns', params.columnNumber);
            for(var i in values) {
                $('.clone select', $(this)).eq(i).val(values[i])
            }
            $(this).drawMultiSelect('manageSelectedValues');

            return this;
        },
        manageSelectedValues : function() {
            var $select = $('.model select', $(this));
            var id = $select.attr('id');
            $select.val([]);
            $('option', $('.' + id)).removeAttr('disabled');
            $('option[data-disabled]', $('.' + id)).attr('disabled', 'disabled');
            $('.' + id).each(function () {
                if ($(this).val() != "") {
                    var values = $select.val();
                    values.push($(this).val());
                    $select.val(values);
                    $('option[value="' + $(this).val() + '"]', $('.' + id).not($(this))).attr('disabled', 'disabled');
                }
            });

            return this;
        },
        addColumns : function(total) {
            var diffColumnNumber = total - $('.clone', $(this)).length;
            var $context = $(this);
            if (diffColumnNumber > 0) {
                var $model = $('.model', $(this));
                var $select = $('select', $model);
                var id = $select.attr('id');
                for(var i = 0; i < diffColumnNumber; i++) {
                    var $clone = $model.clone()
                        .removeClass('model d-none')
                        .addClass('clone ');
                    var $select = $('select', $clone);
                    $('option[data-icon]', $select).each(function () {
                        $(this).prepend($(this).data('icon'));
                    });
                    $select
                        .removeAttr("multiple id name")
                        .addClass(id)
                        .data('id', id)
                        .prepend($('<option>', {
                            value: '',
                            text: '',
                            selected: 'selected',
                        }));
                    $select.on('change', function() {
                        $context.drawMultiSelect('manageSelectedValues');
                    });
                    $model.parent().append($clone);
                }
            } else if (diffColumnNumber < 0) {
                $('.clone:nth-last-child(-n+' + (-diffColumnNumber) + ')', $(this)).remove();
            }
            $(this).drawMultiSelect('manageSelectedValues');

            return this;
        }
    };

    $.fn.drawMultiSelect= function(methodOrOptions) {
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
