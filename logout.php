<?php

require_once __DIR__."/vendor/autoload.php";
require_once __DIR__."/Utils/config.php";

use App\Services\AuthService;

$authService = new AuthService();

$authService->logout();

header("Location: ".$domain);

$exit;
