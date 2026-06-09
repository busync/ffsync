document.addEventListener('DOMContentLoaded', function () {
    const loginBtn = document.querySelector('.main--body--button--first');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');

    loginBtn.addEventListener('click', async function () {
        const data = {
            username: usernameInput.value,
            password: passwordInput.value
        };

        if (data.username.length < 4) {
            alert('Username must be at least 4 characters');
            return;
        }

        if (data.password.length < 6) {
            alert('Password must be at least 6 characters');
            return;
        }

        try {
            const response = await fetch('../core/handlers/login/index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF': window.CSRF_TOKEN
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                alert('Login successful!');
                window.location.href = '../dashboard/';
            } else {
                if (result.errors) {
                    let errorMsg = 'Validation errors:\n';
                    for (const [field, errors] of Object.entries(result.errors)) {
                        errorMsg += `${field}: ${Object.values(errors).join(', ')}\n`;
                    }
                    alert(errorMsg);
                } else {
                    alert(result.error || 'Login failed');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Network error. Please try again.');
        }
    });

    passwordInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            loginBtn.click();
        }
    });

    usernameInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            loginBtn.click();
        }
    });
});