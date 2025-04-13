<!DOCTYPE html>
<html lang="en">
<head>
<header>
        <div class="container">
            <div class="logo">
                <h1>BMC Canteen</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../users/index.php" class="active">Home</a></li>
                    <li><a href="../cms/auth/login.php">Menu</a></li>
                    <li><a href="../../users/aboutus.php">About</a></li>
                    <li><a href="../../users/contactus.php">Contact</a></li>
                </ul>
            </nav>
            <div class="header-icons">
                <a href="../cms/auth/login.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count">0</span>
                </a>
                <a href="../cms/auth/login.php" class="user-icon">
                    <i class="fas fa-user"></i>
                </a>
            </div>
            
        </div>
    </header>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Canteen Management System</title>
    <style>
        /* General Styles */
        :root {
    --primary-color: #ff6b35;
    --primary-dark: #e85a2a;
    --secondary-color: #2b2d42;
    --accent-color: #4ecdc4;
    --light-color: #f8f9fa;
    --dark-color: #212529;
    --gray-color: #6c757d;
    --light-gray: #e9ecef;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --border-radius: 0.25rem;
    --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    --transition: all 0.3s ease;
    --container-width: 1200px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: var(--dark-color);
    background-color: #f5f5f5;
}

a {
    text-decoration: none;
    color: var(--primary-color);
    transition: var(--transition);
}

a:hover {
    color: var(--primary-dark);
}

ul {
    list-style: none;
}

img {
    max-width: 100%;
}

.container {
    width: 100%;
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 1rem;
}

.section-title {
    text-align: center;
    margin-bottom: 2rem;
}

.section-title h2 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: var(--secondary-color);
}

.section-title p {
    color: var(--gray-color);
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    text-align: center;
    cursor: pointer;
    transition: var(--transition);
    border: none;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    color: white;
}

.btn-outline {
    background-color: transparent;
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
}

.btn-outline:hover {
    background-color: var(--primary-color);
    color: white;
}

.btn-block {
    display: block;
    width: 100%;
}

/* Header */
header {
    background-color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 100;
}

header .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
}

.logo h1 {
    font-size: 1.5rem;
    color: var(--primary-color);
}

nav ul {
    display: flex;
}

nav ul li {
    margin-left: 1.5rem;
}

nav ul li a {
    color: var(--secondary-color);
    font-weight: 500;
    padding: 0.5rem;
}

nav ul li a:hover, nav ul li a.active {
    color: var(--primary-color);
}

.header-icons {
    display: flex;
    align-items: center;
}

.cart-icon, .user-icon {
    position: relative;
    margin-left: 1.5rem;
    font-size: 1.2rem;
    color: var(--secondary-color);
}

.cart-icon:hover, .user-icon:hover, .cart-icon.active, .user-icon.active {
    color: var(--primary-color);
}

#cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: var(--primary-color);
    color: white;
    font-size: 0.7rem;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: #333;
    margin: 0;
    padding: 0;
    background-color: #f9f9f9;
}

.contact-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 20px;
}

h1 {
    color: #2c3e50;
    text-align: center;
    margin-bottom: 1rem;
}

.contact-intro {
    text-align: center;
    margin-bottom: 2rem;
    color: #555;
    font-size: 1.1rem;
}

/* Contact Methods */
.contact-methods {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    gap: 20px;
    margin-bottom: 3rem;
}

.contact-card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    width: 300px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.contact-card:hover {
    transform: translateY(-5px);
}

.contact-card i {
    font-size: 2.5rem;
    color: #e74c3c;
    margin-bottom: 1rem;
}

.contact-card h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.contact-card p {
    color: #555;
    margin: 0.5rem 0;
}

.contact-card a {
    color: #3498db;
    text-decoration: none;
    transition: color 0.3s;
}

.contact-card a:hover {
    color: #2980b9;
    text-decoration: underline;
}

/* Contact Form */
.contact-form {
    background: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    margin: 0 auto;
}

.contact-form h2 {
    color: #2c3e50;
    text-align: center;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #555;
    font-weight: 500;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group textarea {
    resize: vertical;
}

.submit-btn {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 12px 25px;
    font-size: 1rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
    display: block;
    margin: 0 auto;
}

.submit-btn:hover {
    background-color: #c0392b;
}

/* Responsive Design */
@media (max-width: 768px) {
    .contact-methods {
        flex-direction: column;
        align-items: center;
    }
    
    .contact-card {
        width: 100%;
        margin-bottom: 20px;
    }
}
</style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
</head>
<body>
    

    <main class="contact-container">
        <h1>Contact Our Canteen</h1>
        <p class="contact-intro">Have questions, feedback, or special requests? We'd love to hear from you!</p>
        
        <div class="contact-methods">
            <div class="contact-card">
                <i class="fas fa-phone-alt"></i>
                <h3>Phone</h3>
                <p><a href="tel:+1234567890">9806643728</a></p>
                <p>Sunday-Friday: 8:00 AM - 5:00 PM</p>
            </div>
            
            <div class="contact-card">
                <i class="fas fa-envelope"></i>
                <h3>Email</h3>
                <p><a href="mailto:BMC@canteenmanagement.com">BMC@canteenmanagement.com</a></p>
                <p>Response time: Within 24 hours</p>
            </div>
            
            <div class="contact-card">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Location</h3>
                <p>9biga road</p>
                <p>Bharatpur_10,Chitwan</p>
            </div>
        </div>
        
</body>
</html>