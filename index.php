<?php
// Imports
include "src/backend/connection.php";
include(__DIR__ . "/src/backend/controller.php");
include("src/backend/tryLogin.php");

// Set cookie and session
ini_set('session.cookie_lifetime', 30 * 24 * 3600);
session_start();
?>

<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title><?php if (isset($_GET['username'])) {
                    echo htmlspecialchars("NesoDB - " . $_GET['username']);
                } else {
                    echo htmlspecialchars("NesoDB");
                } ?></title>
        <link rel="icon" type="image/png" href="https://nesodb.me/img/fav.png">
        <meta name="theme-color" content="#231146" />
        <meta property="og:title" content="<?php if (isset($_GET['username'])) {
                                                echo htmlspecialchars("NesoDB - " . $_GET['username']);
                                            } else {
                                                echo htmlspecialchars("NesoDB");
                                            } ?>" />
        <meta property="og:description" content="<?php if (isset($_GET['username'])) {
                                                        echo htmlspecialchars("View " . $_GET['username'] . "'s nesoberi collection");
                                                    } else {
                                                        echo htmlspecialchars("Nesoberi viewer and tracking site!");
                                                    } ?>" />
        <meta property="og:image" content="https://nesodb.me/img/fav.png" />
        <meta name="description" content="Nesoberi viewer and tracking site!">
        <link rel="stylesheet" href="/src/css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    </head>

    <body>
        <?php
        // Language stuff
        global $preflang;

        function getUserlang($userid, $con)
        {
            $stmt = $con->prepare("SELECT lang FROM Users WHERE UserID = ?");
            $stmt->bind_param("i", $userid);
            $stmt->execute();
            $response = $stmt->get_result();

            $row = mysqli_fetch_array($response);
            return $row["lang"];
        }

        function updateUserLanguage($userid, $language, $con)
        {
            $stmt = $con->prepare("UPDATE Users SET lang = ? WHERE UserID = ?");
            $stmt->bind_param("si", $language, $userid);
            $stmt->execute();
        }

        $cookieLang = "";
        // Fetch value from Cookies
        if (isset($_COOKIE['user_language'])) {
            $cookieLang = $_COOKIE['user_language'];
        }

        // Language was changed through dropdown
        if (isset($_POST['user_language'])) {
            $cookieLang = $_POST['user_language'];
            $userid = $_SESSION['userID'];

            // If user is logged in, save to DB
            if ($userid) {
                updateUserLanguage($userid, $cookieLang, $con);
            }

            setcookie('user_language', $cookieLang, time() + (365 * 24 * 60 * 60)); // Set to expire in 1 year
        }

        if (isset($_SESSION['username'])) {
            $userid = $_SESSION['userID'];
            $userlang = getUserlang($userid, $con);
        }

        if (!empty($userlang)) {
            $preflang = $userlang;
        } else if (!empty($cookieLang)) {
            $preflang = $cookieLang;
        } else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // Chrome gives ja-JP but Firefox gives ja -_-
            $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $temp = $languages[0];
            $preflang = mb_substr($temp, 0, 2);
        } else {
            // Fallback to English
            $preflang = 'en';
        }

        if (file_exists('src/locale/' . $preflang . '.php')) {
            include('src/locale/' . $preflang . '.php');
        } else {
            include('src/locale/en.php');
        }

        // Is there a better way to do this? Maybe. I'm lazy? Maybe.
        echo '<script>var jsLang = "' . $preflang . '";</script>';
        echo '<script>console.log(jsLang)</script>';
        echo '
                <script>            
                    $(document).ready(function() {
                        $("#successful").delay(5000).fadeOut(300, function() {
                            $(this).hide();
                        });
                    });
                </script>
            ';

        // Functions
        function updateUsername($con)
        {
            $oldUsername = $_SESSION['username'];
            $newUsername = $_POST["newUsername"];
            $verifyPincode = $_POST["pincode"];
            $userid = $_SESSION['userID'];
            global $lang;

            if (tryLogin($con, $oldUsername, $verifyPincode)) {
                $sqlUpdate = $con->prepare("UPDATE Users SET Username = ? WHERE Username = ?");
                $sqlUpdate->bind_param("ss", $newUsername, $oldUsername);
                $sqlUpdate->execute();

                echo "<p><span id=\"successful\">" . $lang["updatesuccessful"] . "</span></p>";

                $_SESSION['username'] = $newUsername;
                $sessionUsername = $newUsername;
            } else {
                echo "<p><span id=\"successful\" style=\"background:red;\">" . $lang["updatefailed"] . "</span></p>";
            }
        }

        function updateTwitter($con)
        {
            $twitter = $_POST["twitter"];
            $userid = $_SESSION['userID'];

            $sqlUpdate = $con->prepare("UPDATE Users SET twitter = ? WHERE UserID = ?");
            $sqlUpdate->bind_param("si", $twitter, $userid);
            $sqlUpdate->execute();
            global $lang;

            if ($sqlUpdate) {
                echo "<p><span id=\"successful\">" . $lang["updatesuccessful"] . "</span></p>";
            } else {
                echo "<p><span id=\"successful\" style=\"background:red;\">" . $lang["updatefailed"] . "</span></p>";
            }
        }


        if (isset($_POST['Logout'])) {
            session_destroy();
            header('Location: ./');
        }

        if (isset($_POST['Username']) && isset($_POST['Pincode']) && isset($_POST['Action'])) {
            $username = $_POST['Username'];
            $pincode = $_POST['Pincode'];
            $action = $_POST['Action'];
            if ($action == "0") {
                $userID = tryLogin($con, $username, $pincode);
                if ($userID != NULL) {
                    $_SESSION['username'] = $username;
                    $_SESSION['pincode'] = $pincode;
                    $_SESSION['userID'] = $userID;
                } else {
                    echo "<p><span id=\"successful\" style=\"background:red;\">" . $lang["loginfail"] . "</span></p>";
                }
            } else if ($action == "1") {
                $stmt = $con->prepare("INSERT INTO Users (Username, Pincode) VALUES (?, ?)");
                $stmt->bind_param("ss", $username, $pincode);
                try {
                    if ($stmt->execute()) {
                        echo "<p><span id=\"successful\">" . $lang["registrationsuccess"] . "</span></p>";
                    } else {
                        $translated_error = sprintf($lang['registrationerror'], htmlspecialchars($username, ENT_QUOTES, 'UTF-8'));
                        echo "<p><span id=\"successful\" style=\"background:red;\">" . $translated_error . "</span></p>";
                    }
                } catch (Exception $e) {
                    $translated_error = sprintf($lang['registrationerror'], htmlspecialchars($username, ENT_QUOTES, 'UTF-8'));
                    echo "<p><span id=\"successful\" style=\"background:red;\">" . $translated_error . "</span></p>";
                }
            }
        }

        if (isset($_POST["usernameUpdate"])) {
            updateUsername($con);
        }
        if (isset($_POST["twitterUpdate"])) {
            updateTwitter($con);
        }
        if (!empty($userMessage)) {
            echo $userMessage;
        }

        $urlUsername = isset($_GET['username']) ? $_GET['username'] : NULL;
        $sessionUsername = isset($_SESSION['username']) ? $_SESSION['username'] : NULL;
        $viewingOnly = $urlUsername != NULL && $urlUsername != $sessionUsername;

        $isSignedin = ($sessionUsername != NULL && $urlUsername == NULL);
        $isSignedin = $isSignedin ? 'true' : 'false';
        $username = $viewingOnly ? $urlUsername : $sessionUsername;
        $twitter = getTwitter($con, $username);

        echo
            '<script>
                    var isSignedin = ' . $isSignedin . ';
                    var username = "' . $username . '";
                    var twitter = "' . $twitter . '";
            </script>';

        $idols = getIdols($con, 'lovelive', $preflang);
        $ownedNesos = getOwnedNesoIds($con, $username);
        $listFranchises = getFranchises($con);

        if ($viewingOnly) {
            $username = $_GET['username'];

            if (doesUserExist($con, $username)) {
                echo '<div id="headerdiv" style="display:flex;flex-direction:row;justify-content:center;align-items:center;"><h1 style="text-align: center;margin-right:10px;">' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . $lang['othernesocollection'] . "</h1>";
                $twitter = getTwitter($con, $username);
                if (!is_null($twitter)) {
                    echo '<a href="https://twitter.com/' . htmlspecialchars($twitter, ENT_QUOTES, 'UTF-8') . '" target="_blank" class="twitterlogo"><img src="../img/tw.png" style="width:32px;height:32px;"></a>';
                }
                echo '</div>';
                $translated_creation = sprintf($lang['create'], '</h3>&nbsp<a style="font-size: 1.17em;font-weight:bold;color:#44729c;border-bottom: 2px dotted #44729c;"href="https://nesodb.me" target="_blank">nesodb.me</a><h3 style="display:inline-block;">&nbsp');
                echo '<h3 style="display:inline-block;">' . $translated_creation . '</h3>';
                echo '<style>#franchisePicker, #headerdiv, #link{display: none;} #totalCounter{display: block !important;} #sizeFilterContainer, #idolselectordropdown{display: flex !important;}</style>';
            } else {
                echo '<h1 style="text-align: center;">' . $lang['oops'] . '</h1>';
                echo '<h3 style="text-align: center;">' . $lang['doesntexist'] . '</h3>';
            }
        } else {
            if ($sessionUsername != NULL) {
                echo '<style>#loginheader, #link{display: block !important;} #headerdiv, #sizeFilterContainer{display: flex;}</style>';
                $user_name = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
            }

            if ($sessionUsername === NULL) {
                echo '<style>#loginform{display: block !important;} #sizeFilterContainer,#headerdiv, #link {display: none;} #totaldiv, #idolselectordropdown {display:none !important;} </style>';
                echo '<div class="scroll" id="idolSelectors">';
                echo "</div>";
            }
        }
        ?>
        <div>
            <div id="loginform" style="display:none;">
                <form method="post" style="text-align:center;" id="loginForm" class="loginform">
                    <div class="logindiv" style="display:flex;align-items:center;justify-content:center;">
                        <div class="loginformdiv" style="display:flex; justify-content:center; ">
                            <input type="hidden" name="Action" id="actionArgument" value="0" />
                            <div class="usernamefield" style="display:flex;flex-direction:column;">
                                <label for="Username" style="text-align:center;font-weight:bold;"><?php echo $lang['username'] ?></label>
                                <input type="text" id="Username" name="Username" required style="border-radius: 20px;border:5px white solid;" onkeypress="return RestrictKeyboard()">
                            </div>
                            <br />
                            <div class="pincodefield" style="display:flex;flex-direction:column;">
                                <label for="Pincode" style="text-align:center;font-weight:bold;"><?php echo $lang['pincode'] ?></label>
                                <input type="password" id="Pincode" name="Pincode" pattern="[0-9]{4}" maxlength="4" style="-webkit-text-security: disc;border-radius:20px;border:5px white solid;" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                <p style="margin:0;font-size:15px;"><?php echo $lang['4digit'] ?></p>
                            </div>
                            <br />
                        </div>
                        <div style="display:flex;flex-direction:column;">
                            <input type="submit" value="<?php echo $lang['login'] ?>" id="submitBtn" class="btn" style="font-size:medium;color:white;">
                            <a href="#" id="registerToggle" onclick="toggleRegister();" style="color: white;text-decoration:none;font-size:small;"><?php echo $lang['register'] ?></a>
                        </div>
                </form>
            </div>
        </div>
        <div id="loginheader" style="display:none;">
            <div style="display:flex;align-items:center;justify-content:center;">
                <?php $translated_welcome = sprintf($lang['welcome'], $username) ?>
                <h4 style="text-align:center;margin:10px;"><?php echo htmlspecialchars($translated_welcome); ?></h4>
                <div class="icon-container">
                    <span class="material-symbols-outlined">account_circle</span>
                    <div class="menu">
                        <p><?php echo $lang['updateusername'] ?></p>
                        <form method="post">
                            <label for="newUsername"><?php echo $lang['newusername'] ?></label>
                            <input type="text" name="newUsername" required autocomplete="off">

                            <label for="pincode"><?php echo $lang['pincode'] ?>:</label>
                            <input type="password" name="pincode" required>

                            <input type="submit" name="usernameUpdate" value="<?php echo $lang['update'] ?>" class="btn">
                        </form>

                        <form method="post">
                            <label for="twitter" style="font-weight:600;"><?php echo $lang['twitterupdate'] ?></label>
                            <input type="text" name="twitter" required id="twittertext" onkeypress="return RestrictKeyboard();" maxlength="15">
                            <input type="submit" name="twitterUpdate" value="<?php echo $lang['save'] ?>" class="btn">
                        </form>
                        <div style="text-align:left;"><input class="btn" type="button" onclick="logout();" value="<?php echo $lang['logout'] ?>"></input></div>
                    </div>
                </div>
            </div>
            <div>
                <form method="post" action="" id="language-form">
                    <select class="language-selector" name="user_language" style="border:3px solid white;">
                        <?php
                        $languages = [
                            'en' => 'English',
                            'ja' => '日本語'
                        ];

                        $selectedLang = isset($_SESSION['user_language']) ? $_SESSION['user_language'] : 'en';

                        foreach ($languages as $short => $display) {
                            $selected = ($selectedLang == $short) ? 'selected' : '';
                            echo "<option value=\"$short\" $selected>$display</option>";
                        }
                        ?>
                    </select>
                </form>
            </div>

            <div class="scroll" id="idolSelectors">
                <button class="collapsible" style="background-color: #888;text-align:center;" id="myCollection"><?php echo $lang['mycollection'] ?></button>
            </div>
        </div>
        </div>

        <div id="headerdiv" style="flex-direction:row;justify-content:center;align-items:center;">
            <h1 style="text-align: center;margin-right:10px;"><?php echo $lang['headernesocollection'] ?></h1>
        </div>
        <span id="link">
            <h3 style="padding-bottom:20px;display:inline-block;"><?php echo $lang['shareablelink'] ?></h3>&nbsp<a style="font-size: 1.17em;font-weight:bold;color:#44729c;border-bottom: 2px dotted #44729c;" href="https://nesodb.me/collection/<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>" target="_blank">nesodb.me/collection/<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></a>
        </span>

        <div style="display:none;justify-content:center;align-items:center;" id="idolnameheader">
            <h4 id="currentIdol" style="font-weight:bold;font-size:2em;margin:0;"></h4>
            <img id="currentIdolIcon" style="width:35px;height:35px;display:none;padding-left:8px;"></img>
        </div>

        <div id="topdiv" style="display:flex;justify-content:space-around;">
            <div id="totaldiv">
                <div id="totalCounter" style="display:block;font-size:20px;font-weight:bold;"></div>
                <div id="divCounters"></div>
            </div>
            <div id="dropdowndiv">
                <select id="franchisePicker" onchange="loadIdolSelectors();" style="grid-area: 1 / 1 / 2 / 3;margin:0;">
                    <?php foreach ($listFranchises as &$franchise) {
                        echo '<option value="' . $franchise . '">' . $lang[$franchise] . "</option>";
                    } ?>
                </select>
                <div id="sizeFilterContainer" style="align-items:center;grid-area: 2 / 1 / 3 / 2;">
                    <p style="padding-right: 8px;"><?php echo $lang['size'] ?> </p>
                    <div class="dropdown">
                        <button class="dropbtn" id="selectedSize"><?php echo $lang['all'] ?></button>
                        <div class="dropdown-content" id="sizeFilterOptions"></div>
                    </div>
                </div>
                <div id="idolselectordropdown" style="display:flex;align-items:center;grid-area: 2 / 2 / 3 / 3;">
                    <p style="padding-left: 20px;padding-right: 8px;"><?php echo $lang['idol'] ?></p>
                    <div class="dropdown" id="idolFilterContainer">
                        <button class="dropbtn" id="selectedIdol"><?php echo $lang['all'] ?></button>
                        <div class="dropdown-content" id="idolFilterOptions"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="nesoSpace" id="ownedNesoSpace"></div>

        <div class="nesoSpace" id="idolNesoSpace"></div>

        <form id="logoutForm" method="post">
            <input type="hidden" name="Logout" value="1" />
        </form>

        <div class="icon-div">
            <a href="https://twitter.com/kanadetakami"><img src="../img/tw.png" class="iconsocial"></a>
            <a href="https://ko-fi.com/kanadetakami"><img src="../img/kofi.png" class="iconsocial"></a>
        </div>

        <script src="/src/js/nesoviewer.js"></script>

        <script>
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

            function logout() {
                document.getElementById("logoutForm").submit();
            }

            var ownedNesos = <?php echo json_encode($ownedNesos); ?>;

            generateOwnedNesos();

            var registering = false;
            var registerToggle = document.getElementById("registerToggle");
            var actionArgument = document.getElementById("actionArgument");
            var submitBtn = document.getElementById("submitBtn");

            function RestrictKeyboard() {
                if (event.keyCode == 32 || event.keyCode == 64) {
                    return false;
                }
            }

            function toggleRegister() {
                registering = !registering;

                if (registering) {
                    registerToggle.innerText = "<?php echo $lang['loginexisting'] ?>";
                    actionArgument.value = 1;
                    submitBtn.value = "<?php echo $lang['register'] ?>"
                } else {
                    registerToggle.innerText = "<?php echo $lang['registerquestion'] ?>";
                    actionArgument.value = 0;
                    submitBtn.value = "<?php echo $lang['login'] ?>"
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                var languageSelector = document.querySelector('.language-selector');
                var languageForm = document.getElementById('language-form');

                languageSelector.value = jsLang;

                languageSelector.addEventListener('change', function() {
                    languageForm.submit();
                });
            });

            if (twitter) {
                document.getElementById("twittertext").placeholder = '@' + twitter;
            }
        </script>
    </body>
</html>