<?php
session_start();
require 'db_connection.php'; // adjust path as needed

if (isset($_POST['book_id']) && isset($_SESSION['user_id'])) {
    $book_id = intval($_POST['book_id']);
    $user_id = intval($_SESSION['user_id']);
    $date = date('Y-m-d H:i:s');
    
    // Prevent duplicate requests (optional)
    $check = $conn->prepare("SELECT id FROM book_requests WHERE user_id=? AND book_id=?");
    $check->bind_param("ii", $user_id, $book_id);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        echo "You have already requested this book.";
    } else {
        $stmt = $conn->prepare("INSERT INTO book_requests (user_id, book_id, request_date, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("iis", $user_id, $book_id, $date);
        
        if ($stmt->execute()) {
            echo "Book requested successfully!";
        } else {
            echo "Failed to request the book.";
        }
        $stmt->close();
    }
    $check->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
