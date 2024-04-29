<?php
include '../../includes/db_connect.php';

function fetchAnnouncedMatches()
{
    global $conn;
    $sql_matches = "SELECT * FROM matches";
    return $conn->query($sql_matches);
}

function displayMatchesList($result_matches)
{
    if ($result_matches->num_rows > 0) {
        while ($row = $result_matches->fetch_assoc()) {
            echo "<li>";
            echo "<strong>Date:</strong> {$row['date']} | ";
            echo "<strong>Opponent:</strong> {$row['opponent']} | ";
            echo "<strong>Venue:</strong> {$row['venue']}";
            echo "<a href=\"update_lineup.php?match_id={$row['match_id']}\">Update Lineup</a>";
            echo "</li>";
        }
    } else {
        echo "<li>No matches announced yet.</li>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matches List</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>

<body>
    <div class="container">
        <h2>Announced Matches</h2>
        <ul>
            <?php displayMatchesList(fetchAnnouncedMatches()); ?>
        </ul>
    </div>
</body>

</html>
