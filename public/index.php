<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (current_user()) {
    redirect('/sistema_hospital/public/dashboard.php');
}

redirect('/sistema_hospital/public/login.php');
