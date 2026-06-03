<?php
// admin/manage_employees.php
require_once '../config/database.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    die('Database connection is not available.');
}

function db_escape(mysqli $conn, $value) {
    return $conn->real_escape_string((string) $value);
}

// Check if user is admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle different operations
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle employee update
if(isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $employee_id = db_escape($conn, $_POST['employee_id']);
    $full_name = db_escape($conn, $_POST['full_name']);
    $position = db_escape($conn, $_POST['position']);
    $department_id = intval($_POST['department_id']);
    $email = db_escape($conn, $_POST['email']);
    $phone = db_escape($conn, $_POST['phone']);
    $hire_date = db_escape($conn, $_POST['hire_date']);
    $salary = floatval($_POST['salary']);
    $status = db_escape($conn, $_POST['status']);
    
    $sql = "UPDATE employees SET employee_id='$employee_id', full_name='$full_name', position='$position', 
            department_id=$department_id, email='$email', phone='$phone', hire_date='$hire_date', 
            salary=$salary, status='$status' WHERE id=$id";
    
    if($conn->query($sql)) {
        header("Location: manage_employees.php?msg=updated");
        exit();
    }
}

// Handle employee deletion
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM employees WHERE id=$id");
    header("Location: manage_employees.php?msg=deleted");
    exit();
}

// Get all employees with department names
$sql = "SELECT e.*, d.name as department_name 
        FROM employees e 
        LEFT JOIN departments d ON e.department_id = d.id 
        ORDER BY e.employee_id";
$result = $conn->query($sql);

// Get departments for dropdown
$depts_result = $conn->query("SELECT * FROM departments ORDER BY name");

// Get single employee for editing
$edit_employee = null;
if($action == 'edit' && $id > 0) {
    $edit_sql = "SELECT * FROM employees WHERE id=$id";
    $edit_result = $conn->query($edit_sql);
    $edit_employee = $edit_result->fetch_assoc();
}

include '../includes/header.php';
?>

<div class="container" style="max-width: 1400px;">
    <h2>Manage All Employees</h2>
    
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success">
            <?php 
                if($_GET['msg'] == 'updated') echo "Employee updated successfully!";
                if($_GET['msg'] == 'deleted') echo "Employee deleted successfully!";
                if($_GET['msg'] == 'added') echo "Employee added successfully!";
            ?>
        </div>
    <?php endif; ?>
    
    <!-- Add Employee Form -->
    <div style="background: var(--light); padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h3><?php echo $edit_employee ? 'Edit Employee' : 'Add New Employee'; ?></h3>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $edit_employee['id'] ?? ''; ?>">
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <div class="form-group">
                    <label>Employee ID:</label>
                    <input type="text" name="employee_id" value="<?php echo $edit_employee['employee_id'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" name="full_name" value="<?php echo $edit_employee['full_name'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Position:</label>
                    <input type="text" name="position" value="<?php echo $edit_employee['position'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Department:</label>
                    <select name="department_id" required>
                        <option value="">Select Department</option>
                        <?php while($dept = $depts_result->fetch_assoc()): ?>
                            <option value="<?php echo $dept['id']; ?>" 
                                <?php echo ($edit_employee && $edit_employee['department_id'] == $dept['id']) ? 'selected' : ''; ?>>
                                <?php echo $dept['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo $edit_employee['email'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="text" name="phone" value="<?php echo $edit_employee['phone'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Hire Date:</label>
                    <input type="date" name="hire_date" value="<?php echo $edit_employee['hire_date'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Salary:</label>
                    <input type="number" step="0.01" name="salary" value="<?php echo $edit_employee['salary'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status">
                        <option value="active" <?php echo ($edit_employee && $edit_employee['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($edit_employee && $edit_employee['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        <option value="on_leave" <?php echo ($edit_employee && $edit_employee['status'] == 'on_leave') ? 'selected' : ''; ?>>On Leave</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" name="<?php echo $edit_employee ? 'update' : 'add'; ?>" class="btn">
                <?php echo $edit_employee ? 'Update Employee' : 'Add Employee'; ?>
            </button>
            
            <?php if($edit_employee): ?>
                <a href="manage_employees.php" class="btn" style="background: #95a5a6; text-decoration: none; margin-left: 10px;">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Employees Table -->
    <h3>Employee List</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Position</th>
                <th>Department</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Hire Date</th>
                <th>Salary</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['employee_id']); ?></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['position']); ?></td>
                <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo $row['hire_date']; ?></td>
                <td>$<?php echo number_format($row['salary'], 2); ?></td>
                <td>
                    <span style="background: <?php echo $row['status'] == 'active' ? '#27ae60' : ($row['status'] == 'inactive' ? '#c0392b' : '#f39c12'); ?>; 
                         color: white; padding: 3px 8px; border-radius: 4px;">
                        <?php echo ucfirst($row['status']); ?>
                    </span>
                </td>
                <td>
                    <a href="?action=edit&id=<?php echo $row['id']; ?>" style="background: #3498db; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none;">Edit</a>
                    <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; margin-left: 5px;">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>