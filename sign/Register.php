<?php
include '../DB.php';
include '../Header.php';
?>

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
                    <a class="nav-link" href="../Internship/Internship.php">Internship</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../Home/Home.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#services">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#portfolio">Portfolio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php
// Start a session (if not already started)
session_start();

// Initialize variables to store program information
$programTitle = "";
$programDescription = "";
$programId = null;

// Check if the program ID is set in the URL
if (isset($_GET["programId"])) {
    $programId = $_GET["programId"];
    
    // SQL query to select program information
    $stmt = $conn->prepare("SELECT `Title`, `Description` FROM `Programs` WHERE `Program ID` = ?");
    $stmt->bind_param("i", $programId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

        // Store program information in session variables
        $_SESSION['programId'] = $programId;
        $_SESSION['programTitle'] = $row["Title"];
        $_SESSION['programDescription'] = $row["Description"];
      

    // Close the database statement
    $stmt->close();
}

// Retrieve program information from session variables (if available)
if (isset($_SESSION['programId'])) {
    $programId = $_SESSION['programId'];
    $programTitle = $_SESSION['programTitle'];
    $programDescription = $_SESSION['programDescription'];
}

?>

<div class="title">
    <div class="info-item">
        <span class="info-title">Applying for:</span>
        <span><?php echo "<h3>" . htmlspecialchars($programTitle) . "</h3>"; ?></span>
    </div>
    <div class="info-item">
        <span class="info-title">Description:</span>
        <span><?php echo "<p>" . htmlspecialchars($programDescription) . "</p>"; ?></span>
    </div>
</div>


<div class="contain">
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
        $confirmPassword = $_POST["confirm_password"]; // Add this line
        $mobileNumber = htmlspecialchars($_POST["mobile_number"]);
        $university = htmlspecialchars($_POST["university"]);
        $major = htmlspecialchars($_POST["major"]);
        $graduationDate = $_POST["graduation_date"];
    
    // Perform server-side validation
    if (empty($fullName)) $errors["full_name"] = "Please enter your full name.";

    if (empty($email)) $errors["email"] = "Please enter your email address.";

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors["email"] = "Please enter a valid email address.";

    if (empty($password)) $errors["password"] = "Please enter a password.";

    if (empty($confirmPassword)) $errors["confirm_password"] = "Please confirm your password.";

    if (empty($mobileNumber)) $errors["mobile_number"] = "Please enter your mobile number.";

    elseif (!preg_match('/^[0-9+]+$/', $mobileNumber)) $errors["mobile_number"] = "Mobile number should contain only numbers and the plus symbol (+).";

    if (empty($university)) $errors["university"] = "Please enter your university.";

    if (empty($major)) $errors["major"] = "Please enter your major.";

    if (empty($graduationDate)) $errors["graduation_date"] = "Please select your graduation date.";

    elseif ($graduationDate <= date("Y-m-d")) $errors["graduation_date"] = "Graduation date must be in the future.";

    if ($password !== $confirmPassword) $errors["confirm_password"] = "Passwords do not match.";

    if (empty($_FILES['cv']['name'])) $errors["cv"] = "Please upload your CV.";


        if (empty($errors)) {

            // Condition to check if intern already registered or program is not available
            if(InternEnrolled($email, $programId)){
                echo '<div class="error"><span class="error-icon"></span>' . 'You have already registered for this program.' . "</div>";
            } elseif(!programExists($programId)){
                echo '<div class="error"><span class="error-icon"></span>' . 'Program does not exist.' . "</div>";
            }
            else{
            // Hash the password before storing it in the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Upload the CV and get its location
            $cvLocation = handleFileUpload($email);

            if ($cvLocation) {
                // Insert intern data into the database
                $insertResult = insertIntern($fullName, $email, $hashedPassword, $mobileNumber, $university, $major, $graduationDate, $cvLocation, $programId);

                if ($insertResult) {
                    echo '<div class="success">Registration successful! Your application is pending review.</div>';
                } else {
                    echo '<div class="error"><span class="error-icon"></span>' . 'Registration Error' . "</div>";
                }
            } else {
                echo '<div class="error"><span class="error-icon"></span>' . 'CV file upload failed. Only PDF allowed.' . "</div>";
            }
            }
        }
    }
    ?>

    <h1>Internship Program Registration</h1>
    <form action="Register.php" method="post" novalidate enctype="multipart/form-data">
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
            <label for="mobile_number">Mobile Number:</label>
            <input type="text" name="mobile_number" value="<?php echo isset($_POST['mobile_number']) ? htmlspecialchars($_POST['mobile_number']) : ''; ?>">
            <?php displayError("mobile_number"); ?>
        </div>
        <div class="input-group">
            <label for="university">University:</label>
            <input type="text" name="university" value="<?php echo isset($_POST['university']) ? htmlspecialchars($_POST['university']) : ''; ?>">
            <?php displayError("university"); ?>
        </div>
        <div class="input-group">
            <label for="major">Major:</label>
            <input type="text" name="major" value="<?php echo isset($_POST['major']) ? htmlspecialchars($_POST['major']) : ''; ?>">
            <?php displayError("major"); ?>
        </div>
        <div class="input-group">
            <label for="graduation_date">Graduation Date:</label>
            <input type="date" name="graduation_date" value="<?php echo isset($_POST['graduation_date']) ? htmlspecialchars($_POST['graduation_date']) : ''; ?>">
            <?php displayError("graduation_date"); ?>
        </div>
        <label for="CV">CV:</label>
        <div class="file-upload">
            <input type="file" id="cv" name="cv" class="input-file">
            <label for="cv" class="file-button">
            <span class="file-icon"><i class="fas fa-cloud-upload-alt"></i></span>
            <span class="file-text">Choose a file</span>
            </label>
        </div>
            <p id="file-name" class="file-name">No file chosen</p>
            <?php displayError("cv"); ?>

        <input type="hidden" name="program_id" value="<?php echo isset($_SESSION['programId']) ? $_SESSION['programId'] : ''; ?>">
        <button type="submit">Register</button>
    </form>

    <div class="modern-link-container">
    <a class="modern-link" href="Login.php">Already have an account?</a>
    <span class="link-text">or</span>
    <a class="modern-link" href="../Internship/Internship.php#prog">Go to programs</a>
    </div>

</div>

<?php
    include '../Footer.php';
    mysqli_close($conn);
?>