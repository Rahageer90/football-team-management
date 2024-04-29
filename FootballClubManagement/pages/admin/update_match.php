<?php
include '../../includes/db_connect.php';

// Function to update match result
function updateMatchResult($conn, $match_id, $result)
{
    $sql_update = "UPDATE matches SET result = ? WHERE match_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $result, $match_id);
    $stmt_update->execute();

    if ($stmt_update->errno) {
        return "Error updating match result: " . $stmt_update->error;
    }

    $stmt_update->close();
    return true;
}

// Function to update player stats
function updatePlayerStats($conn, $match_id, $player_id, $minutes_played, $goals_scored, $rating)
{
    $sql_update = "UPDATE match_lineups SET minutes_played = ?, goals_scored = ?, rating = ? WHERE match_id = ? AND player_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("iiiis", $minutes_played, $goals_scored, $rating, $match_id, $player_id);
    $stmt_update->execute();

    if ($stmt_update->errno) {
        return "Error updating player stats: " . $stmt_update->error;
    }

    $stmt_update->close();
    return true;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming form fields are named 'result', 'player_id', 'minutes_played', 'goals_scored', 'rating'
    $result = $_POST['result'];
    $match_id = $_GET['match_id'];

    // Update match result
    $updateResult = updateMatchResult($conn, $match_id, $result);
    if (!$updateResult) {
        $error_message = "Failed to update match result.";
    }

    // Check if lineup is already announced
    if (isset($_POST['player_id'])) {
        foreach ($_POST['player_id'] as $player_id) {
            $minutes_played = $_POST['minutes_played'][$player_id];
            $goals_scored = $_POST['goals_scored'][$player_id];
            $rating = $_POST['rating'][$player_id];
            $updateStats = updatePlayerStats($conn, $match_id, $player_id, $minutes_played, $goals_scored, $rating);
            if (!$updateStats) {
                $error_message = "Failed to update player stats.";
            }
        }
    }

    if (!isset($error_message)) {
        header("Location: announce_match.php");
        exit();
    }
}

// Fetch match details
if (isset($_GET['match_id'])) {
    $match_id = $_GET['match_id'];
    $sql_match = "SELECT * FROM matches WHERE match_id = ?";
    $stmt_match = $conn->prepare($sql_match);
    $stmt_match->bind_param("i", $match_id);
    $stmt_match->execute();
    $result_match = $stmt_match->get_result();

    if ($result_match->num_rows > 0) {
        $match_details = $result_match->fetch_assoc();
    } else {
        $error_message = "Match details not found.";
    }
}

// Fetch players for the match lineup
$sql_players = "SELECT a.*, m.position_played FROM accounts a LEFT JOIN match_lineups m ON a.account_id = m.player_id WHERE m.match_id = ?";
$stmt_players = $conn->prepare($sql_players);
$stmt_players->bind_param("i", $match_id);
$stmt_players->execute();
$result_players = $stmt_players->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Match</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>

<body>
    <div class="container">
        <h2>Update Match</h2>
        <?php if (isset($error_message)) : ?>
            <p><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?match_id=" . $match_id); ?>">
            <label for="result">Result:</label><br>
            <input type="text" id="result" name="result" value="<?php echo isset($match_details['result']) ? $match_details['result'] : ''; ?>" required><br>
            <?php
            // Fetch match lineup status
            $sql_lineup_status = "SELECT * FROM match_lineups WHERE match_id = ?";
            $stmt_lineup_status = $conn->prepare($sql_lineup_status);
            $stmt_lineup_status->bind_param("i", $match_id);
            $stmt_lineup_status->execute();
            $result_lineup_status = $stmt_lineup_status->get_result();

            if ($result_lineup_status->num_rows > 0) {
                echo '<label for="lineup_status">Lineup Status:</label><br>';
                echo 'Lineup Updated<br>';
                // Fetch players and show their stats fields
                while ($row = $result_players->fetch_assoc()) {
                    echo '<label for="player_' . $row['account_id'] . '">' . ($row['name'] ?? 'Unknown') . '</label><br>';
                    echo '<input type="hidden" name="player_id[]" value="' . $row['account_id'] . '">';
                    echo '<label for="minutes_played_' . $row['account_id'] . '">Minutes Played:</label>';
                    echo '<input type="number" id="minutes_played_' . $row['account_id'] . '" name="minutes_played[' . $row['account_id'] . ']" min="0" placeholder="Minutes Played"><br>';
                    echo '<label for="goals_scored_' . $row['account_id'] . '">Goals Scored:</label>';
                    echo '<input type="number" id="goals_scored_' . $row['account_id'] . '" name="goals_scored[' . $row['account_id'] . ']" min="0" placeholder="Goals Scored"><br>';
                    echo '<label for="rating_' . $row['account_id'] . '">Rating:</label>';
                    echo '<input type="number" id="rating_' . $row['account_id'] . '" name="rating[' . $row['account_id'] . ']" min="0" max="10" placeholder="Rating"><br>';
                }
            } else {
                echo 'Lineup not updated yet';
            }
            ?>
            <br>
            <input type="submit" value="Submit">
        </form>
    </div>
</body>

</html>
