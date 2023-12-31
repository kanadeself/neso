<?php
    include ('backend/connection.php');
    session_start();
    if (isset($_SESSION['username']) && isset($_SESSION['pincode'])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newUsername = $_POST["newUsername"];
            $pincode = $_POST["pincode"];
        
            
            $userid = $_SESSION['userID'];
            $sqlPin = "SELECT Pincode FROM Users WHERE UserID=$userid";
            $result = $con->query($sqlPin);
        
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $storedPin = $row["Pincode"];
        
                if ($pincode === $storedPin) {

                    $sqlUpdate = "UPDATE Users SET Username='$newUsername' WHERE UserID=$userid";
        
                    if ($conn->query($sqlUpdate) === TRUE) {
                        echo "OK";
                    } else {
                        echo "NO: " . $conn->error;
                    }
                } else {
                    echo "Incorrect password";
                }
            } else {
                echo "User not found";
            }
        }
    }
?>