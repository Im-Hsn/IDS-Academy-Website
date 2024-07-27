<?php
include '../DB.php';
include '../Header.php';
?>

<title>Employee Login</title>
<link rel="stylesheet" href="../Intern/Style.css">
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
                <a class="nav-link" href="javascript:void(0);" onclick="scrollToElement('../Home/Home.php', 'about')">About</a>
                </li>
                <li class="nav-item">
                <a class="nav-link dropdown-toggle">Login</a>
                    <div class="drop-menu">
                        <a class="drop-item" href="../Intern/Login.php">Intern</a>
                        <a class="drop-item" href="Login.php">Employee</a>
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
            $employeeInfo = authenticateEmployee($conn, $email, $password);
            if (!$employeeInfo) $errors["auth"] = "Incorrect email or password.";
    
            else {
                session_start();
                
                // Store employee information in SESSION variables
                $_SESSION['EmployeeId'] = $employeeInfo['Employee ID'];
                $_SESSION['FullName'] = $employeeInfo['Full Name'];
                $_SESSION['Email'] = $employeeInfo['Email'];
                $_SESSION['Role'] = $employeeInfo['Role'];
                $_SESSION['Major'] = $employeeInfo['Major'];
                $_SESSION['CreationDate'] = $employeeInfo['Creation Date'];
    
                if($employeeInfo['Role'] === "Admin") $s1 = '<div class="success">Welcome back Admin!</div>';
                else {
                    $s2 = '<div class="success">Welcome back ' . $employeeInfo['Role'] . '!</div>';
                }
            }
        }
    }
 ?>

<div class="title">
    <div class="prog-info">
        <span><h2> Log in as employee </h2></span>
    </div>
</div>

<div class="contain">
<?php
if(isset($s1)){ echo $s1;
    echo '<script>';
    echo 'redirect("../Admin/ProgDelete.php")'; 
    echo '</script>';
} elseif(isset($s2)){ echo $s2;
    echo '<script>';
    echo 'redirect("Logged.php")'; 
    echo '</script>';
  }

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
        <a class="modern-link" href="../Home/Home.php">Go to Home</a>
    </div>
</div>

<?php
    include '../Footer.php';
    mysqli_close($conn);
?>