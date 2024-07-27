$(document).ready(function() {
    const navbar = document.querySelector('.navbar');
    const navLinks = document.querySelector('.nav-links');
    const toggleBtn = document.querySelector('.toggle-btn');

    // Smooth scrolling when clicking on links
    $(".nav-links a, .smooth a").on("click", function(event) {
        const hash = this.hash;
        if (hash) {
            event.preventDefault();
            $("html, body").animate({
                scrollTop: $(hash).offset().top
            }, 800, function() {
                window.location.hash = hash;
                toggleNav();
            });
            $(this).blur(); // Remove focus from the clicked link
        }
    });

    // Expands programs
$(".expand-btn").on("click", function() {
    const programDetails = $(this).next(".program-details");
    programDetails.slideToggle(300);
    
    // Toggle the clicked class on the button
    $(this).toggleClass("clicked");
});

    // Function to toggle the 'scrolled' class based on scroll position
    function toggleNavbarBackground() {
        navbar.classList.toggle('scrolled', window.scrollY > 100);
    }

    // Function to toggle the responsive navigation
    function toggleNav() {
        navbar.classList.toggle('active');
        navLinks.classList.toggle('active');
    }

    // Attach the scroll event listener
    window.addEventListener('scroll', toggleNavbarBackground);

    // Event listener for the toggle button
    toggleBtn.addEventListener('click', toggleNav);

    // Close the navigation when a link is clicked (for smaller screens)
    navLinks.addEventListener('click', (event) => {
        if (event.target.tagName === 'A' && !event.target.classList.contains('dropdown-toggle')) {
            toggleNav();
        }
    });

    // Trigger the function once to set the initial state
    toggleNavbarBackground();

    $('#cv').on('change', function() {
        const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
        $('#file-name').text(fileName);
        console.log('File selected:', fileName);
    });

});

function scrollToElement(pageLocation, elementId) {
    const url = `${pageLocation}#${elementId}`;
    window.location.href = url;
}


function openConfirmationModal(itemId, itemTitle, redirectUrl) {
    const modal = document.getElementById('confirmationModal');
    modal.style.display = 'block';

    // Set the item title in the modal header
    document.querySelector('.modal-content h2').textContent = `Confirmation - ${itemTitle}`;

    // Handle Yes button click
    document.getElementById('confirmDelete').addEventListener('click', function() {
        // Redirect to the specified URL when confirmed
        window.location.href = redirectUrl;
    });

    // Handle No button click
    document.getElementById('cancelDelete').addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Handle modal close button click (X)
    document.querySelector('.close').addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Close the modal when the user clicks anywhere outside of it
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}


let selectedInstructors = []; // Array to store selected instructor names
let selectedInstructorIds = []; // Array to store corresponding instructor IDs

function toggleDropdown() {
    var dropdown = document.getElementById("instructorsDropdown");
    dropdown.classList.toggle("active");
}

function addInstructor(instructorId, instructorName) {
    if (instructorId && !selectedInstructorIds.includes(instructorId)) {
        selectedInstructorIds.push(instructorId);
        selectedInstructors.push(instructorName);
        updateSelectedInstructors();
    }
}

function removeInstructor(instructorId) {
    const index = selectedInstructorIds.indexOf(instructorId);
    if (index !== -1) {
        selectedInstructorIds.splice(index, 1);
        selectedInstructors.splice(index, 1);
        updateSelectedInstructors();
    }
}

function updateSelectedInstructors() {
    const selectedInstructorsContainer = document.getElementById("selectedInstructors");
    selectedInstructorsContainer.innerHTML = "";
    selectedInstructors.forEach((instructorName, index) => {
        const instructorTag = document.createElement("div");
        instructorTag.classList.add("instructor-tag");
        instructorTag.textContent = instructorName;
        instructorTag.addEventListener("click", () => {
            removeInstructor(selectedInstructorIds[index]);
        });
        selectedInstructorsContainer.appendChild(instructorTag);
    });

    // Update the hidden input field with selected IDs
    prepareInstructors();
}

function prepareInstructors() {
    // Update the hidden input field with the list of instructor IDs as a comma-separated string
    var instructorsField = document.getElementById('instructorsField');
    instructorsField.value = JSON.stringify(selectedInstructorIds);
}

function filterInstructors() {
    var input, filter, dropdown, options, i;
    input = document.getElementById('instructorSearch');
    filter = input.value.toUpperCase();
    dropdown = document.getElementById("instructorsDropdown");
    options = dropdown.getElementsByClassName('dropdown-option');
    for (i = 0; i < options.length; i++) {
        txtValue = options[i].textContent || options[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            options[i].style.display = "";
        } else {
            options[i].style.display = "none";
        }
    }
}

function downloadCV(event, cvPath) {
    // Prevent the default action
    event.preventDefault();

    // Extract the filename from the cvPath
    var filename = cvPath.split('/').pop();

    // Construct the URL for downloading the CV
    const downloadUrl = 'http://localhost//Hassan%20Khadra%20-%20IDS%20Website/Interns%20CVs/' + encodeURIComponent(filename);

    // Create a new XMLHttpRequest
    var xhr = new XMLHttpRequest();
    xhr.open('GET', downloadUrl, true);
    xhr.responseType = 'blob';

    xhr.onload = function () {
        if (this.status === 200) {
            var blob = new Blob([this.response], { type: 'application/pdf' });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            link.click();
        }
    };

    xhr.send();
}



// JavaScript
function toggleEdit(button) {
    const inputField = button.parentElement.querySelector(".edit-input");
    const saveButton = button.parentElement.querySelector(".save-btn");
    const editButton = button;

    if (inputField.style.display === "none" || inputField.style.display === "") {
        // Input field should be visible
        inputField.style.display = "inline-block";
        // Save button should be visible if it exists
        if (saveButton) {
            saveButton.style.display = "inline-block";
            editButton.style.display = "none";
        }
    } else {
        // Input field should be hidden
        inputField.style.display = "none";
        // Save button should be hidden if it exists
        if (saveButton) {
            saveButton.style.display = "none";
        }
        editButton.style.display = "inline-block";
    }
}


function saveAssessmentLink(button) {
    const inputField = button.parentElement.querySelector(".edit-input");
    const assessmentLink = inputField.value;
    const programId = button.getAttribute("data-program-id");

    // Call the updateExamLink function with the necessary parameters
    updateExamLink(programId, assessmentLink);

    // Hide the input field and Save button again
    inputField.style.display = "none";
    button.style.display = "none";

    // Show the "Edit" button again
    const editButton = button.parentElement.querySelector(".edit-btn");
    editButton.style.display = "inline-block";
}

function updateExamLink(programId, assessmentLink) {
    // Create an AJAX request to a PHP script that will update the exam link
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../UpdateLink.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("action=updateExamLink&programId=" + programId + "&assessmentLink=" + encodeURIComponent(assessmentLink));
}

