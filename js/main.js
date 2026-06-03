// js/main.js
// Additional JavaScript functionality

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if(target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// Lightweight client-side validation helper
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) {
        return true;
    }

    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        }
    });

    return isValid;
}

document.querySelectorAll('[data-confirm]').forEach(element => {
    element.addEventListener('click', function (event) {
        const message = this.getAttribute('data-confirm') || 'Are you sure?';
        if (!window.confirm(message)) {
            event.preventDefault();
        }
    });
});

const currentYear = document.getElementById('site-year');
if (currentYear) {
    currentYear.textContent = new Date().getFullYear();
}

// Print / PDF export
document.querySelectorAll('.js-print').forEach(btn => {
    btn.addEventListener('click', () => window.print());
});