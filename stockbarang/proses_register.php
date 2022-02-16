<?php
require('function.php');

//mendapatkan semua inputan
$namadepan = $_POST['nama_depan'];
$namabelakang = $_POST['nama_belakang'];
$username = $_POST['username'];
$password = $_POST['password'];
$confirmpassword = $_POST['confirm_password'];

//Memastikan inputan tidak kosong
if(empty($namadepan) || empty($namabelakang) || empty($username) || empty($password) || empty($confirmpassword)){
    $msg = "Inputan Tidak Boleh Ada Yang Kosong ";
    header("location:register.php?msg=" . $msg);
    return;
}
//mengecek apakah password sama atau tidak
if($password !== $confirmpassword){
    $msg = "Katasandi tidak sama ";
    header("location:register.php?msg=" . $msg);
    return;
}

//menyimpan ke database
$sql = "INSERT INTO multi_akun (nama_depan, nama_belakang, username, password, confirm_password) VALUES ('$namadepan', '$namabelakang', '$username', '$password', '$confirmpassword')";

if ($conn->query($sql)) {
    $msg = "Register Berhasil";
} else {
    $msg = "Register Gagal";
}

header("location:login.php?msg=" . $msg);