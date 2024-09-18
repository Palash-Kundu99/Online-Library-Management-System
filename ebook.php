<?php
session_start();
include 'db.php';

// Handle search query for eBooks
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

// Fetch eBooks from the database based on the search query
$ebooks_query = "SELECT * FROM pdfs";
if (!empty($search_query)) {
    $search_query = $conn->real_escape_string($search_query);
    $ebooks_query .= " WHERE title LIKE '%$search_query%'";
}
$ebooks = $conn->query($ebooks_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eBooks</title>
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


        /* eBook and Footer Section Styling */
        .container { width: 100%; margin: 30px auto; }
        h2 { text-align: center; }
        .ebooks { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .ebook { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); width: 250px; }
        .ebook img { max-width: 100%; border-radius: 8px; margin-bottom: 10px; }
        .ebook h3 { margin: 0; font-size: 18px; }
        .ebook p { margin: 5px 0 10px; color: #666; }
        .ebook a { text-decoration: none; color: #4CAF50; font-weight: bold; }

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
        footer h3 { margin-bottom: 15px; }
        footer p, footer ul { color: #ddd; font-size: 14px; }
        footer ul { list-style-type: none; padding: 0; }
        footer ul li { margin: 8px 0; }
        footer ul li a { color: #ddd; text-decoration: none; }
        footer ul li a:hover { color: #4CAF50; }
        footer .social-media a { margin: 0 10px; text-decoration: none; color: white; }
        footer .social-media a:hover { color: #4CAF50; }
        .book {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 220px; /* Fixed width for all books */
    height: 350px; /* Fixed height for all books */
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    justify-content: space-between;
}

.book img {
    width: 100%;        /* Make the image fill the width of the container */
    height: 170px;      /* Set a fixed height */
    object-fit: cover;  /* Crop the image to fit within the defined dimensions */
    border-radius: 8px; /* Keep the rounded corners */
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
    <a href="index.php" style="text-decoration: none; color: inherit;">
        <h1>B𝔦𝔟𝔩𝔦𝔬𝔱𝔯𝔢𝔢</h1>
    </a>
</div>

    <div class="search-bar">
        <form action="ebook.php" method="POST">
            <input type="text" name="search" placeholder="Search eBooks..." value="<?= htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <div class="nav-links">
        <?php if (isset($_SESSION['user'])): ?>
            Welcome, <?= $_SESSION['user']['name']; ?> | <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="auth.php">Login / Register</a>
        <?php endif; ?>
       
        <a href="about.html">About Us</a>
        <a href="index.php">Home</a>
        
    </div>
</nav>

<!-- eBook Section -->
<div class="container">
    <h2>Available eBooks</h2>
    
    <!-- eBook Display -->
    <div class="ebooks">
        <?php if ($ebooks->num_rows > 0): ?>
            <?php while ($ebook = $ebooks->fetch_assoc()) { ?>
                <div class="ebook">
                    <img src="uploads/<?= $ebook['cover_image']; ?>" alt="<?= htmlspecialchars($ebook['title']); ?>">
                    <h3><?= htmlspecialchars($ebook['title']); ?></h3>
                    <a href="pdfs/<?= htmlspecialchars($ebook['file_name']); ?>" target="_blank">Read eBook</a>
                </div>
            <?php } ?>
        <?php else: ?>
            <p>No eBooks found matching your search.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Footer Section -->
<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h3>Contact Us</h3>
            <p>123 Library Lane, Booktown, BK 45678</p>
            <p>Email: support@library.com</p>
            <p>Phone: +1 234 567 8900</p>
        </div>

        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>

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
