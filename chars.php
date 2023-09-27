

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
        <title>NesoDB</title>
        <link rel="icon" type="image/png" href="https://nesodb.me/img/fav.png">
        <meta name="theme-color" content="#231146" />
        <meta property="og:title" content="NesoDB" />
        <meta property="og:description" content="All nesoberi outfits (for LoveLive!)" />
        <meta property="og:image" content="https://nesodb.me/img/fav.png" />
        <meta name="description" content="All nesoberi outfits (for LoveLive!)">
        <link rel="stylesheet" href="/src/css/style.css">
        <link rel="stylesheet" href="/src/css/nesocards.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    </head>

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


<div class="nav-top">
    <a href="../chars/" class="nav-text text-active"><?php echo $lang['databasename']; ?></a>
    <a href="../collection" class="nav-text"><?php echo $lang['collectionname']; ?></a>
</div>

<?php
    include "src/backend/connection.php";
    include "src/backend/controller.php";
   
    $idols = getIdols($con);
    
        echo '<div class="scroll">';
        foreach($idols as &$idol) {
            $fullname = [];
            printf("<button class=\"collapsible\" style=\"background-image: url('/src/img/%s/portrait.png'); background-color: %s; \" id=\"idol_%s\" full_name=\"%s\"></button>", strtolower($idol->Name), $idol->Color, strtolower($idol->Name), $preferred_language === "ja" ? $idol->NameJP : $idol->FullName);
        }
        echo "</div>";
    
?>

<h1 id="startmsg"><?php echo $lang['startmsg']; ?></h1>

<div style="display:none;justify-content:center;align-items:center;" id="idolnameheader"><h4 id="currentIdol" style="font-weight:bold;font-size:2em;"></h4>
<img id="currentIdolIcon" style="width:35px;height:35px;display:none;padding-left:8px;"></img></div>

<div id="filterContainer"></div>

<div class="nesoSpace" id="idolNesoSpace">

</div>

<div style="height: 200px;"></div>


<script src="/src/js/nesoviewer.js"></script>


<div class="icon-div">
        <a href="https://twitter.com/kanadetakami"><img src="../img/tw.png" class="iconsocial"></a>
        <a href="https://ko-fi.com/kanadetakami"><img src="../img/kofi.png" class="iconsocial"></a>
    </div>


<script>
    refreshFilterButtonsIdol();
</script>


</html>