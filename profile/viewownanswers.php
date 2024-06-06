<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/../partials/header.php";
}
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Utils/config.php";
use App\Services\AnswerService;


$answerService = new AnswerService();

$userId = isset($_GET['id']) ? $_GET['id'] : $authService->getCurrentUser()->getUserId();
$answers = $answerService->getAnswersByUserID($userId);
?>
<div class="container mt-5">
    <h2>My Answers</h2>
    <hr>
    <!-- Answer List -->
    <div class="card">
        <div class="card-header">
            My Answer List
        </div>
        <div class="card-body">
            <ul class="list-group">
                <?php
                // Loop through the answers and display them
                foreach ($answers as $answer) {
                    echo '<li class="list-group-item">';
                    echo '<h5>' . $answer->body . '</h5>';
                    echo '<small>Created at: ' . $answer->getCreatedAt() . '</small><br/>';
                    echo "<a class='btn btn-outline-primary' href='{$domain}question_details.php?id={$answer->getQuestionId()}'>Go</a>";
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
</div>
</div>