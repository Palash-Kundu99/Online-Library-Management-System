<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit;
}

// Check if any books were selected
if (isset($_POST['book_ids'])) {
    $selected_books = $_POST['book_ids'];

    // Fetch details of selected books from the database
    $book_ids = implode(',', array_map('intval', $selected_books)); // Sanitize book IDs
    $books = $conn->query("SELECT * FROM books WHERE id IN ($book_ids)");
} else {
    echo "No books selected.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Checkout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        
        /* Container Styling */
        .container { width: 80%; margin: 30px auto; padding: 20px; background: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); border-radius: 8px; }
        h2 { text-align: center; margin-bottom: 20px; }
        .book-list { margin-bottom: 30px; }
        .book-list .book { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .book-list .book img { max-width: 80px; }
        .book-list .book h3 { margin: 0; }
        .checkout-form { display: flex; flex-direction: column; gap: 15px; }
        .checkout-form input, .checkout-form button { padding: 10px; font-size: 16px; }
        .checkout-form input { border: 1px solid #ccc; border-radius: 5px; }
        .checkout-form button { background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .checkout-form button:hover { background: #45a049; }
    </style>
</head>
<body>

<!-- Checkout Container -->
<div class="container">
    <h2>Checkout</h2>
    <p>Please review your selected books and complete the form to proceed with booking.</p>
    
    <!-- Selected Books -->
    <div class="book-list">
        <?php while ($book = $books->fetch_assoc()) { ?>
            <div class="book">
                <img src="uploads/<?= $book['image']; ?>" alt="<?= $book['title']; ?>">
                <h3><?= $book['title']; ?></h3>
                <p>by <?= $book['author']; ?></p>
            </div>
        <?php } ?>
    </div>

    <!-- Checkout Form -->
    <form class="checkout-form" id="checkoutForm" action="process_checkout.php" method="POST">
        <input type="text" name="name" value="<?= $_SESSION['user']['name']; ?>" readonly>
        <input type="text" name="No. of days" placeholder="Address" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        
        <!-- Hidden field to send selected book IDs to the processing script -->
        <input type="hidden" name="book_ids" value="<?= htmlspecialchars(implode(',', $selected_books)) ?>">

        <button type="submit">Submit booking</button>
    </form>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Handle form submission and show SweetAlert pop-up
    document.getElementById('checkoutForm').addEventListener('submit', function(event) {
        event.preventDefault();  // Prevent the form from submitting

        // Submit the form data via AJAX or Fetch API
        fetch('process_checkout.php', {
            method: 'POST',
            body: new FormData(this)
        }).then(response => response.text()).then(data => {
            // Show SweetAlert confirmation pop-up
            Swal.fire({
                title: 'Booking Complete!',
                text: 'Please visit our library to collect the books.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                // Optionally, you can redirect the user to another page after confirmation
                window.location.href = 'index.php';
            });
        }).catch(error => {
            console.error('Error:', error);
        });
    });
</script>

</body>
</html>
