(function( $ ){
    var language = null;
    var methods = {
        init : function(params) {
            var $context = $(this);
            if (null == language) {
                $.getJSON('/assets/json/datatable.json', function(data) {
                    language = data;
                    $context.drawDataTable('callback', language, params);
                });
            } else {
                $context.drawDataTable('callback', language, params);
            }

            return this;
        },
        callback: function (data, params) {
            var $context = $(this);
            params.options.language = data;
            params.options.stateSave = true;
            params.options.responsive = true;
            if ($('td.move', $context).length > 0) {
                params.options.columnDefs = params.options.columnDefs || [];
                params.options.columnDefs.push(
                    {
                        targets: [$('td.move', $context).index()],
                        searchable: false,
                        sortable: false,
                        render: function (data, type, full, meta) {
                            if (type === 'display') {
                                return '<i class="fa fa-arrow-circle-up text-dark"></i><i class="fa fa-arrow-circle-down text-dark"></i>'
                            }
                            return data;
                        }
                    }
                );
                params.options.drawCallback = function (settings) {
                    $('tbody tr.first .fa-arrow-circle-up', $context).remove();
                    $('tbody tr.last .fa-arrow-circle-down', $context).remove();
                }
            }
            params.options['retrieve'] = true;
            params.options['drawCallback'] = function( settings ) {
                $('tr[data-url] td:not(:has(a)):not(:has(button))', $(this)).each(function (){
                    $(this).attr('data-url', $(this).parent().data('url'));
                    $(this).initCommon('ajax');
                });
            };

            var table = $context.DataTable(params.options);
            if (params.options.rowReorder) {
                table.on('row-reorder', function (e, details, changes) {
                    var data = [];
                    for (var i in details) {
                        data.push([details[i].oldData, details[i].newData]);
                    }
                    $.post($context.data('urlMove'), { 'moves': data }, function(data) {
                        $.refreshFromAjax(data);
                    })
                });
            }
            if (params.options.order) {
                var $length = $('.dataTables_length').parent();
                var $filter = $('.dataTables_filter').parent();
                var $reset = $('<div class="col-sm-12 col-md-4"><div class="dataTables_reset" id="DataTables_Table_0_reset"><button type="button" class="btn btn-info">Réinitialiser</button></div></div>');
                $length.removeClass('col-md-6').addClass('col-md-4');
                $filter.removeClass('col-md-6').addClass('col-md-4');
                if ($('.dataTables_reset').length == 0) {
                    $length.after($reset);
                    $reset.on('click', function() {
                        table.order(params.options.order).draw(false);
                    })
                }
            }
        }
    };

    $.fn.drawDataTable = function(methodOrOptions) {
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
