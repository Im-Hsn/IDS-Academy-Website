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
$employeeId =  $_SESSION['EmployeeId'];
?>
<title>Employee My Programs</title>

<link rel="stylesheet" href="../Intern/Style.css">
<link rel="stylesheet" href="../Internship/InternshipStyle.css">
<link rel="stylesheet" href="../Admin/Style.css">
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

<section id="programs" class="programs">
    <div class="container">
        <h2>My Programs</h2>
        <div class="program-cards">
            <?php
            $instructorPrograms = getEmployeePrograms($conn, $employeeId);
            if($instructorPrograms){
            foreach ($instructorPrograms as $program) {
            ?>
            <div class="program-card">
                <h3 class="program-title"><?php echo $program["Title"]; ?></h3>
                <p class="program-description"><?php echo $program["Description"]; ?></p>
                <button class="btn btn-primary expand-btn">View details</button>
                <div class="program-details">
                    <div class="program-info">
                        <div class="info-item">
                            <span class="info-title">Instructors:</span>
                            <span class="info-value"><?php echo $program["Instructors"]; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-title">Start Date:</span>
                            <span class="info-value"><?php echo $program["Start Date"]; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-title">End Date:</span>
                            <span class="info-value"><?php echo $program["End Date"]; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-title">Max Capacity:</span>
                            <span class="info-value"><?php echo $program["Max Capacity"]; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-title">Current Capacity:</span>
                            <span class="info-value"><?php echo $program["Current Capacity"]; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-title">Classroom Code:</span>
                            <span class="info-value"><?php echo $program["Classroom Code"]; ?></span>
                        </div>
                    </div>
                    <div class="info-input">
                    <button class="btn btn-primary download-btn edit-btn" onclick="toggleEdit(this)">Edit assessment exam link</button>
                    <input type="text" <?php echo isset($program["Link"]) ? 'value="' . $program["Link"] . '"' : 'placeholder="Enter Assessment Exam Link"'; ?> class="edit-input" style="display: none;">
                    <button class="btn btn-primary download-btn save-btn" onclick="saveAssessmentLink(this)" data-program-id="<?php echo $program['Program ID']; ?>" style="display: none;">Save</button>
                    </div>
                </div>
            </div>
            <?php
            }
        }else  echo "<h4 class='no-cards'>You are not instructing any programs currently.</h4>";
            ?>
        </div>
    </div>
</section>

<?php
include '../Footer.php';
mysqli_close($conn);
?>
