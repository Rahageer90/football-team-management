<?php
include '../../includes/db_connect.php';

function fetchMatchDetails($match_id)
{
    global $conn;
    $sql_match = "SELECT * FROM matches WHERE match_id = ?";
    $stmt_match = $conn->prepare($sql_match);
    $stmt_match->bind_param("i", $match_id);
    $stmt_match->execute();
    $result_match = $stmt_match->get_result();

    if ($result_match->num_rows > 0) {
        return $result_match->fetch_assoc();
    } else {
        return false;
    }
}

function fetchPlayersList()
{
    global $conn;
    $sql_players = "SELECT * FROM accounts WHERE role = 'player'";
    return $conn->query($sql_players);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Lineup</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>

<body>
    <div class="container">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Handle form submission for updating lineup
            // Assuming form fields are named 'player_id[]' and 'position_played[]'
            $match_id = $_POST['match_id'];
            $player_ids = $_POST['player_id'];
            $positions = $_POST['position_played'];

            // Delete existing lineup for the match
            $sql_delete = "DELETE FROM match_lineups WHERE match_id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $match_id);
            $stmt_delete->execute();
            $stmt_delete->close();

            // Insert new lineup
            $sql_insert = "INSERT INTO match_lineups (match_id, player_id, position_played) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iss", $match_id, $player_id, $position_played);

            foreach ($player_ids as $key => $player_id) {
                $position_played = $positions[$key];
                $stmt_insert->execute();
            }

            $stmt_insert->close();
            header("Location: matches_list.php");
            exit();
        } else {
            // Fetch match details
            $match_id = $_GET['match_id'];
            $match_details = fetchMatchDetails($match_id);
            if (!$match_details) {
                echo "<p>Match details not found.</p>";
                exit();
            }

            // Fetch list of players
            $result_players = fetchPlayersList();
        }
        ?>

        <h2>Update Lineup for <?php echo $match_details['opponent']; ?></h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">
            <?php if ($result_players->num_rows > 0) : ?>
                <?php while ($row = $result_players->fetch_assoc()) : ?>
                    <input type="checkbox" id="player_<?php echo $row['account_id']; ?>" name="player_id[]" value="<?php echo $row['account_id']; ?>">
                    <label for="player_<?php echo $row['account_id']; ?>"><?php echo $row['name']; ?></label><br>
                    <select id="position_<?php echo $row['account_id']; ?>" name="position_played[]">
                        <option value="Goalkeeper">Goalkeeper</option>
                        <option value="Defender">Defender</option>
                        <option value="Midfielder">Midfielder</option>
                        <option value="Forward">Forward</option>
                    </select><br>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No players found.</p>
            <?php endif; ?>
            <input type="submit" value="Submit">
        </form>
    </div>
</body>

</html>
