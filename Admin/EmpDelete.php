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
$error = [];
// Check if the employeeId parameter is set in the URL
if (isset($_GET['employeeId'])) {
    $employeeId = $_GET['employeeId'];

    // Check if Emplooyee id is correct
    if(!employeeExists($conn, NULL, NULL, $employeeId)) header("Location: EmpDelete.php");

    if(isOnlyInstructorForAProgram($conn, $employeeId)){
        $error[1] = '<div class="error"><span class="error-icon"></span>This employee is instructing a program that is only being instructed by them. Please delete the program first.</div>';
        $error[2] = $employeeId;
    }
    else{
    // Call the function to delete the employee
    $result = deleteEmployeeById($conn, $employeeId);

    if ($result === true) {
        // Employee deleted successfully, refresh
        header("Location: EmpDelete.php");
    } else {
        // Handle the error
        echo "Failed to delete the employee: " . $result;
    }
    }
}

?>
<title>Admin Delete Employee</title>

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

<section id="employees" class="programs">
    <div class="container">
        <h2>Current Employees and Their Programs</h2>
        <div class="program-cards">
            <?php
            $employees = getAllEmployeesWithPrograms($conn);
            // Loop through the employees and display their information
            foreach ($employees as $employee) {
                ?>
                <div class="program-card">
                    <h3 class="program-title"><?php echo $employee['Full Name']; ?></h3>
                    <p class="program-description"><?php echo $employee['Email']; ?></p>
                    <?php
                        // Check if there is an error for this employee and display it
                        if(isset($error[1]) && $error[2] === $employee['Employee ID']) {
                            echo $error[1];
                        }
                        ?>
                    <button class="btn btn-primary expand-btn">View details</button>
                    <div class="program-details">
                        <div class="program-info">
                            <div class="info-item">
                                <span class="info-title">Role:</span>
                                <span class="info-value"><?php echo $employee['Role']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-title">Major:</span>
                                <span class="info-value"><?php echo $employee['Major']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-title">Creation Date:</span>
                                <span class="info-value"><?php echo $employee['Creation Date']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-title">Programs:</span>
                                <ul class="info-value">
                                    <?php
                                    foreach ($employee['Programs'] as $program) {
                                        echo '<li>' . $program . '</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <button class="btn btn-primary delete-btn" onclick="openConfirmationModal(<?php echo $employee['Employee ID']; ?>, '<?php echo $employee['Full Name']; ?>', '../Admin/EmpDelete.php?employeeId=<?php echo $employee['Employee ID']; ?>')">Delete Employee</button>
                    </div>
                </div>
                <?php
            }
            ?>
            <div id="confirmationModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2></h2>
                    <p>Are you sure you want to delete this Employee?</p>
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
