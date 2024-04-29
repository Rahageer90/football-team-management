<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

include '../../includes/db_connect.php';

function getPlayers($conn)
{
    $sql = "SELECT account_id, name FROM accounts WHERE role='player'";
    $result = $conn->query($sql);

    $options = '';
    if ($result->num_rows > 0) {

        $options .= '<option value="">Select One</option>';
        while ($row = $result->fetch_assoc()) {
            $options .= '<option value="' . $row['account_id'] . '">' . $row['name'] . '</option>';
        }
    }
    return $options;
}


function isPlayerDietExits($conn, $player_id)
{
    $stmt_fetch = $conn->prepare("SELECT * FROM balanced_diet WHERE player_id = ? LIMIT 1");
    $stmt_fetch->bind_param("i", $player_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();


    if ($row = $result->fetch_assoc()) {

        return true;
    }
    return false;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $player_id = $_POST['account_id'];
    $food_details = $_POST['food_details'];

    print_r($player_id);

    $isExits = isPlayerDietExits($conn, $player_id);

    if($isExits){
        $sql_update = "UPDATE balanced_diet SET food_details = ? WHERE player_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $food_details, $player_id);
        $stmt_update->execute();

        $stmt_update->close();

        header("Location: dashboard.php");
        exit();
    }else{
        $sql_insert = "INSERT INTO balanced_diet (food_details, player_id) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("si", $food_details, $player_id);
        $stmt_insert->execute();

        $stmt_insert->close();
        header("Location: dashboard.php");
        exit();

    }


}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Balanced Diet</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
<div class="container">
    <h2>Manage Balanced Diet</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="account_id">Select Player:</label><br>
        <select onchange="onChangeGetDietInfo()" id="account_id" name="account_id" required>
            <?php echo getPlayers($conn); ?>
        </select><br>
        <label for="food_details">Food Details:</label><br>
        <textarea id="food_details" name="food_details" rows="4" cols="50" required></textarea><br><br>
        <input type="submit" value="Update Balanced Diet ">

    </form>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</div>
</body>
</html>


<script>
    async function onChangeGetDietInfo() {
        let selectedValue = document.getElementById("account_id").value;
        console.log("Selected value:", selectedValue);

        
        let url = new URL('<?php echo $BASE_URL ?>');
        let searchParams = url.searchParams;
        searchParams.append('endpoint', 'get_player_diet');
        searchParams.append('player_id', selectedValue);
        let updatedUrl = url.toString();

        let request = await fetch(updatedUrl);
        const res = await request.json();

        document.getElementById('food_details').value = res['data'];

    }
</script>