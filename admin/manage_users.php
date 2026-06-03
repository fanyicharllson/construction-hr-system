<?php
// admin/manage_users.php
require_once '../config/database.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    die('Database connection is not available.');
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'super_admin') {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'CIMEN Limited | Manage Users';

$departments = [];
$departmentResult = $conn->query('SELECT id, name FROM departments ORDER BY name');
if ($departmentResult) {
    while ($department = $departmentResult->fetch_assoc()) {
        $departments[] = $department;
    }
}

if (isset($_POST['add_user'])) {
    $stmt = $conn->prepare('INSERT INTO users (full_name, home_address, email, phone, department_id, username, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'] === 'admin' ? 'admin' : 'employee';
    $departmentId = (int) $_POST['department_id'];

    $stmt->bind_param(
        'ssssisss',
        $_POST['full_name'],
        $_POST['home_address'],
        $_POST['email'],
        $_POST['phone'],
        $departmentId,
        $_POST['username'],
        $hashedPassword,
        $role
    );

    if ($stmt->execute()) {
        header('Location: manage_users.php?msg=added');
        exit();
    }
}

if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $deleteStmt = $conn->prepare('DELETE FROM users WHERE id = ? AND role <> \'super_admin\'');
    $deleteStmt->bind_param('i', $deleteId);
    $deleteStmt->execute();
    header('Location: manage_users.php?msg=deleted');
    exit();
}

$usersQuery = $conn->query('SELECT u.*, d.name AS department_name FROM users u LEFT JOIN departments d ON u.department_id = d.id ORDER BY FIELD(u.role, \'super_admin\', \'admin\', \'employee\'), u.full_name');
$users = $usersQuery ? $usersQuery->fetch_all(MYSQLI_ASSOC) : [];

$superCount = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'super_admin'")->fetch_assoc()['count'] ?? 0;
$adminCount = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'admin'")->fetch_assoc()['count'] ?? 0;
$employeeCount = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'employee'")->fetch_assoc()['count'] ?? 0;

include '../includes/header.php';
?>

<section class="page-card animate-rise">
    <div class="page-heading">
        <div class="eyebrow">Super-admin console</div>
        <h2>User administration</h2>
        <p>Create admin accounts, review all users, and keep the personnel register visible on the frontend.</p>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success no-print">
            <?php
                if ($_GET['msg'] === 'added') echo 'User created successfully.';
                if ($_GET['msg'] === 'deleted') echo 'User removed successfully.';
            ?>
        </div>
    <?php endif; ?>

    <div class="hero-kpis mb-4">
        <div class="metric-card">
            <span>Super admins</span>
            <div class="value"><?php echo (int) $superCount; ?></div>
        </div>
        <div class="metric-card">
            <span>Admins</span>
            <div class="value"><?php echo (int) $adminCount; ?></div>
        </div>
        <div class="metric-card">
            <span>Employees</span>
            <div class="value"><?php echo (int) $employeeCount; ?></div>
        </div>
    </div>

    <div class="page-actions no-print">
        <button type="button" class="btn btn-accent js-print">Export / Print PDF</button>
    </div>

    <div class="auth-card mb-4 no-print">
        <div class="card-heading">
            <h3>Create user</h3>
            <p>Super admins can create admin or employee accounts.</p>
        </div>

        <form method="POST" action="">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Full name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Home address</label>
                    <input type="text" name="home_address" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select" required>
                        <option value="">Select department</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?php echo (int) $department['id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="employee">Employee</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>

            <div class="button-row mt-4">
                <button type="submit" name="add_user" class="btn btn-accent">Create user</button>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="card-heading">
            <h3>Frontend user register</h3>
            <p>All users, admins, and the super admin are visible here for quick review.</p>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Created at</th>
                        <th class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo (int) $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td><?php echo htmlspecialchars($user['department_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td>
                                <span class="status-chip <?php echo $user['role'] === 'super_admin' ? 'status-active' : ($user['role'] === 'admin' ? 'status-leave' : 'status-inactive'); ?>">
                                    <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $user['role']))); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            <td class="no-print">
                                <?php if ($user['role'] !== 'super_admin'): ?>
                                    <a href="?delete=<?php echo (int) $user['id']; ?>" class="btn btn-outline-secondary btn-sm" data-confirm="Delete this user?">Delete</a>
                                <?php else: ?>
                                    <span class="small-copy">Protected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
