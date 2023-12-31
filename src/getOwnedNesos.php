<?php
    include ('backend/connection.php');
    include ('backend/controller.php');
    session_start();
    if (isset($_POST['UserName'])) {
        $varLang = $_POST['varLang'];
        $userName = $_POST['UserName'];
        $listNesos = getOwnedNesos($con, $userName, $varLang);
        echo json_encode($listNesos); 

    } else {
        echo "Missing parameters";
    }
?>