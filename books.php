<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/welcome.php';


if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>  

  <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

<h1>Our Reading Room</h1>

<div class="search-bar">
    <form method="GET" action="books.php">
        <input type="text" name="search" placeholder="Search by title, author or genre..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>
</div>

<div class="books-container">
    <?php
    $search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
    $query = "SELECT * FROM books";
    
    if (!empty($search)) {
        $query .= " WHERE title LIKE '%$search%' OR author LIKE '%$search%' OR genre LIKE '%$search%'";
    }
    
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="book-card">';
            echo '<div class="book-cover">';
            echo '<img src="uploads/' . htmlspecialchars($row['cover_image']) . '" alt="' . htmlspecialchars($row['title']) . '">';
            echo '</div>';
            echo '<div class="book-details">';
            echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
            echo '<p><strong>Author:</strong> ' . htmlspecialchars($row['author']) . '</p>';
            
          if (isset($row['year'])) {
    echo "Year: " . $row['year'];
} else {
    echo "Year: Not available";
}
;  if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
                echo '<a href="request_book.php?book_id=' . urlencode($row['title']) . '" class="btn btn-small">Request Book</a>';
            }
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p class="no-results">No books found. Please try a different search.</p>';
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?>