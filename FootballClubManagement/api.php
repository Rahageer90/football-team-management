<?php
header('Content-Type: application/json');

include 'includes/db_connect.php';



if (isset($_GET['endpoint']) && $_GET['endpoint'] = 'get_player_diet') {

    $player_id = $_GET['player_id'];


    $food_details = '';

    $stmt_fetch = $conn->prepare("SELECT * FROM balanced_diet WHERE player_id = ? LIMIT 1");
    $stmt_fetch->bind_param("i", $player_id); 
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    if ($row = $result->fetch_assoc()) {
        $food_details = $row['food_details'];
    }

    $data = [
        'data' => $food_details
    ];
    $jsonResponse = json_encode($data);
    echo $jsonResponse;
}
