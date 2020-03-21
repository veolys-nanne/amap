$(function() {
    $.extend({
        refreshFromAjax: function (data) {
            if (data['modal']) {
                $('#container-modal').html(data['modal']);
            } else {
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
        }
    });
    $.fn.detachTemp = function() {
        this.data('dt_placeholder', $('<span style="display: none;" />').insertAfter(this));
        return this.detach();
    }
    $.fn.reattach = function() {
        if(this.data('dt_placeholder')) {
            this.insertBefore(this.data('dt_placeholder'));
            this.data('dt_placeholder').remove();
            this.removeData('dt_placeholder');
        }
        else if(window.console && console.error)
            console.error("Unable to reattach this element because its placeholder is not available.");
        return this;
    }
});
