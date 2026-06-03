<?php
require_once '../config/database.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    die('Database connection is not available.');
}

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['admin', 'super_admin'], true)) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'CIMEN Limited | Manage Employees';

$action = $_GET['action'] ?? '';
$employeeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$departments = [];
$departmentResult = $conn->query('SELECT id, name FROM departments ORDER BY name');
if ($departmentResult) {
    while ($department = $departmentResult->fetch_assoc()) {
        $departments[] = $department;
    }
}

if (isset($_POST['add'])) {
    $stmt = $conn->prepare('INSERT INTO employees (employee_id, full_name, position, department_id, email, phone, hire_date, salary, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param(
        'sssisssds',
        $_POST['employee_id'],
        $_POST['full_name'],
        $_POST['position'],
        $_POST['department_id'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['hire_date'],
        $_POST['salary'],
        $_POST['status']
    );

    if ($stmt->execute()) {
        header('Location: manage_employees.php?msg=added');
        exit();
    }
}

if (isset($_POST['update'])) {
    $stmt = $conn->prepare('UPDATE employees SET employee_id = ?, full_name = ?, position = ?, department_id = ?, email = ?, phone = ?, hire_date = ?, salary = ?, status = ? WHERE id = ?');
    $stmt->bind_param(
        'sssisssdsi',
        $_POST['employee_id'],
        $_POST['full_name'],
        $_POST['position'],
        $_POST['department_id'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['hire_date'],
        $_POST['salary'],
        $_POST['status'],
        $_POST['id']
    );

    if ($stmt->execute()) {
        header('Location: manage_employees.php?msg=updated');
        exit();
    }
}

if (isset($_GET['delete'])) {
    $deleteStmt = $conn->prepare('DELETE FROM employees WHERE id = ?');
    $deleteId = (int) $_GET['delete'];
    $deleteStmt->bind_param('i', $deleteId);
    $deleteStmt->execute();
    header('Location: manage_employees.php?msg=deleted');
    exit();
}

$employeesQuery = $conn->query('SELECT e.*, d.name AS department_name FROM employees e LEFT JOIN departments d ON e.department_id = d.id ORDER BY e.employee_id');
$employees = $employeesQuery ? $employeesQuery->fetch_all(MYSQLI_ASSOC) : [];

$editEmployee = null;
if ($action === 'edit' && $employeeId > 0) {
    $editStmt = $conn->prepare('SELECT * FROM employees WHERE id = ? LIMIT 1');
    $editStmt->bind_param('i', $employeeId);
    $editStmt->execute();
    $editResult = $editStmt->get_result();
    $editEmployee = $editResult->fetch_assoc();
}

include '../includes/header.php';
?>

<section class="page-card animate-rise">
    <div class="page-heading">
        <div class="eyebrow">Employee administration</div>
        <h2>Manage the full employee register.</h2>
        <p>Add, update, and remove employees from the central HR table. Print the table to save a PDF if needed.</p>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success no-print">
            <?php
                if ($_GET['msg'] === 'updated') echo 'Employee updated successfully.';
                if ($_GET['msg'] === 'deleted') echo 'Employee deleted successfully.';
                if ($_GET['msg'] === 'added') echo 'Employee added successfully.';
            ?>
        </div>
    <?php endif; ?>

    <div class="page-actions no-print">
        <button type="button" class="btn btn-accent js-print">Export / Print PDF</button>
        <?php if ($editEmployee): ?>
            <a href="manage_employees.php" class="btn btn-outline-light">Cancel edit</a>
        <?php endif; ?>
    </div>

    <div class="auth-card mb-4 no-print">
        <div class="card-heading">
            <h3><?php echo $editEmployee ? 'Edit employee' : 'Add employee'; ?></h3>
            <p>Use the form to maintain the employee record set.</p>
        </div>

        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($editEmployee['id'] ?? ''); ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Employee ID</label>
                    <input type="text" name="employee_id" class="form-control" value="<?php echo htmlspecialchars($editEmployee['employee_id'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Full name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($editEmployee['full_name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Position</label>
                    <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($editEmployee['position'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select" required>
                        <option value="">Select department</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?php echo (int) $department['id']; ?>" <?php echo (($editEmployee['department_id'] ?? '') == $department['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($department['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($editEmployee['email'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($editEmployee['phone'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hire date</label>
                    <input type="date" name="hire_date" class="form-control" value="<?php echo htmlspecialchars($editEmployee['hire_date'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Salary</label>
                    <input type="number" step="0.01" name="salary" class="form-control" value="<?php echo htmlspecialchars($editEmployee['salary'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" <?php echo (($editEmployee['status'] ?? '') === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo (($editEmployee['status'] ?? '') === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        <option value="on_leave" <?php echo (($editEmployee['status'] ?? '') === 'on_leave') ? 'selected' : ''; ?>>On leave</option>
                    </select>
                </div>
            </div>

            <div class="button-row mt-4">
                <button type="submit" name="<?php echo $editEmployee ? 'update' : 'add'; ?>" class="btn btn-accent">
                    <?php echo $editEmployee ? 'Update employee' : 'Add employee'; ?>
                </button>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="card-heading">
            <h3>Employee list</h3>
            <p>Records are arranged in a print-friendly table with the required data fields.</p>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee ID</th>
                        <th>Full name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Hire date</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td><?php echo (int) $employee['id']; ?></td>
                            <td><?php echo htmlspecialchars($employee['employee_id']); ?></td>
                            <td><?php echo htmlspecialchars($employee['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($employee['position']); ?></td>
                            <td><?php echo htmlspecialchars($employee['department_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($employee['email']); ?></td>
                            <td><?php echo htmlspecialchars($employee['phone']); ?></td>
                            <td><?php echo htmlspecialchars($employee['hire_date']); ?></td>
                            <td><?php echo number_format((float) $employee['salary'], 2); ?></td>
                            <td>
                                <span class="status-chip <?php echo $employee['status'] === 'active' ? 'status-active' : ($employee['status'] === 'inactive' ? 'status-inactive' : 'status-leave'); ?>">
                                    <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $employee['status']))); ?>
                                </span>
                            </td>
                            <td class="no-print">
                                <div class="action-row">
                                    <a href="?action=edit&id=<?php echo (int) $employee['id']; ?>" class="btn btn-outline-light btn-sm">Edit</a>
                                    <a href="?delete=<?php echo (int) $employee['id']; ?>" class="btn btn-outline-secondary btn-sm" data-confirm="Delete this employee?">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>