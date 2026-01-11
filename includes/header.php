<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Library Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
           
            <nav  style="padding: 20px; display: flex; justify-content: space-between;width: 100%;">
                <ul >
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="books.php"><i class="fas fa-book"></i> View Books</a></li>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <li><a href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <?php else: ?>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a></li>
                       
                </ul>

                 <?php endif; ?>
                 <ul>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php endif; ?>
                    <ul>
            </nav>
 
        </div>
    </header>
    <main class="container">