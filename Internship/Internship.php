<?php 
include '../DB.php';
include '../Header.php';
?>

<link rel="stylesheet" href="InternshipStyle.css">

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="logo" href="../Home/Home.php">
            <img src="../Images/IDS LOGO.png" alt="IDS Logo">
        </a>
        <!-- Hamburger Menu Button for Mobile -->
        <button class="navbar-toggler toggle-btn" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse nav-links" id="navbarNav">
            <ul class="text-overlay navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#main">Internship</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#prog">About</a>
                </li>
                <li class="nav-item">
                <a class="nav-link dropdown-toggle">Login</a>
                    <div class="drop-menu">
                        <a class="drop-item" href="../Intern/Login.php">Intern</a>
                        <a class="drop-item" href="../Employee/Login.php">Employee</a>
                    </div>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="scrollToElement('../Home/Home.php', 'portfolio')">Portfolio</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="scrollToElement('../Home/Home.php', 'contact')">Contact</a>
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

<!-- Hero Section - Introduction to Internship Program -->
<section class="hero" id="main">
    <div class="container smooth">
        <h1>Welcome to Our Internship Program</h1>
        <div class="intro">
        Unlock your potential and gain real-world experience with our immersive internship opportunities.
        </div>
        <a class="btn btn-primary" href="#prog" >View available programs</a>
    </div>
</section>
<!-- About Section - Learn About the Internship -->
<section id="prog" class="about ab">
    <div class="container text-center">
        <h2>About the Internship</h2>
        <p>Our thoughtfully crafted internship program offers a gateway to immersive hands-on experiences, allowing you to dive deep into real-world scenarios and gain valuable insights...</p>
    </div>
</section>

<section id="programs" class="about">
    <div class="container">
        <h2>Current Internship Programs</h2>
        <div class="program-cards">
            <?php
            $result = getProgramsInfo($conn);

            // Loop through the result set and display program information
            if ($result !== false) {
                foreach ($result as $row) {
                    ?>
                    <div class="program-card">
                        <h3 class="program-title"><?php echo $row["Title"]; ?></h3>
                        <p class="program-description"><?php echo $row["Description"]; ?></p>
                        <button class="btn btn-primary expand-btn">View details</button>
                        <div class="program-details">
                            <div class="program-info">
                                <div class="info-item">
                                    <span class="info-title">Instructors:</span>
                                    <span class="info-value"><?php echo $row["Instructors"]; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-title">Start Date:</span>
                                    <span class="info-value"><?php echo $row["Start Date"]; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-title">End Date:</span>
                                    <span class="info-value"><?php echo $row["End Date"]; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-title">Max Capacity:</span>
                                    <span class="info-value"><?php echo $row["Max Capacity"]; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-title">Current Capacity:</span>
                                    <span class="info-value"><?php echo $row["Current Capacity"]; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-title">Classroom Code:</span>
                                    <span class="info-value"><?php echo $row["Classroom Code"]; ?></span>
                                </div>
                            </div>
                            <button class="btn btn-primary apply-btn" onclick="window.location.href='../Intern/Apply.php?programId=<?php echo $row['Program ID']; ?>'">Apply Now</button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "There are no current internship programs";
            }
            ?>
        </div>
    </div>
</section>

<?php
include '../Footer.php';
mysqli_close($conn);
?>