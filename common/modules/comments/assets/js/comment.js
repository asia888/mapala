/**
 * Comment plugin
 */
(function ($) {
    $.fn.comment = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.comment');
            return false;
        }
    };

    // Default settings
    var defaults = {
        toolsSelector: '.comment-action-buttons',
        formSelector: '#comment-form',
        formContainerSelector: '.comment-form-container',
        contentSelector: '.comment-body',
        cancelReplyBtnSelector: '#cancel-reply',
        pjaxContainerId: '#comment-pjax-container',
        pjaxSettings: {
            timeout: 10000,
            scrollTo: false,
            url: window.location.href
        },
        submitBtnText: 'Comment',
        submitBtnLoadingText: 'Loading...'
    };

    // Methods
    var methods = {
        init: function (options) {
            return this.each(function () {
                var $commentForm = $(this);
                if ($commentForm.data('comment')) {
                    return;
                }
                var settings = $.extend({}, defaults, options || {});
                $commentForm.data('comment', settings);
                $commentForm.on('beforeSubmit.comment', beforeSubmitForm);
                var eventParams = {commentForm: $commentForm};
                
                $(settings.pjaxContainerId).on('click.comment', '[data-action="reply"]', eventParams, reply);
                $(settings.pjaxContainerId).on('click.comment', '[data-action="cancel-reply"]', eventParams, cancelReply);
                $(settings.pjaxContainerId).on('click.comment', '[data-action="delete"]', eventParams, deleteComment);
            });
        },
        data: function () {
            return this.data('comment');
        },
        reset: function (settings) {
            $(settings.pjaxContainerId).each(function () {
                $(this).unbind('.comment');
                $(this).removeData('comment');
            });
            $(settings.formSelector).comment(settings);
        }
    };


    /**
     * This function used for `beforeSubmit` comment form event
     */
    function beforeSubmitForm() {
        var $commentForm = $(this);
        var settings = $commentForm.data('comment');
        var pjaxSettings = $.extend({container: settings.pjaxContainerId}, settings.pjaxSettings);

        $commentForm.find(':submit').prop('disabled', true).text(settings.submitBtnLoadingText);

        $.post($commentForm.attr("action"), $commentForm.serialize(), function (data) {
            if (data.status == 'success') {
                $.pjax(pjaxSettings).done(function () {
                    $commentForm.trigger("reset");
                    methods.reset.call($commentForm, settings);
                });
            }
            else {
                if (data.hasOwnProperty('errors')) {
                    $commentForm.yiiActiveForm('updateMessages', data.errors, true);
                }
                else {
                    $commentForm.yiiActiveForm('updateAttribute', 'commentmodel-content', [data.message]);
                }
                $commentForm.find(':submit').prop('disabled', false).text(settings.submitBtnText);
            }
        }).fail(function (xhr, status, error) {
            alert(error);
        });

        return false;
    }

    /**
     * Reply to comment
     * @param event
     */
    function reply(event) {
        event.preventDefault();
        var $commentForm = event.data.commentForm;
        var settings = $commentForm.data('comment');
        var $this = $(this);
        var parentCommentSelector = $this.parents('[data-comment-content-id="' + $this.data('comment-id') + '"]');
        var Permlink = $this.parents('[data-comment-content-id="' + $this.data('permlink') + '"]');
        var Author = $this.parents('[data-comment-content-id="' + $this.data('author') + '"]');
        
        $commentForm.appendTo(parentCommentSelector);
    
        $commentForm.find('[data-comment="parent-id"]').val($this.data('comment-id'));
        $commentForm.find('[data-comment="author"]').val($this.data('author'));
        $commentForm.find('[data-comment="permlink"]').val($this.data('permlink'));
       
        $commentForm.find(settings.cancelReplyBtnSelector).show();
    }

    /**
     * Cancel reply
     * @param event
     */
    function cancelReply(event) {
        event.preventDefault();
        var $commentForm = event.data.commentForm;
        var settings = $commentForm.data('comment');
        $commentForm.find(settings.cancelReplyBtnSelector).hide();
        var formContainer = $(settings.pjaxContainerId).find(settings.formContainerSelector);
        $commentForm.prependTo(formContainer);
        var main_author = $('#main_author').val();
        var main_permlink = $('#relatedTo').val();
        $commentForm.find('[data-comment="parent-id"]').val(null);
         $commentForm.find('[data-comment="permlink"]').val(main_permlink);
          $commentForm.find('[data-comment="author"]').val(main_author);
    }

    /**
     * Delete the comment
     * @param event
     */
    function deleteComment(event) {
        event.preventDefault();
        var $commentForm = event.data.commentForm;
        var settings = $commentForm.data('comment');
        var $this = $(this);
        $.ajax({
            url: $this.data('url'),
            type: 'DELETE',
            error: function (xhr, status, error) {
                alert(error);
            },
            success: function (result, status, xhr) {
                $this.parents('[data-comment-content-id="' + $this.data('comment-id') + '"]').find(settings.contentSelector).text(result);
                $this.parents(settings.toolsSelector).remove();
            }
        });
    }

})(window.jQuery);