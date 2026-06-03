<?php
// login.php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['forgot_password'])) {
        $email = trim($_POST['email']);
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $conn->prepare('UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?');
        $stmt->bind_param('sss', $token, $expiry, $email);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $success = 'Password recovery request created. Use the token in your email workflow.';
        } else {
            $error = 'Email not found.';
        }
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $stmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header('Location: dashboard.php');
                exit();
            }

            $error = 'Invalid password.';
        } else {
            $error = 'Username not found.';
        }
    }
}

$pageTitle = 'CIMEN Limited | Login';
include 'includes/header.php';
?>

<div class="auth-layout animate-rise">
    <aside class="auth-aside">
        <div>
            <div class="eyebrow">Secure access</div>
            <h2>Sign in to the CIMEN HR suite.</h2>
            <p class="mt-3 mb-0">Employees use this portal to access their records, while privileged users manage departments and workforce data.</p>
        </div>

        <div class="department-summary">
            <div class="mini-card">
                <strong class="d-block text-white mb-2">Admin controls</strong>
                <span>Restricted department access and employee management.</span>
            </div>
            <div class="mini-card">
                <strong class="d-block text-white mb-2">Password recovery</strong>
                <span>Recovery token flow is prepared for email-based reset handling.</span>
            </div>
        </div>
    </aside>

    <section class="auth-card">
        <div class="auth-heading">
            <div class="eyebrow">Employee login</div>
            <h2>Welcome back.</h2>
            <p>Use your username and password to continue.</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="mb-3">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-accent w-100 py-3">Login</button>
        </form>

        <button class="btn btn-outline-light w-100 mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#forgotPasswordForm" aria-expanded="false" aria-controls="forgotPasswordForm">
            Forgot password
        </button>

        <div class="collapse" id="forgotPasswordForm">
            <div class="surface-card p-3 p-lg-4">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <button type="submit" name="forgot_password" class="btn btn-outline-secondary w-100">Send recovery link</button>
                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="mb-2 helper-copy">New employee access</p>
            <a href="register.php" class="btn btn-outline-light">Create an account</a>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>