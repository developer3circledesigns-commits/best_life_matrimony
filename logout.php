<?php
require_once __DIR__ . '/includes/db.php';
if (function_exists('remember_me_clear')) remember_me_clear();
$_SESSION = [];
session_destroy();
header('Location: ./index.php');
exit;
