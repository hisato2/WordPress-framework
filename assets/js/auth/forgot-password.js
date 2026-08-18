document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById(
        'forgot-password-form'
    );

    if (!form) {
        return;
    }

    form.addEventListener('submit', async (event) => {

        event.preventDefault();

        const submitButton = form.querySelector(
            'button[type="submit"]'
        );

        if (submitButton) {
            submitButton.disabled = true;
        }

        const formData = new FormData(form);

        try {

            const response = await fetch(
                `${HKS_CONFIG.apiUrl}/auth/forgot-password.php`,
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

            if (
                result.success === true &&
                result.redirect
            ) {
                window.location.href = result.redirect;
            }

        } catch (error) {

            console.error(
                'Forgot password error:',
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