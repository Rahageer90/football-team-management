<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

include '../../includes/db_connect.php';


function getAnnouncedMatches($conn)
{
    $sql_matches = "SELECT * FROM matches";
    $result_matches = $conn->query($sql_matches);
    $matches = [];
    if ($result_matches->num_rows > 0) {
        while ($row = $result_matches->fetch_assoc()) {
            $matches[] = $row;
        }
    }
    return $matches;
}


function announceMatch($conn, $date, $time, $opponent, $venue)
{
    $sql_match = "INSERT INTO matches (date, time, opponent, venue, result) VALUES (?, ?, ?, ?, 'TBA')";
    $stmt_match = $conn->prepare($sql_match);
    $stmt_match->bind_param("ssss", $date, $time, $opponent, $venue);
    $stmt_match->execute();

    if ($stmt_match->errno) {
        return "Error announcing match: " . $stmt_match->error;
    }

    $match_id = $stmt_match->insert_id;
    $stmt_match->close();

    return $match_id;
}


function deleteMatch($conn, $match_id)
{
    $sql_delete_match = "DELETE FROM matches WHERE match_id = ?";
    $stmt_delete_match = $conn->prepare($sql_delete_match);
    $stmt_delete_match->bind_param("i", $match_id);
    $stmt_delete_match->execute();

    if ($stmt_delete_match->errno) {
        return "Error deleting match: " . $stmt_delete_match->error;
    }

    $stmt_delete_match->close();
    return true;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $date = $_POST['date'];
    $time = $_POST['time'];
    $opponent = $_POST['opponent'];
    $venue = $_POST['venue'];

   
    $announceResult = announceMatch($conn, $date, $time, $opponent, $venue);
    if (is_numeric($announceResult)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = $announceResult;
    }
}

// Handle delete request
if (isset($_GET['delete_match_id'])) {
    $delete_match_id = $_GET['delete_match_id'];
    $deleteResult = deleteMatch($conn, $delete_match_id);
    if ($deleteResult) {
        header("Location: announce_match.php");
        exit();
    } else {
        $error_message = "Failed to delete match.";
    }
}

// Fetch all announced matches
$announced_matches = getAnnouncedMatches($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announce Match</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>

<body>
    <div class="container">
        <h2>Announce Match</h2>
        <?php if (isset($error_message)) : ?>
            <p><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="date">Date:</label><br>
            <input type="date" id="date" name="date" required><br>
            <label for="time">Time:</label><br>
            <input type="time" id="time" name="time" required><br>
            <label for="opponent">Opponent:</label><br>
            <input type="text" id="opponent" name="opponent" required><br>
            <label for="venue">Venue:</label><br>
            <input type="text" id="venue" name="venue" required><br>
            <input type="submit" value="Announce Match">
        </form>
        
        <h3>Announced Matches</h3>
        <ul>
            <?php foreach ($announced_matches as $match) : ?>
                <li>
                    <?php echo $match['date'] . ' - ' . $match['opponent'] . ' at ' . $match['venue']; ?>
                    <?php if ($match['result'] == 'TBA') : ?>
                        <a href="update_match.php?match_id=<?php echo $match['match_id']; ?>">Update Result</a>
                    <?php else : ?>
                        Result: <?php echo $match['result']; ?>
                    <?php endif; ?>
                    <a href="?delete_match_id=<?php echo $match['match_id']; ?>" onclick="return confirm('Are you sure you want to delete this match?')">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>

        <br>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>

</html>
