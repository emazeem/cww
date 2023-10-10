<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BE CLEAN Car Wash</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Additional Custom CSS Styles */
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand img {
            max-width: 100px;
        }
        .navbar-toggler-icon {
            background-color: #fff;
        }
        h2, p {
            color: #333;
        }
        /* Add more custom styles as needed */
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-info">
    <a class="navbar-brand" href="#">
        <img src="{{url('logo.png')}}" alt="BE CLEAN Car Wash Logo">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="#about">About Us</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#services">Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#contact">Contact Us</a>
            </li>
        </ul>
    </div>
</nav>
<!-- About Us Section -->
<section id="about" class="container mt-5">
    <h2>About BE CLEAN Car Wash</h2>
    <p>Experience the art of professional car wash management. At BE CLEAN, we're obsessed with making your car shine. With a commitment to quality and customer satisfaction, we're your trusted partner in keeping your vehicles looking their best.</p>
</section>

<!-- Services Section -->
<section id="services" class="container mt-5">
    <h2>Our Car Wash Services</h2>
    <p>Choose from our range of car wash subscription services designed to meet your unique needs:</p>
    <ul>
        <li><strong>Basic Subscription:</strong> Affordable, regular car maintenance.</li>
        <li><strong>Premium Subscription:</strong> Elevate your car's appearance with our premium package.</li>
        <li><strong>One-Time Service:</strong> Not ready for a subscription? Try our quick refresh service.</li>
    </ul>
</section>
<!-- Privacy Policy Section -->
<section id="privacy-policy" class="container mt-5">
    <h2>Privacy Policy</h2>
    <p>Your privacy is important to us. This Privacy Policy explains how BE CLEAN Car Wash (referred to as "we," "us," or "our") collects, uses, discloses, and safeguards your personal information when you use our services. By using our car wash app, you consent to the practices described in this Privacy Policy.</p>
    <p><strong>Information We Collect:</strong> We may collect personal information, including your name, email address, and contact details, to provide our car wash services and improve your user experience.</p>
    <p><strong>How We Use Your Information:</strong> We use your information to schedule car wash appointments, manage subscriptions, and communicate with you. We do not sell or share your data with third parties without your consent.</p>
    <p><strong>Security:</strong> We take reasonable measures to protect your personal information. We use secure protocols to safeguard your data, but please be aware that no data transmission over the internet is completely secure.</p>
    <p><strong>Contact Us:</strong> If you have any questions or concerns about our Privacy Policy or how we handle your data, please contact us at support@beclean-sa.com.</p>
    <p>This Privacy Policy is subject to change. Please review it periodically for updates. By using our app, you agree to the latest version of this Privacy Policy.</p>
</section>

<section id="contact" class="container mt-5">
    <h2>Contact Us</h2>
    <form id="contact-form">
        <div class="form-group">
            <label for="name">Your Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Your Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="message">Your Message</label>
            <textarea class="form-control" id="message" name="message" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</section>
<!-- Footer Section -->
<footer class="bg-primary text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <img src="{{url('logo.png')}}" alt="BE CLEAN Car Wash Logo" style="max-width: 150px;">
                <p>&copy; 2023 BE CLEAN Car Wash. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-right">
                <p>Contact us: support@beclean-sa.com</p>
            </div>
        </div>
    </div>
</footer>

<!-- Add Bootstrap and jQuery JavaScript libraries -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>

<script>
    document.getElementById('contact-form').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission behavior
        alert('Your message has been sent. Thank you for contacting us!');
        location.reload();
    });
</script>

<script>
    // JavaScript code for form validation and submission can be added here
</script>
</body>
</html>
