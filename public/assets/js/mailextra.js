(function( $ ){
    var methods = {
        init : function(params) {
            if (params && params.preview) {
                var $context = $(this);
                $(this).on('click', function(event) {
                    $context.addMailExtra('preview', event);
                });
            } else {
                $(this).on('click', function (event) {
                    event.preventDefault();
                    if ($(this).is('a')) {
                        var $modal = $('' +
                            '<div class="modal" role="dialog" aria-labelledby="dataExtraLabel" aria-hidden="true">' +
                                '<div class="modal-dialog">' +
                                    '<div class="modal-content">' +
                                        '<div class="modal-header">' +
                                            '<h3 id="dataExtraLabel">Texte complémentaire pour le mail</h3>' +
                                        '</div>' +
                                        '<form action="' + $(this).attr('href') + '" method="post">' +
                                            '<div class="modal-body">' +
                                                '<textarea name="extra"></textarea>' +
                                            '</div>' +
                                            '<div class="modal-footer">' +
                                                '<button type="submit" class="btn btn-success">Envoyer</button>' +
                                                '<button type="button" class="btn btn-info preview">Prévisualisation</button>' +
                                            '</div>' +
                                        '</form>' +
                                    '</div>' +
                                '</div>' +
                            '</div>'
                        );
                    }
                    if ($(this).is('button')) {
                        $(this).addClass('clicked');
                        var $form = $(this).parents('form').eq(0);
                        var $formClone = $(this).parents('form').eq(0).clone();
                        var $buttonClone = $('.clicked', $formClone);
                        $buttonClone.removeClass('mail-extra');
                        $(this).removeClass('clicked');
                        $form.find("select").each(function(i) {
                            $formClone.find("select").eq(i).val($(this).val());
                        });
                        $($formClone).children().css({
                            visibility: "hidden",
                            position: "absolute",
                        });
                        $formClone.append($('<textarea name="extra"></textarea>'));
                        var $modal = $('' +
                            '<div class="modal" role="dialog" aria-labelledby="dataExtraLabel" aria-hidden="true">' +
                                '<div class="modal-dialog">' +
                                    '<div class="modal-content">' +
                                        '<div class="modal-header">' +
                                            '<h3 id="dataExtraLabel">Texte complémentaire pour le mail</h3>' +
                                        '</div>' +
                                        '<div class="modal-body"></div>' +
                                        '<div class="modal-footer">' +
                                            '<button type="button" class="btn btn-success submit">Envoyer</button>' +
                                            '<button type="button" class="btn btn-info preview">Prévisualisation</button>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>'
                        );
                        $('.modal-body', $modal).append($formClone)
                        $('.submit', $modal).on('click', function(event) {
                            $buttonClone.click();
                        });
                    }
                    $('body').append($modal);
                    $modal.on('shown.bs.modal', function (e) {
                        $(this).initCommon();
                    })
                    $modal.on('hidden.bs.modal', function (e) {
                        $(this).remove();
                    });
                    $modal.modal({show: true});
                });
            }

            return this;
        },
        preview: function(event) {
            event.preventDefault();
            var $form = $(this).closest('form');
            var data = $form.serializeArray();
            data.push({name: "preview", value: true});
            $.ajax({
                url: $form.attr('action'),
                type: 'post',
                dataType: 'json',
                data: $.param(data),
                success: function(data) {
                    var body = '';
                    for (var i in data) {
                        body += '' +
                            '<div class="table">' +
                            '    <div class="row">' +
                            '        <div class="cell">De : </div>' +
                            '       <div class="cell">' + Object.keys(data[i].from).join(', ') + '</div>' +
                            '   </div>' +
                            '   <div class="row">' +
                            '        <div class="cell">À: </div>' +
                            '       <div class="cell">' + Object.keys(data[i].to).join(', ') + '</div>' +
                            '   </div>' +
                            '   <div class="row">' +
                            '        <div class="cell">Sujet: </div>' +
                            '       <div class="cell">' + data[i].subject + '</div>' +
                            '   </div>' +
                            '</div>' +
                            '<div class="body">' + data[i].body + '</div>';
                    }
                    var $modal = $('' +
                        '<div class="modal" role="dialog" aria-labelledby="dataExtraLabel" aria-hidden="true">' +
                            '<div class="modal-dialog modal-lg">' +
                                '<div class="modal-content">' +
                                    '<div class="modal-body">' +
                                        body +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>'
                    );
                    $('body').append($modal);
                    $modal.on('hidden.bs.modal', function (e) {
                        $(this).remove();
                    });
                    $modal.modal({show: true});
                }
            });
        },
    };

    $.fn.addMailExtra = function(methodOrOptions) {
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
