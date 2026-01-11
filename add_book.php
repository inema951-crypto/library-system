<?php
include 'includes/config.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 

    $title = sanitizeInput($_POST['title']);
    $author = sanitizeInput($_POST['author']);
    $genre = sanitizeInput($_POST['genre']);
    $year = sanitizeInput($_POST['year']);
    $cover_image = 'default.jpg'; // Default cover image

    // Handle file upload
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/images/covers/';
        $file_ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = uniqid('book_', true) . '.' . $file_ext;
            move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_dir . $new_filename);
            $cover_image = $new_filename;
        }
    }

    $insert_query = "INSERT INTO books (title, author, genre, year, cover_image) 
                    VALUES ('$title', '$author', '$genre', '$year', '$cover_image')";

    if ($conn->query($insert_query)) {
        $_SESSION['success_message'] = "Book added successfully";
        header("Location: admin.php?tab=books");
        exit();
    } else {
        $_SESSION['error_message'] = "Error adding book: " . $conn->error;
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h1>Add New Book</h1>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="add_book.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="form-group">
            <label for="title">Book Title</label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" required>
            </div>
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="year">Publication Year</label>
                <input type="number" id="year" name="year" required>
            </div>
            <div class="form-group">
                <label for="cover_image">Cover Image</label>
                <div class="file-upload">
                    <input type="file" id="cover_image" name="cover_image" accept="image/*">
                    <label for="cover_image" class="file-upload-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Choose file...</span>
                    </label>
                    <div class="file-upload-preview"></div>
                </div>
            </div>
        </div>
        
        <div class="form-footer">
            <button type="submit" class="btn btn-submit">
                <i class="fas fa-plus-circle"></i> Add Book
            </button>
            <a href="admin.php?tab=books" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<script>
// Preview uploaded image
document.getElementById('cover_image').addEventListener('change', function(e) {
    const preview = document.querySelector('.file-upload-preview');
    preview.innerHTML = '';
    
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(this.files[0]);
    }
});
</script>

<?php include 'includes/footer.php'; ?>