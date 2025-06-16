<?php
// Database connection
$servername = "localhost"; // Host name
$username = "root";        // Database username
$password = "";            // Database password
$dbname = "petshop";       // Database name

// Establish connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Database connected successfully!";

// Handle user actions when the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Determine which form is being submitted
    if (isset($_POST["register"])) {
        registerUser($conn);
    } elseif (isset($_POST["login"])) {
        loginUser($conn);
    } elseif (isset($_POST["adopt"])) {
        processAdoptionForm($conn);
    } elseif (isset($_POST["medical"])) {
        processMedicalForm($conn);
    } elseif (isset($_POST["volunteer"])) {
        processVolunteerForm($conn);
    }
}

// User Registration
function registerUser($conn) {
    $username = $conn->real_escape_string($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT); // Secure password hash

    // Using prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password); // "ss" indicates two string parameters

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// User Login
function loginUser($conn) {
    $username = $conn->real_escape_string($_POST["username"]);
    $password = $_POST["password"];

    // Query to find the user by username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and password matches
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            // Login successful
            echo json_encode(["success" => true]);
        } else {
            // Invalid password
            echo json_encode(["success" => false, "message" => "Invalid password"]);
        }
    } else {
        // User not found
        echo json_encode(["success" => false, "message" => "User not found"]);
    }

    $stmt->close();
}

// Process Adoption Form
function processAdoptionForm($conn) {
    $name = $conn->real_escape_string($_POST["name"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $pet_preference = $conn->real_escape_string($_POST["pet_preference"]);

    $stmt = $conn->prepare("INSERT INTO adoption_form (name, email, pet_preference) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $pet_preference);

    if ($stmt->execute()) {
        echo "Adoption form submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Process Medical Assistance Form
function processMedicalForm($conn) {
    $name = $conn->real_escape_string($_POST["name"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $pet_details = $conn->real_escape_string($_POST["pet-details"]);
    $urgency = $conn->real_escape_string($_POST["urgency"]);

    $stmt = $conn->prepare("INSERT INTO medical_form (name, email, pet_details, urgency) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $pet_details, $urgency);

    if ($stmt->execute()) {
        echo "Medical assistance request submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Process Volunteer Registration Form
function processVolunteerForm($conn) {
    $name = $conn->real_escape_string($_POST["name"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $availability = $conn->real_escape_string($_POST["availability"]);
    $skills = $conn->real_escape_string($_POST["skills"]);

    $stmt = $conn->prepare("INSERT INTO volunteer_form (name, email, availability, skills) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $availability, $skills);

    if ($stmt->execute()) {
        echo "Volunteer registration submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Close the connection
$conn->close();
?>