'use strict';


/**
 * Profile
 *
 * プロフィール画面のUI制御
 */
document.addEventListener('DOMContentLoaded', function () {

    const passwordToggle = document.getElementById(
        'passwordChangeToggle'
    );

    const passwordCancel = document.getElementById(
        'passwordChangeCancel'
    );

    const passwordForm = document.getElementById(
        'passwordChangeForm'
    );


    /*
    |--------------------------------------------------------------------------
    | Password Form
    |--------------------------------------------------------------------------
    */


    if (!passwordToggle || !passwordForm) {
        return;
    }


    /**
     * パスワード変更フォームを開く
     */
    function openPasswordForm() {
        passwordForm.hidden = false;

        passwordToggle.setAttribute(
            'aria-expanded',
            'true'
        );


        const currentPassword = document.getElementById(
            'current_password'
        );


        if (currentPassword) {
            currentPassword.focus();
        }
    }


    /**
     * パスワード変更フォームを閉じる
     */
    function closePasswordForm() {
        passwordForm.hidden = true;

        passwordToggle.setAttribute(
            'aria-expanded',
            'false'
        );

        passwordForm.reset();
    }


    /*
    |--------------------------------------------------------------------------
    | Toggle
    |--------------------------------------------------------------------------
    */


    passwordToggle.addEventListener(
        'click',
        function () {

            if (passwordForm.hidden) {
                openPasswordForm();
                return;
            }


            closePasswordForm();
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Cancel
    |--------------------------------------------------------------------------
    */


    if (passwordCancel) {
        passwordCancel.addEventListener(
            'click',
            function () {
                closePasswordForm();
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Validation Error
    |--------------------------------------------------------------------------
    |
    | パスワード変更に失敗して
    |
    | /profile/?password=change
    |
    | へ戻された場合はフォームを再表示する。
    |
    */


    const params = new URLSearchParams(
        window.location.search
    );


    if (params.get('password') === 'change') {
        openPasswordForm();
    }

});