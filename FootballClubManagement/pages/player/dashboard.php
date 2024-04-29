<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

include '../../includes/db_connect.php';

function getUpcomingMatches($conn)
{
    $sql = "SELECT * FROM matches WHERE date >= CURDATE() ORDER BY date ASC LIMIT 5";
    $result = $conn->query($sql);

    $matches = '';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $matches .= '<li>' . $row['opponent'] . ' - ' . $row['date'] . ' ' . $row['time'] . ' - Result: ' . $row['result'] . ' <a href="../../pages/match_lineup.php?match_id=' . $row['match_id'] . '">Details</a></li>';
        }
    } else {
        $matches .= '<li>No upcoming matches</li>';
    }
    return $matches;
}




function getRecentInjuries($conn)
{
    $sql = "SELECT i.description, i.date_of_injury, a.name 
            FROM injuries i
            INNER JOIN accounts a ON i.player_id = a.account_id
            ORDER BY i.date_of_injury DESC LIMIT 5";
    $result = $conn->query($sql);

    $injuries = '';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $injuries .= '<li>' . $row['name'] . ' - ' . $row['description'] . ' - ' . $row['date_of_injury'] . '</li>';
        }
    } else {
        $injuries .= '<li>No recent injuries</li>';
    }
    return $injuries;
}


function getRecentTrainingSessions($conn)
{
    $sql = "SELECT * FROM training_sessions ORDER BY date DESC LIMIT 5"; 
    $result = $conn->query($sql);

    $sessions = '';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sessions .= '<li>' . $row['date'] . ' ' . $row['time'] . ' - ' . $row['location'] . ' - Focus Areas: ' . $row['focus_areas'] . '</li>';
        }
    } else {
        $sessions .= '<li>No recent training sessions</li>';
    }
    return $sessions;
}

function viewBalancedDiet($conn)
{
    $name = $_SESSION['name'];
    $sql = "SELECT food_details FROM balanced_diet WHERE player_id = (SELECT account_id FROM accounts WHERE name = '$name')";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['food_details'];
    } else {
        return 'No balanced diet information available';
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>

<body>
    <div class="container">
        <h2>Welcome, <?php echo $_SESSION['role']; ?>!</h2>
        <h3>Upcoming Matches:</h3>
        <ul>
            <?php echo getUpcomingMatches($conn); ?>
        </ul>
        <h3>Recent Injuries:</h3>
        <ul>
            <?php echo getRecentInjuries($conn); ?>
        </ul>
        <h3>Recent Training Sessions:</h3>
        <ul>
            <?php echo getRecentTrainingSessions($conn); ?>
        </ul>
        <?php if ($_SESSION['role'] === 'player') : ?>
            <h3>Balanced Diet:</h3>
            <p><?php echo viewBalancedDiet($conn); ?></p>
        <?php endif; ?>
        <p><a href="../../logout.php">Logout</a></p>
    </div>
</body>

</html>
