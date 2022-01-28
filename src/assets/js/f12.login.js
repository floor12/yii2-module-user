f12user = {
    checkPasswordOption() {
        if ($('#loginform-use_password').is(':checked')) {
            $('#login-password-block').show();
        } else {
            $('#login-password-block').hide();
        }
    }
}


f12user.checkPasswordOption();

$(document).on('change', '#loginform-use_password', () => {
    f12user.checkPasswordOption();
});