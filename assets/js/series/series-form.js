document.addEventListener('DOMContentLoaded', function () {

    const subscriptionEnabled =
        document.querySelector(
            'input[name="subscription_enabled"]'
        );

    const subscriptionFields =
        document.querySelectorAll(
            '.hks-subscription-field'
        );


    if (
        !subscriptionEnabled
        || subscriptionFields.length === 0
    ) {
        return;
    }

    function updateSubscriptionFields() {

        const isEnabled =
            subscriptionEnabled.checked;

        subscriptionFields.forEach(
            function (field) {

                field.style.display =
                    isEnabled ? '' : 'none';
            }
        );
    }



    subscriptionEnabled.addEventListener(
        'change',
        updateSubscriptionFields
    );
    

    updateSubscriptionFields();




});