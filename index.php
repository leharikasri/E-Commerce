<?php
session_start();
include 'includes/db.php';

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Handle search query
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Handle category filter
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Handle price filter
$minPrice = isset($_GET['minPrice']) ? $_GET['minPrice'] : 0;
$maxPrice = isset($_GET['maxPrice']) ? $_GET['maxPrice'] : 1000;

// Handle pagination
$limit = 12; // Number of products per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build the query
$query = "SELECT * FROM products WHERE 1";

// Search filter
if ($search) {
    $query .= " AND (name LIKE :search OR description LIKE :search)";
}

// Category filter
if ($category) {
    $query .= " AND category = :category";
}

// Price filter
$query .= " AND price BETWEEN :minPrice AND :maxPrice";

// Apply pagination
$query .= " LIMIT :limit OFFSET :offset";

// Prepare statement
$stmt = $conn->prepare($query);
if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
if ($category) {
    $stmt->bindValue(':category', $category);
}
$stmt->bindValue(':minPrice', $minPrice, PDO::PARAM_INT);
$stmt->bindValue(':maxPrice', $maxPrice, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total products for pagination
$countQuery = "SELECT COUNT(*) FROM products WHERE 1";
$countParams = [];

if ($search) {
    $countQuery .= " AND (name LIKE :search OR description LIKE :search)";
    $countParams[':search'] = '%' . $search . '%';
}
if ($category) {
    $countQuery .= " AND category = :category";
    $countParams[':category'] = $category;
}
$countQuery .= " AND price BETWEEN :minPrice AND :maxPrice";
$countParams[':minPrice'] = $minPrice;
$countParams[':maxPrice'] = $maxPrice;

$totalStmt = $conn->prepare($countQuery);
foreach ($countParams as $param => $value) {
    if (in_array($param, [':minPrice', ':maxPrice'])) {
        $totalStmt->bindValue($param, $value, PDO::PARAM_INT);
    } else {
        $totalStmt->bindValue($param, $value);
    }
}
$totalStmt->execute();
$totalProducts = $totalStmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyStore</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Your full CSS (same as you posted) */
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #232f3e;
            padding: 10px 20px;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .header .logo {
            font-size: 24px;
            font-weight: bold;
        }
        .header nav a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            font-size: 16px;
        }
        .header nav a:hover {
            text-decoration: underline;
        }
        .search-bar input[type="text"] {
            padding: 8px;
            border-radius: 4px;
            border: none;
            width: 250px;
        }
        .category-bar {
            background-color: #eaeaea;
            padding: 10px 0;
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }
        .category-bar a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            font-size: 16px;
        }
        .category-bar a:hover {
            color: #007bff;
        }
        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .product {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .product:hover {
            transform: scale(1.05);
        }
        .product img {
            max-width: 100%;
            border-radius: 4px;
            height: 200px;
            object-fit: cover;
        }
        .product h3 {
            font-size: 18px;
            margin: 15px 0;
        }
        .product p {
            font-size: 16px;
            color: #555;
        }
        .product .price {
            font-size: 18px;
            color: #007bff;
            margin: 10px 0;
        }
        .add-to-cart-button {
            background-color: #f0c14b;
            border: 1px solid #a88734;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
            width: 100%;
            font-size: 16px;
        }
        .pagination {
            text-align: center;
            margin: 30px 0;
        }
        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            margin: 0 5px;
            border: 1px solid #ddd;
            color: #333;
            border-radius: 4px;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        footer {
            text-align: center;
            background-color: #232f3e;
            color: white;
            padding: 20px;
            margin-top: 50px;
        }
        @media (max-width: 768px) {
            .header nav {
                display: none;
            }
            .category-bar {
                flex-direction: column;
                gap: 10px;
            }
            .product-list {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="logo">MyStore</div>
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search for products..." value="<?= htmlspecialchars($search); ?>">
            </form>
        </div>
        <nav>
            <a href="pages/login.php">Login</a>
            <a href="pages/register.php">Register</a>
            <a href="pages/cart.php">Cart</a>
            <form method="POST" style="display:inline;">
                <button type="submit" name="logout" style="background:none;color:white;border:none;cursor:pointer;">Logout</button>
            </form>
        </nav>
    </div>

    <!-- Category Bar -->
    <div class="category-bar">
        <a href="?category=Men">Men</a>
        <a href="?category=Women">Women</a>
        <a href="?category=Kids">Kids</a>
        <a href="?category=Kitchen">Kitchen</a>
    </div>

    <!-- Product List -->
    <div class="container">
        <h2>Products</h2>
        <div class="product-list">
            <?php if (empty($products)) : ?>
                <p>No products available.</p>
            <?php else : ?>
                <?php foreach ($products as $product) : ?>
                    <div class="product">
                        <img src="images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                        <h3><?= htmlspecialchars($product['name']); ?></h3>
                        <p><?= htmlspecialchars($product['description']); ?></p>
                        <p class="price">$<?= number_format($product['price'], 2); ?></p>
                        <form method="POST" action="pages/cart.php">
                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                            <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <a href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>&category=<?= urlencode($category); ?>&minPrice=<?= $minPrice; ?>&maxPrice=<?= $maxPrice; ?>" class="<?= ($i == $page) ? 'active' : ''; ?>"><?= $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 MyStore | All Rights Reserved</p>
    </footer>

</body>
</html>
