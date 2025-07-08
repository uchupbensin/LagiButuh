<?php
// File: admin/logout.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/env.php';
require_once '../core/AdminAuth.php';
require_once '../functions/helper_functions.php';

$adminAuth = new AdminAuth();
$adminAuth->logout();

redirect(BASE_URL . '/admin/login.php');
?>
