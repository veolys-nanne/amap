(function( $ ){
    var methods = {
        init : function(params) {
            var $context = $(this)
            var width = $(this).width();
            var $ul = $('ul', $(this));
            var $lis = $('li', $(this));
            $ul.css({width: width * $lis.length});
            $lis.css({width: width, display: 'auto'});
            if ($lis.length > 1) {
                setInterval(function(){
                    $ul.animate({"margin-left": -width}, 1000, function() {
                        $ul.css({"margin-left": 0}).append($('li:first', $ul));
                    });
                }, 5000);
            }

            return this;
        },
    };

    $.fn.addSlideShow = function(methodOrOptions) {
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
})(jQuery);
