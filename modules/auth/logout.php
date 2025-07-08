<?php
// File: modules/auth/logout.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../functions/helper_functions.php';

$auth = new Auth();
$auth->logout();

redirect(BASE_URL . '/login');
?>