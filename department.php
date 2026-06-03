<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['admin', 'super_admin'], true)) {
    header('Location: login.php');
    exit();
}

$departmentName = isset($_GET['dept']) ? trim($_GET['dept']) : '';
$pageTitle = 'CIMEN Limited | Department';

$deptStmt = $conn->prepare('SELECT id, name FROM departments WHERE name = ? LIMIT 1');
$deptStmt->bind_param('s', $departmentName);
$deptStmt->execute();
$deptResult = $deptStmt->get_result();
$selectedDepartment = $deptResult->fetch_assoc();

if (!$selectedDepartment) {
    header('Location: dashboard.php');
    exit();
}

$departmentId = (int) $selectedDepartment['id'];

if (isset($_POST['add_employee'])) {
    $stmt = $conn->prepare('INSERT INTO employees (employee_id, full_name, position, department_id, email, phone, hire_date, salary, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param(
        'sssisssds',
        $_POST['employee_id'],
        $_POST['full_name'],
        $_POST['position'],
        $departmentId,
        $_POST['email'],
        $_POST['phone'],
        $_POST['hire_date'],
        $_POST['salary'],
        $_POST['status']
    );

    if ($stmt->execute()) {
        header('Location: department.php?dept=' . urlencode($departmentName) . '&msg=added');
        exit();
    }
}

if (isset($_POST['update_employee'])) {
    $stmt = $conn->prepare('UPDATE employees SET employee_id = ?, full_name = ?, position = ?, email = ?, phone = ?, hire_date = ?, salary = ?, status = ? WHERE id = ? AND department_id = ?');
    $stmt->bind_param(
        'ssssssdsii',
        $_POST['employee_id'],
        $_POST['full_name'],
        $_POST['position'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['hire_date'],
        $_POST['salary'],
        $_POST['status'],
        $_POST['id'],
        $departmentId
    );

    if ($stmt->execute()) {
        header('Location: department.php?dept=' . urlencode($departmentName) . '&msg=updated');
        exit();
    }
}

if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $deleteStmt = $conn->prepare('DELETE FROM employees WHERE id = ? AND department_id = ?');
    $deleteStmt->bind_param('ii', $deleteId, $departmentId);
    $deleteStmt->execute();
    header('Location: department.php?dept=' . urlencode($departmentName) . '&msg=deleted');
    exit();
}

$action = $_GET['action'] ?? '';
$employeeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$editEmployee = null;

if ($action === 'edit' && $employeeId > 0) {
    $editStmt = $conn->prepare('SELECT * FROM employees WHERE id = ? AND department_id = ? LIMIT 1');
    $editStmt->bind_param('ii', $employeeId, $departmentId);
    $editStmt->execute();
    $editResult = $editStmt->get_result();
    $editEmployee = $editResult->fetch_assoc();
}

$employeesQuery = $conn->prepare('SELECT * FROM employees WHERE department_id = ? ORDER BY employee_id');
$employeesQuery->bind_param('i', $departmentId);
$employeesQuery->execute();
$employeesResult = $employeesQuery->get_result();
$employees = $employeesResult ? $employeesResult->fetch_all(MYSQLI_ASSOC) : [];

include 'includes/header.php';
?>

<section class="page-card animate-rise">
    <div class="page-heading">
        <div class="eyebrow">Department records</div>
        <h2><?php echo htmlspecialchars($selectedDepartment['name']); ?> department</h2>
        <p>Manage the employees assigned to this department. The table is print-ready for PDF export.</p>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success no-print">
            <?php
                if ($_GET['msg'] === 'added') echo 'Employee added successfully.';
                if ($_GET['msg'] === 'updated') echo 'Employee updated successfully.';
                if ($_GET['msg'] === 'deleted') echo 'Employee deleted successfully.';
            ?>
        </div>
    <?php endif; ?>

    <div class="page-actions no-print">
        <button type="button" class="btn btn-accent js-print">Export / Print PDF</button>
        <?php if ($editEmployee): ?>
            <a href="department.php?dept=<?php echo urlencode($departmentName); ?>" class="btn btn-outline-light">Cancel edit</a>
        <?php endif; ?>
    </div>

    <div class="auth-card mb-4 no-print">
        <div class="card-heading">
            <h3><?php echo $editEmployee ? 'Edit employee' : 'Add employee'; ?></h3>
            <p>Only the administrator can manage these records.</p>
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
                <button type="submit" name="<?php echo $editEmployee ? 'update_employee' : 'add_employee'; ?>" class="btn btn-accent">
                    <?php echo $editEmployee ? 'Update employee' : 'Add employee'; ?>
                </button>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="card-heading">
            <h3>Department employee table</h3>
            <p>Ten data fields are visible here, matching the brief before the action column.</p>
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
                            <td><?php echo htmlspecialchars($selectedDepartment['name']); ?></td>
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
                                    <a href="?dept=<?php echo urlencode($departmentName); ?>&action=edit&id=<?php echo (int) $employee['id']; ?>" class="btn btn-outline-light btn-sm">Edit</a>
                                    <a href="?dept=<?php echo urlencode($departmentName); ?>&delete=<?php echo (int) $employee['id']; ?>" class="btn btn-outline-secondary btn-sm" data-confirm="Delete this employee?">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>