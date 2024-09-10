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


if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add medicine to cart
if (isset($_POST['add_to_cart'])) {
    $medicine_id = $_POST['medicine_id'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
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
if (isset($_POST['checkout'])) {

    header('Location: confirmation.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bangladesh Pharmacy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }

        .search-box {
            margin-top: 20px;
            text-align: center;
        }

        .search-box input[type="text"] {
            padding: 8px;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 5px;
        }

        .search-box button {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .medicine-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }

        .medicine {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 20px;
            margin: 10px;
            width: 200px;
            text-align: center;
        }

        .medicine img {
           max-width: 100%;
           height: auto;
          border-radius: 8px; /* Add border-radius for rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add box-shadow for a subtle shadow effect */
}


        .medicine h2 {
            margin-top: 10px;
            margin-bottom: 5px;
            font-size: 18px;
        }

        .medicine .price {
            font-weight: bold;
        }

        .add-to-cart {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .cart {
            margin-top: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 20px;
            width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .cart h2 {
            margin-top: 0;
        }

        .cart ul {
            list-style-type: none;
            padding: 0;
        }

        .cart li {
            margin-bottom: 10px;
        }

        .cart a {
            color: #007bff;
            text-decoration: none;
            margin-left: 10px;
        }

        .cart a:hover {
            text-decoration: underline;
        }

        .cart button {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .empty-cart {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>P H A R M A </h1>
        <div class="search-box">
            <form action="" method="GET">
                <input type="text" name="search" placeholder="Search medicines...">
                <button type="submit">Search</button>
            </form>
        </div>
    </header>

    <div class="medicine-container">
        <?php
        if (isset($_GET['search'])) {
            $searchTerm = $_GET['search'];
            $medicines = searchMedicines($searchTerm);
        } else {
            $sql = "SELECT * FROM medicines";
            $result = mysqli_query($connection, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                $medicines = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
        }

        if (!empty($medicines)) {
            foreach ($medicines as $medicine) {
                echo '<div class="medicine">';
                echo '<img src="meds.avif" alt="' . $medicine["name"] . '">';

                echo '<h2>' . $medicine["name"] . '</h2>';
                echo '<p class="price">Price: ৳ ' . $medicine["price"] . '</p>';
                echo '<form method="POST">';
                echo '<input type="hidden" name="medicine_id" value="' . $medicine["id"] . '">';
                echo '<input type="hidden" name="medicine_name" value="' . $medicine["name"] . '">';
                echo '<input type="hidden" name="medicine_price" value="' . $medicine["price"] . '">';
                echo '<button class="add-to-cart" type="submit" name="add_to_cart">Add to Cart</button>';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo '<p class="empty-cart">No medicines found.</p>';
        }
        ?>
    </div>

    <div class="cart">
        <h2>Shopping Cart</h2>
        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <ul>
                <?php foreach ($_SESSION['cart'] as $medicine_id => $item): ?>
                    <li>
                        <?php echo $item['name']; ?> - 
                        Price: <?php echo $item['price']; ?> - 
                        Quantity: <?php echo $item['quantity']; ?> -
                        <a href="?remove_from_cart=<?php echo $medicine_id; ?>">Remove</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form method="POST">
                <button type="submit" name="checkout">Checkout</button>
            </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
</body>
</html>
