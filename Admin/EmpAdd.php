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
        $fullName = htmlspecialchars($_POST["full_name"]);
        $email = htmlspecialchars($_POST["email"]);
        $password = $_POST["password"];
        $confirmPassword = $_POST["confirm_password"];
        $major = htmlspecialchars($_POST["major"]);
        $role = $_POST["role"];
    
    // Perform server-side validation
    if (empty($fullName)) $errors["full_name"] = "Please enter employee full name.";

    if (empty($email)) $errors["email"] = "Please enter employee email address.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors["email"] = "Please enter a valid email address.";

    if (empty($password)) $errors["password"] = "Please enter a password.";
    if (empty($confirmPassword)) $errors["confirm_password"] = "Please confirm employee password.";
    elseif ($password !== $confirmPassword) $errors["confirm_password"] = "Passwords do not match.";

    if (empty($major)) $errors["major"] = "Please enter employee major.";



    if (empty($errors)) {
        if(employeeExists($conn, $fullName, $email, NULL)) $errors["exists"] = 'An employee with the same email or full name already exists.';

        else{
            // Insert employee data into the database
            $insertResult = addEmployee($conn, $fullName, $email, $password, $role, $major);
            if ($insertResult) $success = '<div class="success">Employee added successfully!</div>';
            else $errors["app"] = $insertResult;
        }
    }    
}
?>

<div class="contain">
<?php
displayError("exists");
displayError("app");
if(isset($success)) echo $success;
?>
    <h1>Add an employee</h1>
    <form action="EmpAdd.php" method="post" novalidate>
        <div class="input-group">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
            <?php displayError("full_name"); ?>
        </div>
        <div class="input-group">
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <?php displayError("email"); ?>
        </div>
        <div class="input-group">
            <label for="password">Password:</label>
            <input type="password" name="password" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>">
            <?php displayError("password"); ?>
        </div>
        <div class="input-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password">
            <?php displayError("confirm_password"); ?>
        </div>
        <div class="input-group">
            <label for="major">Major:</label>
            <input type="text" name="major" value="<?php echo isset($_POST['major']) ? htmlspecialchars($_POST['major']) : ''; ?>">
            <?php displayError("major"); ?>
        </div>
        <input type="hidden" name="role" value="Employee"> <!-- Set the role to "Employee" -->
        <button type="submit">Add employee</button>
    </form>
</div>

<?php
include '../Footer.php';
mysqli_close($conn);
?>