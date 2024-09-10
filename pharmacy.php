<?php
// Initialize a session to manage the shopping cart
session_start();

// Define an array of medicines with their details
$medicines = array(
    array(
        "name" => "Napa",
        "image" => "napa.jpg",
        "price" => 50
    ),
    array(
        "name" => "Ceevit",
        "image" => "ceevit.png",
        "price" => 100
    ),
    array(
        "name" => "Entacyd Plus",
        "image" => "entacyd_plus.png",
        "price" => 80
    ),
    array(
        "name" => "Alben",
        "image" => "alben.png",
        "price" => 120
    ),
    array(
        "name" => "Azithral",
        "image" => "azithral.png",
        "price" => 150
    ),
    array(
        "name" => "Paracetamol",
        "image" => "paracetamol.png",
        "price" => 30
    )
    // Add more medicines here
);

// Function to display medicines
function displayMedicines($medicines) {
    foreach ($medicines as $medicine) {
        echo '<div class="medicine">';
        echo '<img src="' . $medicine["image"] . '" alt="' . $medicine["name"] . '">';
        echo '<h2>' . $medicine["name"] . '</h2>';
        echo '<p class="price">Price: ৳ ' . $medicine["price"] . '</p>';
        echo '<button class="add-to-cart" onclick="addToCart(\'' . $medicine["name"] . '\',' . $medicine["price"] . ')">Add to Cart</button>';
        echo '</div>';
    }
}

// Function to add medicine to cart
function addToCart($medicineName, $price) {
    // Add the medicine to the cart array in the session
    $_SESSION['cart'][] = array(
        "name" => $medicineName,
        "price" => $price
    );
}

// Handle search query if submitted
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    // Implement your search logic here
    // You can filter the $medicines array based on the search term
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bangladesh Pharmacy</title>
    <style>
        /* CSS styles here */
    </style>
</head>
<body>
    <header>
        <h1>Bangladesh Pharmacy</h1>
        <div class="search-box">
            <form action="" method="GET">
                <input type="text" name="search" placeholder="Search medicines...">
                <button type="submit">Search</button>
            </form>
        </div>
    </header>

    <div class="medicine-container">
        <?php displayMedicines($medicines); ?>
    </div>

    <script>
        // JavaScript function to add medicine to cart
        function addToCart(name, price) {
            // Send an AJAX request to a PHP script to add the medicine to the cart
            // You can implement this part using JavaScript and PHP
        }
    </script>
</body>
</html>
