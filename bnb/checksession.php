<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();

    function checkUser()
    {
        $_SESSION['URI'] = '';
        if ($_SESSION['loggedin'] == 1)


            return TRUE;

        else {
            $_SESSION['URI'] = '/' . $_SERVER['REQUEST_URI']; //for development on local host
            // Deployed on InfinityFree and ProFreeHost
            // $_SESSION['URI'] = 'http://bednbreakfast.infinityfreeapp.com/' . $_SERVER['REQUEST_URI']; // for production, update URL
            // $_SESSION['URI'] = 'http://bednbreakfast.unaux.com/bnb/' . $_SERVER['REQUEST_URI']; // for production, update URL
            header(('Location:/bnb/login.php'), true, 303);
        }
    }

    function loginStatus()
    {

        $un = $_SESSION['username'];
        // $id = $_SESSION['userid'];

        if ($_SESSION['loggedin'] == 1) {
            echo "<h5>Logged User: $un </h5>";
        } else {
            // echo "<h4>Logged Out</h4>";
        }
    }

    function login($id, $username)
    {

        if ($_SESSION['loggedin'] == 0 and !empty($_SESSION['URI'])) {
            $uri = $_SESSION['URI'];
        } else {
            $_SESSION['URI'] = '/bnb';
            $uri = $_SESSION['URI'];
        }

        header('Location: /bnb', true, 303);

        $_SESSION['loggedin'] = 1;
        $_SESSION['userid'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['URI'] = '';
    }

    function logout()
    {
        $_SESSION['loggedin'] = 0;
        $_SESSION['userid'] = -1;
        $_SESSION['username'] = '';
        $_SESSION['URI'] = '';
        header('Location:/bnb/login.php', true, 303);
    }
}
