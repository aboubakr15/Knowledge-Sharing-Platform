<?php
// mark_all_notifications_read.php

use App\Services\AuthService;
use App\Services\NotificationService;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Utils/config.php';

$notificationService = new NotificationService();
$notificationService->markAllAsRead((new AuthService())->getCurrentUser()->getUserId());

echo "ok";