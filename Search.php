<?php
require __DIR__ . "/vendor/autoload.php";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/partials/header.php";
}


use App\Utils\DBConnection;

if (!isset($_GET['q']) || empty($_GET['q'])) {
    header('location: ../');
    exit();
}


$conn = new DBConnection();
$db = $conn->getConnection();
$q = trim($_GET['q']);

$sql = "SELECT q.* FROM Questions q";
$tagFilter = isset($_GET['tag']) ? trim($_GET['tag']) : null;
$dateFilter = isset($_GET['date']) ? trim($_GET['date']) : null;

$whereClause = " WHERE 1";
$bindings = [];

if (!empty($tagFilter)) {
    $sql .= " INNER JOIN Question_Tags qt ON q.question_id = qt.question_id";
    $sql .= " INNER JOIN Tags t ON qt.tag_id = t.tag_id";
    $whereClause .= " AND t.name LIKE :tag"; // Use LIKE instead of strict equality
    $bindings[':tag'] = '%' . $tagFilter . '%'; // Add wildcards to search for substrings
}


$whereClause .= " AND (q.title LIKE :query OR q.body LIKE :query)";
$bindings[':query'] = '%' . $q . '%';

if (!empty($dateFilter)) {
    $selectedDate = new DateTime($dateFilter);
    $year = $selectedDate->format('Y');
    $month = $selectedDate->format('m');
    $startDate = $year . '-' . $month . '-01';
    $endDate = date('Y-m-d');
    $whereClause .= " AND (q.created_at >= :start_date AND q.created_at <= :end_date)";
    $bindings[':start_date'] = $startDate;
    $bindings[':end_date'] = $endDate;
}

$sql .= $whereClause;

$stmt = $db->prepare($sql);
$stmt->execute($bindings);
$questionsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$items_count = count($questionsData);

if ($items_count == 0) {
    echo '
        <div class="alert alert-danger d-flex align-items-center container mt-5 " role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
            <div class="text-center">
                There are no results for this search query "' . $q . '"
            </div>
        </div>';
} else {
    echo "<h3 class='col-md-8 m-auto mt-5'> <span class='badge bg-success rounded-pill'>$items_count</span> results found for \"$q\"</h3>";

    echo '
    <div class="col-md-8 m-auto mt-3">
        <form class="row g-3" method="GET">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Filter by Tag" name="tag" id="tag-filter" value="' . ($tagFilter ? htmlspecialchars($tagFilter) : '') . '">
                <div id="tag-suggestions"></div>
            </div>
            <div class="col-md-4">
                <input type="month" class="form-control" name="date" value="' . ($dateFilter ? htmlspecialchars($dateFilter) : '') . '">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </div>
            <input type="hidden" name="q" value="' . htmlspecialchars($q) . '">
        </form>
    </div>';

    foreach ($questionsData as $questionData) {
        $question = new App\Models\Question(
            $questionData['question_id'],
            $questionData['user_id'],
            $questionData['title'],
            $questionData['body'],
            $questionData['created_at'],
            $questionData['updated_at'],
            $questionData['reputations']
        );

        $tagService = new App\Services\TagService();
        $tags = $tagService->getTagsByQuestionID($question->getQuestionId());

        $votingService = new App\Services\VotingService();
        $upvotesCount = $votingService->getUpvotesCount($question->getQuestionId());
        $downvotesCount = $votingService->getDownvotesCount($question->getQuestionId());

        echo '
        <div class="col-md-8 m-auto mt-3 question-card">
            <div class="row border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                <div class="col p-4 d-flex flex-column position-static"> 
                    <a class="d-inline-block mb-2 text-primary" style="text-decoration: none;font-weight: bold;" href="../question_details.php?id=' . $question->getQuestionId() . '">' . htmlspecialchars($question->getTitle()) . '</a>
                    <div class="mb-1 text-muted">' . $question->getCreatedAt() . '</div>
                    <p class="card-text mb-auto">' . htmlspecialchars(substr($question->getBody(), 0, 100)) . '...</p>
                    <div class="d-flex">
                        <span class="text-success me-2">' . $upvotesCount . ' Upvotes</span>
                        <span class="text-danger">' . $downvotesCount . ' Downvotes</span>
                    </div>
                </div>
            </div>
        </div>';
    }
}

$db = null;
?>
<script>
    // Add event listeners to each question card
    document.querySelectorAll('.question-card').forEach(card => {
        card.addEventListener('mouseover', function () {
            // Change cursor to pointer
            card.style.cursor = 'pointer';
        });

        card.addEventListener('click', function () {
            // Redirect to the question details page when clicked
            window.location.href = card.querySelector('a').href;
        });
    });
</script>