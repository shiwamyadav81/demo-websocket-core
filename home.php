<?php
session_start();
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
    session_write_close();
} else {
    // since the username is not set in session, the user is not-logged-in
    // he is trying to access this page unauthorized
    // so let's clear all session variables and redirect him to index
    session_unset();
    session_write_close();
    $url = "./index.php";
    header("Location: $url");
}

?>
<HTML>

<HEAD>
    <TITLE>Welcome</TITLE>
    <link href="assets/css/style.css" type="text/css" rel="stylesheet" />
    <link href="assets/css/user-registration.css" type="text/css" rel="stylesheet" />
    <script src="assets/jquery/jquery-3.3.1.js" type="text/javascript"></script>

</HEAD>

<BODY>
    <div class="container">
        <div class="page-header">
            <span class="login-signup"><a href="logout.php">Logout</a></span>
        </div>
        <div class="page-content">Welcome <?php echo $username; ?></div>
    </div>
    <hr>
    <div class="notification-section-container" style="text-align: center;">
        <div class="notification-content">
            <span id="new-user"></span>
        </div>
    </div>

    <script>
        var conn = new WebSocket('ws://localhost:8080');
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        conn.onmessage = function(e) {
            console.log(e.data);
            showNotification(e.data);
        };



        function showNotification(username) {
            alert(username);
            var newUserNotification = $('#new-user');
            var usernameSpan = $('<h4>').text(username);
            newUserNotification.append(usernameSpan);

            var notificationSection = $('.notification-section');

            // Add row styling
            newUserNotification.addClass('row-style');
        }
    </script>

</BODY>

</HTML>