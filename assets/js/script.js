/**
 * Community Portal for Village Democracy
 * Client-Side JavaScript (script.js)
 * Clean, Simple Vanilla JavaScript for Form Validation & Popups
 */

// Function to confirm deletion of records (Announcements, Schemes, etc.)
function confirmDelete(itemType) {
    var message = "Are you sure you want to delete this " + (itemType ? itemType : "record") + "?\nThis action cannot be undone.";
    return confirm(message);
}

// Function to validate Citizen Registration Form
function validateRegistrationForm() {
    var fullName = document.getElementById("full_name");
    var email = document.getElementById("email");
    var phone = document.getElementById("phone");
    var village = document.getElementById("village_name");
    var password = document.getElementById("password");
    var confirmPassword = document.getElementById("confirm_password");

    // Check empty fields
    if (fullName && fullName.value.trim() === "") {
        alert("Please enter your full name.");
        fullName.focus();
        return false;
    }

    if (email && email.value.trim() === "") {
        alert("Please enter your email address.");
        email.focus();
        return false;
    }

    // Phone number validation (10 digits)
    if (phone) {
        var phoneVal = phone.value.trim();
        var phonePattern = /^[0-9]{10}$/;
        if (!phonePattern.test(phoneVal)) {
            alert("Please enter a valid 10-digit mobile phone number.");
            phone.focus();
            return false;
        }
    }

    if (village && village.value.trim() === "") {
        alert("Please enter your village name.");
        village.focus();
        return false;
    }

    // Password validation
    if (password) {
        if (password.value.length < 6) {
            alert("Password must be at least 6 characters long.");
            password.focus();
            return false;
        }

        if (confirmPassword && password.value !== confirmPassword.value) {
            alert("Password and Confirm Password do not match. Please re-enter.");
            confirmPassword.focus();
            return false;
        }
    }

    return true;
}

// Function to validate Grievance Submission Form
function validateGrievanceForm() {
    var title = document.getElementById("title");
    var category = document.getElementById("category");
    var description = document.getElementById("description");

    if (title && title.value.trim() === "") {
        alert("Please provide a title for your grievance.");
        title.focus();
        return false;
    }

    if (category && category.value === "") {
        alert("Please select a grievance category.");
        category.focus();
        return false;
    }

    if (description && description.value.trim().length < 15) {
        alert("Please provide a meaningful grievance description (at least 15 characters).");
        description.focus();
        return false;
    }

    return true;
}

// Function to validate Feedback Form
function validateFeedbackForm() {
    var subject = document.getElementById("subject");
    var message = document.getElementById("message");

    if (subject && subject.value.trim() === "") {
        alert("Please enter a subject for your feedback.");
        subject.focus();
        return false;
    }

    if (message && message.value.trim().length < 10) {
        alert("Please enter your feedback message (at least 10 characters).");
        message.focus();
        return false;
    }

    return true;
}

