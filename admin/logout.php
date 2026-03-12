<?php
require_once '../config/koneksi.php';
session_unset();
session_destroy();
session_regenerate_id(true);
header("Location: login.php?pesan=logout");
exit();