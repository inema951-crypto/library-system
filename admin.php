<?php
include 'includes/config.php'; // This already starts the session


redirectIfNotLoggedIn();
redirectIfNotAdmin();



// Now safe to use $_SESSION
if (isset($_SESSION['full_name'])) {
    $name = htmlspecialchars($_SESSION['full_name']);

    $hour = date("H");
    if ($hour < 12) {
        $greeting = "Good morning";
    } elseif ($hour < 18) {
        $greeting = "Good afternoon";
    } else {
        $greeting = "Good evening";
    }

    echo "<div class='welcome-message'><h2>$greeting, $name!</h2></div>";
} else {
    echo "<div class='welcome-message'><h2>Welcome, Guest!</h2></div>";
}


// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $request_id = sanitizeInput($_POST['request_id']);
        $new_status = sanitizeInput($_POST['new_status']);
        
        $update_query = "UPDATE book_requests SET status = '$new_status'";
        
        // Add rejection reason if provided
        if ($new_status === 'Rejected' && !empty($_POST['rejection_reason'])) {
            $reason = sanitizeInput($_POST['rejection_reason']);
            $update_query .= ", rejection_reason = '$reason'";
        } else {
            $update_query .= ", rejection_reason = NULL";
        }
        
        $update_query .= " WHERE id = '$request_id'";
        
        if ($conn->query($update_query)) {
            $_SESSION['success_message'] = "Request status updated to " . htmlspecialchars($new_status);
        } else {
            $_SESSION['error_message'] = "Error updating request: " . $conn->error;
        }
    }
    
    // Handle user edits
    if (isset($_POST['edit_user'])) {
        $user_id = sanitizeInput($_POST['user_id']);
        $student_id = sanitizeInput($_POST['student_id']);
        $full_name = sanitizeInput($_POST['full_name']);
        $email = sanitizeInput($_POST['email']);
        $department = sanitizeInput($_POST['department']);
        $year = sanitizeInput($_POST['year']);
         $role = sanitizeInput($_POST['role']);
        
        $update_query = "UPDATE users SET 
                        student_id = '$student_id',
                        full_name = '$full_name',
                        email = '$email',
                        department = '$department',
                        year = '$year',
                        role = '$role'
                        WHERE id = '$user_id'";
        
        if ($conn->query($update_query)) {
            $_SESSION['success_message'] = "User updated successfully";

        } else {
            $_SESSION['error_message'] = "Error updating user: " . $conn->error;
        }
    }
    
    // Handle book edits
    if (isset($_POST['edit_book'])) {
        $book_id = sanitizeInput($_POST['book_id']);
        $title = sanitizeInput($_POST['title']);
        $author = sanitizeInput($_POST['author']);
        $genre = sanitizeInput($_POST['genre']);
        $year = sanitizeInput($_POST['year']);
        
        $update_query = "UPDATE books SET 
                        title = '$title',
                        author = '$author',
                        genre = '$genre',
                        year = '$year'
                        WHERE id = '$book_id'";
        
        if ($conn->query($update_query)) {
            $_SESSION['success_message'] = "Book updated successfully";
        } else {
            $_SESSION['error_message'] = "Error updating book: " . $conn->error;
        }
    }
    
    header("Location: admin.php");
    exit();
}

// Handle deletions
if (isset($_GET['delete'])) {
    $type = sanitizeInput($_GET['type']);
    $id = sanitizeInput($_GET['delete']);
    
    if ($type === 'request') {
        $delete_query = "DELETE FROM book_requests WHERE id = '$id'";
        $success_msg = "Request deleted successfully";
    } elseif ($type === 'user') {
        $delete_query = "DELETE FROM users WHERE id = '$id'";
        $success_msg = "User deleted successfully";
    } elseif ($type === 'book') {
        $delete_query = "DELETE FROM books WHERE id = '$id'";
        $success_msg = "Book deleted successfully";
    }
    
    if ($conn->query($delete_query)) {
        $_SESSION['success_message'] = $success_msg;
    } else {
        $_SESSION['error_message'] = "Error deleting $type: " . $conn->error;
    }
    header("Location: admin.php");
    exit();
}

// Fetch data based on current tab
$current_tab = isset($_GET['tab']) ? sanitizeInput($_GET['tab']) : 'requests';

// Fetch requests
if ($current_tab === 'requests') {
    $query = "SELECT r.id, r.book_title, r.status, r.rejection_reason,
                     u.id as user_id, u.student_id, u.full_name, u.email, u.department, u.year
              FROM book_requests r
              JOIN users u ON r.user_id = u.id
              ORDER BY FIELD(r.status, 'Pending', 'Approved', 'Rejected'), r.id DESC";
    $requests_result = $conn->query($query);
}

// Fetch users
if ($current_tab === 'users') {
    $query = "SELECT * FROM users ORDER BY role DESC, full_name ASC";
    $users_result = $conn->query($query);
}

// Fetch books
if ($current_tab === 'books') {
    $query = "SELECT * FROM books ORDER BY title ASC";
    $books_result = $conn->query($query);
}
?>

<?php include 'includes/header.php'; ?>

<div class="admin-container">
    <h1>Admin Dashboard</h1>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>
    
    <div class="admin-tabs">
        <a href="?tab=requests" class="<?php echo $current_tab === 'requests' ? 'active' : ''; ?>">
            <i class="fas fa-book"></i> Book Requests
        </a>
        <a href="?tab=users" class="<?php echo $current_tab === 'users' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="?tab=books" class="<?php echo $current_tab === 'books' ? 'active' : ''; ?>">
            <i class="fas fa-book-open"></i> Books
        </a>
    </div>
    
    <div class="admin-content">
        <?php if ($current_tab === 'requests'): ?>
            <!-- Book Requests Tab -->
            <div class="admin-actions">
                <div class="status-filters">
                    <a  class="btn-approve" href="?tab=requests&filter=all" class="<?php echo (!isset($_GET['filter']) || $_GET['filter'] === 'all') ? 'btn-approve' : ''; ?>">All</a>
                    <a   href="?tab=requests&filter=pending" class="<?php echo (isset($_GET['filter']) && $_GET['filter'] === 'pending') ? 'btn-approve' : 'btn-delete'; ?>">Pending</a>
                    <a  href="?tab=requests&filter=approved" class="<?php echo (isset($_GET['filter']) && $_GET['filter'] === 'approved') ? 'btn-approve' : 'btn-delete'; ?>">Approved</a>
                    <a  href="?tab=requests&filter=rejected" class="<?php echo (isset($_GET['filter']) && $_GET['filter'] === 'rejected') ? 'btn-approve' : 'btn-delete'; ?>">Rejected</a>
                </div>
            </div>
            
            <?php if ($requests_result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Book Requested</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $requests_result->fetch_assoc()): ?>
                                <?php 
                                // Skip if filter doesn't match
                                if (isset($_GET['filter']) && $_GET['filter'] !== 'all' && 
                                    strtolower($row['status']) !== $_GET['filter']) {
                                    continue;
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <strong>Name: </strong><?php echo htmlspecialchars($row['full_name']); ?></strong>
                                            <div><strong>User ID: </strong><?php echo htmlspecialchars($row['student_id']); ?></div>
                                            <div><strong>Department: </strong><?php echo htmlspecialchars($row['department']); ?> - Year <?php echo htmlspecialchars($row['year']); ?></div>
                                            <div><strong>E-mail: </strong><?php echo htmlspecialchars($row['email']); ?></div>
                                        </div>
                                    </td>

                                    <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <div class="action-buttons">
                                            <!-- Approve Button -->
                                            <form method="POST" action="admin.php" class="inline-form">
                                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="new_status" value="Approved">
                                                <button type="submit" name="update_status" class="btn btn-approve" onclick="return confirm('Approve this request?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            
                                            <!-- Reject Button with Modal -->
                                            <button class="btn btn-reject" onclick="openRejectModal(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <button class="btn btn-delete" onclick="confirmDelete('request', <?php echo $row['id']; ?>)">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-book-open"></i>
                    <p>No book requests found.</p>
                </div>
            <?php endif; ?>
            
            <!-- Reject Modal (hidden by default) -->
            <div id="rejectModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeRejectModal()">&times;</span>
                    <h3>Reject Book Request</h3>
                    <form method="POST" action="admin.php">
                        <input type="hidden" name="request_id" id="modal_request_id">
                        <input type="hidden" name="new_status" value="Rejected">
                        
                        <div class="form-group">
                            <label for="rejection_reason">Reason for Rejection (Optional)</label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="3"></textarea>
                        </div>
                        
                        <div class="modal-actions">
                            <button type="button" class="btn btn-cancel" onclick="closeRejectModal()">Cancel</button>
                            <button type="submit" name="update_status" class="btn btn-confirm-reject">Confirm Rejection</button>
                        </div>
                    </form>
                </div>
            </div>
            
        <?php elseif ($current_tab === 'users'): ?>
            <!-- Users Tab -->
            <div class="admin-actions">
                <a href="register.php" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Add New User
                </a>
            </div>
            
            <?php if ($users_result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Year</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $users_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['department']); ?></td>
                                    <td><?php echo htmlspecialchars($row['year']); ?></td>
                                    <td>
                                        <span class="role-badge role-<?php echo strtolower($row['role']); ?>">
                                            <?php echo htmlspecialchars($row['role']); ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <div class="action-buttons">
                                            <!-- Edit Button with Modal -->
                                            <button class="btn-edit" onclick="openEditUserModal(
                                                <?php echo $row['id']; ?>,
                                                '<?php echo htmlspecialchars($row['student_id']); ?>',
                                                '<?php echo htmlspecialchars($row['full_name']); ?>',
                                                '<?php echo htmlspecialchars($row['email']); ?>',
                                                '<?php echo htmlspecialchars($row['department']); ?>',
                                                '<?php echo htmlspecialchars($row['year']); ?>',
                                                '<?php echo htmlspecialchars($row['role']); ?>'
                                            )">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <?php if ($row['role'] !== 'admin' || $users_result->num_rows > 1): ?>
                                                <button class="btn-delete" onclick="confirmDelete('user', <?php echo $row['id']; ?>)">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-users"></i>
                    <p>No users found.</p>
                </div>
            <?php endif; ?>
            
            <!-- Edit User Modal -->
            <div id="editUserModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeEditUserModal()">&times;</span>
                    <h3>Edit User</h3>
                    <form method="POST" action="admin.php">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit_student_id">Student ID</label>
                                <input type="text" id="edit_student_id" name="student_id" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_full_name">Full Name</label>
                                <input type="text" id="edit_full_name" name="full_name" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_email">Email</label>
                            <input type="email" id="edit_email" name="email" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit_department">Department</label>
                                <select id="edit_department" name="department" required>
                                    <option value="Computer Science">Computer Science</option>
                                    <option value="Business">Business</option>
                                    <option value="Theology">Theology</option>
                                    <option value="Education">Education</option>
                                    <option value="Health Sciences">Health Sciences</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_year">Year</label>
                                <select id="edit_year" name="year" required>
                                    <option value="1">Year 1</option>
                                    <option value="2">Year 2</option>
                                    <option value="3">Year 3</option>
                                    <option value="4">Year 4</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_role">Role</label>
                            <select id="edit_role" name="role" >
                                <option value="student">Student</option>
                                <option value="admin">Admin</option>
                            </select>
                            <small>Role can only be changed by another admin</small>
                        </div>
                        
                        <div class="modal-actions">
                            <button type="button" class="btn btn-cancel" onclick="closeEditUserModal()">Cancel</button>
                            <button type="submit" name="edit_user" class="btn btn-save">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            
        <?php elseif ($current_tab === 'books'): ?>
            <!-- Books Tab -->
            <div class="admin-actions">
                <a href="add_book.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Book
                </a>
              
                <div class="search-bar">
    <form method="GET" action="books.php">
        <input type="text" name="search" placeholder="Search books..." id="bookSearch" >
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>
</div>
            </div>
            
            <?php if ($books_result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="data-table" id="booksTable">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Genre</th>
                                <th>Year</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $books_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                                    <td><?php echo htmlspecialchars($row['genre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['year']); ?></td>
                                    <td class="actions">
                                        <div class="action-buttons">
                                            <!-- Edit Button with Modal -->
                                            <button class="btn-edit" onclick="openEditBookModal(
                                                <?php echo $row['id']; ?>,
                                                '<?php echo htmlspecialchars($row['title']); ?>',
                                                '<?php echo htmlspecialchars($row['author']); ?>',
                                                '<?php echo htmlspecialchars($row['genre']); ?>',
                                                '<?php echo htmlspecialchars($row['year']); ?>'
                                            )">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <button class="btn-delete" onclick="confirmDelete('book', <?php echo $row['id']; ?>)">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-book-open"></i>
                    <p>No books found.</p>
                </div>
            <?php endif; ?>
            
            <!-- Edit Book Modal -->
            <div id="editBookModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeEditBookModal()">&times;</span>
                    <h3>Edit Book</h3>
                    <form method="POST" action="admin.php">
                        <input type="hidden" name="book_id" id="edit_book_id">
                        
                        <div class="form-group">
                            <label for="edit_title">Title</label>
                            <input type="text" id="edit_title" name="title" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit_author">Author</label>
                                <input type="text" id="edit_author" name="author" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_genre">Genre</label>
                                <input type="text" id="edit_genre" name="genre" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_year">Publication Year</label>
                            <input type="number" id="edit_year" name="year" required>
                        </div>
                        
                        <div class="modal-actions">
                            <button type="button" class="btn btn-cancel" onclick="closeEditBookModal()">Cancel</button>
                            <button type="submit" name="edit_book" class="btn btn-save">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Reject Modal Functions
function openRejectModal(requestId) {
    document.getElementById('modal_request_id').value = requestId;
    document.getElementById('rejectModal').style.display = 'block';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
    document.getElementById('rejection_reason').value = '';
}

// Edit User Modal Functions
function openEditUserModal(id, studentId, fullName, email, department, year, role) {
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_student_id').value = studentId;
    document.getElementById('edit_full_name').value = fullName;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_department').value = department;
    document.getElementById('edit_year').value = year;
    document.getElementById('edit_role').value = role;
    
    document.getElementById('editUserModal').style.display = 'block';
}

function closeEditUserModal() {
    document.getElementById('editUserModal').style.display = 'none';
}

// Edit Book Modal Functions
function openEditBookModal(id, title, author, genre, year) {
    document.getElementById('edit_book_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_author').value = author;
    document.getElementById('edit_genre').value = genre;
    document.getElementById('edit_year').value = year;
    
    document.getElementById('editBookModal').style.display = 'block';
}

function closeEditBookModal() {
    document.getElementById('editBookModal').style.display = 'none';
}

// Delete Confirmation
function confirmDelete(type, id) {
    if (confirm(`Are you sure you want to delete this ${type}? This action cannot be undone.`)) {
        window.location.href = `admin.php?delete=${id}&type=${type}`;
    }
}

// Book Search Functionality
document.getElementById('bookSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#booksTable tbody tr');
    
    rows.forEach(row => {
        const title = row.cells[0].textContent.toLowerCase();
        const author = row.cells[1].textContent.toLowerCase();
        const genre = row.cells[2].textContent.toLowerCase();
        const year = row.cells[3].textContent.toLowerCase();

        
        if (title.includes(searchTerm) || year.includes(searchTerm) || author.includes(searchTerm) || genre.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}
</script>

<?php include 'includes/footer.php'; ?>