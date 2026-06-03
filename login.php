<?php
// login.php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['forgot_password'])) {
        $email = $conn->real_escape_string($_POST['email']);
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $sql = "UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiry, $email);
        
        if($stmt->execute() && $stmt->affected_rows > 0) {
            // In a real application, send email here
            $success = "Password reset link has been sent to your email! (Demo: Token: " . $token . ")";
        } else {
            $error = "Email not found!";
        }
    } else {
        $username = $conn->real_escape_string($_POST['username']);
        $password = $_POST['password'];
        
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if(password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Username not found!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BuildMaster Construction</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Employee Login</h2>
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="#" onclick="showForgotPassword()">Forgot Password?</a>
        </div>
        
        <div id="forgot-form" style="display: none; margin-top: 20px;">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Enter your email for password recovery:</label>
                    <input type="email" name="email" required>
                </div>
                <button type="submit" name="forgot_password" class="btn btn-secondary">Send Recovery Email</button>
            </form>
        </div>
        
        <p style="text-align: center; margin-top: 20px;">
            <a href="register.php" class="btn" style="display: inline-block; text-decoration: none;">Sign Up</a>
        </p>
    </div>
    
    <script>
        function showForgotPassword() {
            var form = document.getElementById('forgot-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>