<?php
    function tryLogin($con, $username, $pincode) {
        // It's important to use prepared statements as below to prevent SQL injections!!!
        $stmt = $con->prepare("SELECT * FROM Users WHERE Username = ? AND Pincode = ?");
        $stmt->bind_param("ss", $username, $pincode);
        $stmt->execute();
        $response = $stmt->get_result();

        return mysqli_num_rows($response) > 0;
    }
?>