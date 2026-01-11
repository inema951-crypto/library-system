<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Books</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>


<section class="hero">
    <div class="hero-content">
        <h1>Welcome to AUCA Library</h1>
        <p>Digital Access to Infinite Knowledge</p>
        <div class="hero-buttons">
            <a href="books.php" class="btn btn-primary">Browse Books</a>
            <a href="register.php" class="btn btn-secondary">Join Now</a>
        </div>
    </div>
    <div class="hero-image">
        <img src="assets/images/library.jpg" alt="Library Banner">
    </div>
</section>

<section class="features">
    <h2>Our Features</h2>
    <div class="feature-grid">
        <div class="feature-card">
            <i class="fas fa-book-open"></i>
            <h3>User Management</h3>
            <p>Access thousands of books across various disciplines.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-laptop"></i>
            <h3>🔔 Instant Notifications</h3>
            <p>Request and manage books from anywhere, anytime.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-user-shield"></i>
            <h3>Secure my System</h3>
            <p>Your data and privacy are our top priority.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>