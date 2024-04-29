<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/db_connect.php'; 

function fetchMatchLineupDetails($conn)
{
    if (isset($_GET['match_id'])) {
        $match_id = $_GET['match_id'];

        $sql_match = "SELECT * FROM matches WHERE match_id = ?";
        $stmt_match = $conn->prepare($sql_match);
        $stmt_match->bind_param("i", $match_id);
        $stmt_match->execute();
        $result_match = $stmt_match->get_result();

        if ($result_match->num_rows == 1) {
            $match = $result_match->fetch_assoc();

            $sql_lineup = "SELECT ml.player_id, ml.position_played, ml.minutes_played, ml.rating, a.name 
                           FROM match_lineups ml
                           INNER JOIN accounts a ON ml.player_id = a.account_id
                           WHERE ml.match_id = ?";
            $stmt_lineup = $conn->prepare($sql_lineup);
            $stmt_lineup->bind_param("i", $match_id);
            $stmt_lineup->execute();
            $result_lineup = $stmt_lineup->get_result();

            return array("match" => $match, "lineup" => $result_lineup);
        } else {
            return "Match not found.";
        }
    } else {
        return "Match ID not provided.";
    }
}

$matchLineupDetails = fetchMatchLineupDetails($conn);

if (is_array($matchLineupDetails)) {
    $match = $matchLineupDetails['match'];
    $result_lineup = $matchLineupDetails['lineup'];
} else {
    $error_message = $matchLineupDetails;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Lineup Details</title>
    <link rel="stylesheet" href="../../assets/css/styles.css"> 
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Match Lineup Details</h2>
        <?php if (isset($error_message)) : ?>
            <p><?php echo $error_message; ?></p>
        <?php else : ?>
            <h3>Match Details:</h3>
            <p><strong>Opponent:</strong> <?php echo $match['opponent']; ?></p>
            <p><strong>Date:</strong> <?php echo $match['date']; ?></p>
            <p><strong>Time:</strong> <?php echo $match['time']; ?></p>
            <p><strong>Venue:</strong> <?php echo $match['venue']; ?></p>

            <h3>Lineup Details:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Player Name</th>
                        <th>Position Played</th>
                        <th>Minutes Played</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_lineup->num_rows > 0) : ?>
                        <?php while ($row = $result_lineup->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row['name']; ?></td> 
                                <td><?php echo $row['position_played']; ?></td>
                                <td><?php echo $row['minutes_played']; ?></td>
                                <td><?php echo $row['rating']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4">No lineup details available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <br>
    </div>
</body>

</html>
