<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login($role, $redirect)
{
    if (empty($_SESSION[$role])) {
        header("Location: $redirect");
        exit();
    }
}

function logout_user($redirect)
{
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
    header("Location: $redirect");
    exit();
}
?>
