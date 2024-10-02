<?php
// Start session and initialize variables
session_start();
$error = '';
$success = '';
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
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Check if the username already exists
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $userExists = $stmt->fetchColumn();

        if ($userExists) {
            $error = 'Username already exists.';
        } else {
            // Insert the new user into the users table
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
                $success = 'Signup successful! You can now <a href="login.php">login</a>.';
            } else {
                $error = 'Failed to sign up. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
</head>
<body>
    <h2>Sign Up</h2>
    
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Username:</label>
        <input type="text" name="username" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit">Sign Up</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</body>
</html>
