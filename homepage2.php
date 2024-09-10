<?php
session_start();

// Check if supplier is logged in, redirect to login page if not


// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pharma";

// Create connection
$connection = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to sanitize input
function sanitize($input) {
    global $connection;
    return mysqli_real_escape_string($connection, $input);
}

// Function to fetch order requests
function getOrderRequests() {
    global $connection;
    $sql = "SELECT * FROM orders WHERE statuss = 'pending'";
    $result = mysqli_query($connection, $sql);
    $orders = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
    }
    return $orders;
}

// Function to send order
function sendOrder($order_id) {
    global $connection;
    $sql = "UPDATE orders SET statuss = 'sent' WHERE id = '$order_id'";
    if (mysqli_query($connection, $sql)) {
        return true;
    } else {
        return false;
    }
}

// Process order sending
if (isset($_POST['send_order'])) {
    $order_id = $_POST['order_id'];
    if (sendOrder($order_id)) {
        echo "Order sent successfully.";
    } else {
        echo "Error sending order.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Homepage</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .logout {
            float: right;
            margin-top: -40px;
        }

        .logout a {
            color: #fff;
            text-decoration: none;
        }

        .order-requests {
            padding: 20px;
        }

        .order {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .order p {
            margin: 5px 0;
        }

        .order form {
            display: inline;
        }

        .order button {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 8px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }

        .order button:hover {
            background-color: #45a049;
        }

        p.no-requests {
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>
    <header>
        <h1>Supplier Homepage</h1>
        <div class="logout">
            <a href="supplier_logout.php">Logout</a>
        </div>
    </header>

    <div class="order-requests">
        <h2>Order Requests</h2>
        <?php
        $orderRequests = getOrderRequests();
        if (!empty($orderRequests)) {
            foreach ($orderRequests as $order) {
                echo '<div class="order">';
                echo '<p>Order ID: ' . $order['id'] . '</p>';
                echo '<p>Order Date: ' . $order['order_dates'] . '</p>';
                echo '<form method="POST">';
                echo '<input type="hidden" name="order_id" value="' . $order['id'] . '">';
                echo '<button type="submit" name="send_order">Send Order</button>';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo '<p class="no-requests">No order requests.</p>';
        }
        ?>
    </div>
</body>
</html>