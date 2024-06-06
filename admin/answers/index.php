<?php
require_once __DIR__ . "/../../vendor/autoload.php";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/../partials/admin_header.php";
}


use App\Services\AnswerService;
use App\Services\UserService;
use \App\Services\QuestionService;

$answerService = new AnswerService();
$userService = new UserService();
$questionService = new QuestionService();

$answers = $answerService->getAll();

if (isset($_POST['delete_answer'])) {
    $answerId = $_POST['answer_id'];

    $answerService->delete($answerId);

    header("Location: index.php");
    exit();
}
?>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2 class="display-6 text-center">Answers</h2>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center">
                        <tr>
                            <td>Answer ID</td>
                            <td>Username</td>
                            <td>Question</td>
                            <td>Body</td>
                            <td>Created At</td>
                            <td>Reputations</td>
                        </tr>
                        <?php
                        // Display answers
                        foreach ($answers as $answer) {
                            echo "<tr>";
                            echo "<td>" . $answer->getAnswerId() . "</td>";
                            echo "<td>" . $userService->getById($answer->getUserId())->getUsername() . "</td>";
                            echo "<td>" . substr($questionService->getById($answer->getQuestionId())->getBody(), 0, 20) . " ...</td>";
                            echo "<td>" . substr($answer->getBody(), 0, 20) . "...</td>";
                            echo "<td>" . $answer->getCreatedAt() . "</td>";
                            echo "<td>" . $answer->getReputations() . "</td>";
                            echo "<td>";
                            echo "<form method='post' onsubmit='return confirm(\"Are you sure you want to delete this answer?\")'>";
                            echo "<input type='hidden' name='answer_id' value='" . $answer->getAnswerId() . "' />";
                            echo "<button type='submit' class='btn btn-sm btn-danger d-flex p-2' name='delete_answer'>Delete</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>