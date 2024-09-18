<?php
session_start();
include 'db.php';

$admin_password = 'admin123';  // Admin password

// Check if the correct password is provided
if (!isset($_SESSION['admin_authenticated'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_password'])) {
        if ($_POST['admin_password'] === $admin_password) {
            $_SESSION['admin_authenticated'] = true;
        } else {
            echo "<script>alert('Incorrect password!');</script>";
        }
    }

    if (!isset($_SESSION['admin_authenticated'])) {
        // Show password prompt if the admin is not authenticated
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin Login</title>
            <style>
                body { font-family: Arial, sans-serif; background: #f0f0f0; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                .login-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.2); }
                h2 { margin-bottom: 20px; color: #333; }
                label { display: block; margin-bottom: 8px; font-weight: bold; }
                input { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; }
                button { width: 100%; padding: 10px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
                button:hover { background: #45a049; }
            </style>
        </head>
        <body>
        <div class="login-container">
            <h2>Admin Login</h2>
            <form method="POST">
                <label>Enter Admin Password:</label>
                <input type="password" name="admin_password" required>
                <button type="submit">Login</button>
            </form>
        </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Handle book upload after admin is authenticated
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['book_image'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    
    // Handle file upload
    $image = $_FILES['book_image'];
    $image_name = time() . "_" . basename($image['name']);
    $image_path = 'uploads/' . $image_name;

    // Move uploaded image to 'uploads' directory
    if (move_uploaded_file($image['tmp_name'], $image_path)) {
        $conn->query("INSERT INTO books (title, author, image) VALUES ('$title', '$author', '$image_name')");
        $_SESSION['success_message'] = 'Book and image uploaded successfully!';
    } else {
        $_SESSION['success_message'] = 'Image upload failed!';
    }
    header("Location: admin.php");
    exit;
}



// Handle order deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_order_id'])) {
    $order_id = intval($_POST['delete_order_id']);
    $conn->query("DELETE FROM orders WHERE id = $order_id");
    $_SESSION['success_message'] = 'Order deleted successfully!';
    header("Location: admin.php");
    exit;
}

// Fetch orders and PDFs from the database
$orders = $conn->query("SELECT * FROM orders");
$pdfs = $conn->query("SELECT * FROM pdfs");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 90%; max-width: 1200px; margin: 30px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .upload-form, .orders, .pdfs { margin-bottom: 30px; }
        .upload-form { padding: 20px; background: #f9f9f9; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .upload-form h3 { margin-bottom: 20px; color: #333; }
        .upload-form label { display: block; margin: 10px 0 5px; color: #555; }
        .upload-form input[type="text"], .upload-form input[type="file"], .upload-form button { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        .upload-form button { background: #4CAF50; color: white; border: none; cursor: pointer; }
        .upload-form button:hover { background: #45a049; }
        .order, .pdf-item { border-bottom: 1px solid #ddd; padding: 10px 0; margin-bottom: 15px; background: #f9f9f9; border-radius: 8px; }
        .order h4, .pdf-item h4 { margin: 0 0 10px; color: #333; }
        .order p, .pdf-item p { margin: 5px 0; color: #666; }
        .order button, .pdf-item button { background: #ff4d4d; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; }
        .order button:hover, .pdf-item button:hover { background: #e03e3e; }
    </style>
</head>
<body>

<!-- Admin Container -->
<div class="container">
    <h2>Admin Panel</h2>

    <!-- Upload Book Form -->
    <div class="upload-form">
        <h3>Upload a New Book</h3>
        <form method="POST" enctype="multipart/form-data">
            <label>Title:</label>
            <input type="text" name="title" required>
            
            <label>Author:</label>
            <input type="text" name="author" required>
            
            <label>Book Image:</label>
            <input type="file" name="book_image" accept="image/*" required>
            
            <button type="submit">Upload Book</button>
        </form>
    </div>



    <!-- Display Orders -->
    <div class="orders">
        <h3>Recent Orders</h3>
        <?php while ($order = $orders->fetch_assoc()) { ?>
            <div class="order">
                <h4>Order by <?= htmlspecialchars($order['user_name']); ?></h4>
                <p>Address: <?= htmlspecialchars($order['address']); ?></p>
                <p>Phone: <?= htmlspecialchars($order['phone']); ?></p>
                
                <?php
                // Fetch book details for the given book IDs
                $book_ids = explode(',', $order['book_ids']);
                $books = $conn->query("SELECT * FROM books WHERE id IN (" . implode(',', $book_ids) . ")");
                ?>
                
                <h5>Books Ordered:</h5>
                <?php while ($book = $books->fetch_assoc()) { ?>
                    <div class="book">
                        <h6><?= htmlspecialchars($book['title']); ?> by <?= htmlspecialchars($book['author']); ?></h6>
                        <img src="uploads/<?= htmlspecialchars($book['image']); ?>" alt="<?= htmlspecialchars($book['title']); ?>" style="max-width: 100px; max-height: 150px;">
                    </div>
                <?php } ?>
                
                <!-- Delete Order Button -->
                <form method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="delete_order_id" value="<?= $order['id']; ?>">
                    <button type="submit">Delete Order</button>
                </form>
            </div>
        <?php } ?>
    </div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Check for success messages
    <?php if (isset($_SESSION['success_message'])) { ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= $_SESSION['success_message']; ?>',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['success_message']); ?>
    <?php } ?>
</script>

</body>
</html>
