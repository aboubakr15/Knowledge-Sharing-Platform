<?php

use App\Services\AuthService;

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../Utils/config.php';

$authService = new AuthService();
?>

<html>

<head>
    <title>Title</title>
    <link rel="stylesheet" href="<?php echo $domain; ?>/css/bootstrap.min.css" />
</head>

<body>
    <nav class="navbar navbar-dark navbar-expand-lg bg-dark ">
        <div class="container-fluid">

            <a class="navbar-brand" href="<?php echo $domain; ?>">Knowledge</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
                aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page"
                            href="<?php echo $domain; ?>admin/index.php">Home</a>
                    </li>
                    <?php if (!$authService->isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $domain; ?>login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $domain; ?>register.php">Register</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Welcome, <?php echo $authService->getCurrentUser()->username; ?>
                            </a>
                        </li>
                        <?php if ($authService->getCurrentUser()->getRole() == "admin"): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $domain; ?>/admin/users/">Users</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $domain; ?>/admin/tags/">Tags</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $domain; ?>/admin/questions/">Questions</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $domain; ?>/admin/answers/">Answers</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link logout-btn" href="<?php echo $domain; ?>logout.php">Logout</a>
                        </li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="logoutConfirmationModal" tabindex="-1" aria-labelledby="logoutConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutConfirmationModalLabel">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="logoutLink" href="<?php echo $domain; ?>logout.php" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo $domain; ?>/js/bootstrap.min.js"></script>
    <script>
        // JavaScript to handle logout confirmation
        document.addEventListener("DOMContentLoaded", function () {
            var logoutButton = document.querySelector('.logout-btn');
            logoutButton.addEventListener('click', function (event) {
                event.preventDefault();
                var logoutConfirmationModal = new bootstrap.Modal(document.getElementById(
                    'logoutConfirmationModal'));
                logoutConfirmationModal.show();
            });
        });
    </script>