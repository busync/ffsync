document.addEventListener('DOMContentLoaded', function () {
    const registerBtn = document.querySelector('.main--body--button--first');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const repeatPasswordInput = document.getElementById('repeat-password');

    registerBtn.addEventListener('click', async function () {
        const data = {
            username: usernameInput.value,
            password: passwordInput.value,
            repeat_password: repeatPasswordInput.value
        };

        if (data.username.length < 4) {
            alert('Юзернейм должен быть больше 4 символов');
            return;
        }

        if (data.password.length < 6) {
            alert('Пароль должен быть больше 6 символов');
            return;
        }

        if (data.password !== data.repeat_password) {
            alert('Пароли не совпадают');
            return;
        }

        try {
            const response = await fetch('../core/handlers/registration/index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF': window.CSRF_TOKEN
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = '../dashboard/';
            } else {
                if (result.errors) {
                    let errorMsg = 'Validation errors:\n';
                    for (const [field, errors] of Object.entries(result.errors)) {
                        errorMsg += `${field}: ${Object.values(errors).join(', ')}\n`;
                    }
                    alert(errorMsg);
                } else {
                    alert(result.error || 'Произошла ошибка!');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ошибка сети');
        }
    });
});