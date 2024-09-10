<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Assuming username is 'root'
$password = ""; // Assuming password is empty
$database = "pharma";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from SignUp.html form
$username = $_POST['username'];
$name = $_POST['name'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$email = $_POST['email'];
$phone_number = $_POST['phone'];
$password = $_POST['password'];
$user_type = "customer"; // Set user type as 'customer'

// Prepare SQL statement to insert data into user_info table
$sql = "INSERT INTO user_info (username, name, age, gender, email, phone_number, password, user_type) 
        VALUES ('$username', '$name', $age, '$gender', '$email', '$phone_number', '$password', '$user_type')";

// Execute SQL statement
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close database connection
$conn->close();
?>
