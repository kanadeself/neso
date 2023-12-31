<?php
    include ('backend/connection.php');
    include ('backend/controller.php');
    session_start();
    if (isset($_POST['FullName'])) {
        $varLang = $_POST['varLang'];
        $fullName = $_POST['FullName'];
        $userName = "";
        if(isset($_SESSION["username"])) {
            $userName = $_SESSION["username"];
        }
        $listNesos = getNesosByIdol($con, $fullName, $varLang, $userName);
        echo json_encode($listNesos); 

    } else {
        echo "Missing parameters";
    }
?>