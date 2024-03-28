<?php

namespace Demo;

class Member
{

    private $ds;

    function __construct()
    {
        require_once __DIR__ . '/../lib/DataSource.php';
        $this->ds = new DataSource();
    }


    public function isUsernameExists($username)
    {
        $query = 'SELECT * FROM tbl_member where username = ?';
        $paramType = 's';
        $paramValue = array(
            $username
        );
        $resultArray = $this->ds->select($query, $paramType, $paramValue);
        $count = 0;
        if (is_array($resultArray)) {
            $count = count($resultArray);
        }
        if ($count > 0) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }


    public function isEmailExists($email)
    {
        $query = 'SELECT * FROM tbl_member where email = ?';
        $paramType = 's';
        $paramValue = array(
            $email
        );
        $resultArray = $this->ds->select($query, $paramType, $paramValue);
        $count = 0;
        if (is_array($resultArray)) {
            $count = count($resultArray);
        }
        if ($count > 0) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }


    public function registerMember()
    {
        // Sanitize user input
        $username = htmlspecialchars($_POST["username"]);
        $email = htmlspecialchars($_POST["email"]);

        // Check if username or email already exists
        $isUsernameExists = $this->isUsernameExists($username);
        $isEmailExists = $this->isEmailExists($email);

        // Prepare response array
        $response = [];

        if ($isUsernameExists) {
            $response = [
                "status" => "error",
                "message" => "Username already exists."
            ];
        } elseif ($isEmailExists) {
            $response = [
                "status" => "error",
                "message" => "Email already exists."
            ];
        } else {
            try {
                // Hash the password
                $hashedPassword = password_hash($_POST["signup-password"], PASSWORD_DEFAULT);

                // Insert new member into database
                $query = 'INSERT INTO tbl_member (username, password, email) VALUES (?, ?, ?)';
                $paramType = 'sss';
                $paramValue = [$username, $hashedPassword, $email];
                $memberId = $this->ds->insert($query, $paramType, $paramValue);

                // If insertion successful, send WebSocket message
                if (!empty($memberId)) {
                    echo "<script type='text/javascript'>
                    var conn = new WebSocket('ws://localhost:8080');
                    conn.onopen = function() {
                        conn.send('$username');
                    };
                </script>";

                    $response = [
                        "status" => "success",
                        "message" => "You have registered successfully."
                    ];
                }
            } catch (\Exception $e) {
                $response = [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ];
            }
        }
        return $response;
    }


    public function getMember($username)
    {
        $query = 'SELECT * FROM tbl_member where username = ?';
        $paramType = 's';
        $paramValue = array(
            $username
        );
        $memberRecord = $this->ds->select($query, $paramType, $paramValue);
        return $memberRecord;
    }

    /**
     * to login a user
     *
     * @return string
     */
    public function loginMember()
    {
        $memberRecord = $this->getMember($_POST["username"]);
        $loginPassword = 0;
        if (!empty($memberRecord)) {
            if (!empty($_POST["login-password"])) {
                $password = $_POST["login-password"];
            }
            $hashedPassword = $memberRecord[0]["password"];
            $loginPassword = 0;
            if (password_verify($password, $hashedPassword)) {
                $loginPassword = 1;
            }
        } else {
            $loginPassword = 0;
        }
        if ($loginPassword == 1) {
            // login sucess so store the member's username in
            // the session
            session_start();
            $_SESSION["username"] = $memberRecord[0]["username"];
            session_write_close();
            $url = "./home.php";
            header("Location: $url");
        } else if ($loginPassword == 0) {
            $loginStatus = "Invalid username or password.";
            return $loginStatus;
        }
    }
}
