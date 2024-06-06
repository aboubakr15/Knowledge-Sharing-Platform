<?php
require_once __DIR__ . "/../../vendor/autoload.php";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . "/../partials/admin_header.php";
}

use App\Services\TagService;

$tagService = new TagService();

$tags = $tagService->getAll();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_tag"])) {
    $tag_id = $_POST["delete_tag"];

    $tagService = new App\Services\TagService();

    $tagService->delete($tag_id);

    header("Location: index.php");
    exit();
}

?>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2 class="display-6 text-center">Tags</h2>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center">
                        <tr>
                            <td>Tag ID</td>
                            <td>Name</td>
                            <td>Number of questions</td>
                            <td>Action</td> <!-- Add a new column for actions -->
                        </tr>
                        <?php
                        // Display tags
                        foreach ($tags as $tag) {
                            echo "<tr>";
                            echo "<td>" . $tag->getTagId() . "</td>";
                            echo "<td>" . $tag->getName() . "</td>";
                            echo "<td>" . $tagService->getTagQuestionCount($tag->getTagId()) . "</td>";
                            echo "<td class='d-flex justify-content-center gap-2'>";
                            // Create a form with delete button
                            echo "<form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this tag?\")'>";
                            echo "<input type='hidden' name='delete_tag' value='" . $tag->getTagId() . "'>";
                            echo "<button type='submit' class='btn btn-danger'>Delete</button>";
                            echo "</form>";
                            echo "<a href='edit.php?tag_id=" . $tag->getTagId() . "' class='btn btn-primary' style='height: fit-content;'>Edit</a>";
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