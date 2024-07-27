<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "IDS Academy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to retrieve program information along with instructor names and assessment exam link
function getProgramsInfo($conn) {
    $sql = "SELECT p.`Program ID`, p.`Title`, p.`Description`, p.`Start Date`, p.`End Date`, p.`Max Capacity`, p.`Current Capacity`, p.`Classroom Code`, GROUP_CONCAT(e.`Full Name` SEPARATOR ', ') as `Instructors`, COALESCE(ael.`Exam Link`, '') as `Link`
    FROM `Programs` p
    LEFT JOIN `Program_Instructors` pi ON p.`Program ID` = pi.`Program ID`
    LEFT JOIN `Employee` e ON pi.`Instructor ID` = e.`Employee ID`
    LEFT JOIN `Assessment_Exam_Links` ael ON p.`Program ID` = ael.`Program ID`
    GROUP BY p.`Program ID`";

    $result = $conn->query($sql);

    if (!$result) {
        // Handle any errors
        return false;
    } else {
        // Initialize an array to store program information
        $programs = array();

        // Check if there are rows in the result
        if ($result->num_rows > 0) {
            // Fetch and store program information in the array
            while ($row = $result->fetch_assoc()) {
                $programs[] = $row;
            }
            return $programs;
        } else {
            // No programs found
            return false;
        }
    }
}


function getEmployeePrograms($conn, $employeeId) {
    // SQL query to retrieve programs associated with the employee
    $sql = "SELECT p.`Program ID`, p.`Title`, p.`Description`, p.`Start Date`, p.`End Date`, p.`Max Capacity`, p.`Current Capacity`, p.`Classroom Code`, GROUP_CONCAT(e.`Full Name` SEPARATOR ', ') as `Instructors`, ael.`Exam Link` as `Link`
            FROM `Programs` p
            LEFT JOIN `Program_Instructors` pi ON p.`Program ID` = pi.`Program ID`
            LEFT JOIN `Employee` e ON pi.`Instructor ID` = e.`Employee ID`
            LEFT JOIN `Assessment_Exam_Links` ael ON p.`Program ID` = ael.`Program ID`
            WHERE e.`Employee ID` = $employeeId
            GROUP BY p.`Program ID`";

    $result = $conn->query($sql);

    if (!$result) {
        // Handle any errors
        return false;
    }

    // Initialize an array to store program information
    $programs = array();

    if ($result->num_rows > 0) {
        // Fetch and store program information in the array
        while ($row = $result->fetch_assoc()) {
            $programs[] = $row;
        }
        return $programs;
    } else {
        // Employee doesn't have any programs
        return false;
    }
}


// Function to get program information by program ID and store it in session variables
function getProgramInfoAndStoreInSession($conn, $programId) {
    $stmt = $conn->prepare("SELECT `Title`, `Description` FROM `Programs` WHERE `Program ID` = ?");
    $stmt->bind_param("i", $programId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return false;
    } else {
        $row = $result->fetch_assoc();
        // Store program information in session variables
        $_SESSION['programId'] = $programId;
        $_SESSION['programTitle'] = $row["Title"];
        $_SESSION['programDescription'] = $row["Description"];
        
        return true; // Program information successfully stored
    }
}

function getInternPrograms($conn, $internId) {
    $programs = array();

    // Prepare and execute the SQL statement to retrieve programs for the given intern ID
    $sql = "SELECT p.`Program ID`, p.`Title`, p.`Description`, p.`Start Date`, p.`End Date`, p.`Max Capacity`, p.`Current Capacity`, p.`Classroom Code`, pi.`Application Status`, GROUP_CONCAT(e.`Full Name` SEPARATOR ', ') as `Instructors`, ael.`Exam Link` as `Link`
            FROM `Programs` p
            INNER JOIN `Program_Interns` pi ON p.`Program ID` = pi.`Program ID`
            LEFT JOIN `Program_Instructors` pin ON p.`Program ID` = pin.`Program ID`
            LEFT JOIN `Employee` e ON pin.`Instructor ID` = e.`Employee ID`
            LEFT JOIN `Assessment_Exam_Links` ael ON p.`Program ID` = ael.`Program ID`
            WHERE pi.`Intern ID` = ?
            GROUP BY p.`Program ID`";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $internId);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();

    // Loop through the result set and store program information in the array
    while ($row = $result->fetch_assoc()) {
        $program = array(
            'Program ID' => $row['Program ID'],
            'Title' => $row['Title'],
            'Description' => $row['Description'],
            'Start Date' => $row['Start Date'],
            'End Date' => $row['End Date'],
            'Max Capacity' => $row['Max Capacity'],
            'Current Capacity' => $row['Current Capacity'],
            'Classroom Code' => $row['Classroom Code'],
            'Application Status' => $row['Application Status'],
            'Instructors' => $row['Instructors'],
            'Link' => $row['Link'],
        );
        $programs[] = $program;
    }
    return $programs;
}









function internExists($conn, $internId) {
    // Prepare and execute the SQL statement to check if the intern exists by ID
    $sql = "SELECT COUNT(*) AS count FROM `Interns` WHERE `Intern ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $internId);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // If the count is greater than 0, the intern with the provided ID exists
    return $row['count'] > 0;
}

function insertIntern($conn, $fullName, $email, $hashedPassword, $mobileNumber, $university, $major, $graduationDate, $cvLocation, $programId)
{
    try {
        // Insert intern data into the database and get the intern ID
        $internId = insertData($conn, $fullName, $email, $hashedPassword, $mobileNumber, $university, $major, $graduationDate, $cvLocation);

        if (!$internId) {
            throw new Exception('Failed to insert intern data');
        }

        // Enroll the intern in the program and update the current capacity
        $enrolled = enrollIntern($conn, $programId, $internId);

        if (!$enrolled) {
            throw new Exception('Failed to enroll intern in the program');
        }

        // Increment the current capacity
        incrementCurrentCapacity($conn, $programId);

        return true; // Success
    } catch (Exception $e) {
        return false; // Failure
    }
}

// Function to check if the program is full
function isProgramFull($conn, $programId)
{
    $sql = "SELECT `Max Capacity`, `Current Capacity` FROM `Programs` WHERE `Program ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $programId);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $maxCapacity = $row['Max Capacity'];
        $currentCapacity = $row['Current Capacity'];

        // Check if the program is full
        if ($currentCapacity >= $maxCapacity) {
            return true; // Program is full
        }
    }
    return false; // Program is not full
}

// Function to increment the current capacity of a program
function incrementCurrentCapacity($conn, $programId)
{
    $sql = "UPDATE `Programs` SET `Current Capacity` = `Current Capacity` + 1 WHERE `Program ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $programId);
    $stmt->execute();
}

// Function to insert intern data into the database
function insertData($conn, $fullName, $email, $hashedPassword, $mobileNumber, $university, $major, $graduationDate, $cvLocation)
{
    try {
        // Prepare and execute the SQL statement to insert data into the Interns table
        $sql = "INSERT INTO `Interns` (`Full Name`, `Email`, `Password`, `Mobile Number`, `University`, `Major`, `Graduation Date`, `CV`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $fullName, $email, $hashedPassword, $mobileNumber, $university, $major, $graduationDate, $cvLocation);

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert intern data.");
        }
        return $conn->insert_id; // Return the ID of the newly inserted intern

    } catch (Exception $e) {
        return false;
    }
}






// Function to enroll the intern in a program and check if the program is full
function enrollIntern($conn, $programId, $internId)
{
    try {
        // Prepare and execute the SQL statement to enroll the intern in a program
        $enrollSql = "INSERT INTO `Program_Interns` (`Program ID`, `Intern ID`) VALUES (?, ?)";
        $enrollStmt = $conn->prepare($enrollSql);
        $enrollStmt->bind_param("ii", $programId, $internId);

        if (!$enrollStmt->execute()) {
            throw new Exception('Failed to enroll intern in the program');
        }

        // Increment the current capacity
        incrementCurrentCapacity($conn, $programId);

        return true;
    } catch (Exception $e) {
        // Re-throw the exception for higher-level handling, or log it
        throw $e;
    }
}

function InternEnrolled($conn, $email, $programId)
{
    // Prepare and execute the SQL statement to check if the intern is enrolled in the program
    $sql = "SELECT COUNT(*) AS count FROM `Program_Interns` WHERE `Intern ID` = (SELECT `Intern ID` FROM `Interns` WHERE `Email` = ?) AND `Program ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $programId);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Check if the intern is enrolled
    if ($row["count"] > 0) {
        return true;
    } else {
        return false;
    }
}









function getPendingInternsInfo($conn) {
    // Prepare and execute the SQL statement to retrieve intern information with "Pending" status
    $sql = "SELECT i.*, pi.`Application Status`, p.`Title` as `Program Name`, pi.`Program ID`
            FROM `Interns` i
            INNER JOIN `Program_Interns` pi ON i.`Intern ID` = pi.`Intern ID`
            INNER JOIN `Programs` p ON pi.`Program ID` = p.`Program ID`
            WHERE pi.`Application Status` = 'Pending'";
    
    $result = $conn->query($sql);

    if (!$result) {
        // Handle any errors
        return false;
    }

    // Check if there are no pending interns
    if ($result->num_rows === 0) {
        return false;
    }
    $internsInfo = array();

    // Fetch the result
    while ($row = $result->fetch_assoc()) {
        $internInfo = array(
            'Intern ID' => $row['Intern ID'],
            'Full Name' => $row['Full Name'],
            'Email' => $row['Email'],
            'Mobile Number' => $row['Mobile Number'],
            'University' => $row['University'],
            'Major' => $row['Major'],
            'Graduation Date' => $row['Graduation Date'],
            'CV' => $row['CV'],
            'Application Status' => $row['Application Status'],
            'Program Name' => $row['Program Name'],
            'Program ID' => $row['Program ID'] // Include Program ID in the result
        );
        $internsInfo[] = $internInfo;
    }
    return $internsInfo;
}

function updateInternApplicationStatus($conn, $internId, $programId, $applicationStatus) {
    try {
        // Prepare and execute the SQL statement to update the intern's application status
        $sql = "UPDATE `Program_Interns` SET `Application Status` = ? WHERE `Intern ID` = ? AND `Program ID` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $applicationStatus, $internId, $programId);

        if ($stmt->execute()) {
            return true; // Application status updated successfully
        } else {
            return false; // Application status update failed
        }
    } catch (Exception $e) {
        return false; // Error occurred during the update
    }
}

function getInternIdByEmail($conn, $email)
{
    // Prepare and execute the SQL statement to retrieve the intern ID by email
    $sql = "SELECT `Intern ID` FROM `Interns` WHERE `Email` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();
    
    // Check if a matching intern record was found
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["Intern ID"];
    } else {
        return false; // Intern not found
    }
}

function getInternInformation($conn, $internId) {
    $internInfo = array();

    $sql = "SELECT `Full Name`, `Email`, `Mobile Number`, `University`, `Major`, `Graduation Date`
            FROM `Interns`
            WHERE `Intern ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $internId);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();

    // Check if a matching intern record was found
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Store intern information in the array
        $internInfo['Full Name'] = $row['Full Name'];
        $internInfo['Email'] = $row['Email'];
        $internInfo['Mobile Number'] = $row['Mobile Number'];
        $internInfo['University'] = $row['University'];
        $internInfo['Major'] = $row['Major'];
        $internInfo['Graduation Date'] = $row['Graduation Date'];

        return $internInfo; // Return the intern's information array
    } else {
        return false; // Intern not found
    }
}

// Function to fetch instructor data from the database
function fetchInstructorsFromDatabase($conn) {
    $instructors = array();

    $sql = "SELECT `Employee ID`, `Full Name` FROM `Employee` WHERE `Role` IN ('Employee', 'Instructor')";
    $result = $conn->query($sql);

    if (!$result) {
        // Handle any errors
        return $instructors;
    }
    // Fetch the result
    while ($row = $result->fetch_assoc()) {
        $instructors[] = array(
            'id' => $row['Employee ID'],
            'name' => $row['Full Name']
        );
    }
    return $instructors;
}

function employeeExists($conn, $fullName, $email, $Id) {
    if (isset($Id)) {
        // If Id is set, perform the check using the Id
        $sql = "SELECT COUNT(*) AS count FROM `Employee` WHERE `Employee ID` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $Id);
        $stmt->execute();
    } else {
        // If Id is not set, perform the check using full name or email
        $sql = "SELECT COUNT(*) AS count FROM `Employee` WHERE `Full Name` = ? OR `Email` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $fullName, $email);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // If the count is greater than 0, an employee with the same full name or email (or Id) exists
    return $row['count'] > 0;
}

function getAllEmployeesWithPrograms($conn) {
    $employees = array();

    // Prepare and execute the SQL statement to retrieve all employees
    $sql = "SELECT `Employee ID`, `Full Name`, `Email`, `Role`, `Major`, `Creation Date` FROM `Employee`";
    $result = $conn->query($sql);

    if (!$result) {
        // Handle any errors
        return $employees;
    }

    // Fetch the result
    while ($row = $result->fetch_assoc()) {
        // Check if the role is "Admin"
        if ($row['Role'] !== 'Admin') {
            $employeeId = $row['Employee ID'];

            // Prepare and execute the SQL statement to retrieve program names for the employee
            $programSql = "SELECT p.`Title`
                FROM `Programs` p
                INNER JOIN `Program_Instructors` pi ON p.`Program ID` = pi.`Program ID`
                WHERE pi.`Instructor ID` = ?";
            $programStmt = $conn->prepare($programSql);
            $programStmt->bind_param("i", $employeeId);
            $programStmt->execute();

            $programResult = $programStmt->get_result();

            $programNames = array();
            while ($programRow = $programResult->fetch_assoc()) {
                $programNames[] = $programRow['Title'];
            }

            // Add employee data and associated program names to the employees array
            $employees[$employeeId] = array(
                'Employee ID' => $employeeId,
                'Full Name' => $row['Full Name'],
                'Email' => $row['Email'],
                'Role' => $row['Role'],
                'Major' => $row['Major'],
                'Creation Date' => $row['Creation Date'],
                'Programs' => $programNames,
            );
        }
    }

    return $employees;
}












function authenticateIntern($conn, $email, $password)
{
    // Prepare and execute the SQL statement to retrieve the user by email
    $sql = "SELECT `Password`, `Intern ID` FROM `Interns` WHERE `Email` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    // Fetch the result
    $result = $stmt->get_result();

    // Check if a matching user record was found
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['Password'];
        $internId = $row['Intern ID'];

        // Verify the provided password against the stored password hash
        if (password_verify($password, $storedPassword)) {
            // Password is correct, return the user's ID
            return $internId;
        }
    }
    // Authentication failed, return false
    return false;
}

function authenticateEmployee($conn, $email, $password)
{
    // Prepare and execute the SQL statement to retrieve the employee by email
    $sql = "SELECT `Employee ID`, `Full Name`, `Email`, `Role`, `Major`, `Creation Date` FROM `Employee` WHERE `Email` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();

    // Check if a matching employee record was found
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $employeeId = $row['Employee ID'];
        $fullName = $row['Full Name'];
        $role = $row['Role'];
        $major = $row['Major'];
        $creationDate = $row['Creation Date'];

        // Retrieve the stored password hash for the employee
        $sqlPassword = "SELECT `Password` FROM `Employee` WHERE `Employee ID` = ?";
        $stmtPassword = $conn->prepare($sqlPassword);
        $stmtPassword->bind_param("i", $employeeId);
        $stmtPassword->execute();
        $resultPassword = $stmtPassword->get_result();

        // Check if the employee record's password matches the provided password
        if ($resultPassword->num_rows === 1) {
            $passwordRow = $resultPassword->fetch_assoc();
            $storedPasswordHash = $passwordRow['Password'];

            if (password_verify($password, $storedPasswordHash)) {
                // Password is correct, return employee information as an array
                $employeeInfo = array(
                    'Employee ID' => $employeeId,
                    'Full Name' => $fullName,
                    'Email' => $email,
                    'Role' => $role,
                    'Major' => $major,
                    'Creation Date' => $creationDate,
                );
                return $employeeInfo;
            }
        }
    }
    // Authentication failed, return false
    return false;
}






function addEmployee($conn, $fullName, $email, $password, $role, $major) {
    // Hash the provided password before storing it in the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute the SQL statement to insert an employee into the `Employee` table
    $sql = "INSERT INTO `Employee` (`Full Name`, `Email`, `Password`, `Role`, `Major`, `Creation Date`)
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $fullName, $email, $hashedPassword, $role, $major);

    if ($stmt->execute()) {
        return true; // Employee added successfully
    } else {
        return false; // Employee insertion failed
    }
}

function addProgram($conn, $title, $description, $startDate, $endDate, $maxCapacity, $classroomCode, $instructorIds) {
    try {
        // Start a transaction to ensure data integrity
        $conn->begin_transaction();

        // Step 1: Loop through each instructor ID and update their role to "Instructor" if they have the role "Employee"
        foreach ($instructorIds as $instructorId) {
            $instructorExists = updateEmployeeRole($conn, $instructorId);

            if (!$instructorExists) {
                throw new Exception('Instructor does not exist or role update failed');
            }
        }

        // Step 2: Insert the program into the `Programs` table
        $programId = insertProgram($conn, $title, $description, $startDate, $endDate, $maxCapacity, $classroomCode);

        if (!$programId) {
            throw new Exception('Failed to insert program');
        }

        // Step 3: Add each instructor to the `Program_Instructors` table
        foreach ($instructorIds as $instructorId) {
            $instructorAdded = addInstructorToProgram($conn, $programId, $instructorId);

            if (!$instructorAdded) {
                throw new Exception('Failed to add instructor to program');
            }
        }
        // Commit the transaction
        $conn->commit();
        return true; // Program added successfully

    } catch (Exception $e) {
        // If any step fails, rollback the transaction
        $conn->rollback();
        return 'Failed to add program: ' . $e->getMessage();
    }
}

function isProgramExistsByClassroomCode($conn, $classroomCode) {
    // Prepare the SQL statement to check if a program exists with the given classroom code
    $sql = "SELECT COUNT(*) AS programCount FROM `Programs` WHERE `Classroom Code` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $classroomCode);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();

    // Retrieve the program count from the result
    $row = $result->fetch_assoc();
    $programCount = $row['programCount'];

    // Check if a program with the given classroom code exists
    return $programCount > 0;
}

function updateEmployeeRole($conn, $employeeId) {
    // Check if the employee exists and has the role "Employee"
    $sql = "SELECT `Role` FROM `Employee` WHERE `Employee ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $role = $row['Role'];

        // If the role is "Employee," update it to "Instructor"
        if ($role === "Employee") {
            $updateSql = "UPDATE `Employee` SET `Role` = 'Instructor' WHERE `Employee ID` = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("i", $employeeId);

            if ($updateStmt->execute()) {
                return true; // Role updated successfully
            }
        } else {
            return true; // Instructor already has the role "Instructor"
        }
    }
    return false; // Employee does not exist or role update failed
}

function insertProgram($conn, $title, $description, $startDate, $endDate, $maxCapacity, $classroomCode) {
    // Prepare and execute the SQL statement to insert a program into the `Programs` table
    $sql = "INSERT INTO `Programs` (`Title`, `Description`, `Start Date`, `End Date`, `Max Capacity`, `Current Capacity`, `Classroom Code`)
            VALUES (?, ?, ?, ?, ?, 0, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $title, $description, $startDate, $endDate, $maxCapacity, $classroomCode);

    if ($stmt->execute()) {
        return $conn->insert_id; // Return the ID of the newly inserted program
    } else {
        return false; // Program insertion failed
    }
}

function programExists($conn, $title) {
    $sql = "SELECT COUNT(*) AS count FROM `Programs` WHERE `Title` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $title);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // If there is a program with the given title, return true, otherwise return false
    return $row['count'] > 0;
}

function addInstructorToProgram($conn, $programId, $instructorId) {
    // Prepare and execute the SQL statement to add an instructor to the `Program_Instructors` table
    $sql = "INSERT INTO `Program_Instructors` (`Program ID`, `Instructor ID`)
            VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $programId, $instructorId);

    return $stmt->execute();
}

function updateExamLink($conn, $programId, $assessmentLink) {
    // Check if the Program ID exists in the table
    $checkSql = "SELECT 1 FROM `Assessment_Exam_Links` WHERE `Program ID` = ?";
    $checkStmt = $conn->prepare($checkSql);

    if (!$checkStmt) {
        // Handle any errors in preparing the statement
        return false;
    }

    $checkStmt->bind_param("i", $programId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Program ID exists, so update the Exam Link
        $updateSql = "UPDATE `Assessment_Exam_Links` SET `Exam Link` = ? WHERE `Program ID` = ?";
        $updateStmt = $conn->prepare($updateSql);

        if (!$updateStmt) {
            // Handle any errors in preparing the update statement
            return false;
        }

        $updateStmt->bind_param("si", $assessmentLink, $programId);
        $result = $updateStmt->execute();
    } else {
        // Program ID does not exist, so insert a new row
        $insertSql = "INSERT INTO `Assessment_Exam_Links` (`Program ID`, `Exam Link`) VALUES (?, ?)";
        $insertStmt = $conn->prepare($insertSql);

        if (!$insertStmt) {
            // Handle any errors in preparing the insert statement
            return false;
        }

        $insertStmt->bind_param("is", $programId, $assessmentLink);
        $result = $insertStmt->execute();
    }
    return $result;
}



function deleteEmployeeById($conn, $employeeId) {
    try {
        // Start a transaction to ensure data integrity
        $conn->begin_transaction();

        // Step 1: Check if the employee exists and has the role "Instructor"
        $employeeRole = getEmployeeRoleById($conn, $employeeId);

        if ($employeeRole === 'Instructor') {
            // Step 2: Delete the employee's occurrences in the `Program_Instructors` table
            deleteEmployeeFromProgramInstructors($conn, $employeeId);
        }

        // Step 3: Delete the employee from the `Employee` table
        deleteEmployee($conn, $employeeId);

        // Commit the transaction
        $conn->commit();

        return true; // Employee deleted successfully
    } catch (Exception $e) {
        // If any step fails, rollback the transaction
        $conn->rollback();
        return 'Failed to delete employee: ' . $e->getMessage();
    }
}

function isOnlyInstructorForAProgram($conn, $instructorId) {
    // Prepare and execute the SQL statement to check if the instructor is the only instructor for any program
    $sql = "SELECT COUNT(*) AS count FROM `Program_Instructors` WHERE `Instructor ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $instructorId);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // If the count is 1, the instructor is the only instructor for a program
    return $row['count'] === 1;
}

function getEmployeeRoleById($conn, $employeeId) {
    // Prepare and execute the SQL statement to retrieve the role of the employee by ID
    $sql = "SELECT `Role` FROM `Employee` WHERE `Employee ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return $row['Role']; // Return the employee's role
    } else {
        return false; // Employee does not exist or role retrieval failed
    }
}

function deleteEmployeeFromProgramInstructors($conn, $employeeId) {
    // Prepare and execute the SQL statement to delete the employee from the `Program_Instructors` table
    $sql = "DELETE FROM `Program_Instructors` WHERE `Instructor ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
}

function deleteEmployee($conn, $employeeId) {
    // Prepare and execute the SQL statement to delete the employee from the `Employee` table
    $sql = "DELETE FROM `Employee` WHERE `Employee ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
}











function deleteProgramById($conn, $programId) {
    try {
        // Start a transaction to ensure data integrity
        $conn->begin_transaction();

        // Step 1: Get the list of instructor IDs associated with the program
        $instructorIds = getInstructorIdsForProgram($conn, $programId);

        // Step 2: Delete records from `Program_Instructors` associated with the program
        deleteProgramInstructors($conn, $programId);

        // Step 3: Delete records from `Program_Interns` associated with the program
        deleteProgramInterns($conn, $programId);

        // Step 4: Delete the program from the `Programs` table
        deleteProgram($conn, $programId);

        // Step 5: Check if each instructor is only instructing this program, and if so, update their role to "Employee"
        foreach ($instructorIds as $instructorId) {
            if (isOnlyInstructorForProgram($conn, $instructorId, $programId)) {
                updateInstructorRoleToEmployee($conn, $instructorId);
            }
        }

        // Commit the transaction
        $conn->commit();

        return true; // Program deleted successfully
    } catch (Exception $e) {
        // If any step fails, rollback the transaction
        $conn->rollback();
        return 'Failed to delete program';
    }
}

function getInstructorIdsForProgram($conn, $programId) {
    $instructorIds = array();

    // Prepare and execute the SQL statement to retrieve instructor IDs associated with the program
    $sql = "SELECT `Instructor ID` FROM `Program_Instructors` WHERE `Program ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $programId);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $instructorIds[] = $row['Instructor ID'];
    }

    return $instructorIds;
}

function isOnlyInstructorForProgram($conn, $instructorId, $programId) {
    // Prepare and execute the SQL statement to check if the instructor is only instructing this program
    $sql = "SELECT COUNT(*) AS count FROM `Program_Instructors` WHERE `Instructor ID` = ? AND `Program ID` != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $instructorId, $programId);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // If the count is 0, the instructor is only instructing this program
    return $row['count'] === 0;
}

function updateInstructorRoleToEmployee($conn, $instructorId) {
    // Prepare and execute the SQL statement to update the role of the instructor to "Employee"
    $sql = "UPDATE `Employee` SET `Role` = 'Employee' WHERE `Employee ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $instructorId);
    $stmt->execute();
}

function deleteProgramInstructors($conn, $programId) {
    $sql = "DELETE FROM `Program_Instructors` WHERE `Program ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $programId);
    $stmt->execute();
}

function deleteProgramInterns($conn, $programId) {
    $sql = "DELETE FROM `Program_Interns` WHERE `Program ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $programId);
    $stmt->execute();
}

function deleteProgram($conn, $programId) {
    $sql = "DELETE FROM `Programs` WHERE `Program ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $programId);
    $stmt->execute();
}








function handleFileUpload($email)
{
    // Define the directory to store CVs
    $cvDirectory = __DIR__ . '/Interns CVs/';
    
    // Create the 'Interns CVs' directory if it doesn't exist
    if (!is_dir($cvDirectory)) mkdir($cvDirectory, 0777, true);


    // Get the file extension of the uploaded CV
    $fileExtension = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);

    // Validate file type (e.g., allow only PDF files)
    $allowedExtensions = array("pdf");
    if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
        return false; // Invalid file type
    }

    // Generate a unique filename using the intern's email
    $cvFileName = $email . '.' . $fileExtension;

    // Define the target path for storing the CV
    $targetPath = $cvDirectory . $cvFileName;

    // Check if the file has been successfully uploaded and moved
    if (move_uploaded_file($_FILES['cv']['tmp_name'], $targetPath)) {
        return $targetPath; // Return the new file location
    } else {
        return false; // File upload failed
    }
}
?>