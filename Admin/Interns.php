<?php
include '../DB.php';
include '../Header.php';

// Resume the session
session_start();

// Redirect if Admin is not logged in
if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== "Admin") {
    header("Location: ../Employee/Login.php");
}

// Handle logout
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: ../Employee/Login.php");
}

// Check if the internId parameter is set in the URL
if (isset($_GET['internId']) && isset($_GET['programId']) && isset($_GET['action'])) {
    $internId = $_GET['internId'];
    $programId = $_GET['programId'];
    $applicationStatus = $_GET['action'];

    // Check if Intern id is correct
    if (!internExists($conn, $internId)) header("Location: Interns.php");
    // To make sure the program id is correct
    if (!getProgramInfoAndStoreInSession($conn, $programId)) header("Location: Interns.php");

    if($applicationStatus === "Accepted" || $applicationStatus === "Rejected"){

    // Handle accepting or rejecting an intern
    $result = updateInternApplicationStatus($conn, $internId, $programId, $applicationStatus);

    if ($result === true) {
        // Intern accepted successfully, you can redirect or display a success message
        header("Location: Interns.php");
    } else {
        // Handle the error
        echo "Failed to accept or reject the intern: " . $result;
    }
}
}
?>

<title>Admin Manage Interns</title>

<link rel="stylesheet" href="../Intern/Style.css">
<link rel="stylesheet" href="../Internship/InternshipStyle.css">
<link rel="stylesheet" href="../Employee/Style.css">
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
                    <a class="nav-link" href="../Employee/Profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Interns.php">Internship Applications</a>
                </li>
                <li class="nav-item">
                <a class="nav-link dropdown-toggle">Manage programs</a>
                    <div class="drop-menu">
                    <a class="drop-item" href="ProgAdd.php">Add</a>
                        <a class="drop-item" href="ProgDelete.php">Delete</a>
                    </div>
                </li>
                <li class="nav-item">
                <a class="nav-link dropdown-toggle">Manage Employees</a>
                    <div class="drop-menu">
                    <a class="drop-item" href="EmpAdd.php">Add</a>
                        <a class="drop-item" href="EmpDelete.php">Delete</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?logout=true">Logout</a>
                </li>
            </ul>
        </div>
        <!-- User Icon and Name -->
        <a href="../Employee/Profile.php" class="user-info">
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
                            <div class="accept-reject-container">
                            <button class="btn btn-primary apply-btn accept-btn" onclick="location.href='Interns.php?internId=<?php echo $intern['Intern ID']; ?>&programId=<?php echo $intern['Program ID']; ?>&action=Accepted'">Accept</button>
                            <button class="btn btn-primary delete-btn reject-btn" onclick="location.href='Interns.php?internId=<?php echo $intern['Intern ID']; ?>&programId=<?php echo $intern['Program ID']; ?>&action=Rejected'">Reject</button>
                            </div>
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
