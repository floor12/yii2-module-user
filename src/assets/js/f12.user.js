f12user = {

    sendPasswordLink: function (user_id) {
        if (confirm(sendPasswordLinkConfirmText))
            $.ajax({
                url: '/user/admin/password-send',
                method: 'POST',
                data: {id: user_id},
                error: function (response) {
                    processError(response)
                },
                success: function (response) {
                    info(response, 1);
                    $.pjax.reload({container: '#items'})
                }
            })
    }
}
