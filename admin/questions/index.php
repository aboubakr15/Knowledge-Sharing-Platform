<?php
require_once __DIR__ . "/../../vendor/autoload.php";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/../partials/admin_header.php";
}

use App\Services\QuestionService;
use App\Services\UserService;

$questionService = new QuestionService();
$userService = new UserService();
$questions = $questionService->getAll();

if (isset($_POST['delete_question'])) {
    $questionID = $_POST['question_id'];

    $questionService->delete($questionID);

    header("Location: index.php");
    exit();
}

?>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2 class="display-6 text-center"> Questions </h2>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center">
                        <tr>
                            <td>Question_id</td>
                            <td>Username</td>
                            <td>Title</td>
                            <td>Body</td>
                            <td>Created at</td>
                            <td>Updated at</td>
                            <td>Reputations</td>
                            <td>Options</td>
                        </tr>
                        <?php

                        foreach ($questions as $question) {
                            echo "<tr>";
                            echo "<td>" . $question->getQuestionId() . "</td>";
                            echo "<td>" . $userService->getById($question->getUserId())->getUsername() . "</td>";
                            echo "<td>" . $question->getTitle() . "</td>";
                            echo "<td>" . $question->getBody() . "</td>";
                            echo "<td>" . $question->getCreatedAt() . "</td>";
                            echo "<td>" . $question->getUpdatedAt() . "</td>";
                            echo "<td>" . $question->getReputations() . "</td>";
                            echo "<td>";
                            echo "<form method='post' onsubmit='return confirm(\"Are you sure you want to delete this question?\")'>";
                            echo "<input type='hidden' name='question_id' value='" . $question->getQuestionID() . "' />";
                            echo "<button type='submit' class='btn btn-sm btn-danger d-flex p-2' name='delete_question'>Delete</button>";
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