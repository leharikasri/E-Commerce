<?php
include '../includes/db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Flash message
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Search functionality
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $conn->query("SELECT * FROM products");
}
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sort functionality (by price)
if (isset($_GET['sort'])) {
    if ($_GET['sort'] === 'asc') {
        usort($products, fn($a, $b) => $a['price'] <=> $b['price']);
    } elseif ($_GET['sort'] === 'desc') {
        usort($products, fn($a, $b) => $b['price'] <=> $a['price']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .search-bar input[type="text"] {
            padding: 8px;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-bar button {
            padding: 8px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            margin-left: 5px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #218838;
        }
        .add-product {
            background-color: #007bff;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .add-product:hover {
            background-color: #0056b3;
        }
        .sort-links a {
            margin: 0 5px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .sort-links a:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        td img {
            width: 60px;
            height: auto;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .actions a {
            margin: 0 5px;
            padding: 6px 12px;
            color: #007bff;
            text-decoration: none;
            border: 1px solid #007bff;
            border-radius: 4px;
            font-size: 14px;
            display: inline-block;
        }
        .actions a:hover {
            background-color: #007bff;
            color: white;
        }
        .btn-back {
            display: block;
            width: 180px;
            margin: 30px auto 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
        .flash-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Manage Products</h2>

    <?php if ($message): ?>
        <div class="flash-message"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="top-bar">
        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Search by name" value="<?= htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <a href="add_product.php" class="add-product">+ Add New Product</a>
    </div>

    <div class="sort-links">
        <b>Sort by Price:</b> 
        <a href="?sort=asc<?= $search ? '&search=' . urlencode($search) : ''; ?>">Low to High</a> | 
        <a href="?sort=desc<?= $search ? '&search=' . urlencode($search) : ''; ?>">High to Low</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>

        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td><?= $product['id']; ?></td>
                    <td><?= htmlspecialchars($product['name']); ?></td>
                    <td>$<?= number_format($product['price'], 2); ?></td>
                    <td><?= htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></td>
                    <td><img src="../images/<?= htmlspecialchars($product['image']); ?>" alt="Product Image"></td>
                    <td class="actions">
                        <a href="edit_product.php?id=<?= $product['id']; ?>">Edit</a>
                        <a href="delete_product.php?id=<?= $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No products found.</td>
            </tr>
        <?php endif; ?>

    </table>

    <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
</div>

</body>
</html>
