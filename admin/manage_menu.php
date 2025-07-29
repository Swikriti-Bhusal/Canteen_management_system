<?php
require '../config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $targetDir = "../uploads/";
        $imageName = basename($_FILES["image"]["name"]);
        $filename = uniqid() . '_' . $imageName;
        $targetFile = $targetDir . $filename;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if ($_FILES["image"]["tmp_name"]) {
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check === false) {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        } else {
            echo "Image is required for new items.";
            $uploadOk = 0;
        }

        if ($_FILES["image"]["size"] > 2000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1 && move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $stmt = $conn->prepare("INSERT INTO food_items (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdss", $_POST['name'], $_POST['description'], $_POST['price'], $filename, $_POST['category']);
            $stmt->execute();
            $stmt->close();
        }

    } elseif (isset($_POST['update'])) {
        if ($_FILES["image"]["size"] > 0) {
            $targetDir = "../uploads/";
            $imageName = basename($_FILES["image"]["name"]);
            $filename = uniqid() . '_' . $imageName;
            $targetFile = $targetDir . $filename;
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check === false) {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            if ($_FILES["image"]["size"] > 2000000) {
                echo "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            if ($uploadOk == 1 && move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                if (!empty($_POST['old_image']) && file_exists($targetDir . $_POST['old_image'])) {
                    unlink($targetDir . $_POST['old_image']);
                }

                $stmt = $conn->prepare("UPDATE food_items SET name = ?, description = ?, price = ?, image = ?, category = ? WHERE id = ?");
                $stmt->bind_param("ssdssi", $_POST['name'], $_POST['description'], $_POST['price'], $filename, $_POST['category'], $_POST['id']);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            $stmt = $conn->prepare("UPDATE food_items SET name = ?, description = ?, price = ?, category = ? WHERE id = ?");
            $stmt->bind_param("ssdsi", $_POST['name'], $_POST['description'], $_POST['price'], $_POST['category'], $_POST['id']);
            $stmt->execute();
            $stmt->close();
        }
    }

} elseif (isset($_GET['delete'])) {
    $food_id = $_GET['delete'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // 1. Get image path first
        $stmt = $conn->prepare("SELECT image FROM food_items WHERE id = ?");
        $stmt->bind_param("i", $food_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();  // ✅ FIXED: assign to variable first
        $imagePath = $row['image'];
        $stmt->close();

        // 2. Delete from order_items
        $stmt = $conn->prepare("DELETE FROM order_items WHERE food_id = ?");
        $stmt->bind_param("i", $food_id);
        $stmt->execute();
        $stmt->close();

        // 3. Delete from food_items
        $stmt = $conn->prepare("DELETE FROM food_items WHERE id = ?");
        $stmt->bind_param("i", $food_id);
        $stmt->execute();
        $stmt->close();

        // 4. Delete image file
        if ($imagePath && file_exists("../uploads/" . $imagePath)) {
            unlink("../uploads/" . $imagePath);
        }

        $conn->commit();

        // Redirect after successful deletion
        header("Location: manage_menu.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Error deleting item: " . $e->getMessage());
    }
}

// Fetch all food items
$result = $conn->query("SELECT * FROM food_items ORDER BY id DESC");
$foodItems = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background-color: #2c3e50;
            color: white;
        }

        .admin-header ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            height: 60px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .admin-header li {
            margin-right: 1.5rem;
            position: relative;
        }

        .admin-header a {
            color: #ecf0f1;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.75rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        body { 
            font-family: Arial, sans-serif; 
            margin: 20px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background-color: #f2f2f2; 
        }
        form { 
            margin-bottom: 20px; 
            padding: 20px; 
            border: 1px solid #ddd; 
            background: #f9f9f9;
        }
        input, textarea, select { 
            margin-bottom: 10px; 
            width: 100%; 
            padding: 8px; 
            box-sizing: border-box;
        }
        button { 
            padding: 8px 15px; 
            background: #4CAF50; 
            color: white; 
            border: none; 
            cursor: pointer; 
            margin-right: 5px;
        }
        button.delete { 
            background: #f44336; 
        }
        .image-preview { 
            max-width: 100px; 
            max-height: 100px; 
            margin-top: 10px; 
        }
        .current-image { 
            max-width: 100px; 
            max-height: 100px; 
        }
        .error { 
            color: red; 
            font-size: 12px; 
            margin-top: -8px; 
            margin-bottom: 10px; 
            display: none; 
        }
        .error.show { 
            display: block; 
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <ul>
            <li><a href="index.php" class="active">Dashboard</a></li>
            <li><a href="orders.php">Orders</a></li>
            <li><a href="manage_menu.php">Menu</a></li>
            <li><a href="manage_users.php">Users</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </header>

    <h1 style="text-align: center;">Food Menu Admin</h1>

    <form method="post" enctype="multipart/form-data" id="foodForm">
        <input type="hidden" name="id" id="itemId">
        <input type="hidden" name="old_image" id="oldImage">
        
        <div>
            <label for="itemName">Food Name</label>
            <input type="text" name="name" id="itemName" placeholder="Food Name" required>
            <div id="nameError" class="error">Food name should contain only alphabets and spaces</div>
        </div>
        
        <div>
            <label for="itemDesc">Description</label>
            <textarea name="description" id="itemDesc" placeholder="Description" required></textarea>
            <div id="descError" class="error">Description should contain only alphabets, spaces, and punctuation</div>
        </div>
        
        <div>
            <label for="itemPrice">Price</label>
            <input type="number" step="0.01" name="price" id="itemPrice" placeholder="Price" required>
            <div id="priceError" class="error">Price should be a positive number</div>
        </div>
        
        <div>
            <label for="itemImage">Image</label>
            <input type="file" name="image" id="itemImage" accept="image/*">
            <div id="imagePreviewContainer">
                <img id="imagePreview" class="image-preview" style="display:none;">
            </div>
            <div id="currentImageContainer"></div>
        </div>
        
        <div>
            <label for="itemCategory">Category</label>
            <select name="category" id="itemCategory" required>
                <option value="Main Course">Main Course</option>
                <option value="Dessert">Dessert</option>
            </select>
        </div>
        
        <div class="action-buttons">
            <button type="submit" name="add" id="addBtn">Add Item</button>
            <button type="submit" name="update" id="updateBtn" style="display:none;">Update Item</button>
            <button type="button" id="cancelBtn" style="display:none;">Cancel</button>
        </div>
    </form>
    
    <!-- Food Items Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Image</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($foodItems as $item): ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['description']) ?></td>
                    <td>Rs<?= number_format($item['price'], 2) ?></td>
                    <td><img src="../uploads/<?= htmlspecialchars($item['image']) ?>" class="current-image" onerror="this.style.display='none'"></td>
                    <td><?= $item['category'] ?></td>
                    <td class="action-buttons">
                        <button onclick="editItem(<?= $item['id'] ?>, '<?= addslashes($item['name']) ?>', '<?= addslashes($item['description']) ?>', <?= $item['price'] ?>, '<?= addslashes($item['image']) ?>', '<?= $item['category'] ?>')">Edit</button>
                        <a href="?delete=<?= $item['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this item? This will also delete all related order items.')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <script>
        function validateName(name) {
            const regex = /^[a-zA-Z\s]+$/;
            return regex.test(name);
        }
        
        function validateDescription(desc) {
            const regex = /^[a-zA-Z0-9\s.,!?'-]+$/;
            return regex.test(desc);
        }
        
        function validatePrice(price) {
            return price > 0;
        }
        
        // Form validation
        document.getElementById('foodForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate name
            const name = document.getElementById('itemName').value.trim();
            const nameError = document.getElementById('nameError');
            if (!validateName(name)) {
                nameError.classList.add('show');
                isValid = false;
            } else {
                nameError.classList.remove('show');
            }
            
            // Validate description
            const desc = document.getElementById('itemDesc').value.trim();
            const descError = document.getElementById('descError');
            if (!validateDescription(desc)) {
                descError.classList.add('show');
                isValid = false;
            } else {
                descError.classList.remove('show');
            }
            
            // Validate price
            const price = parseFloat(document.getElementById('itemPrice').value);
            const priceError = document.getElementById('priceError');
            if (!validatePrice(price)) {
                priceError.classList.add('show');
                isValid = false;
            } else {
                priceError.classList.remove('show');
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        document.getElementById('itemName').addEventListener('input', function() {
            const name = this.value.trim();
            const nameError = document.getElementById('nameError');
            if (!validateName(name)) {
                nameError.classList.add('show');
            } else {
                nameError.classList.remove('show');
            }
        });
        
        document.getElementById('itemDesc').addEventListener('input', function() {
            const desc = this.value.trim();
            const descError = document.getElementById('descError');
            if (!validateDescription(desc)) {
                descError.classList.add('show');
            } else {
                descError.classList.remove('show');
            }
        });
        
        document.getElementById('itemPrice').addEventListener('input', function() {
            const price = parseFloat(this.value);
            const priceError = document.getElementById('priceError');
            if (!validatePrice(price)) {
                priceError.classList.add('show');
            } else {
                priceError.classList.remove('show');
            }
        });
        
        // Image preview functionality
        document.getElementById('itemImage').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        function editItem(id, name, desc, price, image, category) {
            document.getElementById('itemId').value = id;
            document.getElementById('itemName').value = name;
            document.getElementById('itemDesc').value = desc;
            document.getElementById('itemPrice').value = price;
            document.getElementById('oldImage').value = image;
            document.getElementById('itemCategory').value = category;
            
            // Show current image
            const currentImageContainer = document.getElementById('currentImageContainer');
            currentImageContainer.innerHTML = `
                <p>Current Image:</p>
                <img src="../uploads/${image}" class="current-image" onerror="this.style.display='none'">
                <input type="hidden" name="current_image" value="${image}">
            `;
            
            // Hide image preview if showing
            document.getElementById('imagePreview').style.display = 'none';
            
            // Toggle buttons
            document.getElementById('addBtn').style.display = 'none';
            document.getElementById('updateBtn').style.display = 'inline-block';
            document.getElementById('cancelBtn').style.display = 'inline-block';
            
            // Clear any error messages when editing
            document.querySelectorAll('.error').forEach(el => el.classList.remove('show'));
            
            // Scroll to form
            document.getElementById('foodForm').scrollIntoView({ behavior: 'smooth' });
        }
        
        document.getElementById('cancelBtn').addEventListener('click', function() {
            // Reset form
            document.getElementById('foodForm').reset();
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('currentImageContainer').innerHTML = '';
            
            // Toggle buttons
            document.getElementById('addBtn').style.display = 'inline-block';
            document.getElementById('updateBtn').style.display = 'none';
            document.getElementById('cancelBtn').style.display = 'none';
            
            // Clear error messages
            document.querySelectorAll('.error').forEach(el => el.classList.remove('show'));
        });
    </script>
</body>
</html>