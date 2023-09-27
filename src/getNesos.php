<?php
    include ('backend/connection.php');
    include ('backend/controller.php');
    session_start();
    if (isset($_POST['IdolName'])) {
        $idolName = $_POST['IdolName'];
        $varLang = $_POST['varLang'];
        $listNesos = getNesosByIdol($con, $idolName, $varLang);
        echo json_encode($listNesos); 

    } else {
        echo "Missing parameters";
    }
?>