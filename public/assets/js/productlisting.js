(function( $ ){
    var methods = {
        init : function(params) {
            $('.toggle-producer', $(this)).on('click', function (event) {
                event.preventDefault();
                var $i = $('i', $(this)).toggleClass('fa-arrow-up fa-arrow-down');
                $('tr[data-producer-id="' + $i.data('producer') + '"]').toggle();
            });

            $(this).addProductListing('addTotals');

            return this;
        },
        addTotals : function() {
            var $context = $(this)
            $('.total-product-id', $(this)).each(function () {
                $context.addProductListing('addTotal', '[data-product-id="' + $(this).data('product-id') + '"]', $(this));
            });
            $('.total-producer-id', $(this)).each(function () {
                $context.addProductListing('addTotal', '[data-producer-id="' + $(this).data('producer-id') + '"]', $(this));
            });
            $(this).addProductListing('addTotal', '[data-product-id]', $('.total', $(this)));

            return this;
        },
        addTotal : function(selector, $target) {
            var total = 0;
            $('span' + selector, $(this)).each(function () {
                total += $(this).data('price') * parseInt($(this).html());
            });
            $('input' + selector, $(this)).each(function () {
                total += $(this).data('price') * parseInt($(this).val());
            });
            $target.html((Math.round(total * 100) / 100).toFixed(2));

            return this;
        },
    };

    $.fn.addProductListing = function(methodOrOptions) {
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
