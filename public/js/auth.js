document.addEventListener('DOMContentLoaded', function () {
    // Password visibility toggle
    window.togglePassword = function (inputId) {
        const input = document.getElementById(inputId);
        const eyeIcon = document.getElementById(inputId + '-eye');

        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    };

    // Form validation
    const forms = document.querySelectorAll('.auth-form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const submitButton = form.querySelector('.btn-primary');

            // Add loading state
            submitButton.classList.add('loading');
            submitButton.disabled = true;

            // Remove loading state after 5 seconds (fallback)
            setTimeout(() => {
                submitButton.classList.remove('loading');
                submitButton.disabled = false;
            }, 5000);
        });
    });

    // Real-time password strength indicator (for registration)
    const passwordInput = document.getElementById('password');
    if (passwordInput && window.location.pathname.includes('register')) {
        passwordInput.addEventListener('input', function () {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            updatePasswordStrengthIndicator(strength);
        });
    }

    // Password confirmation validation
    const passwordConfirmInput = document.getElementById('password_confirmation');
    if (passwordConfirmInput) {
        passwordConfirmInput.addEventListener('input', function () {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;

            if (confirmPassword && password !== confirmPassword) {
                this.classList.add('error');
                showPasswordMismatchError();
            } else {
                this.classList.remove('error');
                hidePasswordMismatchError();
            }
        });
    }

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 10) {
                value = value.substring(0, 10);
                value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            }
            e.target.value = value;
        });
    }

    // Auto-hide flash messages
    const flashMessages = document.querySelectorAll('.alert');
    flashMessages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 5000);
    });
});

function calculatePasswordStrength(password) {
    let strength = 0;

    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    return strength;
}

function updatePasswordStrengthIndicator(strength) {
    // Remove existing indicator
    const existingIndicator = document.querySelector('.password-strength');
    if (existingIndicator) {
        existingIndicator.remove();
    }

    // Create new indicator
    const passwordGroup = document.getElementById('password').closest('.form-group');
    const indicator = document.createElement('div');
    indicator.className = 'password-strength';

    const strengthLevels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const strengthColors = ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#28a745'];

    if (strength > 0) {
        indicator.innerHTML = `
            <div class="strength-bar">
                <div class="strength-fill" style="width: ${(strength / 5) * 100}%; background-color: ${strengthColors[strength - 1]}"></div>
            </div>
            <span class="strength-text" style="color: ${strengthColors[strength - 1]}">${strengthLevels[strength - 1]}</span>
        `;

        // Add CSS for strength indicator
        if (!document.querySelector('#password-strength-styles')) {
            const styles = document.createElement('style');
            styles.id = 'password-strength-styles';
            styles.textContent = `
                .password-strength {
                    margin-top: 0.5rem;
                }
                .strength-bar {
                    height: 4px;
                    background-color: #e0e0e0;
                    border-radius: 2px;
                    overflow: hidden;
                    margin-bottom: 0.25rem;
                }
                .strength-fill {
                    height: 100%;
                    transition: all 0.3s ease;
                }
                .strength-text {
                    font-size: 0.75rem;
                    font-weight: 600;
                }
            `;
            document.head.appendChild(styles);
        }

        passwordGroup.appendChild(indicator);
    }
}

function showPasswordMismatchError() {
    const confirmGroup = document.getElementById('password_confirmation').closest('.form-group');
    let errorMsg = confirmGroup.querySelector('.password-mismatch-error');

    if (!errorMsg) {
        errorMsg = document.createElement('span');
        errorMsg.className = 'error-message password-mismatch-error';
        errorMsg.textContent = 'Passwords do not match';
        confirmGroup.appendChild(errorMsg);
    }
}

function hidePasswordMismatchError() {
    const errorMsg = document.querySelector('.password-mismatch-error');
    if (errorMsg) {
        errorMsg.remove();
    }
}

// Social login handlers (placeholder)
document.addEventListener('click', function (e) {
    if (e.target.closest('.btn-google')) {
        e.preventDefault();
        console.log('Google login clicked');
        // Implement Google OAuth here
    }

    if (e.target.closest('.btn-facebook')) {
        e.preventDefault();
        console.log('Facebook login clicked');
        // Implement Facebook OAuth here
    }
});