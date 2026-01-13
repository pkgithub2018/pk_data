<?php
session_start();
$_SESSION['uid'] = 27;
$_SESSION['gid'] = 3;
$_GET['appid'] = 163;
$_GET['action'] = 'generate';

include 'certificate_pdf.php';
?>