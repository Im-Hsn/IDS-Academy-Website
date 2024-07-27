<?php
include 'DB.php';

// Get the action from the POST data
$action = $_POST['action'];

// Call the appropriate function based on the action
if ($action == 'updateExamLink') {
  // Get the program ID and assessment link from the POST data
  $programId = $_POST['programId'];
  $assessmentLink = $_POST['assessmentLink'];

  // Call the updateExamLink function with these parameters
  $result = updateExamLink($conn, $programId, $assessmentLink);

  // Send a response back to JavaScript
  echo json_encode(array('success' => $result));
}
?>
