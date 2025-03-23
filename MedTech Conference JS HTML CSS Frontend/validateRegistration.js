// element selectors for validation
const elements = {
    dob: { input: document.getElementById("dob"), error: document.getElementById("dobError") },
    email: { input: document.getElementById("email"), error: document.getElementById("email-error") },
    reenterPassword: { input: document.getElementById("confirm-password"), error: document.getElementById("confirm-password-error") },
    firstname: { input: document.getElementById("first-name"), error: document.getElementById("first-name-error") },
    lastname: { input: document.getElementById("last-name"), error: document.getElementById("last-name-error") },
    password: { input: document.getElementById("password"), error: document.getElementById("password-error") }
};

// regular expression patterns to adhere to
const patterns = {
    email: /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/,
    name: /^[a-zA-Z\s'-]+$/,
    dob: /^(\d{2})\/(\d{2})\/(\d{4})$/
};

// standardising the validation function format, using regex
const validateField = (input, error, pattern, message) => {
    if (!pattern.test(input.value)) {
        error.textContent = message;
        error.style.color = "red";
        return false;
    }
    error.textContent = "No errors";
    error.style.color = "green";
    return true;
};

// standardising the validation function format based on boolean passed in
const validateFieldBoolean = (boolean, error, message) => {
    if (!boolean) {
        error.textContent = message;
        error.style.color = "red";
        return false;
    }
    error.textContent = "No errors";
    error.style.color = "green";
    return true;
};

// validating email using regex and standardised validation function
const validateEmail = () => validateField(elements.email.input, elements.email.error, patterns.email, "Invalid email format");

// validating password: 8 characters, and include uppercase, lowercase, and numerical characters 
const validatePassword = () => {
    const { input, error } = elements.password;
    const value = input.value;
    const isValid = value.length >= 8 && /[A-Z]/.test(value) && /[a-z]/.test(value) && /\d/.test(value);
    validateFieldBoolean(isValid, error, "Password must contain at least 8 characters, an uppercase letter, a lowercase letter, and a number");
    return isValid;
};

// validating password matches
const passwordsMatch = () => {
    const { input: passwordInput } = elements.password;
    const { input: reenterInput, error } = elements.reenterPassword;
    const isValid = passwordInput.value === reenterInput.value && reenterInput.value !== "";
    validateFieldBoolean(isValid, error, "Passwords must match");
    return isValid;
};

// Validation for the first name field
const validateFirstname = () => {
    return validateField(
        elements.firstname.input,
        elements.firstname.error,
        patterns.name,
        "First name should contain only letters, spaces, hyphens, or apostrophes"
    );
};

// Validation for the last name field
const validateLastname = () => {
    return validateField(
        elements.lastname.input,
        elements.lastname.error,
        patterns.name,
        "Last name should contain only letters, spaces, hyphens, or apostrophes"
    );
};

// Validation for realistic DOB, max 124 years old
const validateDOB = () => {
    const { input, error } = elements.dob;
    const [_, day, month, year] = input.value.match(patterns.dob) || [];
    const isValidDate = (d, m, y) => {
        const maxDay = new Date(y, m, 0).getDate();
        return d > 0 && d <= maxDay && m > 0 && m <= 12 && y >= 1900 && y <= new Date().getFullYear();
    };
    const isValid = day && month && year && isValidDate(+day, +month, +year);
    validateFieldBoolean(isValid, error, "Invalid DOB");
    return isValid;
};

// Form validation for all parameters
const validateForm = () => {
    const validations = [
        validateEmail(),
        validatePassword(),
        passwordsMatch(),
        validateFirstname(),
        validateLastname(),
        validateDOB()
    ];
    const allValid = validations.every(Boolean);
    if (!allValid) alert("Please make sure to fill in all fields correctly!");
    return allValid;
};