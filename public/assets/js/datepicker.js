(function( $ ){
    var language = null;
    var methods = {
        init : function(params) {
            var $context = $(this);
            var options = $.extend({}, params || {}, $.datepicker.regional['fr']);
            options['minDate'] = 0;
            options['beforeShowDay'] = (function($context)
                {
                    return function(date) {
                        var css = [];
                        var datestring = ('0' + date.getDate()).slice(-2) + '/' +
                            ('0' + (date.getMonth() + 1)).slice(-2) + '/' +
                            date.getFullYear();
                        if ($context.data('unavailabilityDates')) {
                            css.push(-1 != $.inArray(datestring, $context.data('unavailabilityDates')) ? 'unavailability' : 'availability');
                        }
                        if (-1 != $.inArray(datestring, $context.data('currentPlanningDates') || [])) {
                            css.push('in-current-planning');
                        }
                        else if (-1 != $.inArray(datestring, $context.data('planningDates') || [])) {
                            css.push('in-planning');
                        }
                        return [-1 == $.inArray(datestring, $context.data('unselectableDates') || []), css.join(' ')];
                    }
                })($(this));
            $(this).datepicker(options);
            $(this).on('create-widget', function(event, datetext, target) {
                $context.drawDatepicker('createWidget', datetext, target);
            });
            $(this).on('remove-widget', function(event, datetext, target) {
                $context.drawDatepicker('removeWidget', datetext, target);
            });

            return this;
        },
        createWidget: function (dateText, target) {
            var values = $(this).data(target);
            var $container = $('.widget-date-choice .values');
            var $newWidget = $($container.data('prototype').replace(/__name__/g, $container.data('widgetCounter')));
            values.push(dateText);
            $(this).data(target, values);
            $container.data('widget-counter', $container.data('widget-counter') + 1);
            $('input[type="text"]', $newWidget).val(dateText);
            $container.append($newWidget);
            $(this)
                .datepicker('refresh')
                .datepicker('setDate', dateText);


        },
        removeWidget: function (dateText, target) {
            var $context = $(this);
            var values = $(this).data(target);
            var $container = $('.widget-date-choice .values');
            values.splice(values.indexOf(dateText), 1);
            $(this).data(target, values);
            $('input[type="text"]', $container).each(function() {
                if ($(this).val() == dateText){
                    $('#' + $(this).attr('id').replace(/^(.*)_date$/, '$1')).remove();
                    $context
                        .datepicker('refresh')
                        .datepicker('setDate', dateText);
                }
            });
        }
    };

    $.fn.drawDatepicker = function(methodOrOptions) {
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
