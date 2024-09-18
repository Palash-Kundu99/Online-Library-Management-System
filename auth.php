<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Registration logic
    if (isset($_POST['register'])) {
        $name = $_POST['name'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')");
        $_SESSION['user'] = ['name' => $name, 'email' => $email];
        header('Location: index.php');
    }

    // Login logic
    if (isset($_POST['login'])) {
        $result = $conn->query("SELECT * FROM users WHERE email='$email'");
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = ['name' => $user['name'], 'email' => $user['email']];
                header('Location: index.php');
            } else {
                echo "Invalid password!";
            }
        } else {
            echo "No user found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .gradient-custom-2 {
            background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);
        }

        .gradient-form {
            height: 100vh;
        }

        .card-body {
            padding: 2rem;
        }

        .btn-primary {
            background-color: #4CAF50;
            border: none;
        }

        .btn-outline-danger {
            border-color: #d8363a;
            color: #d8363a;
        }

        .btn-outline-danger:hover {
            background-color: #d8363a;
            color: white;
        }

        .form-label {
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 0.3rem;
            padding: 0.75rem 1.25rem;
        }

        .btn-block {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<section class="h-100 gradient-form" style="background-color: #eee;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-xl-10">
        <div class="card rounded-3 text-black">
          <div class="row g-0">
            <!-- Left Column - Registration/Login Form -->
            <div class="col-lg-6">
              <div class="card-body p-md-5 mx-md-4">

                <div class="text-center">
                  <img src="img/logo.png" style="width: 100px;" alt="Library Logo">
                  <h4 class="mt-1 mb-5 pb-1">Welcome to Bibliotree</h4>
                </div>

                <!-- Register Form -->
                <form method="POST">
                  <p class="text-center mb-4">Please register below</p>

                  <div class="form-outline mb-4">
                    <input type="text" name="name" class="form-control" required />
                    <label class="form-label">Name</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="email" name="email" class="form-control" required />
                    <label class="form-label">Email</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="password" name="password" class="form-control" required />
                    <label class="form-label">Password</label>
                  </div>

                  <div class="text-center mb-4">
                    <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit" name="register">Register</button>
                  </div>
                </form>

                <!-- Login Form -->
                <h4 class="text-center mb-4">Already have an account?</h4>
                <form method="POST">
                  <div class="form-outline mb-4">
                    <input type="email" name="email" class="form-control" required />
                    <label class="form-label">Email</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="password" name="password" class="form-control" required />
                    <label class="form-label">Password</label>
                  </div>

                  <div class="text-center mb-4">
                    <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit" name="login">Login</button>
                    <a class="text-muted" href="#!">Forgot password?</a>
                  </div>
                </form>

              </div>
            </div>

            <!-- Right Column - Background & Info -->
            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                <h4 class="mb-4">Access the world of books</h4>
                <p class="small mb-0">Explore, discover, and enjoy thousands of books with our library system. Your journey to knowledge and imagination starts here.</p>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

</body>
</html>
