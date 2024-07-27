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

$internId = $_SESSION['internId'];

// Check if intern data is already in session, if not, retrieve it from the database
if (!isset($_SESSION['internData'])) {
    $internData = getInternInformation($conn, $internId);

    if (!$internData) {
        // Handle the case where the intern dataxa is not found
        echo '<div class="error"><span class="error-icon"></span>' . 'Intern data not found' . "</div>";
    } else {
        // Store intern information in session variables
        $_SESSION['internData'] = $internData;
    }
    // Retrieve the intern's programs
    $internPrograms = getInternPrograms($conn, $internId);
    $_SESSION['internPrograms'] = $internPrograms;
}

// Access intern data from session
$internData = $_SESSION['internData'];
$internPrograms = $_SESSION['internPrograms'];
?>

<title>My Programs</title>
<link rel="stylesheet" href="Style.css">
<link rel="stylesheet" href="../Internship/InternshipStyle.css">
<link rel="stylesheet" href="../Admin/Style.css">

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

<section id="programs" class="programs">
    <div class="container">
        <h2>My Internship Programs</h2>
        <div class="program-cards">
            <?php
            foreach ($internPrograms as $program) {
            ?>
            <div class="program-card">
                <h3 class="program-title"><?php echo $program["Title"]; ?></h3>
                <p class="program-description"><?php echo $program["Description"]; ?></p>
                <div class="program-application-status <?php echo $program["Application Status"] == 'Accepted' ? 'success' : 'error'; ?>">
                    <span class="status-value"><?php echo $program["Application Status"]; ?></span>
                </div>
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
                    <button class="btn btn-primary download-btn" onclick="toggleEdit(this)">View assessment exam link</button>
                    <input type="text" <?php echo isset($program["Link"]) ? 'value="' . $program["Link"] . '"' : 'placeholder="Exam link has not been set yet"'; ?> class="edit-input" style="display: none;">
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
    </div>
</section>

<?php
include '../Footer.php';
mysqli_close($conn);
?>
