const nameField = document.getElementById("name");
const anonCheck = document.getElementById("anonymous");
const messageField = document.getElementById("message");
const msgCount = document.getElementById("msgCount");

// Initialize character count
msgCount.textContent = messageField.value.length;

// Toggle name & anonymous
anonCheck.addEventListener("change", function() {
    if (this.checked) {
        nameField.value = "";
        nameField.classList.add("disabled-field");
        nameField.disabled = true;
    } else {
        nameField.classList.remove("disabled-field");
        nameField.disabled = false;
        nameField.focus();
    }
});

nameField.addEventListener("input", function() {
    if (this.value.length > 0) {
        anonCheck.checked = false;
    }
});

// Character counter with visual feedback
messageField.addEventListener("input", function() {
    const length = this.value.length;
    msgCount.textContent = length;
    
    // Remove existing classes
    msgCount.parentElement.classList.remove("warning", "danger");
    
    if (length >= 90) {
        msgCount.parentElement.classList.add("danger");
    } else if (length >= 75) {
        msgCount.parentElement.classList.add("warning");
    }
    
    if (length >= 100) {
        this.value = this.value.substring(0, 100);
        msgCount.textContent = 100;
    }
});

// Add form validation feedback
document.getElementById("postForm").addEventListener("submit", function(e) {
    const name = nameField.value.trim();
    const anonymous = anonCheck.checked;
    const message = messageField.value.trim();

    if (!anonymous && name.length === 0) {
        e.preventDefault();
        nameField.focus();
        nameField.style.borderColor = "#dc3545";
        setTimeout(() => {
            nameField.style.borderColor = "";
        }, 2000);
    }

    if (message.length === 0) {
        e.preventDefault();
        messageField.focus();
        messageField.style.borderColor = "#dc3545";
        setTimeout(() => {
            messageField.style.borderColor = "";
        }, 2000);
    }
});

// Initialize state based on form values
if (anonCheck.checked) {
    nameField.classList.add("disabled-field");
    nameField.disabled = true;
}