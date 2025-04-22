
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS - Food Ordering System</title>
    <link rel="stylesheet" href="../cms/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .menu {
            padding: 50px 20px;
        }
        .food-items {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .item {
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 200px;
        }
        .item img {
            width: 150px;
            height: 150px;
            border-radius: 5px;
        }
        h2{
            text-align: center;
        }
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
    <header>
        <div class="container">
            <div class="logo">
                <h1>BMC Canteen</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../cms/index.php" class="active">Home</a></li>
                    <li><a href="../cms/auth/login.php">Menu</a></li>
                    <li><a href="../cms/aboutus.php">About</a></li>
                    <li><a href="../cms/contactus.php">Contact</a></li>
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
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Canteen <br>Management System</h1>
                <p>Order your favorite meals online, pay with ease, and enjoy delicious food without the wait.</p>
                <div class="hero-buttons">
                    <a href="../cms/auth/login.php" class="btn btn-primary">Order Now <i class="fas fa-arrow-right"></i></a>
                    <a href="../cms/aboutus.php" class="btn btn-outline">Learn More</a>
                </div>
            </div>
            <div class="hero-image">
            <img src="..\cms\uploads\main\biryani.jpg" alt="Delicious food">
            </div>
        </div>
        <section id="menu" class="menu">
            <h2>Available Items</h2>
            <div class="food-items">
        
                <div class="item"><img src="..\cms\uploads\main\veg momo.jpg" ><h3>Veg Momo</h3><p>Rs. 130</p></div>
                <div class="item"><img src="..\cms\uploads\main\v burger.jpg" ><h3>Burger</h3><p>Rs. 150</p></div>
                <div class="item"><img src="..\cms\uploads\pizza.jpg" ><h3>Pizza</h3><p>Rs. 400</p></div>
                <div class="item"><img src="..\cms\uploads\main\chicken momo.jpg" ><h3>Chicken Momo</h3><p>Rs. 150</p></div>
                <div class="item"><img src="..\cms\uploads\main\cc.jpg" ><h3>Chicken Chowmein</h3><p>Rs. 140</p></div>
                <div class="item"><img src="..\cms\uploads\main\v burger.jpg" ><h3>CBurger</h3><p>Rs. 170</p></div>
                <div class="item"><img src="..\cms\uploads\main\pzza.jpg" ><h3>Chicken cheesePizza</h3><p>Rs. 350</p></div>
                <div class="item"><img src="..\cms\uploads\desert\cocacola.jpg" ><h3>Coke</h3><p>Rs. 50</p></div>
                <div class="item"><img src="..\cms\uploads\desert\cof.jpg" ><h3>Coffee</h3><p>Rs. 100</p></div>
            </div>
        </section>
        <div class="feature-cards">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3>Browse Menu</h3>
                <p>Explore our wide variety of meals and snacks</p>
                <a href="../cms/auth/login.php" class="btn btn-outline">View Menu <i class="fas fa-chevron-right"></i></a>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>Order Online</h3>
                <p>Skip the line and order ahead</p>
                <a href="../cms/auth/login.php" class="btn btn-primary">Order Now <i class="fas fa-chevron-right"></i></a>
            </div>
    </section>
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
                        <li><a href="./auth/login.php">Menu</a></li>
                        <li><a href="aboutus.php">About Us</a></li>
                        <li><a href="contactus.php">Contact</a></li>
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
