<?php
// department.php
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$department = isset($_GET['dept']) ? $conn->real_escape_string($_GET['dept']) : '';
$is_admin = ($_SESSION['role'] == 'admin');

// Get department ID
$dept_sql = "SELECT id FROM departments WHERE name = ?";
$stmt = $conn->prepare($dept_sql);
$stmt->bind_param("s", $department);
$stmt->execute();
$dept_result = $stmt->get_result();
$dept_id = $dept_result->fetch_assoc()['id'] ?? null;

// Handle CRUD operations for admin
if($is_admin && $dept_id) {
    if(isset($_POST['add_employee'])) {
        $employee_id = $conn->real_escape_string($_POST['employee_id']);
        $full_name = $conn->real_escape_string($_POST['full_name']);
        $position = $conn->real_escape_string($_POST['position']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $hire_date = $conn->real_escape_string($_POST['hire_date']);
        $salary = $conn->real_escape_string($_POST['salary']);
        $status = $conn->real_escape_string($_POST['status']);
        
        $sql = "INSERT INTO employees (employee_id, full_name, position, department_id, email, phone, hire_date, salary, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssds", $employee_id, $full_name, $position, $dept_id, $email, $phone, $hire_date, $salary, $status);
        if($stmt->execute()) {
            $success = "Employee added successfully!";
        }
    }
    
    if(isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $conn->query("DELETE FROM employees WHERE id = $id AND department_id = $dept_id");
        header("Location: department.php?dept=" . urlencode($department));
        exit();
    }
    
    if(isset($_POST['update_employee'])) {
        $id = intval($_POST['id']);
        $employee_id = $conn->real_escape_string($_POST['employee_id']);
        $full_name = $conn->real_escape_string($_POST['full_name']);
        $position = $conn->real_escape_string($_POST['position']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $hire_date = $conn->real_escape_string($_POST['hire_date']);
        $salary = $conn->real_escape_string($_POST['salary']);
        $status = $conn->real_escape_string($_POST['status']);
        
        $sql = "UPDATE employees SET employee_id=?, full_name=?, position=?, email=?, phone=?, hire_date=?, salary=?, status=? 
                WHERE id=? AND department_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssdsii", $employee_id, $full_name, $position, $email, $phone, $hire_date, $salary, $status, $id, $dept_id);
        $stmt->execute();
        $success = "Employee updated successfully!";
    }
}

// Fetch employees for this department
$employees = [];
if($dept_id) {
    $sql = "SELECT * FROM employees WHERE department_id = $dept_id ORDER BY employee_id";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

include 'includes/header.php';
?>

<div class="table-container">
    <h2><?php echo htmlspecialchars($department); ?> Department</h2>
    
    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if($is_admin): ?>
        <div style="margin-bottom: 20px;">
            <button onclick="toggleAddForm()" class="btn" style="width: auto; background: var(--success);">+ Add Employee</button>
        </div>
        
        <div id="add-form" style="display: none; background: var(--light); padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3>Add New Employee</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Employee ID:</label>
                    <input type="text" name="employee_id" required>
                </div>
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" name="full_name" required>
                </div>
                <div class="form-group">
                    <label>Position:</label>
                    <input type="text" name="position" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="text" name="phone">
                </div>
                <div class="form-group">
                    <label>Hire Date:</label>
                    <input type="date" name="hire_date" required>
                </div>
                <div class="form-group">
                    <label>Salary:</label>
                    <input type="number" step="0.01" name="salary" required>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="on_leave">On Leave</option>
                    </select>
                </div>
                <button type="submit" name="add_employee" class="btn">Add Employee</button>
            </form>
        </div>
    <?php endif; ?>
    
    <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Position</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Hire Date</th>
                <th>Salary</th>
                <th>Status</th>
                <?php if($is_admin): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($employees as $emp): ?>
            <tr>
                <td><?php echo htmlspecialchars($emp['employee_id']); ?></td>
                <td><?php echo htmlspecialchars($emp['full_name']); ?></td>
                <td><?php echo htmlspecialchars($emp['position']); ?></td>
                <td><?php echo htmlspecialchars($emp['email']); ?></td>
                <td><?php echo htmlspecialchars($emp['phone']); ?></td>
                <td><?php echo htmlspecialchars($emp['hire_date']); ?></td>
                <td>$<?php echo number_format($emp['salary'], 2); ?></td>
                <td>
                    <span style="background: <?php echo $emp['status'] == 'active' ? '#27ae60' : ($emp['status'] == 'inactive' ? '#c0392b' : '#f39c12'); ?>; 
                         color: white; padding: 3px 8px; border-radius: 4px;">
                        <?php echo ucfirst($emp['status']); ?>
                    </span>
                </td>
                <?php if($is_admin): ?>
                    <td>
                        <button onclick="editEmployee(<?php echo $emp['id']; ?>)" style="background: #3498db; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Edit</button>
                        <a href="?dept=<?php echo urlencode($department); ?>&delete=<?php echo $emp['id']; ?>" 
                           onclick="return confirm('Are you sure?')" 
                           style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; margin-left: 5px;">Delete</a>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if($is_admin): ?>
<script>
function toggleAddForm() {
    var form = document.getElementById('add-form');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function editEmployee(id) {
    // In a real app, you'd load the data and show an edit modal
    // For demo purposes, we'll just alert
    alert('Edit functionality would open a modal with employee details. In production, this would load the employee data for editing.');
}
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>