<?php
session_start();
$page = isset($_GET['page']) ? $_GET['page'] : "";

if (isset($_SESSION['login'])) {
    if ($page == "" || $page == "login") {
        header("Location: dashboard/index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPFC</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!--bootstrap css-->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>
<!--navbar-->  
<nav class="navbar navbar-expand-sm bg-primary navbar-dark">
<ul class="navbar-nav">
    <li class="nav-item active">
      <a class="nav-link" href="index.php?page=login">Quality Control Kendaraan Rantis Sentra Surya Ekajaya</a>
    </li>
  </ul>
</nav>
<!--container-->
<?php
$page = isset($_GET['page']) ? $_GET['page'] : "";
$action = isset($_GET['action']) ? $_GET['action'] : "";

if ($page==""){
    include "login.php"; // default ke login
}
elseif ($page=="login"){
    include "login.php"; // route login
}
elseif ($page=="welcome"){
    include "welcome.php"; // kalau mau tetap ada welcome
}
elseif ($page=="NAMA_PAGE"){
    if ($action==""){
        include "NAMA_HALAMAN";
    }elseif ($action=="NAMA_ACTION"){
        include "NAMA_HALAMAN";
    }else{
        include "NAMA_HALAMAN";
    }
}
else{
    include "welcome.php"; // fallback
}
?>