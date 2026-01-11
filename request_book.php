

<?php
 include 'includes/config.php'; 

// Example: Fetch user-submitted data (you can also use POST)
$user_id = $_SESSION['user_id']; // assuming user is logged in
$book_title = $_GET['book_id']; // make sure form uses POST
$status = 'pending'; // default status
$rejection_reason = ''; // default empty

// Prepare the query to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO book_requests (user_id, book_title, status, rejection_reason) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $user_id, $book_title, $status, $rejection_reason);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Book request submitted successfully.";
} else {
    $_SESSION['error_message'] = "Failed to submit request: " . $stmt->error;
}


// Redirect or display feedback
header("Location: books.php");
?>