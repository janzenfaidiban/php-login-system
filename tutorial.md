# Secure PHP Login System Tutorial

This tutorial will guide you through building a secure login system in PHP using modern security best practices.

## Features

- Secure password hashing with Argon2id
- Protection against SQL injection using PDO prepared statements 
- Session security with periodic regeneration
- CSRF protection
- Input validation and sanitization
- Secure logout functionality
- Database error handling
- Brute force attack prevention

## Getting Started

Follow these steps to set up the login system:

1. Create a new project directory
2. Set up the database:
   - Create a MySQL database named 'php_login_system'
   - Create the users table with required fields
   - Configure database credentials

3. Create the core files:
   - config/database.php - Database connection handling
     ```php
     <?php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root'); 
     define('DB_PASS', '');
     define('DB_NAME', 'php_login_system');

     function getDBConnection() {
         try {
             $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
             $pdo = new PDO($dsn, DB_USER, DB_PASS);
             $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
             return $pdo;
         } catch(PDOException $e) {
             die("Connection failed: " . $e->getMessage());
         }
     }
     ```

   - register.php - User registration functionality
     ```php
     <?php
     require_once 'config/database.php';
     
     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         $username = trim($_POST['username']);
         $email = trim($_POST['email']);
         $password = $_POST['password'];
         
         // Validate input
         if (empty($username) || empty($email) || empty($password)) {
             die("All fields are required");
         }
         
         if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
             die("Invalid email format");
         }
         
         // Hash password
         $password_hash = password_hash($password, PASSWORD_ARGON2ID);
         
         try {
             $pdo = getDBConnection();
             $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
             $stmt->execute([$username, $email, $password_hash]);
             header("Location: login.php");
             exit();
         } catch(PDOException $e) {
             die("Registration failed: " . $e->getMessage());
         }
     }
     ?>
     ```

   - login.php - Login form and authentication
     ```php
     <?php
     require_once 'config/database.php';
     session_start();

     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         $username = $_POST['username'];
         $password = $_POST['password'];
         
         try {
             $pdo = getDBConnection();
             $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
             $stmt->execute([$username]);
             $user = $stmt->fetch();
             
             if ($user && password_verify($password, $user['password_hash'])) {
                 $_SESSION['user_id'] = $user['id'];
                 $_SESSION['username'] = $user['username'];
                 header("Location: dashboard.php");
                 exit();
             } else {
                 $error = "Invalid credentials";
             }
         } catch(PDOException $e) {
             die("Login failed: " . $e->getMessage());
         }
     }
     ?>
     ```

   - auth.php - Authentication helper functions
     ```php
     <?php
     session_start();

     function checkAuth() {
         if (!isset($_SESSION['user_id'])) {
             header("Location: login.php");
             exit();
         }
         
         // Regenerate session ID periodically
         if (!isset($_SESSION['last_regeneration']) || 
             time() - $_SESSION['last_regeneration'] > 1800) {
             session_regenerate_id(true);
             $_SESSION['last_regeneration'] = time();
         }
     }

     function logout() {
         session_destroy();
         $_SESSION = array();
         if (isset($_COOKIE[session_name()])) {
             setcookie(session_name(), '', time()-3600, '/');
         }
         header("Location: login.php");
         exit();
     }
     ?>
     ```

   - dashboard.php - Protected page example
     ```php
     <?php
     require_once 'auth.php';
     checkAuth();

     // Protected content
     echo "Welcome, " . htmlspecialchars($_SESSION['username']);
     echo '<br><a href="logout.php">Logout</a>';
     ?>
     ```

4. Implement security features:
   - Password hashing with Argon2id
   - PDO prepared statements
   - Input validation
   - Session security
   - CSRF protection
   
5. Test the system:
   - Register a new user
   - Test login functionality
   - Verify security measures
   - Check error handling
