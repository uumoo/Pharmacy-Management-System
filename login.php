<?php
// Database connection details
$servername = "localhost"; // Change this if your database is hosted elsewhere
$username = "root"; // Your database username
$password = ""; // Your database password, leave it empty if no password is set
$dbname = "pharma"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details from the database based on username and password
$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM user_info WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // User found, fetch user type
    $row = $result->fetch_assoc();
    $userType = $row['user_type'];
    $_SESSION['username'] = $row['username'];

    // Redirect based on user type
    if ($userType == 'customer') {
        header("Location: homepage1.php");
        exit();
    } elseif ($userType == 'supplier') {
        header("Location: homepage2.php");
        exit();
    }
} else {
    // User not found or incorrect credentials
    echo "Invalid username or password";
}

$conn->close();
?>
