
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Canteen Management System</title>
    <link rel="stylesheet" href="../cms/style.css">
    <header>
        <div class="container">
            <div class="logo">
                <h1>BMC CMS</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../users/menu.php" class="active">Home</a></li>
                    <li><a href="../cms/auth/login.php">Menu</a></li>
                    <li><a href="../cms/aboutus.php">About</a></li>
                    <li><a href="../cms/contactus.php">Contact</a></li>
                    
        </div>  
                </ul>
            </nav>
            

        </div>
    </header>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
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
        h1{
            text-align: center;
        }
        .contact-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .contact-intro {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--gray-color);
        }

        .contact-methods {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
            margin-bottom: 3rem;
        }

        .contact-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            width: 300px;
            text-align: center;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }

        .contact-card:hover {
            transform: translateY(-5px);
        }

        .contact-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .contact-form {
            background: white;
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--box-shadow);
            max-width: 800px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--gray-color);
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-size: 1rem;
        }

        .submit-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 1rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .submit-btn:hover {
            background-color: var(--primary-dark);
        }

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
        
       
    </main>
</body>
</html>