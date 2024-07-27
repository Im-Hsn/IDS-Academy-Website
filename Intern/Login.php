<?php
include '../DB.php';
include '../Header.php';
?>

<title>Intern Login</title>
<link rel="stylesheet" href="Style.css">
<script src="../redirect.js"></script>

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
                    <a class="nav-link" href="../Internship/Internship.php">Internship</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../Internship/Internship.php">About</a>
                </li>
                <li class="nav-item">
                <a class="nav-link dropdown-toggle">Login</a>
                    <div class="drop-menu">
                        <a class="drop-item" href="Login.php?Id=<?php echo 'a'; ?>">Intern</a>
                        <a class="drop-item" href="../Employee/Login.php">Employee</a>
                    </div>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="scrollToElement('../Home/Home.php', 'portfolio')">Portfolio</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="scrollToElement('../Home/Home.php', 'contact')">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php
// Start a session (if not already started)
session_start();

//To unset session if moving to login from navbar
if (isset($_GET["Id"])) {
    session_unset();
}

if(isset($_SESSION['programId'])){
$programId = $_SESSION['programId'];
$programTitle = $_SESSION['programTitle'];
$programDescription = $_SESSION['programDescription'];
?>

<div class="title">
    <div class="prog-info">
        <span class="prog-title">Applying for:</span>
        <span><?php echo "<h3>" . htmlspecialchars($programTitle) . "</h3>"; ?></span>
    </div>
    <div class="prog-info">
        <span class="prog-title">Description:</span>
        <span><?php echo "<p>" . htmlspecialchars($programDescription) . "</p>"; ?></span>
    </div>
</div>

<?php
}  else {
    $p="p"; //random var
    ?>
    <div class="title">
    <div class="prog-info">
        <span><h2> Log in as intern </h2></span>
    </div>
    </div>
        <div class="pad">
    <?php
    }
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
        $email = htmlspecialchars($_POST["email"]);
        $password = $_POST["password"];

        // Perform server-side validation
        if (empty($email)) $errors["email"] = "Please enter your email address.";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors["email"] = "Please enter a valid email address.";

        if (empty($password)) $errors["password"] = "Please enter a password.";

        if (empty($errors)) {

            // Check if the program is full
            if ((isset($programId)) && isProgramFull($conn, $programId)) $errors["enrolled"] = "Sorry, this program has reached maximum capacity.";

            if(!authenticateIntern($conn, $email, $password)) $errors["auth"] = "Incorrect email or password.";

            else{
            $internId = getInternIdByEmail($conn, $email);
            // Unset all the session variables
            session_unset();
            // Destroy the current session
            session_destroy();
            // Start new session
            session_start();
            $_SESSION['internId'] = $internId;

            if(isset($programId)){ //Intern applying for program

                // Intern has applied already
                if(InternEnrolled($conn, $email, $programId)) $errors["enrolled"] = "You have already applied for this program.";
                
                // Intern has not applied
                elseif(!isProgramFull($conn, $programId)){
                    if(enrollIntern($conn, $programId, $internId)) $errors["success"] = 's';
                    else $errors["app"] = '<div class="error"><span class="error-icon"></span>' . $enrollIntern . "</div>";
                }
                echo '<script>';
                echo 'redirect("Logged.php")'; 
                echo '</script>';
            }
            else header("Location: Logged.php");
        }
        }
    }
 ?>
 

<div class="contain">
<?php
if(isset($errors["success"])) echo '<div class="success">Registration successful! Your application is pending review.</div>';
displayError("enrolled");
?>
    <h1>Login</h1>
    <?php displayError("auth"); ?>
    <form action="Login.php" method="post" novalidate>
        <div class="input-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <?php displayError("email"); ?>
        </div>
        <div class="input-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
            <?php displayError("password"); ?>
        </div>
        <button type="submit">Login</button>
    </form>

    <div class="modern-link-container">
    <?php
    if (!isset($_SESSION['programId'])) {
        ?>
        <a class="modern-link" href="../Internship/Internship.php#prog">Don't have an account?</a>
        <?php
    } else{
        ?>
        <a class="modern-link" href="Apply.php">Don't have an account?</a>
        <?php
    }
    ?>
        <span class="link-text">or</span>
        <a class="modern-link" href="../Internship/Internship.php#prog">Go to programs</a>
    </div>
</div>
<?php

if(isset($p)){
    ?>
        </div>
    <?php
    }    ?>
    <?php

    include '../Footer.php';
    mysqli_close($conn);
?>