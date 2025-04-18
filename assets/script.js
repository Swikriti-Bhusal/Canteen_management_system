
function validateUsername() {
    const username = document.getElementById('username').value;
    const usernameError = document.getElementById('usernameError');
    if (!/^[A-Za-z]+$/.test(username)) {
        usernameError.textContent = "Username should contain only alphabets.";
        return false;
    } else {
        usernameError.textContent = "";
        return true;
    }
}

function validateEmail() {
    const email = document.getElementById('email').value;
    const emailError = document.getElementById('emailError');
    if (!/^[^\s@]+@gmail\.com$/.test(email)) {
        emailError.textContent = "Email must be a valid @gmail.com address and should contain only aplbhabet.";
        return false;
    } else {
        emailError.textContent = "";
        return true;
    }
}

function validatePhone() {
    const phone_no = document.getElementById('phone_no').value;
    const phoneError = document.getElementById('phoneError');
    if (!/^\d{10}$/.test(phone_no) || /(\d)\1{2,}/.test(phone_no)) {
        phoneError.textContent = "Phone number must be 10 digits and should not contain repeating sequences like 111, 222, etc.";
        return false;
    } else {
        phoneError.textContent = "";
        return true;
    }
}

function validateAddress() {
    const address = document.getElementById('address').value.trim();
    const addressError = document.getElementById('addressError');
    
    if (!/(?=.*[a-zA-Z])(?=.*[0-9])/.test(address)) {
        addressError.textContent = "Address must contain at least one alphabet and one number.";
        return false;
    } else {
        addressError.textContent = "";
        return true;
    }
}

   
    
function validatePassword() {
    const password = document.getElementById('password').value;
    const passwordError = document.getElementById('passwordError');
    if (!/(?=.*[A-Za-z])(?=.*\d)/.test(password)) {
        passwordError.textContent = "Password must contain at least one alphabet and one number.";
        return false;
    } else {
        passwordError.textContent = "";
        return true;
    }
}

// Attach event listeners to input fields
document.getElementById('username').addEventListener('input', validateUsername);
document.getElementById('email').addEventListener('input', validateEmail);
document.getElementById('phone_no').addEventListener('input', validatePhone);
document.getElementById('address').addEventListener('input', validateAddress);
document.getElementById('password').addEventListener('input', validatePassword);

// Final form validation before submission
function validateForm() {
    return validateUsername() && validateEmail() && validatePhone() && validateAddress() && validatePassword();
}

    function updateQuantity(button, change) {
        const input = button.parentElement.querySelector('.quantity-input');
        let newValue = parseInt(input.value) + change;
        if (newValue < 1) newValue = 1;
        if (newValue > 10) newValue = 10;
        input.value = newValue;
    }
    
    /*menu*/
    
        // Validation functions
        function validateName(name) {
            const regex = /^[a-zA-Z\s]+$/;
            return regex.test(name);
        }
        
        function validateDescription(desc) {
            const regex = /^[a-zA-Z\s.,!?'-]+$/;
            return regex.test(desc);
        }
        
        function validatePrice(price) {
            const regex = /^[1-9]\d{0,3}(\.\d{1,2})?$/;
            return regex.test(price) && parseFloat(price) > 0 && price.length <= 4;
        }
        
        // Form validation
        document.getElementById('foodForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate name
            const name = document.getElementById('itemName').value.trim();
            const nameError = document.getElementById('nameError');
            if (!validateName(name)) {
                nameError.classList.add('show');
                isValid = false;
            } else {
                nameError.classList.remove('show');
            }
            
            // Validate description
            const desc = document.getElementById('itemDesc').value.trim();
            const descError = document.getElementById('descError');
            if (!validateDescription(desc)) {
                descError.classList.add('show');
                isValid = false;
            } else {
                descError.classList.remove('show');
            }
            
            // Validate price
            const price = document.getElementById('itemPrice').value;
            const priceError = document.getElementById('priceError');
            if (!validatePrice(price)) {
                priceError.classList.add('show');
                isValid = false;
            } else {
                priceError.classList.remove('show');
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        document.getElementById('itemName').addEventListener('input', function() {
            const name = this.value.trim();
            const nameError = document.getElementById('nameError');
            if (!validateName(name)) {
                nameError.classList.add('show');
            } else {
                nameError.classList.remove('show');
            }
        });
        
        document.getElementById('itemDesc').addEventListener('input', function() {
            const desc = this.value.trim();
            const descError = document.getElementById('descError');
            if (!validateDescription(desc)) {
                descError.classList.add('show');
            } else {
                descError.classList.remove('show');
            }
        });
        
        document.getElementById('itemPrice').addEventListener('input', function() {
            const price = this.value;
            const priceError = document.getElementById('priceError');
            if (!validatePrice(price)) {
                priceError.classList.add('show');
            } else {
                priceError.classList.remove('show');
            }
        });
        
        // Image preview functionality
        document.getElementById('itemImage').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        function editItem(id, name, desc, price, image, category) {
            document.getElementById('itemId').value = id;
            document.getElementById('itemName').value = name;
            document.getElementById('itemDesc').value = desc;
            document.getElementById('itemPrice').value = price;
            document.getElementById('oldImage').value = image;
            document.getElementById('itemCategory').value = category;
            
            // Show current image
            const currentImageContainer = document.getElementById('currentImageContainer');
            currentImageContainer.innerHTML = `<p>Current Image:</p><img src="${image}" class="current-image">`;
            
            document.getElementById('addBtn').style.display = 'none';
            document.getElementById('updateBtn').style.display = 'inline-block';
            document.getElementById('cancelBtn').style.display = 'inline-block';
            
            // Clear any error messages when editing
            document.getElementById('nameError').classList.remove('show');
            document.getElementById('descError').classList.remove('show');
            document.getElementById('priceError').classList.remove('show');
        }
        
        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('itemId').value = '';
            document.getElementById('itemName').value = '';
            document.getElementById('itemDesc').value = '';
            document.getElementById('itemPrice').value = '';
            document.getElementById('itemImage').value = '';
            document.getElementById('oldImage').value = '';
            document.getElementById('itemCategory').value = 'Main Course';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('currentImageContainer').innerHTML = '';
            
            document.getElementById('addBtn').style.display = 'inline-block';
            document.getElementById('updateBtn').style.display = 'none';
            document.getElementById('cancelBtn').style.display = 'none';
            
            // Clear error messages when canceling
            document.getElementById('nameError').classList.remove('show');
            document.getElementById('descError').classList.remove('show');
            document.getElementById('priceError').classList.remove('show');
        });
    