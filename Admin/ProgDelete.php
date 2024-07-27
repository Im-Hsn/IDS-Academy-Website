<?php
include '../DB.php';
include '../Header.php';

// Resume the session
session_start();

// Redirect if Admin is not logged in
if (!isset($_SESSION['Role']) || $_SESSION['Role']!=="Admin") {
    header("Location: ../Employee/Login.php");
}

// Handle logout
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: ../Employee/Login.php");
}

// Check if the programId parameter is set in the URL
if (isset($_GET['programId'])) {
    $programId = $_GET['programId'];

    // To make sure the program id is correct
    if (!getProgramInfoAndStoreInSession($conn, $programId)) header("Location: ProgDelete.php");

    // Call the function to delete the program
    $result = deleteProgramById($conn, $programId);

    if ($result === true) {
        // Program deleted successfully, refresh
        header("Location: ProgDelete.php");
    } else {
        // Handle the error
        echo "Failed to delete the program: " . $result;
    }
}

?>
<title>Admin Delete Programs</title>

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

<section id="programs" class="programs">
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
                            <div class="info-input">
                                <button class="btn btn-primary download-btn edit-btn" onclick="toggleEdit(this)">Edit assessment exam link</button>
                                <input type="text" class="edit-input" style="display: none;" placeholder="Enter Assessment Exam Link" <?php echo isset($row["Link"]) ? 'value="' . $row["Link"] . '"' : ''; ?>>
                                <button class="btn btn-primary download-btn save-btn" onclick="saveAssessmentLink(this)" data-program-id="<?php echo $row['Program ID']; ?>" style="display: none;">Save</button>
                            </div>
                            <button class="btn btn-primary delete-btn" onclick="openConfirmationModal(<?php echo $row['Program ID']; ?>, '<?php echo $row['Title']; ?>', '../Admin/ProgDelete.php?programId=<?php echo $row['Program ID']; ?>')">Delete Program</button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "There are no current internship programs";
            }
            ?>
            <div id="confirmationModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2></h2>
                    <p>Are you sure you want to delete this program?</p>
                    <button class="confirm-btn" id="confirmDelete">Yes</button>
                    <button class="confirm-btn" id="cancelDelete">No</button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include '../Footer.php';
mysqli_close($conn);
?>
