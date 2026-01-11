<?php include 'includes/config.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and process form data
    $errors = [];
    $student_id = sanitizeInput($_POST['student_id']);
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $department = sanitizeInput($_POST['department']);
    $year = sanitizeInput($_POST['year']);
    $book_requested = isset($_POST['book_requested']) ? sanitizeInput($_POST['book_requested']) : '';

    // Validation
    if (empty($student_id)) $errors[] = "Student ID is required";
    if (empty($full_name)) $errors[] = "Full name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($password)) $errors[] = "Password is required";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    if (empty($department)) $errors[] = "Department is required";
    if (empty($year) || !is_numeric($year)) $errors[] = "Valid year of study is required";

    if (empty($errors)) {
        // Check if email or student ID already exists
        $check_query = "SELECT id FROM users WHERE email = '$email' OR student_id = '$student_id'";
        $check_result = $conn->query($check_query);
        
        if ($check_result->num_rows > 0) {
            $errors[] = "Email or Student ID already registered";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $insert_user = "INSERT INTO users (student_id, full_name, email, password, department, year) 
                           VALUES ('$student_id', '$full_name', '$email', '$hashed_password', '$department', '$year')";
            
            if ($conn->query($insert_user)) {
                $user_id = $conn->insert_id;
                
                // Insert book request if provided
                if (!empty($book_requested)) {
                    $insert_request = "INSERT INTO book_requests (user_id, book_title) VALUES ('$user_id', '$book_requested')";
                    $conn->query($insert_request);
                }
                
                $_SESSION['success_message'] = "Registration and book request submitted successfully. Please visit the librarian to collect the book.";
                header("Location: login.php");
                exit();
            } else {
                $errors[] = "Error: " . $conn->error;
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h1>Student Registration</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="register.php">
        <div class="form-group">
            <label for="student_id">Student ID</label>
            <input type="text" id="student_id" name="student_id" required 
                   value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required 
                   value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <div class="form-group">
            <label for="department">Department</label>
            <select id="department" name="department" required>
                <option value="">Select Department</option>
                <option value="Computer Science" <?php echo (isset($_POST['department']) && $_POST['department'] === 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
                <option value="Business" <?php echo (isset($_POST['department']) && $_POST['department'] === 'Business') ? 'selected' : ''; ?>>Business</option>
                <option value="Theology" <?php echo (isset($_POST['department']) && $_POST['department'] === 'Theology') ? 'selected' : ''; ?>>Theology</option>
                <option value="Education" <?php echo (isset($_POST['department']) && $_POST['department'] === 'Education') ? 'selected' : ''; ?>>Education</option>
                <option value="Health Sciences" <?php echo (isset($_POST['department']) && $_POST['department'] === 'Health Sciences') ? 'selected' : ''; ?>>Health Sciences</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="year">Year of Study</label>
            <select id="year" name="year" required>
                <option value="">Select Year</option>
                <option value="1" <?php echo (isset($_POST['year']) && $_POST['year'] == 1) ? 'selected' : ''; ?>>Year 1</option>
                <option value="2" <?php echo (isset($_POST['year']) && $_POST['year'] == 2) ? 'selected' : ''; ?>>Year 2</option>
                <option value="3" <?php echo (isset($_POST['year']) && $_POST['year'] == 3) ? 'selected' : ''; ?>>Year 3</option>
                <option value="4" <?php echo (isset($_POST['year']) && $_POST['year'] == 4) ? 'selected' : ''; ?>>Year 4</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="book_requested">Book Request (Optional)</label>
            <input type="text" id="book_requested" name="book_requested" 
                   value="<?php echo isset($_POST['book_requested']) ? htmlspecialchars($_POST['book_requested']) : (isset($_GET['request_book']) ? htmlspecialchars($_GET['request_book']) : ''); ?>">
        </div>
        
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    
    <div class="form-footer">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>