<?php
session_start();
require '../config.php';

// Get category filter
$category = isset($_GET['category']) ? $_GET['category'] : 'All';

// Get search query
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch all food items
if ($category === 'All') {
    $result = $conn->query("SELECT * FROM food_items");
} else {
    $stmt = $conn->prepare("SELECT * FROM food_items WHERE category = ?");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
}
$foodItems = $result->fetch_all(MYSQLI_ASSOC);

// ===== 1. LINEAR SEARCH FUNCTION ===== //
function searchFoodItems($items, $query) {
    $results = [];
    foreach ($items as $item) {
        if (stripos($item['name'], $query) !== false) {
            $results[] = $item;
        }
    }
    return $results;
}

// ===== 2. RECOMMENDATION FUNCTION ALGORITHM ===== //
function getRecommendedItems($user_id, $conn) {
    // Step 1: Get all data we need
    $allOrders = $conn->query("SELECT * FROM orders")->fetch_all(MYSQLI_ASSOC);
    $allOrderItems = $conn->query("SELECT * FROM order_items")->fetch_all(MYSQLI_ASSOC);
    $allFoodItems = $conn->query("SELECT * FROM food_items")->fetch_all(MYSQLI_ASSOC);
    
    // Step 2: Find current user's orders
    $userOrders = array_filter($allOrders, function($order) use ($user_id) {
        return $order['user_id'] == $user_id;
    });
    
    // If user has no orders, return popular items
    if (empty($userOrders)) {
        return array_slice($allFoodItems, 0, 3);
    }
    
    // Step 3: Find items the user has ordered
    $userOrderIds = array_column($userOrders, 'id');
    $userOrderedItems = array_filter($allOrderItems, function($item) use ($userOrderIds) {
        return in_array($item['order_id'], $userOrderIds);
    });
    
    // Step 4: Find similar users (who ordered the same items)
    $similarUsers = [];
    foreach ($allOrderItems as $item) {
        if (in_array($item['order_id'], $userOrderIds)) continue;
        
        foreach ($userOrderedItems as $userItem) {
            if ($item['food_id'] == $userItem['food_id']) {
                $similarUsers[$item['order_id']] = true;
                break;
            }
        }
    }
    
    // Step 5: Find popular items among similar users
    $similarItems = [];
    foreach ($allOrderItems as $item) {
        if (isset($similarUsers[$item['order_id']])) {
            if (!isset($similarItems[$item['food_id']])) {
                $similarItems[$item['food_id']] = 0;
            }
            $similarItems[$item['food_id']]++;
        }
    }
    
    // Step 6: Sort and get top recommendations
    arsort($similarItems);
    $recommendedFoodIds = array_slice(array_keys($similarItems), 0, 3);
    
    $recommendations = array_filter($allFoodItems, function($item) use ($recommendedFoodIds) {
        return in_array($item['id'], $recommendedFoodIds);
    });
    
    // Add popular items if we don't have enough recommendations
    if (count($recommendations) < 3) {
        $popularItems = array_slice($allFoodItems, 0, 3 - count($recommendations));
        $recommendations = array_merge($recommendations, $popularItems);
    }
    
    return array_values($recommendations);
}

// Apply search if query exists
if (!empty($searchQuery)) {
    $foodItems = searchFoodItems($foodItems, $searchQuery);
    $showRecommendations = false;
} else {
    $showRecommendations = isset($_SESSION['user_id']);
    $recommendations = $showRecommendations ? getRecommendedItems($_SESSION['user_id'], $conn) : [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../includes/header.php'; ?>
    <title>Our Menu</title>
    <link rel="stylesheet" href="./cms/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .tab {
            padding: 10px 20px;
            margin: 5px;
            background: #eee;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            color: black;
        }
        .tab.active {
            background: #ff6b00;
            color: white;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .food-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .food-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .food-info {
            padding: 15px;
        }
        .food-name {
            font-weight: bold;
            margin: 0 0 10px 0;
            font-size: 1.2rem;
        }
        .food-desc {
            color: #666;
            margin: 0 0 10px 0;
            font-size: 0.9rem;
        }
        .food-price {
            font-weight: bold;
            color: #ff6b00;
            font-size: 1.1rem;
        }
        .no-items {
            text-align: center;
            grid-column: 1 / -1;
            padding: 40px;
            color: #666;
        }
        .add-to-cart {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 10px;
            background: #ff6b00;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            width: 100%;
        }
        .add-to-cart:hover {
            background: #e55b00;
        }
        .quantity-selector {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .quantity-btn {
            padding: 5px 10px;
            background: #eee;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .quantity-input {
            width: 40px;
            text-align: center;
            margin: 0 5px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .cart-message {
            background: #4CAF50;
            color: white;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        @media (max-width: 600px) {
            .menu-grid {
                grid-template-columns: 1fr;
            }
        }
        .recommendations-section {
            margin: 30px 0;
            padding: 20px 0;
            border-top: 2px solid #ff6b00;
        }
        .recommendations-section h3 {
            color: #ff6b00;
            font-size: 1.3rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Our Delicious Menu</h1>
            <p>Fresh ingredients, authentic flavors, made with love</p>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="" class="search-form" style="margin-bottom: 20px; text-align: center;">
            <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
            <input type="text" name="search" placeholder="Search for food..." 
                   value="<?= htmlspecialchars($searchQuery) ?>" style="padding: 10px; width: 300px;">
            <button type="submit" style="padding: 10px 15px; background: #ff6b00; color: white; border: none; border-radius: 4px;">Search</button>
            <a href="?category=<?= $category ?>" style="padding: 10px 15px; margin-left: 5px; background: #ddd; color: black; text-decoration: none; border-radius: 4px;">Clear</a>
        </form>

        <?php if (isset($cart_message)): ?>
            <div class="cart-message"><?= htmlspecialchars($cart_message) ?></div>
        <?php endif; ?>

        <!-- Category Tabs -->
        <div class="tabs">
            <a href="?category=All" class="tab <?= $category === 'All' ? 'active' : '' ?>">All Items</a>
            <a href="?category=Main Course" class="tab <?= $category === 'Main Course' ? 'active' : '' ?>">Main Courses</a>
            <a href="?category=Dessert" class="tab <?= $category === 'Dessert' ? 'active' : '' ?>">Desserts</a>
        </div>

        <!-- Main Menu Grid -->
        <div class="menu-grid">
            <?php if (count($foodItems) > 0): ?>
                <?php foreach ($foodItems as $item): ?>
                    <div class="food-card">
                        <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="food-img">
                        <div class="food-info">
                            <h3 class="food-name"><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="food-desc"><?= htmlspecialchars($item['description']) ?></p>
                            <p class="food-price">Rs<?= number_format($item['price'], 0) ?></p>
                            <form action="../users/add_to_cart.php" method="post">
                                <input type="hidden" name="food_id" value="<?= $item['id'] ?>">
                                <div class="quantity-selector">
                                    <button type="button" class="quantity-btn minus" onclick="updateQuantity(this, -1)">-</button>
                                    <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="10">
                                    <button type="button" class="quantity-btn plus" onclick="updateQuantity(this, 1)">+</button>
                                </div>
                                <button type="submit" class="add-to-cart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-items">
                    <p>No items found<?= !empty($searchQuery) ? ' for "' . htmlspecialchars($searchQuery) . '"' : '' ?>.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recommendations Section -->
        <?php if ($showRecommendations && !empty($recommendations)): ?>
        <div class="recommendations-section">
            <h3 style="text-align: center;">Recommended For You</h3>
            <div class="menu-grid">
                <?php foreach ($recommendations as $item): ?>
                <div class="food-card">
                    <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="food-img">
                    <div class="food-info">
                        <h3 class="food-name"><?= htmlspecialchars($item['name']) ?></h3>
                        <p class="food-desc"><?= htmlspecialchars($item['description']) ?></p>
                        <p class="food-price">Rs<?= number_format($item['price'], 0) ?></p>
                        <form action="../users/add_to_cart.php" method="post">
                            <input type="hidden" name="food_id" value="<?= $item['id'] ?>">
                            <div class="quantity-selector">
                                <button type="button" class="quantity-btn minus" onclick="updateQuantity(this, -1)">-</button>
                                <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="10">
                                <button type="button" class="quantity-btn plus" onclick="updateQuantity(this, 1)">+</button>
                            </div>
                            <button type="submit" class="add-to-cart">Add to Cart</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <script src="/cms/assets/script.js"></script>
</body>
</html>