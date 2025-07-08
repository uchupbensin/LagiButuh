class Validator {
    static validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    static validatePassword(password) {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
        const re = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
        return re.test(password);
    }

    static validatePhone(phone) {
        // Simple phone validation - adjust based on your needs
        const re = /^[0-9]{10,15}$/;
        return re.test(phone);
    }

    static validateNotEmpty(value) {
        return value.trim() !== '';
    }

    static validateForm(formData, rules) {
        const errors = {};
        
        for (const field in rules) {
            const value = formData[field] || '';
            const fieldRules = rules[field];
            
            for (const rule of fieldRules) {
                if (rule === 'required' && !this.validateNotEmpty(value)) {
                    errors[field] = 'This field is required';
                    break;
                }
                
                if (rule === 'email' && !this.validateEmail(value)) {
                    errors[field] = 'Please enter a valid email address';
                    break;
                }
                
                if (rule === 'password' && !this.validatePassword(value)) {
                    errors[field] = 'Password must be at least 8 characters with uppercase, lowercase and number';
                    break;
                }
                
                if (rule === 'phone' && !this.validatePhone(value)) {
                    errors[field] = 'Please enter a valid phone number';
                    break;
                }
                
                // Add more custom rules as needed
            }
        }
        
        return {
            isValid: Object.keys(errors).length === 0,
            errors
        };
    }
}

// Form Validation Example
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            const formData = {};
            const inputs = form.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                formData[input.name] = input.value;
            });
            
            // Define validation rules
            const rules = {
                name: ['required'],
                email: ['required', 'email'],
                password: ['required', 'password'],
                phone: ['phone']
                // Add more fields as needed
            };
            
            const validation = Validator.validateForm(formData, rules);
            
            if (!validation.isValid) {
                event.preventDefault();
                event.stopPropagation();
                
                // Display errors
                for (const field in validation.errors) {
                    const input = form.querySelector(`[name="${field}"]`);
                    const errorElement = document.createElement('div');
                    errorElement.className = 'invalid-feedback';
                    errorElement.textContent = validation.errors[field];
                    
                    // Remove existing feedback
                    const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                    if (existingFeedback) {
                        existingFeedback.remove();
                    }
                    
                    input.classList.add('is-invalid');
                    input.parentNode.appendChild(errorElement);
                }
            }
            
            form.classList.add('was-validated');
        });
    });
    
    // Real-time validation
    document.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
                const feedback = this.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.remove();
                }
            }
        });
    });
});

export default Validator;