<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';

logoutUser();

header('Location: ../home/index.php');
exit;
