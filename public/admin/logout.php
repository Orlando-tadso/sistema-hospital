<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/admin_auth.php';

admin_logout();
redirect('/sistema_hospital/public/admin/login.php');
