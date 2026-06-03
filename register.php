<?php
// register.php
require_once 'config/database.php';

$departments = [];
$departmentQuery = $conn->query('SELECT id, name FROM departments ORDER BY name');
if ($departmentQuery) {
    while ($department = $departmentQuery->fetch_assoc()) {
        $departments[] = $department;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $home_address = trim($_POST['home_address']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $department_id = (int) $_POST['department_id'];
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_sql = 'SELECT id FROM users WHERE username = ? OR email = ?';
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $error = 'Username or email already exists.';
    } else {
        $sql = 'INSERT INTO users (full_name, home_address, email, phone, department_id, username, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, "employee")';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssiss', $full_name, $home_address, $email, $phone, $department_id, $username, $password);

        if ($stmt->execute()) {
            $success = 'Registration successful. You can log in now.';
            header('refresh:2;url=login.php');
        } else {
            $error = 'Registration failed: ' . $conn->error;
        }
    }
}

$pageTitle = 'CIMEN Limited | Register';
include 'includes/header.php';
?>

<div class="auth-layout animate-rise">
    <aside class="auth-aside">
        <div>
            <div class="eyebrow">Employee onboarding</div>
            <h2>Create a professional account in minutes.</h2>
            <p class="mt-3 mb-0">Employees register with their department so the HR team can organize records correctly from day one.</p>
        </div>

        <div class="department-summary">
            <div class="mini-card">
                <strong class="d-block text-white mb-2">Required details</strong>
                <span>Full name, home address, contact information, username, password, and department.</span>
            </div>
            <div class="mini-card">
                <strong class="d-block text-white mb-2">Account policy</strong>
                <span>Registration creates an employee account by default. Admin privileges are reserved.</span>
            </div>
        </div>
    </aside>

    <section class="auth-card">
        <div class="auth-heading">
            <div class="eyebrow">Sign up</div>
            <h2>Employee registration</h2>
            <p>Complete the form to create your account.</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="row g-3">
                <div class="col-12">
                    <label for="full_name" class="form-label">Full name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required>
                </div>

                <div class="col-12">
                    <label for="home_address" class="form-label">Home address</label>
                    <textarea id="home_address" name="home_address" rows="3" class="form-control" required></textarea>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone number</label>
                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="Optional">
                </div>

                <div class="col-md-6">
                    <label for="department_id" class="form-label">Department</label>
                    <select id="department_id" name="department_id" class="form-select" required>
                        <option value="">Select department</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?php echo (int) $department['id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>

                <div class="col-12">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn btn-accent w-100 py-3 mt-4">Create account</button>
        </form>

        <div class="text-center mt-4">
            <p class="mb-2 helper-copy">Already have an account?</p>
            <a href="login.php" class="btn btn-outline-light">Login here</a>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>