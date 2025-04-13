
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
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="../users/menu.php">Menu</a></li>
                    <li><a href="../users/aboutus.php">About</a></li>
                    <li><a href="contactus.php">Contact</a></li>
                                       
                                </ul>
            </nav>
            <div class="header-icons">
                <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count">0</span>
                </a>
                <a href="login.php" class="user-icon">
                    <i class="fas fa-user"></i>
                </a>
            </div>
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>
            
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Swisha Canteen</title>

    <style>
        
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f9f9f9;
    color: #333;
}

.container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
}


.about-us {
    padding: 50px 0;
    text-align: center;
}

.about-us h1 {
    font-size: 2.5rem;
    color: #e67e22;
    margin-bottom: 10px;
}

.about-us .tagline {
    font-size: 1.2rem;
    color: #777;
    margin-bottom: 3px;
    padding: 10px;
}

.about-us .description {
    font-size: 1rem;
    line-height: 1.5;
    color: #555;
    margin-bottom: 2px;
    text-align: justify;
    padding: 0;
}

/* Team Section */
.team-section {
    margin-top: 50px;
}

.team-section h2 {
    font-size: 2rem;
    color: #e67e22;
    margin-bottom: 20px;
}

.team-members {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    
}

.team-member {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 30%;
    margin-bottom: 20px;
    text-align: center;
}

.team-member img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin-bottom: 15px;
}

.team-member h3 {
    font-size: 1.2rem;
    color: #333;
    margin-bottom: 5px;
}

.team-member p {
    font-size: 0.9rem;
    color: #777;
}

/* Responsive Design */
@media (max-width: 768px) {
    .team-member {
        width: 45%;
    }
}

@media (max-width: 480px) {
    .team-member {
        width: 100%;
    }
}
/* Footer Styles */
       /* General Footer Styling */
       footer {
        background-color: #2c3e50;
        color: #ecf0f1;
        padding: 40px 0;
        font-family: 'Arial', sans-serif;
    }
    
    footer a {
        color: #ecf0f1;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    footer a:hover {
        color: #3498db;
    }
    
    /* Container Styling */
    footer .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    /* Footer Content Layout */
    footer .footer-content {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 20px;
    }
    
    footer .footer-section {
        flex: 1;
        min-width: 200px;
        margin-bottom: 20px;
    }
    
    /* About Section */
    footer .footer-section.about h3 {
        font-size: 24px;
        margin-bottom: 15px;
    }
    
    footer .footer-section.about p {
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 15px;
    }
    
    footer .contact-info p {
        font-size: 14px;
        margin: 5px 0;
        display: flex;
        align-items: center;
    }
    
    footer .contact-info i {
        margin-right: 10px;
        color: #3498db;
    }
    
    /* Quick Links Section */
    footer .footer-section.links h3 {
        font-size: 24px;
        margin-bottom: 15px;
    }
    
    footer .footer-section.links ul {
        list-style: none;
        padding: 0;
    }
    
    footer .footer-section.links ul li {
        margin-bottom: 10px;
    }
    
    footer .footer-section.links ul li a {
        font-size: 14px;
    }
    
    /* Opening Hours Section */
    footer .footer-section.hours h3 {
        font-size: 24px;
        margin-bottom: 15px;
    }
    
    footer .footer-section.hours ul {
        list-style: none;
        padding: 0;
    }
    
    footer .footer-section.hours ul li {
        font-size: 14px;
        margin-bottom: 10px;
    }
    
    
    /* Footer Bottom Styling */
    footer .footer-bottom {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #34495e;
        margin-top: 20px;
    }
    
    footer .footer-bottom p {
        font-size: 14px;
        margin-bottom: 10px;
    }
    
    footer .social-icons {
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    
    footer .social-icons a {
        font-size: 18px;
        color: #ecf0f1;
        transition: color 0.3s ease;
    }
    
    footer .social-icons a:hover {
        color: #3498db;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        footer .footer-content {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
    
        footer .footer-section {
            text-align: center;
        }
    
        footer .contact-info p {
            justify-content: center;
        }
    
        footer .footer-section.newsletter form {
            flex-direction: column;
        }
    
        footer .footer-section.newsletter input[type="email"] {
            width: 100%;
        }
    
        footer .footer-section.newsletter button {
            width: 100%;
        }
    }

</style>
</head>
<body>
    <section class="about-us">
        <div class="container">
            <div class="about-content">
                <h1>About Swisha Canteen</h1>
                <p class="tagline"><strong>Serving Deliciousness Since 2023</strong></p>
                <p class="description">
                   <strong> Welcome to Swisha Canteen, your go-to spot for delicious and affordable meals on campus. We are passionate about serving high-quality food that satisfies your cravings and fuels your day. 
                    Our mission is to make every meal a memorable experience by combining fresh ingredients,  and a touch of home.</strong>
                </p>
                <p class="description">
                   <strong> "At Swisha Canteen, we believe that great food brings people together. Whether you're grabbing a quick bite between classes or enjoying a leisurely meal with friends, we strive to create a welcoming atmosphere where everyone feels at home.
                     Join us on this culinary journey and discover why Swisha Canteen is the heart of good food and great vibes on campus."
</strong></p>
                <div class="team-section">
                    <h2>Meet Our Team</h2>
                    <div class="team-members">
                        <div class="team-member">
                            <img src="/canteen/assets/image/swi.jpg" alt="Team Member 1">
                            <h3>Miss swikriti</h3>
                            <p>Head Chef</p>
                        </div>
                        <div class="team-member">
                            <img src="/canteen/assets/image/team1.jpg" alt="Team Member 2">
                            <h3>Mr Zayn</h3>
                            <p>Manager</p>
                        </div>
                        <div class="team-member">
                            <img src="/canteen/assets/image/team3.jpg" alt="Team Member 3">
                            <h3>Miss Barsha</h3>
                            <p>Customer Support</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>



<footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section about">
                    <h3>Campus Canteen</h3>
                    <p>Your go-to place for delicious meals on campus. Order online and skip the wait!</p>
                    <div class="contact-info">
                        <p><i class="fas fa-map-marker-alt"></i> Bharapur-10,9biga</p>
                        <p><i class="fas fa-phone"></i> +977-1-XXXXXXX</p>
                        <p><i class="fas fa-envelope"></i> BMC @campuscanteen.com</p> 
                    </div>
                </div>
                <div class="footer-section links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="menu.php">Menu</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="orders.php">Track Order</a></li>
                    </ul>
                </div>
                <div class="footer-section hours">
                    <h3>Opening Hours</h3>
                    <ul>
                        <li>Sunday - Friday: 7:30 AM - 8:00 PM</li>
                        <li>Saturday: Closed</li>
                    </ul>
                </div>
                
                    </form>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Campus Canteen. All rights reserved.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>
