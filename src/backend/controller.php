<?php
    include(realpath(__DIR__) . '/models/idol.php');
    include(realpath(__DIR__) . '/models/neso.php');
    
    global $preferred_language;
    if (file_exists($_SERVER['DOCUMENT_ROOT'].'/src/locale/' . $preferred_language . '.php')) {
        include($_SERVER['DOCUMENT_ROOT'].'/src/locale/' . $preferred_language . '.php');
    } else {
        include($_SERVER['DOCUMENT_ROOT'].'/src/locale/en.php');
    }

    function getIdols($con) {
        global $preferred_language;
        $listIdols = [];
        $stmt = $con->prepare("SELECT * FROM Nesos N JOIN Idols I ON N.IdolID = I.IdolID; ");
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
               $currentIdol->Nesos[] = new Neso($row, $currentIdol->Name);
            }
            $listIdols[] = $currentIdol;
        }
        return $listIdols;
    }

    function getOwnedNesoIds($con, $userName) {
        $listNesos = [];
        $stmt = $con->prepare("SELECT NesoID FROM NesoOwnership WHERE Username = ?");
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $response = $stmt->get_result();
        if (mysqli_num_rows($response) > 0) {
            while($row = mysqli_fetch_array($response)) {
               $listNesos[] = intval($row["NesoID"]);
            }
        }
        return $listNesos;
    }

    function getOwnedNesos($con, $userName) {
        $listNesos = [];
        global $preferred_language;
        
        $nesoNameColumn = ($preferred_language === 'ja') ? 'N.NesoNameJP' : 'N.NesoName';
        $sizeColumn = ($preferred_language === 'ja') ? 'N.SizeJP' : 'N.Size';
        
        $stmt = $con->prepare("SELECT N.NesoID, $nesoNameColumn AS NesoName, $sizeColumn AS Size, N.ImageFileName, I.IdolName 
            FROM NesoOwnership NO JOIN Nesos N ON NO.NesoID = N.NesoID JOIN Idols I ON N.IdolID = I.IdolID 
            WHERE NO.Username = ?;");
        
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $response = $stmt->get_result();
        
        if (mysqli_num_rows($response) > 0) {
            while($row = mysqli_fetch_array($response)) {
               $listNesos[] = new Neso($row, explode(" ", $row["IdolName"])[0]);
            }
        }
        
        return $listNesos;
    }
    function getNesosByIdol($con, $name, $varLang) {
        $listNesos = [];
        $nesoNameColumn = ($varLang === 'ja') ? 'N.NesoNameJP' : 'N.NesoName';
        $nesoSizeColumn = ($varLang === 'ja') ? 'N.SizeJP' : 'N.Size';
        $nesoExclusiveColumn = ($varLang === 'ja') ? 'N.ExclusiveJP' : 'N.Exclusive';
    
        $stmt = $con->prepare("SELECT * FROM (
            SELECT N.NesoID, N.ImageFileName, I.IdolName, $nesoNameColumn AS NesoName, $nesoSizeColumn AS Size, ReleaseYear, ActualSize, $nesoExclusiveColumn AS Exclusive, CASE
            WHEN Size = 'Petit' THEN 1 
            WHEN Size = 'KCM' THEN 2
            WHEN Size = 'NNN' THEN 3
            WHEN Size = 'JNN' THEN 4
            WHEN Size = 'MJNN' THEN 5
            WHEN Size = 'LL' THEN 6
            WHEN Size = 'TJNN' THEN 7
            ELSE 8 END AS SizeOrder
            FROM Nesos N JOIN Idols I ON N.IdolID = I.IdolID WHERE LOWER(I.IdolName) LIKE CONCAT(LOWER(?), ' %')
    ) A ORDER BY SizeOrder");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $response = $stmt->get_result();
    
        if (mysqli_num_rows($response) > 0) {
            while ($row = mysqli_fetch_array($response)) {
                $listNesos[] = new Neso($row, $name);
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

?>
