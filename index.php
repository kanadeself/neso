
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

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content=" width=device-width, initial-scale=1.0">
        <title>NesoDB</title>
        <link rel="icon" type="image/png" href="./img/fav.png">
        <meta name="theme-color" content="#231146" />
        <meta property="og:title" content="NesoDB" />
        <meta property="og:description" content="Informative site all about nesoberis" />
        <meta property="og:image" content="https://nesodb.me/img/fav.png" />
        <meta name="description" content="Informative site all about nesoberis">
        <link rel="stylesheet" href="./styles.css">
        <script src="./jquery-3.6.1.min.js"></script>
    </head>
    <body>
    <div class="everything">
        <ul>
            <li><div><a href="./chars/" class="button"><p class="t1"><?php echo $lang['nesodb']; ?></p></a></div></li>
            <li><div><a href="./collection/" class="button"><p class="t1"><?php echo $lang['nesobericollection']; ?></p></a></div></li>
        </ul>
    </div>
    <div class="icon-div">
        <a href="https://twitter.com/kanadetakami"><img src="img/tw.png" class="icon"></a>
        <a href="https://ko-fi.com/kanadetakami"><img src="img/kofi.png" class="icon"></a>
    </div>
    </body>
</html> 