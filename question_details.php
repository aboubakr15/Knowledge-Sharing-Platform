<?php
namespace App;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/partials/header.php";
}

require_once __DIR__ . "/vendor/autoload.php";

use App\Models\Vote;
use App\Models\Answer;
use App\Services\QuestionService;
use App\Services\TagService;
use App\Services\UserService;
use App\Services\AnswerService;
use App\Services\VotingService;
use App\Services\AuthService;
use App\Utils\Utils;

$authService = new AuthService();
$questionService = new QuestionService();
$userService = new UserService();
$answerService = new AnswerService();
$tagService = new TagService();
$voteService = new VotingService();

$tags = $tagService->getAll();

$logged_in_user = $authService->getCurrentUser();
$userID = $logged_in_user->getUserId();
$isAdmin = $logged_in_user->getRole() == "admin";

// Retrieving the question ID from the URL parameter
if (isset($_GET['id'])) {
    $questionId = $_GET['id'];

    $question = $questionService->getById($questionId);
    $answers = $answerService->getAnswersByQuestionID($questionId);
} else {
    header("Location: index.php");
    exit();
}

// Handling delete question submission
if (isset($_POST['delete_question'])) {
    $questionID = $_POST['question_id'];

    $questionService->delete($questionID);

    header("Location: index.php");
    exit();
}

// Handling voting for questions
if (isset($_POST['vote_question'])) {
    $questionID = $_POST['question_id'];
    $voteType = $_POST['vote_type'];

    $vote = new Vote($logged_in_user->getUserId(), $questionID, 0, $voteType);

    $reputation = $question->getReputations();

    if ($voteService->add_vote($vote)) {
        if ($voteType == "upvote") {
            $reputation += 1;
        } else {
            $reputation -= 1;
        }
    } else {
        if ($voteType == "upvote") {
            $reputation += 2;
        } else {
            $reputation -= 2;
        }
    }

    $question->setReputations($reputation);

    $questionService->update($questionID, $question);


    header_remove("Location");
    header("Location: question_details.php?id=$questionID");
    exit;
}

// Handling voting for answers
if (isset($_POST["vote_answer"])) {
    $votedAnswerId = $_POST['answer_id'];
    $voteType = $_POST['vote_type'];

    $voted_answer = $answerService->getById($votedAnswerId);
    if ($voted_answer) {
        $vote = new Vote($logged_in_user->getUserId(), 0, $votedAnswerId, $voteType);
        $reputation = $voted_answer->getReputations();
        if ($voteService->add_vote($vote, "answer")) {
            if ($voteType == "upvote") {
                $reputation += 1;
            } else {
                $reputation -= 1;
            }
        } else {
            if ($voteType == "upvote") {
                $reputation += 2;
            } else {
                $reputation -= 2;
            }
        }

        $voted_answer->setReputations($reputation);
        $answerService->update($votedAnswerId, $voted_answer);

        header_remove("Location");
        header("Location: " . $domain . "question_details.php?id=" . $questionId);
        exit;
    } else {
        echo "Error: The answer does not exist.";
    }
}

// Handling answer submission
if (isset($_POST['submit_answer'])) {
    $answerText = $_POST['answer'];
    $questionID = $_POST['question_id'];

    $currentDateTime = date("Y-m-d H:i:s");
    $voted_answer = new Answer($logged_in_user->getUserId(), $questionID, $answerText, $currentDateTime, null, null);

    $answerService = new AnswerService();
    $answerService->create($voted_answer);
    header_remove("Location");
    header("Location: question_details.php?id=" . $questionID);
    exit;
}

// Handling delete answer submission
if (isset($_POST['delete_answer'])) {
    $answerId = $_POST['answer_id'];

    $answerService->delete($answerId);
    header("Location: question_details.php?id=" . $questionId);

    exit();
}

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card bg-light mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <?php $user = $userService->getById($question->getUserID()); ?>
                        <?php $userImage = $user->photo; ?>
                        <img src="<?php echo $userImage; ?>" alt="User Photo" class="rounded-circle me-2"
                            style="width: 40px; height: 40px;">
                        <div>
                            <h6 class="mb-0"><?php echo $user->username; ?></h6>
                            <small class="text-muted"><?php echo Utils::time_since($question->getCreatedAt()) ?></small>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <h5 class="card-title"><?php echo $question->getTitle(); ?></h5>
                    <p class="card-text">
                        <?php foreach ($tagService->getTagsByQuestionID($question->getQuestionID()) as $tag): ?>
                            <span class="badge bg-primary"><?php echo $tag->getName(); ?></span>
                        <?php endforeach; ?>
                    </p>
                    <p class="card-text"><?php echo $question->getBody(); ?></p>
                    <p class="card-text"><small class="text-muted">Posted by
                            <?php echo $userService->getById($question->getUserID())->getUsername(); ?>
                            <?php echo Utils::time_since($question->getCreatedAt()); ?></small></p>
                    <div class="btn-group gap-2" role="group" aria-label="Vote Question">
                        <form method="post">
                            <input type="hidden" name="question_id" value="<?php echo $question->getQuestionID(); ?>">
                            <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-thumbs-up"></i>
                                <?php echo $voteService->getUpvotesCount($question->getQuestionId()) ?></button>
                            <input type="hidden" name="vote_question" value="true">
                            <input type="hidden" name="vote_type" value="upvote">
                        </form>
                        <form method="post">
                            <input type="hidden" name="question_id" value="<?php echo $question->getQuestionID(); ?>">
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-thumbs-down"></i>
                                <?php echo $voteService->getDownvotesCount($question->getQuestionId()) ?></button>
                            <input type="hidden" name="vote_question" value="true">
                            <input type="hidden" name="vote_type" value="downvote">
                        </form>
                        <?php if ($question->getUserID() == $userID || $isAdmin): ?>
                            <form method="post" action="">
                                <input type="hidden" name="question_id" value="<?php echo $question->getQuestionID(); ?>">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i>
                                    Delete</button>
                                <input type="hidden" name="delete_question" value="true">
                            </form>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h5 class="card-title">Your Answer</h5>
                    <form method="post">
                        <input type="hidden" name="question_id" value="<?php echo $questionId ?>">
                        <div class="form-group">
                            <textarea class="form-control" name="answer" rows="3" placeholder="Enter your answer here"
                                required></textarea>
                        </div>
                        <button type="submit" name="submit_answer" class="btn btn-primary mt-2">Post Your
                            Answer</button>
                    </form>
                </div>
            </div>


            <?php foreach ($answers as $ans): ?>
                <div class="card bg-light mb-3">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <?php $user = $userService->getById($ans->getUserID()); ?>
                            <?php $userImage = $user->getPhoto(); ?>
                            <img src="<?php echo $userImage; ?>" alt="User Photo" class="rounded-circle me-2"
                                style="width: 40px; height: 40px;">
                            <div>
                                <h6 class="mb-0"><?php echo $user->username; ?></h6>
                                <small class="text-muted"><?php echo Utils::time_since($question->getCreatedAt()) ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="card-text"><?php echo $ans->getBody(); ?></p>
                        <p class="card-text"><small class="text-muted">Posted by
                                <?php echo $userService->getById($ans->getUserID())->getUsername(); ?>
                                <?php echo Utils::time_since($ans->getCreatedAt()); ?></small></p>
                        <div class="btn-group gap-2" role="group" aria-label="Vote Answer">
                            <form method="post">
                                <input type="hidden" name="answer_id" value="<?php echo $ans->getAnswerId(); ?>">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-thumbs-up"></i>
                                    <?php echo $voteService->getUpvotesCountForAnswers($ans->getAnswerId()); ?></button>
                                <input type="hidden" name="vote_answer" value="true">
                                <input type="hidden" name="vote_type" value="upvote">
                            </form>
                            <form method="post">
                                <input type="hidden" name="answer_id" value="<?php echo $ans->getAnswerId(); ?>">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-thumbs-down"></i>
                                    <?php echo $voteService->getDownvotesCountForAnswers($ans->getAnswerId()); ?></button>
                                <input type="hidden" name="vote_answer" value="true">
                                <input type="hidden" name="vote_type" value="downvote">
                            </form>
                        </div>
                        <?php if ($ans->getUserID() == $userID): ?>
                            <div class="btn-group" role="group" aria-label="Answer Actions">
                                <form method="post" action="edit_answer.php">
                                    <input type="hidden" name="answer_id" value="<?php echo $ans->getAnswerId(); ?>">
                                    <button type="submit" class="btn btn-link"><i class="fas fa-edit"></i></button>
                                </form>
                                <form method="post" action=""
                                    onsubmit="return confirm('Are you sure you want to delete this answer?')">
                                    <input type="hidden" name="answer_id" value="<?php echo $ans->getAnswerId(); ?>">
                                    <button type="submit" class="btn btn-link"><i class="fas fa-trash-alt"></i></button>
                                    <input type="hidden" name="delete_answer" value="true">
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>


        </div>
    </div>
</div>

<?php require_once __DIR__ . "/partials/footer.php"; ?>