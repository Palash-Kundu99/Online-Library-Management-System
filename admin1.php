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

// Handle PDF and cover image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf_file']) && isset($_FILES['cover_image'])) {
    $title = $_POST['title'];

    // Handle PDF file upload
    $pdf = $_FILES['pdf_file'];
    $pdf_name = time() . "_" . basename($pdf['name']);
    $pdf_path = 'pdfs/' . $pdf_name;

    // Handle cover image upload
    $cover_image = $_FILES['cover_image'];
    $cover_image_name = time() . "_" . basename($cover_image['name']);
    $cover_image_path = 'uploads/' . $cover_image_name;

    // Move uploaded files to their respective directories
    if (move_uploaded_file($pdf['tmp_name'], $pdf_path) && move_uploaded_file($cover_image['tmp_name'], $cover_image_path)) {
        $conn->query("INSERT INTO pdfs (file_name, cover_image, title) VALUES ('$pdf_name', '$cover_image_name', '$title')");
        $_SESSION['success_message'] = 'PDF and cover image uploaded successfully!';
    } else {
        $_SESSION['success_message'] = 'File upload failed!';
    }
    header("Location: admin1.php");
    exit;
}

// Handle order deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_order_id'])) {
    $order_id = intval($_POST['delete_order_id']);
    $conn->query("DELETE FROM orders WHERE id = $order_id");
    $_SESSION['success_message'] = 'Order deleted successfully!';
    header("Location: admin1.php");
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
        .pdf-item img { max-width: 100px; max-height: 150px; display: block; margin-bottom: 10px; }
    </style>
</head>
<body>

<!-- Admin Container -->
<div class="container">
    <h2>Admin Panel</h2>

    <!-- Upload PDF Form -->
    <div class="upload-form">
        <h3>Upload a New PDF with Cover</h3>
        <form method="POST" enctype="multipart/form-data">
            <label>Title:</label>
            <input type="text" name="title" required>
            
            <label>Cover Image:</label>
            <input type="file" name="cover_image" accept="image/*" required>

            <label>PDF File:</label>
            <input type="file" name="pdf_file" accept="application/pdf" required>
            
            <button type="submit">Upload PDF</button>
        </form>
    </div>

    <!-- Display Uploaded PDFs -->
    <div class="pdfs">
        <h3>Uploaded PDFs</h3>
        <?php while ($pdf = $pdfs->fetch_assoc()) { ?>
            <div class="pdf-item">
                <img src="uploads/<?= htmlspecialchars($pdf['cover_image']); ?>" alt="<?= htmlspecialchars($pdf['title']); ?>">
                <h4><?= htmlspecialchars($pdf['title']); ?></h4>
                <a href="pdfs/<?= htmlspecialchars($pdf['file_name']); ?>" target="_blank">View PDF</a>
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
