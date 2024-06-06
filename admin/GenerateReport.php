<?php
// Include the TCPDF library
// Include the necessary files and initialize the UserService
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Utils/config.php";
// require_once __DIR__ . "/../Utils/TCPDF/tcpdf.php";
use TCPDF;
use App\Services\UserService;
use App\Services\QuestionService;

// Create an instance of the UserService
$userService = new UserService();
$questionService = new QuestionService();

// Fetch the required data
$usersJoinedStatistics = $userService->getUsersJoinedStatistics();
$usersWithHighestReputations = $userService->getUsersWithHighestReputations();
$usersWithMostBadges = $userService->getUsersWithMostBadges();
$mostUpvotedQuestions = $questionService->getMostUpvotedQuestions();
$questionsWithHighestReputations = $questionService->getQuestionsWithHighestReputations();

// Create a new TCPDF instance
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Admin');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Report');
$pdf->SetSubject('Report');
$pdf->SetKeywords('Report');

$header = 'Management Report';

// Set header content
$pdf->setPrintHeader(true);
$pdf->SetHeaderData('', 0, '', $header);

// Set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set margins
$pdf->SetMargins(15, 25, 15);

// Add a page
$pdf->AddPage();

// Footer content
$footer = '<table width="100%">
    <tr>
        <td align="left">Page ' . $pdf->getAliasNumPage() . ' of ' . $pdf->getAliasNbPages() . '</td>
        <td align="right">Report Footer</td>
    </tr>
</table>';

// Set footer content
$pdf->setPrintFooter(true);
$pdf->SetFooterData('', 0, '', $footer);

// Add content to the PDF
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Write(0, 'Users Joined:', '', 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Ln();
$pdf->Write(0, '--------------------------------------------------------------', '', 0, 'L');
$pdf->Ln();

$pdf->Write(0, 'Last Day: ' . $usersJoinedStatistics['last_day'], '', 0, 'L');
$pdf->Ln();
$pdf->Ln();
$pdf->Write(0, 'Last Week: ' . $usersJoinedStatistics['last_week'], '', 0, 'L');
$pdf->Ln();
$pdf->Ln();
$pdf->Write(0, 'Last Month: ' . $usersJoinedStatistics['last_month'], '', 0, 'L');
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Write(0, 'Users with Highest Reputations:', '', 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Ln();
$pdf->Write(0, '--------------------------------------------------------------', '', 0, 'L');
$pdf->Ln();

foreach ($usersWithHighestReputations as $user) {
    if (isset($user['username']) && isset($user['reputations'])) {
        $pdf->Write(0, $user['username'] . ' (Reputation: ' . $user['reputations'] . ')', '', 0, 'L');
        $pdf->Ln();
        $pdf->Ln();
    }
}
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Write(0, 'Users with Most Badges:', '', 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Ln();
$pdf->Write(0, '--------------------------------------------------------------', '', 0, 'L');
$pdf->Ln();

foreach ($usersWithMostBadges as $user) {
    if (isset($user['username']) && isset($user['badge_count'])) {
        $pdf->Write(0, $user['username'] . ' (Badges: ' . $user['badge_count'] . ')', '', 0, 'L');
        $pdf->Ln();
        $pdf->Ln();
    }
}
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Write(0, 'Most Upvoted Questions:', '', 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Ln();
$pdf->Write(0, '--------------------------------------------------------------', '', 0, 'L');
$pdf->Ln();

foreach ($mostUpvotedQuestions as $question) {
    if (isset($question['question_id']) && isset($question['title']) && isset($question['upvotes'])) {
        $pdf->Write(0, 'Question ID: ' . $question['question_id'], '', 0, 'L');
        $pdf->Ln();
        $pdf->Write(0, 'Title: ' . $question['title'], '', 0, 'L');
        $pdf->Ln();
        $pdf->Write(0, 'Upvotes: ' . $question['upvotes'], '', 0, 'L');
        $pdf->Ln();
        $pdf->Ln();
    }
}
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Write(0, 'Questions with Highest Reputations:', '', 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Ln();
$pdf->Write(0, '--------------------------------------------------------------', '', 0, 'L');
$pdf->Ln();

foreach ($questionsWithHighestReputations as $question) {
    if (isset($question['question_id']) && isset($question['title']) && isset($question['reputations'])) {
        $pdf->Write(0, 'Question ID: ' . $question['question_id']);
        $pdf->Ln();
        $pdf->Write(0, 'Title: ' . $question['title'], '', 0, 'L');
        $pdf->Ln();
        $pdf->Write(0, 'Reputations: ' . $question['reputations'], '', 0, 'L');
        $pdf->Ln();
        $pdf->Ln();
    }
}
$pdf->Ln();
$pdf->Ln();

// Close and output PDF document
$pdf->Output('report.pdf', 'D'); // D means force download

// Terminate script execution
exit;
?>