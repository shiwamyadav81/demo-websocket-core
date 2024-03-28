<?php
session_start();
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
    session_write_close();

    echo "<script type='text/javascript'>
        setTimeout(function() {
        showNotification('$username');
        showToast('jooooo');
    }, 1000);
    </script>";
} else {
    // since the username is not set in session, the user is not-logged-in
    // he is trying to access this page unauthorized

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
    <script src="https://cdn.tailwindcss.com"></script>
</HEAD>

<BODY>
    <div class="container mt-8">
        <div class="page-header">
            <span class="login-signup"><a href="logout.php"> <button class="border-solid border-2 !border-red-500 !text-red-500 !bg-white !px-4"> Logout </button></a></span>
        </div>
        <div class="page-content font-medium text-3xl">Welcome <span class="font-bold underline text-green-600"> <?php echo $username; ?></span></div>
    </div>
    <div class="container bg-white shadow-lg rounded-lg overflow-hidden" id="new-user">
        <div class="px-4 py-4 bg-gray-100">
            <h2 class="text-xl font-semibold text-gray-800">Notification List of Users Getting Registered</h2>
        </div>
    </div>

    <div id="toaster" class="toaster hidden bg-green-500 text-white py-2 px-4 rounded-md shadow-lg">
        <span id="toasterMessage"></span>
    </div>

    <script>
        var conn = new WebSocket('ws://localhost:8080');
        conn.onopen = function(e) {
            console.log("Connection established!");
        };
        conn.onmessage = function(e) {
            console.log(e.data);
            showNotification(e.data);
            showToast(e.data);
        };

        function showNotification(username) {
            // alert(username);
            var newUserNotification = $('#new-user');
            var usernameSpan = `<div class="p-4">
                                <div class="flex items-center py-2">
                                    <img class="h-8 w-8 mr-2" src="assets/images/user-logo.png" alt="User avatar">
                                    <p class="text-cyan-600 font-bold"> ${username} </p>
                                </div>
                                <hr class="border-gray-300">
                                </div>`;
            newUserNotification.append(usernameSpan);
        }

        function showToast(message) {
            $('#toasterMessage').text('New User Registered: ' + message);
            $('#toaster').removeClass('hidden').fadeIn();
            setTimeout(function() {
                $('#toaster').fadeOut();
            }, 10000);
        }
    </script>

</BODY>

</HTML>