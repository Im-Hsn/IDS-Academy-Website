<?php
include '../DB.php';
include '../Header.php';

// Resume the session
session_start();

// Redirect if Employee is not logged in
if (!isset($_SESSION['Role'])) {
    header("Location: Login.php");
}

// Handle logout
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: Login.php");
}
?>
<title>Employee View Interns</title>

<link rel="stylesheet" href="../Intern/Style.css">
<link rel="stylesheet" href="../Internship/InternshipStyle.css">
<link rel="stylesheet" href="Style.css">


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
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="Profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Interns.php">Internship Applications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Logged.php">My programs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?logout=true">Logout</a>
                </li>
            </ul>
        </div>
        <!-- User Icon and Name -->
        <a href="Profile.php" class="user-info">
        <i class="fas fa-user"></i>
        <span class="user-name"><?php echo $_SESSION['FullName']; ?></span>
        </a>
    </div>
</nav>

<section id="interns" class="programs">
    <div class="container">
        <h2>Pending Intern Applications</h2>
        <div class="program-cards">
            <?php
            $interns = getPendingInternsInfo($conn);
            if ($interns){
            // Loop through the pending interns and display their information
            foreach ($interns as $intern) {
                $cvPath = str_replace('\\', '/', $intern['CV']);
                ?>
                <div class="program-card">
                    <h3 class="program-title"><?php echo $intern['Full Name']; ?></h3>
                    <p class="program-description"><?php echo $intern['Email']; ?></p>
                    <button class="btn btn-primary expand-btn">View details</button>
                    <div class="program-details">
                        <div class="program-info">
                            <div class="info-item">
                                <span class="info-title">Intern ID:</span>
                                <span class="info-value"><?php echo $intern['Intern ID']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-title">Mobile Number:</span>
                                <span class="info-value"><?php echo $intern['Mobile Number']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-title">University:</span>
                                <span class="info-value"><?php echo $intern['University']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-title">Major:</span>
                                <span class="info-value"><?php echo $intern['Major']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-title">Graduation Date:</span>
                                <span class="info-value"><?php echo $intern['Graduation Date']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-title">Program Name:</span>
                                <span class="info-value"><?php echo $intern['Program Name']; ?></span>
                            </div>
                        </div>
                        <div class="btn-container">
                            <button class="btn btn-primary download-btn" onclick="downloadCV(event, '<?php echo $cvPath; ?>')">Download CV</button>
                        </div>
                    </div>
                </div>
                <?php
            }
         } else echo "<h4 class='no-cards'>There are no pending internship applications currently.</h4>";
            ?>
        </div>
    </div>
</section>

<?php
include '../Footer.php';
mysqli_close($conn);
?>
