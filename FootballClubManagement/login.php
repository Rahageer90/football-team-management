<?php
include 'includes/db_connect.php';
session_start();

if (isset($_SESSION['username'])) {
    $role = $_SESSION['role'];
    switch ($role) {
        case 'admin':
            header("Location: pages/admin/dashboard.php");
            break;
        case 'player':
            header("Location: pages/player/dashboard.php");
            break;
        case 'physio':
            header("Location: pages/physio/dashboard.php");
            break;
        case 'doctor':
            header("Location: pages/doctor/dashboard.php");
            break;
        case 'coach':
            header("Location: pages/coach/dashboard.php");
            break;
        default:
            header("Location: login.php");
            break;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM accounts WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['name'] = $row['name']; 
        switch ($row['role']) {
            case 'player':
                header("Location: pages/player/dashboard.php");
                break;
            case 'physio':
                header("Location: pages/physio/dashboard.php");
                break;
            case 'doctor':
                header("Location: pages/doctor/dashboard.php");
                break;
            case 'coach':
                header("Location: pages/coach/dashboard.php");
                break;
            default:
                header("Location: login.php");
                break;
        }
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php
    if (isset($error)) {
        echo '<p style="color:red;">' . $error . '</p>';
    }
    ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" value="Login">
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>
</body>
</html>
