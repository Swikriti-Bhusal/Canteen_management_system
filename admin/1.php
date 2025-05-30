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
            if($check === false) {
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

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        if ($uploadOk == 1 && move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $stmt = $conn->prepare("INSERT INTO food_items (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdss", $_POST['name'], $_POST['description'], $_POST['price'], $filename, $_POST['category']);
            // $stmt->bind_param("ssdss", $_POST['name'], $_POST['description'], $_POST['price'], $targetFile, $_POST['category']);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['update'])) {
        if ($_FILES["image"]["size"] > 0) {
            $targetDir = "../uploads/";
            $imageName = basename($_FILES["image"]["name"]);
            $targetFile = $targetDir . uniqid() . '_' . $imageName;
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if($check === false) {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            if ($_FILES["image"]["size"] > 2000000) {
                echo "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }
            
            if ($uploadOk == 1 && move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                if (!empty($_POST['old_image']) && file_exists($_POST['old_image'])) {
                    unlink($_POST['old_image']);
                }
                
                $stmt = $conn->prepare("UPDATE food_items SET name = ?, description = ?, price = ?, image = ?, category = ? WHERE id = ?");
                $stmt->bind_param("ssdssi", $_POST['name'], $_POST['description'], $_POST['price'], $targetFile, $_POST['category'], $_POST['id']);
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
    $stmt = $conn->prepare("SELECT image FROM food_items WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    $result = $stmt->get_result();
    $imagePath = $result->fetch_assoc()['image'];
    $stmt->close();
    
    if ($imagePath && file_exists($imagePath)) {
        unlink($imagePath);
    }
    
    $stmt = $conn->prepare("DELETE FROM food_items WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    $stmt->close();
}

// Fetch all food items
$result = $conn->query("SELECT * FROM food_items");
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
            padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { margin-bottom: 20px; padding: 20px; border: 1px solid #ddd; }
        input, textarea, select { margin-bottom: 10px; width: 100%; padding: 8px; }
        button { padding: 8px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        button.delete { background: #f44336; }
        .image-preview { max-width: 100px; max-height: 100px; margin-top: 10px; }
        .current-image { max-width: 100px; max-height: 100px; }
        .error { color: red; font-size: 12px; margin-top: -8px; margin-bottom: 10px; display: none; }
        .error.show { display: block; }
    </style>
</head>
<body>
<body class="bg-gray-100">
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
            <input type="text" name="name" id="itemName" placeholder="Food Name" required>
            <div id="nameError" class="error">Food name should contain only alphabets and spaces</div>
        </div>
        
        <div>
            <textarea name="description" id="itemDesc" placeholder="Description" required></textarea>
            <div id="descError" class="error">Description should contain only alphabets, spaces, and punctuation</div>
        </div>
        
        <div>
            <input type="number" step="0.01" name="price" id="itemPrice" placeholder="Price" required>
            <div id="priceError" class="error">Price should be a 4-digit number starting from 1</div>
        </div>
        
        <input type="file" name="image" id="itemImage" accept="image/*">
        <div id="imagePreviewContainer">
            <img id="imagePreview" class="image-preview" style="display:none;">
        </div>
        <div id="currentImageContainer"></div>
        
        <select name="category" id="itemCategory" required>
            <option value="Main Course">Main Course</option>
            <option value="Dessert">Dessert</option>
        </select>
        
        <button type="submit" name="add" id="addBtn">Add Item</button>
        <button type="submit" name="update" id="updateBtn" style="display:none;">Update Item</button>
        <button type="button" id="cancelBtn" style="display:none;">Cancel</button>
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
                    <td><img src="<?= htmlspecialchars($item['image']) ?>" class="current-image"></td>
                    <td><?= $item['category'] ?></td>
                    <td>
                        <button onclick="editItem(<?= $item['id'] ?>, '<?= addslashes($item['name']) ?>', '<?= addslashes($item['description']) ?>', <?= $item['price'] ?>, '<?= addslashes($item['image']) ?>', '<?= $item['category'] ?>')">Edit</button>
                        <a href="?delete=<?= $item['id'] ?>" class="delete" onclick="return confirm('Are you sure?')">Delete</a>
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
            const regex = /^[a-zA-Z\s.,!?'-]+$/;
            return regex.test(desc);
        }
        
        function validatePrice(price) {
            const regex = /^[1-9]\d{0,3}(\.\d{1,2})?$/;
            return regex.test(price) && parseFloat(price) > 0 && price.length <= 4;
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
            const price = document.getElementById('itemPrice').value;
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
            const price = this.value;
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
            currentImageContainer.innerHTML = `<p>Current Image:</p><img src="${image}" class="current-image">`;
            
            document.getElementById('addBtn').style.display = 'none';
            document.getElementById('updateBtn').style.display = 'inline-block';
            document.getElementById('cancelBtn').style.display = 'inline-block';
            
            // Clear any error messages when editing
            document.getElementById('nameError').classList.remove('show');
            document.getElementById('descError').classList.remove('show');
            document.getElementById('priceError').classList.remove('show');
        }
        
        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('itemId').value = '';
            document.getElementById('itemName').value = '';
            document.getElementById('itemDesc').value = '';
            document.getElementById('itemPrice').value = '';
            document.getElementById('itemImage').value = '';
            document.getElementById('oldImage').value = '';
            document.getElementById('itemCategory').value = 'Main Course';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('currentImageContainer').innerHTML = '';
            
            document.getElementById('addBtn').style.display = 'inline-block';
            document.getElementById('updateBtn').style.display = 'none';
            document.getElementById('cancelBtn').style.display = 'none';
            
            // Clear error messages when canceling
            document.getElementById('nameError').classList.remove('show');
            document.getElementById('descError').classList.remove('show');
            document.getElementById('priceError').classList.remove('show');
        });
    </script>
    <script src="/cms/assets/script.js"></script>
</body>
</html> 

