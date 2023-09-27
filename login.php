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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    </head>
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

    include ("src/backend/connection.php");
    include ("src/backend/tryLogin.php");
    if (isset($_POST['Username']) && isset($_POST['Pincode']) && isset($_POST['Action'])) {
        $username = $_POST['Username'];
        $pincode = $_POST['Pincode'];
        $action = $_POST['Action'];
    
        if($action == "0") {
            if (tryLogin($con, $username, $pincode)) {
                session_start();
                $_SESSION['username'] = $username;
                $_SESSION['pincode'] = $pincode;
                header('Location: /collection');
            } else {
                echo '<div class="error">' . $lang["loginfail"] . '</div>';
    
            }
        } else if($action == "1") {
            $stmt = $con->prepare("INSERT INTO Users (Username, Pincode) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $pincode);
            try{
                if($stmt->execute()) {
                    echo '<div class="success">' . $lang["registrationsuccess"] . '</div>';
                } else {
                    $translated_error = sprintf($lang['registrationerror'], htmlspecialchars($username, ENT_QUOTES, 'UTF-8'));
                    echo '<div class="error">' . $translated_error . '</div>';
                }
            } catch(Exception $e) {
                $translated_error = sprintf($lang['registrationerror'], htmlspecialchars($username, ENT_QUOTES, 'UTF-8'));
                echo '<div class="error">' . $translated_error . '</div>';
            } 

                
        }
    }
   
?>
<html>
    <body style="display:flex;justify-content:center;align-items:center;">
        <form method="post" style="text-align:center;" id="loginForm" class="loginform">
            <div>
                <h1 id="header"><?php echo $lang['login']; ?></h1>
                <div style="display:flex; flex-direction:row; justify-content:center; ">
                    <input type="hidden" name="Action" id="actionArgument" value="0"/>
                    <div style="display:flex;flex-direction:column;padding-right:20px;">
                        <label for="Username" style="text-align:center;font-weight:bold;"><?php echo $lang['username']; ?></label>
                        <input type="text" id="Username" name="Username" required style="border-radius: 20px;border:5px white solid;" onkeypress="return RestrictSpace()">
                    </div>
                    <br/>   
                <div style="display:flex;flex-direction:column;">
                    <label for="Pincode" style="text-align:center;font-weight:bold;"><?php echo $lang['pincode']; ?></label>
                    <input type="number" id="Pincode" name="Pincode" pattern="[0-9]{4}" maxlength="4" style="-webkit-text-security: disc;border-radius:20px;border:5px white solid;" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"> 
                    <p style="margin:0;font-size:15px;"><?php echo $lang['4digit']; ?></p>
                </div>
                <br/>
            </div>
            <input type="submit" value="<?php echo $lang['login']; ?>" id="submitBtn" class="btn" style="margin-top:20px;margin-bottom:10px;">
            <br/>

            <a href="#" id="registerToggle" onclick="toggleRegister();" style="color: white;text-decoration:none;"><?php echo $lang['registerquestion']; ?></a>
            <br/>
        </form>
    </body>
</html>

<script>
    var registering = false;
    var registerToggle = document.getElementById("registerToggle");
    var actionArgument = document.getElementById("actionArgument");
    var submitBtn = document.getElementById("submitBtn");
    var headertext = document.getElementById("header");
    function RestrictSpace() {
    if (event.keyCode == 32) {
        return false;
    }
}
    function toggleRegister() {
        registering = !registering;
        if(registering) {
            headertext.innerHTML = "<?php echo $lang['register']; ?>";
            registerToggle.innerText = "<?php echo $lang['loginexisting']; ?>";
            actionArgument.value = 1;
            submitBtn.value = "<?php echo $lang['register']; ?>"
        } else {
            headertext.innerHTML = "<?php echo $lang['login']; ?>";
            registerToggle.innerText = "<?php echo $lang['registerquestion']; ?>";
            actionArgument.value = 0; 
            submitBtn.value = "<?php echo $lang['login']; ?>"
        }
    }
</script>

