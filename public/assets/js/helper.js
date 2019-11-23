$(function() {
    $.extend({
        refreshFromAjax: function (data) {
            $('#container-header').html(data['header']);
            $('#container-flash').html(data['flash']);
            $('#container-body').html(data['body']);
            if ('navbar' in data) {
                $('#container-navbar').html(data['navbar']);
            }
            $(data['script']).filter("script").each(function () {
                var ajaxScript = new Function($(this).text());
                ajaxScript();
            });
        }
    });
});