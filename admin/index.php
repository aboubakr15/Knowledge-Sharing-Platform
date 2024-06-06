<?php

require_once __DIR__ . "/../vendor/autoload.php";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/partials/admin_header.php";
}

// Instantiate the services
$userService = new App\Services\UserService();
$questionService = new App\Services\QuestionService();
$tagService = new App\Services\TagService();
$answerService = new App\Services\AnswerService();

// Get counts from the services
$userCount = count($userService->getAll());
$questionCount = count($questionService->getAll());
$tagCount = count($tagService->getAll());
$answerCount = count($answerService->getAll());

?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f5f5f5;
    }

    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        color: #333;
        /* Heading color */
        margin-bottom: 30px;
    }

    .card {
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .users-card {
        background-color: #4caf50;
    }

    .questions-card {
        background-color: #2196f3;
    }

    .tags-card {
        background-color: #ff9800;
    }

    .answers-card {
        background-color: #f44336;
    }

    .count {
        font-size: 36px;
        font-weight: bold;
        text-align: center;
        color: #fff;
    }

    .generate-btn {
        display: block;
        width: 100%;
        padding: 15px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        text-align: center;
        text-decoration: none;
    }

    .generate-btn:hover {
        background-color: #0056b3;
    }
</style>
</head>

<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <!-- Users Card -->
        <div class="card users-card">
            <h2 style="color: #fff;">Users</h2>
            <div class="count"><?php echo $userCount; ?></div>
        </div>
        <!-- Questions Card -->
        <div class="card questions-card">
            <h2 style="color: #fff;">Questions</h2>
            <div class="count"><?php echo $questionCount; ?></div>
        </div>
        <!-- Tags Card -->
        <div class="card tags-card">
            <h2 style="color: #fff;">Tags</h2>
            <div class="count"><?php echo $tagCount; ?></div>
        </div>
        <!-- Answers Card -->
        <div class="card answers-card">
            <h2 style="color: #fff;">Answers</h2>
            <div class="count"><?php echo $answerCount; ?></div>
        </div>
        <!-- Generate Report Button -->
        <a href='<?php echo $domain; ?>/admin/GenerateReport.php' class="generate-btn">Generate Report</a>
    </div>
</body>

</html>