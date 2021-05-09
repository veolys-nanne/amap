(function( $ ){
    var methods = {
        init : function(params) {
            var $context = $(this), $player = $('.player', $(this)), width = $(this).width(), $ul = $('ul', $(this)), $lis = $('li', $(this));
            $ul.css({width: width * $lis.length});
            $lis.css({width: width, display: 'auto'});
            if ($lis.length > 1) {
                $player.data('pause', false);
                $player.show();
                $player.data('timeout', setTimeout(function() {$context.addSlideShow('animate')}, 5000));
            }
            $('.fa-chevron-circle-left', $player).on('click', function() {
                clearTimeout($player.data('timeout'));
                $context.addSlideShow('animate', true);
            });
            $('.fa-play-circle', $player).on('click', function() {
                clearTimeout($player.data('timeout'));
                $player.data('pause', false);
                $context.addSlideShow('animate');
            });
            $('.fa-pause-circle', $player).on('click', function() {
                clearTimeout($player.data('timeout'));
                $player.data('pause', true);
            });
            $('.fa-chevron-circle-right', $player).on('click', function() {
                clearTimeout($player.data('timeout'));
                $context.addSlideShow('animate');
            });

            return this;
        },
        animate: function(inversed) {
            var $context = $(this), $player = $('.player', $(this)), width = $(this).width(), $ul = $('ul', $(this));
            if (inversed) {
                $ul.css({"margin-left": -width}).prepend($('li:last', $ul));
                $ul.animate({"margin-left": 0}, 1000);
            } else {
                $ul.animate({"margin-left": -width}, 1000, function() {
                    $ul.css({"margin-left": 0}).append($('li:first', $ul));
                });
            }
            if(!$player.data('pause')) {
                $player.data('timeout', setTimeout(function() {$context.addSlideShow('animate')}, 5000));
            }
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
