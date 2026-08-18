document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById(
        'reset-password-form'
    );

    if (!form) {
        return;
    }

    form.addEventListener('submit', async (event) => {

        event.preventDefault();

        const submitButton = form.querySelector(
            'button[type="submit"]'
        );

        const passwordInput = form.querySelector(
            '[name="password"]'
        );

        const passwordConfirmationInput = form.querySelector(
            '[name="password_confirmation"]'
        );

        /*
        |--------------------------------------------------------------------------
        | パスワード一致確認
        |--------------------------------------------------------------------------
        */

        if (
            passwordInput &&
            passwordConfirmationInput &&
            passwordInput.value !==
                passwordConfirmationInput.value
        ) {
            alert(
                'パスワードと確認用パスワードが一致していません。'
            );

            passwordConfirmationInput.focus();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 二重送信防止
        |--------------------------------------------------------------------------
        */

        if (submitButton) {
            submitButton.disabled = true;
        }

        const formData = new FormData(form);

        try {

            const response = await fetch(
                `${HKS_CONFIG.apiUrl}/auth/reset-password.php`,
                {
                    method: 'POST',
                    body: formData
                }
            );

            const result = await response.json();

            alert(
                result.message
                ?? '処理結果を取得できませんでした。'
            );

            /*
            |--------------------------------------------------------------------------
            | 成功時
            |--------------------------------------------------------------------------
            */

            if (
                result.success === true &&
                result.redirect
            ) {
                window.location.href = result.redirect;
            }

        } catch (error) {

            console.error(
                'Reset password error:',
                error
            );

            alert(
                '通信エラーが発生しました。'
            );

        } finally {

            if (submitButton) {
                submitButton.disabled = false;
            }
        }

    });

});