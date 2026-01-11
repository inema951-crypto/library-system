<?php include 'includes/config.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = "Email and password are required";
    } else {
        // Check user credentials
        $query = "SELECT id, role, student_id, full_name, email, password FROM users WHERE email = '$email'";
        $result = $conn->query($query);
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['student_id'] = $user['student_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: books.php");
                }
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}

// Display success message after registration if exists
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h1>Login to Your Account</h1>
    
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    
    <div class="form-footer">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>