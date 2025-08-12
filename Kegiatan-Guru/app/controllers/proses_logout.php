<?php
session_start();

// Hapus semua data session
session_destroy();

// Redirect ke halaman login
header("Location: /KODINGAN/PWPB/Aplikasi-Guru/");
exit();
?> 