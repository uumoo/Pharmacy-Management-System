<?php
session_start();

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

// Function to fetch medicines based on search term
function searchMedicines($searchTerm) {
    global $connection;
    $safeSearchTerm = sanitize($searchTerm);
    $sql = "SELECT * FROM medicines WHERE name LIKE '%$safeSearchTerm%'";
    $result = mysqli_query($connection, $sql);
    $medicines = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $medicines[] = $row;
        }
    }
    return $medicines;
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add medicine to cart
if (isset($_POST['add_to_cart'])) {
    $medicine_id = $_POST['medicine_id'];
    if (isset($_SESSION['cart'][$medicine_id])) {
        $_SESSION['cart'][$medicine_id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$medicine_id] = [
            'name' => $_POST['medicine_name'],
            'price' => $_POST['medicine_price'],
            'quantity' => 1
        ];
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Remove medicine from cart
if (isset($_GET['remove_from_cart'])) {
    $medicine_id = $_GET['remove_from_cart'];
    if (isset($_SESSION['cart'][$medicine_id])) {
        unset($_SESSION['cart'][$medicine_id]);
    }
    header('Location: homepage1.php');
    exit;
}

// Checkout and redirect to confirmation page
if (isset($_POST['confirm_order'])) {
    $sql = "SELECT * FROM medicines";
    $result = mysqli_query($connection, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $medicines = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    foreach ($_SESSION['cart'] as $item) {
        $itemName = $item['name'];
        $itemQuantity = $item['quantity']; echo'hi'. $itemName .'there'. $itemQuantity .'whatt';

        foreach ($medicines as $medicine) {
            if ($medicine["name"] == $itemName) {
                $available = $medicine["on_stock"]; 
                $left = $available - $itemQuantity;
                
                if ($left < $medicine["minimum_quantity"]) {
                    $left = $left + $medicine["minimum_order_quantity"];
                }
                
                $updateQuery = "UPDATE medicines SET on_stock = $left WHERE name = '$itemName'";
                if (mysqli_query($connection, $updateQuery)) {
                    echo "Record updated successfully for $itemName<br>";
                } else {
                    echo "Error updating record: " . mysqli_error($connection) . "<br>";
                }
            }
        }
    }
    

    // Close connection
    mysqli_close($connection);
    unset($_SESSION['cart']);
    header('Location: homepage1.php');
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            background-color: #4caf50;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Confirmation</h1>
        <div>
            <h2>Shopping Cart</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalPrice = 0;
                    if(isset($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $item): 
                            $itemName = $item['name'];
                            $itemPrice = $item['price'];
                            $itemQuantity = $item['quantity'];
                            $itemTotalPrice = $itemPrice * $itemQuantity;
                            $totalPrice += $itemTotalPrice;
                        ?>
                            <tr>
                                <td><?php echo $itemName; ?></td>
                                <td><?php echo $itemPrice; ?></td>
                                <td><?php echo $itemQuantity; ?></td>
                                <td><?php echo $itemTotalPrice; ?></td>
                            </tr>
                        <?php endforeach; 
                    } ?>
                </tbody>
            </table>
        </div>
        <div>
            <h2>Total Price</h2>
            <p><?php echo "Total Price: ৳ " . $totalPrice; ?></p>
        </div>
        <form method="POST">
            <button type="submit" name="confirm_order">Confirm Order</button>
        </form>
    </div>
</body>
</html>
