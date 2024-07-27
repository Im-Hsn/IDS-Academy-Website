<?php include '../Header.php'; ?>
<link rel="stylesheet" href="HomeStyle.css">

<nav class="navbar navbar-expand-lg navbar-light smooth">
    <div class="container">
        <a class="logo" href="#hero">
            <img src="../Images/IDS LOGO.png" alt="IDS Logo">
        </a>
        <button class="navbar-toggler toggle-btn" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse nav-links" id="navbarNav">
            <ul class="text-overlay navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../Internship/Internship.php">Internship</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About</a>
                </li>
                <li class="nav-item">
                <a class="nav-link dropdown-toggle">Login</a>
                    <div class="drop-menu">
                        <a class="drop-item" href="../Intern/Login.php">Intern</a>
                        <a class="drop-item" href="../Employee/Login.php">Employee</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#portfolio">Portfolio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php
// Start a new session or resume the existing session
session_start();

// Destroy all sessions
session_destroy();
?>

<section id="hero" class="hero">
    <div class="container">
            <div class="col-md-8 text-left">
                <h1 class="display-4">Welcome to IDS Academy</h1>
                <p><strong>Join our latest internship program now!</strong></p>
                <a href="../Internship/Internship.php" class="btn btn-primary">Learn More</a>
            </div>
    </div>
</section>

<section id="about" class="about text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="display-4">About Us</h2>
                <p class="lead">Integrated Digital Systems (IDS) is a software solutions provider delivering full cycle software development services and products since 1991.
With a team of more than a hundred professionals, IDS excels in providing turnkey solutions in Information Technology to a diversified range of industries, on an international scale.
Today, IDS positions itself as a key regional player in software development in the MENA region.</p>
            </div>
        </div>
    </div>
</section>

<section id="services" class="services text-center">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2 class="display-4">Our Services</h2>
                <p class="lead">We offer a wide range of services to cater to your business needs.</p>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="service-item">
                    <h3>Web Development</h3>
                    <p>We create modern and responsive websites that engage your audience.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-item">
                    <h3>Digital Marketing</h3>
                    <p>We employ effective digital strategies to boost your online presence.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-item">
                    <h3>Consulting</h3>
                    <p>Our expert team provides valuable insights for your business growth.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="portfolio" class="portfolio text-center">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2 class="display-4">Our Portfolio</h2>
                <p class="lead">Explore some of the amazing projects we've had the privilege to work on.</p>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="portfolio-item">
                    <img src="../Images/project1.png" alt="Project 1">
                    <h4>Python Project</h4>
                    <p>Used Python for test automation and auto-remediation of issues within the platform to enable fast and efficient delivery of new features.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="portfolio-item">
                    <img src="../Images/project2.png" alt="Project 2">
                    <h4>Java Project</h4>
                    <p>Created a bank management software using Java.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="portfolio-item">
                    <img src="../Images/project3.png" alt="Project 3">
                    <h4>PHP Project</h4>
                    <p>Managed a company's website back-end features to ensure database connectivity.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="contact" class="contact text-center">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2 class="display-4">Contact Us</h2>
                <p class="lead">Get in touch with our team for inquiries and collaborations.</p>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6 offset-md-3">
                <form class="contact-form">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Your Name">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" placeholder="Your Email">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="5" placeholder="Your Message"></textarea>
                    </div>
                    <button type="submit" class="btn">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include '../Footer.php'; ?>