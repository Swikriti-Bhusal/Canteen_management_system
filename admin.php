<?php
require '../config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = "uploads/";
    $imageName = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . uniqid() . '_' . $imageName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (max 2MB)
    if ($_FILES["image"]["size"] > 2000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if (isset($_POST['add'])) {
        // Add new item
        if ($uploadOk == 1 && move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $stmt = $pdo->prepare("INSERT INTO food_items (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $targetFile,
                $_POST['category']
            ]);
        }
    } elseif (isset($_POST['update'])) {
        // Update item
        if ($uploadOk == 1 && move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Delete old image if exists
            if (!empty($_POST['old_image']) && file_exists($_POST['old_image'])) {
                unlink($_POST['old_image']);
            }
            
            $stmt = $pdo->prepare("UPDATE food_items SET name = ?, description = ?, price = ?, image = ?, category = ? WHERE id = ?");
            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $targetFile,
                $_POST['category'],
                $_POST['id']
            ]);
        } else {
            // Update without changing image
            $stmt = $pdo->prepare("UPDATE food_items SET name = ?, description = ?, price = ?, category = ? WHERE id = ?");
            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $_POST['category'],
                $_POST['id']
            ]);
        }
    }
} elseif (isset($_GET['delete'])) {
    // Delete item
    // First get image path to delete the file
    $stmt = $pdo->prepare("SELECT image FROM food_items WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $imagePath = $stmt->fetchColumn();
    
    if ($imagePath && file_exists($imagePath)) {
        unlink($imagePath);
    }
    
    // Then delete the record
    $stmt = $pdo->prepare("DELETE FROM food_items WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
}

// Get all food items for display
$stmt = $pdo->query("SELECT * FROM food_items");
$foodItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { margin-bottom: 20px; padding: 20px; border: 1px solid #ddd; }
        input, textarea, select { margin-bottom: 10px; width: 100%; padding: 8px; }
        button { padding: 8px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        button.delete { background: #f44336; }
        .image-preview { max-width: 100px; max-height: 100px; margin-top: 10px; }
        .current-image { max-width: 100px; max-height: 100px; }
    </style>
</head>
<body>
    <h1>Food Menu Admin</h1>
    
    <!-- Add/Edit Form -->
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" id="itemId">
        <input type="hidden" name="old_image" id="oldImage">
        <input type="text" name="name" id="itemName" placeholder="Food Name" required>
        <textarea name="description" id="itemDesc" placeholder="Description" required></textarea>
        <input type="number" step="0.01" name="price" id="itemPrice" placeholder="Price" required>
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
                    <td>$<?= number_format($item['price'], 2) ?></td>
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
        });
    </script>
</body>
</html>