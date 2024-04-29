<?php
session_start();
if(!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

include '../../includes/db_connect.php';


function updateInjuryDetails($conn, $injury_id, $player_id, $description, $date_of_injury) {
    $sql_update = "UPDATE injuries SET player_id = ?, description = ?, date_of_injury = ? WHERE injury_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("issi", $player_id, $description, $date_of_injury, $injury_id);
    return $stmt_update->execute();
}


function deleteInjury($conn, $injury_id) {
    $sql_delete = "DELETE FROM injuries WHERE injury_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $injury_id);
    return $stmt_delete->execute();
}


function addNewInjury($conn, $player_id, $description, $date_of_injury) {
    $sql_add = "INSERT INTO injuries (player_id, description, date_of_injury) VALUES (?, ?, ?)";
    $stmt_add = $conn->prepare($sql_add);
    $stmt_add->bind_param("iss", $player_id, $description, $date_of_injury);
    return $stmt_add->execute();
}


if(isset($_POST['update'])) {
    $injury_id = $_POST['injury_id'];
    $player_id = $_POST['player_id'];
    $description = $_POST['description'];
    $date_of_injury = $_POST['date_of_injury'];

    if(updateInjuryDetails($conn, $injury_id, $player_id, $description, $date_of_injury)) {
        echo '<script>alert("Injury details updated successfully!");</script>';
    } else {
        echo '<script>alert("Error updating injury details!");</script>';
    }
}


if(isset($_POST['delete'])) {
    $injury_id = $_POST['injury_id'];

    if(deleteInjury($conn, $injury_id)) {
        echo '<script>alert("Injury deleted successfully!");</script>';
    } else {
        echo '<script>alert("Error deleting injury!");</script>';
    }
}


if(isset($_POST['add'])) {
    $player_id = $_POST['player_id'];
    $description = $_POST['new_description'];
    $date_of_injury = $_POST['new_date_of_injury'];

    if(addNewInjury($conn, $player_id, $description, $date_of_injury)) {
        echo '<script>alert("New injury added successfully!");</script>';
    } else {
        echo '<script>alert("Error adding new injury!");</script>';
    }
}

$sql_players = "SELECT account_id, name FROM accounts WHERE role = 'player'";
$result_players = $conn->query($sql_players);


$sql_injuries = "SELECT i.injury_id, i.player_id, i.description, i.date_of_injury, a.name 
                 FROM injuries i
                 INNER JOIN accounts a ON i.player_id = a.account_id";
$result_injuries = $conn->query($sql_injuries);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Injuries</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Manage Injuries</h2>
        <h3>Add New Injury</h3>
        <form method="POST">
            <label>Player:</label>
            <select name="player_id">
                <?php
                if ($result_players->num_rows > 0) {
                    while ($player = $result_players->fetch_assoc()) {
                        echo '<option value="' . $player['account_id'] . '">' . $player['name'] . '</option>';
                    }
                } else {
                    echo '<option value="">No players available</option>';
                }
                ?>
            </select><br>
            <label>Description:</label>
            <input type="text" name="new_description"><br>
            <label>Date of Injury:</label>
            <input type="date" name="new_date_of_injury"><br>
            <input type="submit" name="add" value="Add">
        </form>
        
        <h3>Existing Injuries</h3>
        <ul>
            <?php
            if ($result_injuries->num_rows > 0) {
                while($row = $result_injuries->fetch_assoc()) {
                    echo '<li>' . $row['name'] . ' - ' . $row['description'] . ' - ' . $row['date_of_injury'];
                    echo '<form method="POST">';
                    echo '<input type="hidden" name="injury_id" value="' . $row['injury_id'] . '">';
                    echo '<input type="hidden" name="player_id" value="' . $row['player_id'] . '">';
                    echo '<label>Description:</label>';
                    echo '<input type="text" name="description" value="' . $row['description'] . '"><br>';
                    echo '<label>Date of Injury:</label>';
                    echo '<input type="date" name="date_of_injury" value="' . $row['date_of_injury'] . '"><br>';
                    echo '<input type="submit" name="update" value="Update">';
                    echo '<input type="submit" name="delete" value="Delete">';
                    echo '</form>';
                    echo '</li>';
                }
            } else {
                echo '<li>No injuries</li>';
            }
            ?>
        </ul>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
