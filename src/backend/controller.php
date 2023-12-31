<?php
    include(realpath(__DIR__) . '/models/idol.php');
    include(realpath(__DIR__) . '/models/neso.php');
    
    global $preferred_language;
    if (file_exists($_SERVER['DOCUMENT_ROOT'].'/src/locale/' . $preferred_language . '.php')) {
        include($_SERVER['DOCUMENT_ROOT'].'/src/locale/' . $preferred_language . '.php');
    } else {
        include($_SERVER['DOCUMENT_ROOT'].'/src/locale/en.php');
    }

    function getIdols($con, $franchise, $preflang) {
        $listIdols = [];

        $stmt = $con->prepare("SELECT * FROM Nesos N JOIN Idols I ON N.IdolID = I.IdolID WHERE I.franchise = ?; ");
        if (!empty($franchise)) {
            $stmt->bind_param("s", $franchise);
        } else {
            $stmt->bind_param("s", "lovelive");
        }
        $stmt->execute();
        $response = $stmt->get_result();
        if (mysqli_num_rows($response) > 0) {
            $currentIdol = NULL;
            while($row = mysqli_fetch_array($response)) {
                if($currentIdol == NULL || (intval($row["IdolID"]) != $currentIdol->Id)) {
                    if($currentIdol != NULL) {
                        $listIdols[] = $currentIdol;
                    }
                    $currentIdol = new Idol($row);
               } 
               $currentIdol->Nesos[] = new Neso($row, $preflang);
            }
            $listIdols[] = $currentIdol;
        }
        return $listIdols;
    }

     function getOwnedNesoIds($con, $username) {
        $listNesos = [];
        $stmt = $con->prepare("SELECT NesoID FROM NesoOwnership WHERE UserID = (SELECT UserID FROM Users WHERE Username = ?)");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $response = $stmt->get_result();
        if (mysqli_num_rows($response) > 0) {
            while($row = mysqli_fetch_array($response)) {
               $listNesos[] = intval($row["NesoID"]);
            }
        }
        return $listNesos;
    } 

    function getOwnedNesos($con, $userName, $varLang) {
        $listNesos = [];
        
        $stmt = $con->prepare("SELECT *, (SELECT COUNT(*) FROM NesoOwnership WHERE NesoID = N.NesoID AND UserID <> IFNULL((SELECT UserID FROM Users WHERE Username = ?), -1)) AS OwnedBy FROM NesoOwnership NO JOIN Nesos N ON NO.NesoID = N.NesoID JOIN Idols I ON N.IdolID = I.IdolID 
            WHERE NO.UserID = (SELECT UserID FROM Users WHERE Username = ?);");
        
        $stmt->bind_param("ss", $userName, $userName);
        $stmt->execute();
        $response = $stmt->get_result();
        
        if (mysqli_num_rows($response) > 0) {
            while($row = mysqli_fetch_array($response)) {
               $listNesos[] = new Neso($row, $varLang);
            }
        }
        
        return $listNesos;
    }

    function getNesosByIdol($con, $fullname, $varLang, $userName) {
        $listNesos = [];
    
        $stmt = $con->prepare("SELECT *, CASE
            WHEN Size = 'Petit' THEN 1 
            WHEN Size = 'KCM' THEN 2
            WHEN Size = 'NNN' THEN 3
            WHEN Size = 'JNN' THEN 4
            WHEN Size = 'MJNN' THEN 5
            WHEN Size = 'LL' THEN 6
            WHEN Size = 'TJNN' THEN 7
            ELSE 8 END AS SizeOrder, 
            (SELECT COUNT(*) FROM NesoOwnership WHERE NesoID = N.NesoID AND UserID <> IFNULL((SELECT UserID FROM Users WHERE Username = ?), -1)) AS OwnedBy
            FROM Nesos N JOIN Idols I ON N.IdolID = I.IdolID 
            WHERE REPLACE(LOWER(I.IdolName), ' ', '') LIKE CONCAT(TRIM(?), '%') ORDER BY SizeOrder");
        $stmt->bind_param("ss", $userName, $fullname);
        $stmt->execute();
        $response = $stmt->get_result();
    
        if (mysqli_num_rows($response) > 0) {
            while ($row = mysqli_fetch_array($response)) {
                $name = explode(" ", $row["IdolName"]);
                $listNesos[] = new Neso($row, $varLang);
            }
        }
    
        return $listNesos;
    }
    
    function doesUserExist($con, $userName) {
        $stmt = $con->prepare("SELECT * FROM Users WHERE Username = ?");
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $response = $stmt->get_result();
        return (mysqli_num_rows($response) > 0);
    }

    function getTwitter($con, $username) {
        $stmt = $con->prepare("SELECT twitter FROM Users WHERE Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $response = $stmt->get_result();
        if (mysqli_num_rows($response) > 0) {
            $row = mysqli_fetch_array($response);
            return $row["twitter"];
        }
        return "";
    }

    function getFranchises($con) {
        $stmt = $con->prepare("SELECT DISTINCT franchise FROM Idols;");
        $stmt->execute();
        $response = $stmt->get_result();
        $listFranchises = [];
        if (mysqli_num_rows($response) > 0) {
            while ($row = mysqli_fetch_array($response)) {
                $listFranchises[] = $row["franchise"];
            }
        }

        return $listFranchises;
    }
?>
