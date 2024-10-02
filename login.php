<?php
// Start session and initialize variables
session_start();
$error = '';

$host = 'localhost';
$dbname = 'cat1';
$username_db = 'vera'; 
$password_db = 'vera';  

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture input data
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($username) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        // Query to find the user
        $stmt = $pdo->prepare('SELECT username, password FROM users WHERE username = :username');
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // Fetch user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['username'] = $user['username'];
            header('Location: welcome.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Username:</label>
        <input type="text" name="username" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
</body>
</html>
