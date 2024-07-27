<?php
include '../DB.php';
include '../Header.php';

// Start or resume the session
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['internId'])) {
    header("Location: Login.php");
}

// Handle logout
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: Login.php");
}

// Access intern data from session
$internData = $_SESSION['internData'];

?>
<title>Intern Profile</title>

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
            <ul class="text-overlay navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="Profile.php">Profile</a>
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
        <span class="user-name"><?php echo $internData['Full Name']; ?></span>
        </a>
    </div>
</nav>

<!-- Profile Content -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <!-- User Profile Card -->
            <div class="card user-profile-card">
                <div class="card-body text-center">
                    <i class="fas fa-user fa-5x mb-3"></i>
                    <h4 class="card-title"><?php echo $internData['Full Name']; ?></h4>
                    <p class="card-text"><?php echo $internData['Email']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <!-- User Details -->
            <div class="card user-details-card">
                <div class="card-body">
                    <h4 class="card-title">Intern Details</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Mobile Number:</strong> <?php echo $internData['Mobile Number']; ?>
                        </li>
                        <li class="list-group-item">
                            <strong>University:</strong> <?php echo $internData['University']; ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Major:</strong> <?php echo $internData['Major']; ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Graduation Date:</strong> <?php echo $internData['Graduation Date']; ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include '../Footer.php';
mysqli_close($conn);
?>
