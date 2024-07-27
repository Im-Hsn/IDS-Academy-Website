# IDS Academy Website Project

## Table of Contents
- [Introduction](#introduction)
- [Website Functionality Overview](#website-functionality-overview)
- [Methodology and Technologies Used](#methodology-and-technologies-used)
  - [Methodology](#methodology)
  - [Technologies Used](#technologies-used)
- [Summary](#summary)
- [Challenges Faced](#challenges-faced)
  - [JavaScript Proficiency](#javascript-proficiency)
  - [Complex Database Functions](#complex-database-functions)
  - [Responsive Design Complexity](#responsive-design-complexity)
- [Conclusion](#conclusion)
- [Recordings](#recordings)

## Introduction
This document serves the purpose of providing a comprehensive overview of the IDS Academy website project, highlighting its key features, objectives, and the challenges encountered during its development. The IDS Academy website is designed with the primary aim of delivering a user-friendly interface tailored to the needs of IDS interns, employees, and administrators. Its core functionalities encompass course application and instruction, program management, and seamless data storage within the database. Within the following sections, we will delve into the intricacies of this project, shedding light on both its successes and the hurdles I have encountered during my development journey.

## Website Functionality Overview
The IDS Academy website is designed to serve IDS interns, employees, and administrators. It offers the following core functionalities:

1. **User Registration and Authentication:**
    - Users can login as either interns or employees.
    - User data includes full name, email, password, and role.
2. **Program Management:**
    - Admins can create and manage educational programs.
    - Program details include a title, description, start and end dates, maximum capacity, and a classroom code.
    - Admins can assign employees to programs who become instructors.
3. **Instructor Assignment:**
    - Admins can manage instructors.
    - Instructors are selected from the list of employees.
4. **Intern Application:**
    - Interns can view program details created by admins and stored in the database.
    - Interns can apply to specific programs by filling out their information and uploading a CV.
    - An intern can apply for another program if they already applied for one by logging in.
5. **Admin Review and Acceptance:**
    - Admins can review intern applications.
    - Admins have access to interns’ CVs.
    - Admins can either accept or reject interns for specific programs.
6. **Program Capacity Management:**
    - The system tracks the current capacity of programs, ensuring they do not exceed the maximum capacity.
7. **Content Management:**
    - Pages can be marked as active or inactive and prioritized.
8. **Lookup Management:**
    - A lookup system is in place to categorize items.
    - Employees can create and manage lookup items.
9. **Certificate Management:**
    - Certificates can be issued to interns.
    - Certificates are associated with specific interns and include a certificate file.
10. **Assessment and Exam Links:**
    - Instructors can associate exam links with specific programs.

## Methodology and Technologies Used

### Methodology
The development of the IDS Academy website followed a structured and iterative approach, combining elements of Agile development methodologies to ensure flexibility and adaptability throughout the project. This allowed for the ability to respond to requirements effectively.

1. **Agile Development Principles:** The project embraced Agile development principles, even in the absence of a dedicated team. This allowed for individual adaptability and incremental development.
2. **Iterative Development:** The development process was organized into iterative cycles to systematically enhance features and functionalities. Each iteration involved planning, coding, testing, and refinement, ensuring continuous progress.

### Technologies Used
The IDS Academy website leverages a robust stack of technologies to deliver its features and functionality seamlessly. Below are the core technologies employed in the development process:
1. **HTML (HyperText Markup Language):** Used to structure the website's content and layout.
2. **CSS (Cascading Style Sheets):** Employed for styling and presentation, ensuring a visually appealing user interface.
3. **Bootstrap:** Utilized to enhance the website's responsiveness and provide a consistent and mobile-friendly design.
4. **JavaScript:** Employed for client-side interactivity and to enhance the user experience.
5. **jQuery:** A JavaScript library used to simplify DOM manipulation and event handling.
6. **MySQL:** Chosen as the relational database management system (RDBMS) to store and manage data efficiently.
7. **phpMyAdmin:** A web-based database management tool used to interact with and manage the MySQL database.
8. **XAMPP (Apache + MySQL + PHP + Perl):** The XAMPP development environment facilitated local development and testing of the website.
9. **Apache Web Server:** Utilized as the web server to serve web content and PHP scripts.
10. **PHP (Hypertext Preprocessor):** The core server-side scripting language used for dynamic content generation, user authentication, and database interactions.

## Summary
This technology stack was thoughtfully chosen to provide a robust and scalable foundation for the IDS Academy website. While the project was executed independently, the methodology and technology selection ensured a systematic development process and a user-friendly website tailored to the needs of IDS interns, employees, and administrators.

## Challenges Faced
During the development of the IDS Academy website, several challenges were encountered. These challenges, although formidable, ultimately contributed to my growth as a developer and enriched the project's final outcome.

### JavaScript Proficiency
- **Challenge:** One of the initial challenges was the necessity for JavaScript for my design choice, a technology I was less familiar with. It was essential for implementing features like smooth scrolling and triggering the CV download function for admins.
- **Impact:** Initially, this challenge resulted in slower progress and a learning curve. However, with determination and continuous learning, I gradually improved my JavaScript skills. As a result, I successfully incorporated the required functionality, enhancing the user experience and meeting project goals.

### Complex Database Functions
- **Challenge:** The project required a range of complex database functions, such as user authentication and intricate joins involving multiple tables, as seen in the case of the inner join between the instructors, program instructors, and programs tables.
- **Impact:** Dealing with complex database operations posed a significant challenge. It required meticulous planning and execution. While it did extend the development timeline, it also enhanced the database's efficiency and data retrieval capabilities. Ultimately, it ensured the website's robust functionality.

### Responsive Design Complexity
- **Challenge:** Implementing responsive design for mobile and tablet users presented difficulties, especially as the number of pages increased.
- **Impact:** Initially, this challenge led to design inconsistencies and usability issues on mobile and tablet devices. However, through careful design iteration and utilizing Bootstrap, I successfully achieved responsive design across the website. This improvement significantly enhanced the user experience, making the website accessible and user-friendly on various screen sizes.

## Conclusion
In conclusion, the IDS Academy website project represents a successful endeavor in creating a user-friendly platform for IDS interns, employees, and administrators. Despite initial challenges, including learning JavaScript, managing complex database operations, and implementing responsive design, the project has delivered a robust solution.

The adoption of Agile principles, combined with a well-chosen technology stack, facilitated flexibility and adaptability throughout the development process. Overcoming these challenges not only enhanced the project but also contributed to personal and professional growth.

As the IDS Academy website continues to evolve, it is poised to play a pivotal role in program management and the overall learning experience. This project serves as a testament to dedication and development skills, with the potential for future enhancements and innovations.

## Recordings

### Home Page
[![Home Page Recording](https://img.shields.io/badge/Watch-Home%20Page%20Recording-brightgreen)](https://github.com/Im-Hsn/IDS-Academy-Website/recordings/home.mp4)

### Admin Page
[![Admin Page Recording](https://img.shields.io/badge/Watch-Admin%20Page%20Recording-brightgreen)](https://github.com/Im-Hsn/IDS-Academy-Website/recordings/admin.mp4)

### Applicant Page
[![Applicant Page Recording](https://img.shields.io/badge/Watch-Applicant%20Page%20Recording-brightgreen)](https://github.com/Im-Hsn/IDS-Academy-Website/recordings/applicant.mp4)
