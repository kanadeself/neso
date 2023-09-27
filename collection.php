
<?php 
    global $preferred_language;
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $preferred_languagetemp = $languages[0];
            $preferred_language = mb_substr($preferred_languagetemp, 0, 2);
        } else {
            // Default to a fallback language if the header is not present
            $preferred_language = 'en'; // English
        }
        if (file_exists('src/locale/' . $preferred_language . '.php')) {
            include('src/locale/' . $preferred_language . '.php');
        } else {
            // Default to English if the language file doesn't exist
            include('src/locale/en.php');
        }
        echo '<script>var jsLang = "' . $preferred_language . '";</script>';
        echo '<script>console.log(jsLang)</script>'
?>

<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content=" width=device-width, initial-scale=1.0">
        <title>Neso Collection</title>
        <link rel="icon" type="image/png" href="https://nesodb.me/img/fav.png">
        <meta name="theme-color" content="#231146" />
        <meta property="og:title" content="Neso collection" />
        <meta property="og:description" content="Keep track of all your nesoberis!" />
        <meta property="og:image" content="https://nesodb.me/img/fav.png" />
        <meta name="description" content="Keep track of all your nesoberis!">
        <link rel="stylesheet" href="/src/css/style.css">
        <link rel="stylesheet" href="/src/css/nesocards.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="./filter.js"></script>
    </head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3WYPSNK7E7"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-3WYPSNK7E7');
</script>


<style>
    div.scroll {
        display: flex;
        position: fixed;
        bottom: 0;
        left: 50%;
        transform: translate(-50%, -0%);
        margin: 4px, 4px;
        background-color: rgba(255, 255, 255, 0.3);
        width: 95vw;
        overflow-x: auto;
        overflow-y: hidden;
    }

    .button {
        background-color: black;
        color: #ffffff;
        font-size: 1em;
        position: relative;
        outline: none;
        border-radius: 50px;
        cursor: pointer;
        height: 40px;
        width: 100px;
        text-decoration: none;
        border: 2px white solid;
        font-weight: bold;
    }

    .btn:hover {
        transform: scale(1);
    }

    body {
        margin-top: 30px;
        text-align:center;
    }
</style>

<script>
    function logout() {
        document.getElementById("logoutForm").submit();
    }
</script>

<div class="nav-top">
    <a href="../chars/" class="nav-text"><?php echo $lang['databasename']; ?></a>
    <a href="../collection" class="nav-text text-active"><?php echo $lang['collectionname']; ?></a>
</div>

<?php
    include "src/backend/connection.php";
    include "src/backend/controller.php";
    session_start();
    
    if (isset($_POST['Logout'])) {
        session_destroy();
        header('Location: ./login');
    } 

    $urlUsername = isset($_GET['username']) ? $_GET['username'] : NULL;
    $sessionUsername = isset($_SESSION['username']) ? $_SESSION['username'] : NULL;
    $viewingOnly = $urlUsername != NULL && $urlUsername != $sessionUsername;

    if($sessionUsername != NULL) {
        $user_name = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
        $translated_welcome = sprintf($lang['welcome'], $user_name);
        
        echo '<h4 style="text-align:center;margin:10px;">' . $translated_welcome . '</h4>';
        echo '<div style="text-align:center;"><input class="btn" type="button" onclick="logout();" value="' . $lang['logout'] . '"></input></div>';
    }

    if($viewingOnly) {
        $username = $_GET['username'];
        if(doesUserExist($con, $username)) {
            echo '<div id="headerdiv" style="display:flex;flex-direction:row;justify-content:center;align-items:center;"><h1 style="text-align: center;margin-right:10px;">' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . $lang['othernesocollection'] . "</h1></div>";
            $translated_creation = sprintf($lang['create'], '</h3>&nbsp<a style="font-size: 1.17em;font-weight:bold;color:#44729c;border-bottom: 2px dotted #44729c;"href="https://nesodb.me/collection" target="_blank">nesodb.me/collection</a><h3 style="display:inline-block;">&nbsp');
            echo '<h3 style="display:inline-block;">' . $translated_creation . '</h3>';
        } else {
            echo '<h1 style="text-align: center;">' . $lang['oops'] . '</h1>';
            echo '<h3 style="text-align: center;">' . $lang['doesntexist'] . '</h3>';
        }
        
    } else {
        if(!isset($_SESSION['username'])) {
            // User not logged in, trying to view their own collection
            header('Location: ./login');
        } else {
            $username = $_SESSION['username'];
            echo '<div id="headerdiv" style="display:flex;flex-direction:row;justify-content:center;align-items:center;"><h1 style="text-align: center;margin-right:10px;">' . $lang['headernesocollection'] . '</h1></div>';
            echo '<h3 style="text-align:center;margin:0;    ">' . $lang['trackyourcollection'] . '</h3>';
            echo '<h3 style="display: inline-block;padding-bottom:20px;">' . $lang['shareablelink'] . '</h3>&nbsp<a style="font-size: 1.17em;font-weight:bold;color:#44729c;border-bottom: 2px dotted #44729c;" href="https://nesodb.me/collection/' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . '" target="_blank">nesodb.me/collection/' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . '</a>';
            echo '<div style="display:none;justify-content:center;align-items:center;" id="idolnameheader"><h4 id="currentIdol" style="font-weight:bold;font-size:2em;"></h4>';
            echo '<img id="currentIdolIcon" style="width:35px;height:35px;display:none;padding-left:8px;"></img></div>';
        }
    }
   
    $idols = getIdols($con);
    $ownedNesos = getOwnedNesos($con, $username);

    // Print idol selection scroll bar
    if(!$viewingOnly) {
        echo '<div class="scroll">';
        echo '<button onclick="refreshFilterButtonsOwned()" class="collapsible" style="background-color: #888;text-align:center;" id="myCollection">' . $lang['mycollection'] . '</button>';
        foreach($idols as &$idol) {
            $fullname = [];
            printf("<button class=\"collapsible\" style=\"background-image: url('/src/img/%s/portrait.png'); background-color: %s; \" id=\"idol_%s\" full_name=\"%s\"></button>", strtolower($idol->Name), $idol->Color, strtolower($idol->Name), $preferred_language === "ja" ? $idol->NameJP : $idol->FullName);
        }
        echo "</div>";
    }
    
?>
<div id="totalCounter" style="font-size:20px;font-weight:bold;">
</div>
<div id="divCounters">

</div>
<div id="filterContainer"></div>


<div class="nesoSpace" id="ownedNesoSpace">
    <?php
        $ownedNesoIDs = [];
        foreach($ownedNesos as &$neso) {
            printf('<div class="itemcontainer filterDiv show %s" ownedneso-id="%s" idolname="%s" size="%s">', $neso->Size, $neso->Id, strtolower($neso->IdolName), $neso->Size);
            echo '  <div class="rectangle">';
            printf('      <div class="owned">', $neso->Id);
            printf('          <p class="bhtext" style="font-size: 25px;">%s</p>', $neso->Size);
            printf('</div><div><img class="nesoimg" src="/src/img/%s/%s">', strtolower($neso->IdolName), $neso->ImageFileName);
            printf('</div><div class="bottomheader"><p class="bhtext">%s</p></div></div></div>', $neso->Name);
            $ownedNesoIDs[] = $neso->Id;
        }
    ?>  
</div>


<div class="nesoSpace" id="idolNesoSpace">

</div>

<div style="height: 200px;"></div>


<script src="/src/js/nesosupload.js"></script>
<form id="logoutForm" method="post">
    <input type="hidden" name="Logout" value="1"/>
</form>

<div class="icon-div">
        <a href="https://twitter.com/kanadetakami"><img src="../img/tw.png" class="iconsocial"></a>
        <a href="https://ko-fi.com/kanadetakami"><img src="../img/kofi.png" class="iconsocial"></a>
    </div>

<?php
    if(isset($ownedNesoIDs)) {
        echo "<script>var ownedNesos = [" . join(",", $ownedNesoIDs) . "]</script>";
    }
?>

<script>
    generateCounters();
    generateTotal();
    refreshFilterButtonsOwned();
</script>


</html>