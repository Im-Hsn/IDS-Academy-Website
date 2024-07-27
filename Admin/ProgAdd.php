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

?>
<title>Admin Add Programs</title>

<link rel="stylesheet" href="../Intern/Style.css">
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

<?php
// Initialize variables to store validation errors
$errors = [];

// Function to display errors under input fields
function displayError($fieldName)
{
    global $errors;
    if (!empty($errors[$fieldName])) {
        echo '<div class="error"><span class="error-icon"></span>' . htmlspecialchars($errors[$fieldName]) . '</div>';
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = htmlspecialchars($_POST["title"]);
    $description = htmlspecialchars($_POST["description"]);
    $startDate = $_POST["start_date"];
    $endDate = $_POST["end_date"];
    $maxCapacity = intval($_POST["max_capacity"]);
    $classroomCode = htmlspecialchars($_POST["classroom_code"]);
    $instructor_ids = json_decode($_POST['instructor_ids'], true);

    if (empty($instructor_ids)) $errors["instructor_ids"] = "Please select at least one instructor.";

    // Perform server-side validation
    if (empty($title)) $errors["title"] = "Please enter a title.";
    elseif (programExists($conn, $title)) $errors["title"] = "A program with such title already exists.";

    if (empty($description)) $errors["description"] = "Please enter a description.";

    if (empty($startDate)) $errors["start_date"] = "Please select a start date.";
    elseif ($startDate <= date("Y-m-d")) $errors["start_date"] = "Start date must be in the future.";

    if (empty($endDate)) $errors["end_date"] = "Please select an end date.";
    elseif ($endDate <= date("Y-m-d")) $errors["end_date"] = "End date must be in the future.";
    if (!empty($startDate) && !empty($endDate) && $endDate <= $startDate) $errors["end_date"] = "End date must be after the start date.";

    if (empty($maxCapacity) || $maxCapacity <= 0) $errors["max_capacity"] = "Please enter a valid maximum capacity.";

    if (empty($classroomCode)) $errors["classroom_code"] = "Please enter a classroom code.";
    elseif (!preg_match('/^[0-9]+$/', $classroomCode)) $errors["classroom_code"] = "Classroom code should contain only numbers.";
    elseif (isProgramExistsByClassroomCode($conn, $classroomCode)) $errors["classroom_code"] = "A program with this classroom code already exists.";

    if (empty($errors)) {
        // Add the program to the database
        $addProgramResult = addProgram($conn, $title, $description, $startDate, $endDate, $maxCapacity, $classroomCode, $instructor_ids);

        if ($addProgramResult === true) {
            $successMessage = '<div class="success">Program added successfully.</div>';
        } else {
            $errors["add_program"] = $addProgramResult;
        }
    }
}
?>

<div class="contain">
    <?php
    if (isset($successMessage)) {
        echo $successMessage;
    } else {
        displayError("add_program");
    }
    ?>
    <h1>Add Program</h1>
    <form action="ProgAdd.php" method="post" novalidate>
        <div class="input-group">
            <label for="title">Title:</label>
            <input type="text" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
            <?php displayError("title"); ?>
        </div>
        <div class="input-group">
            <label for="description">Description:</label>
            <textarea name="description"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            <?php displayError("description"); ?>
        </div>
        <div class="input-group">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" value="<?php echo isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ''; ?>">
            <?php displayError("start_date"); ?>
        </div>
        <div class="input-group">
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" value="<?php echo isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : ''; ?>">
            <?php displayError("end_date"); ?>
        </div>
        <div class="input-group">
            <label for="max_capacity">Max Capacity:</label>
            <input type="number" name="max_capacity" value="<?php echo isset($_POST['max_capacity']) ? htmlspecialchars($_POST['max_capacity']) : ''; ?>">
            <?php displayError("max_capacity"); ?>
        </div>
        <div class="input-group">
            <label for="classroom_code">Classroom Code:</label>
            <input type="text" name="classroom_code" value="<?php echo isset($_POST['classroom_code']) ? htmlspecialchars($_POST['classroom_code']) : ''; ?>">
            <?php displayError("classroom_code"); ?>
        </div>

        <div class="input-group">
            <div class="dropdown">
                <div class="dropdown-title">Instructors:</div>
                <label id="dropdownButton" class="dropdown-button" onclick="toggleDropdown()">Add an instructor</label>
                    <div class="dropdown-options" id="instructorsDropdown">
                    <input type="text" id="instructorSearch" onkeyup="filterInstructors()" placeholder="Search for instructors..">
                    <?php
                    // Fetch instructors from the database
                    $instructors = fetchInstructorsFromDatabase($conn);                    
                    foreach ($instructors as $instructor) {
                        echo '<div class="dropdown-option" onclick="addInstructor(' . htmlspecialchars($instructor['id']) . ', \'' . htmlspecialchars($instructor['name']) . '\')">' . htmlspecialchars($instructor['name']) . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="selected-instructor" id="selectedInstructors">
            <!-- Selected instructors will be added here -->
            </div>
            <input type="hidden" name="instructor_ids" id="instructorsField" value="">
            <?php displayError("instructor_ids"); ?>
        <button type="submit">Add Program</button>
    </form>
</div>

<?php
include '../Footer.php';
mysqli_close($conn);
?>