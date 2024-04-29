<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'coach') {
    header("Location: ../../login.php");
    exit();
}

include '../../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $coach_id = $_SESSION['coach_id'];
    $date = $_POST['date'];
    $duration = $_POST['duration'];
    $location = $_POST['location'];
    $focus_areas = $_POST['focus_areas'];

    $sql = "INSERT INTO training_sessions (coach_id, date, duration, location, focus_areas) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issis", $coach_id, $date, $duration, $location, $focus_areas);
    $stmt->execute();

    $stmt->close();

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announce Training</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Announce Training</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="date">Date:</label><br>
            <input type="date" id="date" name="date" required><br>
            <label for="duration">Duration:</label><br>
            <input type="text" id="duration" name="duration" required><br>
            <label for="location">Location:</label><br>
            <input type="text" id="location" name="location" required><br>
            <label for="focus_areas">Focus Areas:</label><br>
            <input type="text" id="focus_areas" name="focus_areas" required><br><br>
            <input type="submit" value="Announce Training">
        </form>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
