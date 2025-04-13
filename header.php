<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canteen Management System</title>

    <style>
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

.menu-toggle {
    display: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--secondary-color);
}

/* Hero Section */
.hero {
    padding: 4rem 0;
    background-color: var(--light-color);
}

.hero .container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    align-items: center;
}

.hero-content h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: var(--secondary-color);
}

.hero-content p {
    margin-bottom: 2rem;
    color: var(--gray-color);
    font-size: 1.1rem;
}

.hero-buttons {
    display: flex;
    gap: 1rem;
}

.hero-image {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--box-shadow);
}
</style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>BMC CMS</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../users/menu.php" class="active">Home</a></li>
                    <li><a href="../users/menu.php">Menu</a></li>
                    <li><a href="../users/aboutus.php">About</a></li>
                    <li><a href="contactus.php">Contact</a></li>
        </div>  
                </ul>
            </nav>
            
            <div class="header-icons">
                <a href="../users/cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count">0</span>
                </a>
                <a href="../auth/login.php" class="user-icon">
                    <i class="fas fa-user"></i>
                </a>
            </div>
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>
 