<?php
    function tryLogin($con, $username, $pincode) {
        // It's important to use prepared statements as below to prevent SQL injections!!!
        $stmt = $con->prepare("SELECT * FROM Users WHERE Username = ? AND Pincode = ?");
        $stmt->bind_param("ss", $username, $pincode);
        $stmt->execute();
        $response = $stmt->get_result();
        
        if (mysqli_num_rows($response) > 0) {
            $row = mysqli_fetch_array($response);
            return $row["UserID"];
        } else {
            return NULL;
        }
    }
?>
