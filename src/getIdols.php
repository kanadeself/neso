<?php
    include ('backend/connection.php');
    include ('backend/controller.php');
    if (isset($_POST['franchise'])) {
        $franchise = $_POST['franchise'];
        $preflang = $_POST['preflang'];
        $listIdols = getIdols($con, $franchise, $preflang);
        echo json_encode($listIdols); 

    } else {
        echo "Missing parameters";
    }
?>