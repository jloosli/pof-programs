/**
 * Created with JetBrains PhpStorm.
 * User: Jared Loosli
 * Date: 9/17/12
 * Time: 8:09 AM
 * To change this template use File | Settings | File Templates.
 */
(function ($) {
    // for addprogram
    $(".add_program").click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $.post(pom_add.ajaxurl, {
            user:$this.data('user'),
            program_id:$this.data('program_id'),
            _ajax_nonce:$this.data('nonce'),
            toDo:'add',
            action:"pom_addprogram"
        }, function (result) {
            console.log("SUCCESS!");
            console.log(result);
            var newmessage =$("<div></div>");
            if (result.success) {
                newmessage.addClass('success').html(result.after);
            } else {
                newmessage.addClass('warning').html("Error: Couldn't add capability");
            }
            $this.replaceWith(newmessage);
        }, "json")
            .fail(function(reason) {
                "use strict";
                console.error("FAIL");
                console.log(reason);
                //console.log(reason.getAllResponseHeaders());
            });
    });
})(jQuery);
