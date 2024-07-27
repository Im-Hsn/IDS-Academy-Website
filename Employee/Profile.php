<title>Employee Profile</title>
<link rel="stylesheet" href="../Intern/Style.css">
<link rel="stylesheet" href="Style.css">

<?php
include '../DB.php';
include '../Header.php';

// Start or resume the session
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
if($_SESSION["Role"]==="Admin"){
?>

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
                    <a class="nav-link" href="../Admin/Interns.php">Internship Applications</a>
                </li>
                <li class="nav-item">
                <a class="nav-link dropdown-toggle">Manage programs</a>
                    <div class="drop-menu">
                    <a class="drop-item" href="../Admin/ProgAdd.php">Add</a>
                        <a class="drop-item" href="../Admin/ProgDelete.php">Delete</a>
                    </div>
                </li>
                <li class="nav-item">
                <a class="nav-link dropdown-toggle">Manage Employees</a>
                    <div class="drop-menu">
                    <a class="drop-item" href="../Admin/EmpAdd.php">Add</a>
                        <a class="drop-item" href="../Admin/EmpDelete.php">Delete</a>
                    </div>
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
<?php
}else{
?>
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
<?php
}
?>
<!-- Profile Content -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <!-- User Profile Card -->
            <div class="card user-profile-card">
                <div class="card-body text-center">
                    <i class="fas fa-user fa-5x mb-3"></i>
                    <h4 class="card-title"><?php echo $_SESSION['FullName']; ?></h4>
                    <p class="card-text"><?php echo $_SESSION['Email']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <!-- User Details -->
            <div class="card user-details-card">
                <div class="card-body">
                    <h4 class="card-title">Employee Details</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Employee Role:</strong> <?php echo $_SESSION['Role']; ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Major:</strong> <?php echo $_SESSION['Major']; ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Joined On:</strong> <?php echo $_SESSION['CreationDate']; ?>
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
