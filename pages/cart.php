
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];  // Assuming user ID is stored in session

// Handle Add to Cart with Quantity
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;  // Default to 1 if quantity is not set

    // Check if product is already in the user's cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        // Update quantity if the product is already in the cart
        $new_quantity = $cart_item['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$new_quantity, $user_id, $product_id]);
    } else {
        // Add new product to the cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
}

// Handle Product Removal from Cart
if (isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
}

// Handle Quantity Update
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Update the quantity in the cart
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$quantity, $user_id, $product_id]);
}

// Fetch the user's cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_cost = 0;  // Initialize total cost variable
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f1f3f6;
        margin: 0;
        padding: 0;
        color: #212121;
    }
    .container {
        width: 95%;
        max-width: 1200px;
        margin: 30px auto;
        background-color: #fff;
        padding: 20px 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }
    h2 {
        font-size: 26px;
        font-weight: 500;
        text-align: left;
        margin-bottom: 25px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e0e0e0;
    }
    .cart-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 20px 0;
        border-bottom: 1px solid #e0e0e0;
    }
    .cart-item img {
        max-width: 120px;
        height: auto;
        margin-right: 20px;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
    }
    .item-details {
        flex-grow: 1;
    }
    .item-name {
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 8px;
    }
    .item-price {
        font-size: 16px;
        font-weight: 400;
        color: #388e3c;
    }
    .item-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
    .item-actions form {
        margin-top: 8px;
    }
    .item-actions button {
        background-color: #2874f0;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 2px;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.3s;
    }
    .item-actions button:hover {
        background-color: #0b63ce;
    }
    .quantity {
        width: 70px;
        padding: 6px;
        margin-right: 10px;
        border: 1px solid #c2c2c2;
        border-radius: 2px;
        text-align: center;
    }
    .cart-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e0e0e0;
    }
    .cart-actions a {
        background-color: #fb641b;
        color: white;
        padding: 14px 22px;
        border-radius: 2px;
        text-decoration: none;
        font-size: 16px;
        font-weight: 500;
        transition: background 0.3s;
    }
    .cart-actions a:hover {
        background-color: #d95b13;
    }
    .total-cost {
        font-size: 24px;
        font-weight: 500;
        color: #212121;
        text-align: right;
        margin-top: 20px;
    }
    .empty-cart {
        text-align: center;
        font-size: 20px;
        color: #757575;
        padding: 50px 0;
    }
</style>
</head>
<body>
    <div class="container">
        <h2>Your Cart</h2>
        <?php
        if (empty($cart_items)) {
            echo "<p class='empty-cart'>Your cart is empty.</p>";
        } else {
            // Fetch product details for each cart item
            $product_ids = array_column($cart_items, 'product_id');
            $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
            $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
            $stmt->execute($product_ids);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($products as $product) {
                $quantity = 0;
                foreach ($cart_items as $cart_item) {
                    if ($cart_item['product_id'] == $product['id']) {
                        $quantity = $cart_item['quantity'];
                        break;
                    }
                }
                $total_cost += $product['price'] * $quantity; // Add product price * quantity to total cost

                echo "<div class='cart-item'>
                        <img src='../images/{$product['image']}' alt='{$product['name']}' class='item-image'>
                        <div class='item-details'>
                            <div class='item-name'>{$product['name']}</div>
                            <div class='item-price'>\${$product['price']} x $quantity</div>
                        </div>
                        <div class='item-actions'>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='product_id' value='{$product['id']}'>
                                <input type='number' name='quantity' value='$quantity' class='quantity' min='1' required>
                                <button type='submit' name='update_quantity'>Update Quantity</button>
                            </form>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='product_id' value='{$product['id']}'>
                                <button type='submit' name='remove_from_cart'>Remove</button>
                            </form>
                        </div>
                      </div>";
            }
        }
        ?>
        <?php if (!empty($cart_items)) : ?>
            <div class="total-cost">
                Total: $<?= number_format($total_cost, 2); ?>
            </div>
        <?php endif; ?>
        <div class="cart-actions">
            <a href="../index.php">Back to Shop</a>
            <a href="checkout.php">Proceed to Checkout</a>
        </div>
    </div>
</body>
</html>
