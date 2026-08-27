<?php
require_once __DIR__ . '/includes/config.php';
remember_me_clear();
$_SESSION = [];
session_destroy();
header('Location: ./index.php');
exit;
