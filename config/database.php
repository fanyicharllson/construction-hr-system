<?php
// config/database.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST') ?: (file_exists('/.dockerenv') ? 'db' : '127.0.0.1'));
}

if (!defined('DB_PORT')) {
    define('DB_PORT', (int) (getenv('DB_PORT') ?: 3306));
}

if (!defined('DB_USER')) {
    define('DB_USER', getenv('DB_USER') ?: 'root');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', getenv('DB_PASS') ?: 'root');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', getenv('DB_NAME') ?: 'construction_hr');
}

class Database {
    private $connection;
    
    public function __construct() {
        $this->connect();
        $this->ensureSchema();
    }
    
    private function connect() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $lastException = null;

        for ($attempt = 1; $attempt <= 10; $attempt++) {
            try {
                $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
                $this->connection->set_charset("utf8mb4");
                return;
            } catch (mysqli_sql_exception $exception) {
                $lastException = $exception;

                if ($attempt < 10) {
                    usleep(500000);
                    continue;
                }
            }
        }

        $message = 'Database connection failed after multiple attempts. Check DB_HOST, DB_PORT, DB_USER, DB_PASS, and DB_NAME, and make sure MySQL is running.';
        die($message . ' ' . $lastException->getMessage());
    }

    private function ensureSchema() {
        $departmentColumn = $this->connection->query("SHOW COLUMNS FROM users LIKE 'department_id'");
        if ($departmentColumn && $departmentColumn->num_rows === 0) {
            $this->connection->query("ALTER TABLE users ADD COLUMN department_id INT NULL AFTER phone");
        }

        $this->connection->query("ALTER TABLE users MODIFY role ENUM('super_admin', 'admin', 'employee') DEFAULT 'employee'");
        $this->connection->query("UPDATE users SET role = 'super_admin' WHERE username = 'admin' AND role = 'admin'");
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }
    
    public function escapeString($string) {
        return $this->connection->real_escape_string($string);
    }
}

$database = new Database();
$conn = $database->getConnection();
?>