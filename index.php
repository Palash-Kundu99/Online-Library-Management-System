<?php
session_start();
include 'db.php';

// Handle search query
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

// Fetch books from the database based on the search query
$books_query = "SELECT * FROM books";
if (!empty($search_query)) {
    $search_query = $conn->real_escape_string($search_query);
    $books_query .= " WHERE title LIKE '%$search_query%' OR author LIKE '%$search_query%'";
}
$books = $conn->query($books_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        
        /* Navbar Styling */
        nav { 
            background: #4CAF50; 
            padding: 15px 0; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        nav .logo {
            display: flex;
            align-items: center;
        }
        nav .logo img { 
            height: 50px; 
            margin-right: 15px; 
        }
        nav .nav-links { 
            display: flex; 
            align-items: center; 
        }
        nav a { 
            color: white; 
            margin: 0 15px; 
            text-decoration: none; 
            font-weight: bold;
        }
        nav a:hover {
            color: #f0f0f0;
        }
        /* Search Bar Styling */
        nav .search-bar {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 5px;
            padding: 5px 10px;
        }
        nav .search-bar input {
            border: none;
            padding: 8px;
            border-radius: 5px;
            font-size: 16px;
            width: 200px;
        }
        nav .search-bar button {
            border: none;
            background: #4CAF50;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        nav .search-bar button:hover {
            background: #45a049;
        }

        /* Book and Footer Section Styling */
        .container { width: 100%; margin: 30px auto; }
        h2 { text-align: center; }
        .books { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .book { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); width: 250px; }
        .book img { max-width: 100%; border-radius: 8px; margin-bottom: 10px; }
        .book h3 { margin: 0; font-size: 18px; }
        .book p { margin: 5px 0 10px; color: #666; }
        .book a { text-decoration: none; color: #4CAF50; font-weight: bold; }

        /* Footer Styling */
        footer {
            background-color: #333;
            color: white;
            padding: 40px 0;
            margin-top: 50px;
        }
        footer .footer-container { display: flex; justify-content: space-between; width: 80%; margin: 0 auto; }
        footer .footer-section {
            width: 30%;
        }
        footer h3 {
            color: #fff;
            margin-bottom: 15px;
        }
        footer p, footer ul {
            color: #ddd;
            font-size: 14px;
        }
        footer ul {
            list-style-type: none;
            padding: 0;
        }
        footer ul li {
            margin: 8px 0;
        }
        footer ul li a {
            color: #ddd;
            text-decoration: none;
        }
        footer ul li a:hover {
            color: #4CAF50;
        }
        footer .social-media a {
            margin: 0 10px;
            text-decoration: none;
            color: white;
        }
        footer .social-media a:hover {
            color: #4CAF50;
        }
        .books {
    display: flex;
    flex-wrap: wrap;
    gap: 20px; /* Adjust as needed for spacing between books */
    justify-content: center;
}

.book {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: calc(20% - 20px); /* Adjust width to fit 5 books per row with gap */
    box-sizing: border-box; /* Ensures padding and border are included in the width */
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.book img {
    width: 100%; /* Make sure the image fills the width of the card */
    height: 270px; /* Fixed height for images */
    object-fit: cover; /* Ensure images cover the area without distortion */
    border-radius: 8px;
    margin-bottom: 10px;
}

.book h3 {
    margin: 0;
    font-size: 18px;
}

.book p {
    margin: 5px 0 10px;
    color: #666;
}


@media (max-width: 1200px) {
    .book {
        width: calc(25% - 20px); /* Adjust width for medium screens to 4 books per row */
    }
}

@media (max-width: 800px) {
    .book {
        width: calc(33.33% - 20px); /* Adjust width for smaller screens to 3 books per row */
    }
}

@media (max-width: 600px) {
    .book {
        width: calc(50% - 20px); /* Adjust width for very small screens to 2 books per row */
    }
}

    </style>
</head>
<body>

<!-- Navbar -->
<nav>
    <div class="logo">
        <img src="img/logo.png" alt="Library Logo" style="width: 70px; height: 70px; margin-right: 15px;">
        <h1>B𝔦𝔟𝔩𝔦𝔬𝔱𝔯𝔢𝔢</h1>
    </div>
    <div class="search-bar">
        <form action="index.php" method="POST">
            <input type="text" name="search" placeholder="Search books..." value="<?= htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <div class="nav-links">
        <?php if (isset($_SESSION['user'])): ?>
            Welcome, <?= $_SESSION['user']['name']; ?> | <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="auth.php">Login / Register</a>
        <?php endif; ?>
        <a href="about.html" style="color: white; margin: 0 15px; text-decoration: none;">About Us</a>
       
        <a href="ebook.php" style="color: white; margin: 0 15px; text-decoration: none;">Find Ebook</a>
    </div>
</nav>

<!-- Book Section -->
<div class="container">
    <h2>Available Books</h2>
    
    <!-- Book Selection Form -->
    <form action="book.php" method="POST">
        <div class="books">
            <?php if ($books->num_rows > 0): ?>
                <?php while ($book = $books->fetch_assoc()) { ?>
                    <div class="book">
                        <img src="uploads/<?= $book['image']; ?>" alt="<?= $book['title']; ?>">
                        <h3><?= $book['title']; ?></h3>
                        <p>by <?= $book['author']; ?></p>

                        <!-- Add to Cart checkbox -->
                        <input type="checkbox" name="book_ids[]" value="<?= $book['id'] ?>"> I want to borrow this
                    </div>
                <?php } ?>
            <?php else: ?>
                <p>No books found matching your search.</p>
            <?php endif; ?>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <button type="submit" style="background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Proceed to Checkout</button>
        </div>
    </form>
</div>

<!-- Footer Section -->
<footer>
    <div class="footer-container">
        <!-- Contact Us Section -->
        <div class="footer-section">
            <h3>Contact Us</h3>
            <p>123 Library Lane, Booktown, BK 45678</p>
            <p>Email: support@library.com</p>
            <p>Phone: +1 234 567 8900</p>
        </div>

        <!-- Quick Links Section -->
        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><?php if (isset($_SESSION['user'])): ?>
                    Welcome, <?= $_SESSION['user']['name']; ?> | <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="auth.php">Login / Register</a>
                <?php endif; ?></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>

        <!-- Social Media Section -->
        <div class="footer-section">
            <h3>Follow Us</h3>
            <p>Stay connected with us on social media:</p>
            <div class="social-media">
                <a href="https://facebook.com">Facebook</a>
                <a href="https://twitter.com">Twitter</a>
                <a href="https://instagram.com">Instagram</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
