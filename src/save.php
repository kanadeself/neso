<?php
    include ('backend/connection.php');
    include ('backend/controller.php');
    include ("backend/tryLogin.php");
    session_start();
    if (isset($_SESSION['username']) && isset($_SESSION['pincode']) && isset($_POST['NesoID'])) {
        $username = $_SESSION['username'];
        $userid = $_SESSION['userID'];
        $pincode = $_SESSION['pincode'];
        $nesoId = intval($_POST['NesoID']);

        // Let's check the user's credentials first
        if(tryLogin($con, $username, $pincode)) {
            $ownedNesos = getOwnedNesoIds($con, $username);
            echo "Saving neso with ID " . $nesoId;
            if(in_array($nesoId, $ownedNesos)) {
                $query = "DELETE FROM NesoOwnership WHERE UserID = ? AND NesoID = ?";
            } else {
                $query = "INSERT INTO NesoOwnership (UserID, NesoID) VALUES (?, ?)";
            }
            $stmt = $con->prepare($query);
            $stmt->bind_param("si", $userid, $nesoId); 
            $stmt->execute();
            echo " OK";
        }

    } else {
        echo "Missing parameters";
    }
?>