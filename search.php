<?php
include("global.php");
include("header.php");
?>
<link rel="icon" type="image/png" href="./favicon.ico">
<link rel="stylesheet" type="text/css" href="./css/global.css">
<link rel="stylesheet" type="text/css" href="./css/index.css">
<?php
if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $query = trim($_GET['query']);
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
    $results_per_page = 10;
    $offset = ($page - 1) * $results_per_page;

    $search_term = '%' . $query . '%';

    $count_stmt = $mysqli->prepare("SELECT COUNT(*) FROM videos WHERE videotitle LIKE ?");
    $count_stmt->bind_param("s", $search_term);
    $count_stmt->execute();
    $count_stmt->bind_result($total_results);
    $count_stmt->fetch();
    $count_stmt->close();

    $total_pages = ceil($total_results / $results_per_page);

    $stmt = $mysqli->prepare("SELECT id, videotitle, description FROM videos WHERE videotitle LIKE ? ORDER BY date DESC LIMIT ?, ?");
    $stmt->bind_param("sii", $search_term, $offset, $results_per_page);
    $stmt->execute();
    $stmt->bind_result($id, $videotitle, $description);

    echo '<center><div style="margin-top: 20px; width: 70%; text-align: left;">';
    echo "<p>Showing results for '<strong>" . htmlspecialchars($query) . "</strong>' — $total_results found</p><hr>";

    while ($stmt->fetch()) {
        echo '<div style="margin-bottom: 20px;">';
        echo '<h3><a href="viewvideo.php?id=' . htmlspecialchars($id) . '">' . htmlspecialchars($videotitle) . '</a></h3>';
        echo '<p>' . htmlspecialchars($description) . '</p>';
        echo '<hr>';
        echo '</div>';
    }
    $stmt->close();

    echo '<div style="text-align: center;">';
    if ($page > 1) {
        echo '<a href="?query=' . urlencode($query) . '&page=' . ($page - 1) . '">Previous</a> ';
    }
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $page) {
            echo '<strong>' . $i . '</strong> ';
        } else {
            echo '<a href="?query=' . urlencode($query) . '&page=' . $i . '">' . $i . '</a> ';
        }
    }
    if ($page < $total_pages) {
        echo '<a href="?query=' . urlencode($query) . '&page=' . ($page + 1) . '">Next</a>';
    }
    echo '</div>';

    echo '</div></center>';
}
?>

<?php
include("footer.php");
?>
