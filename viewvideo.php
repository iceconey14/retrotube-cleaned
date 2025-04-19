<?php include("global.php");?>
<!DOCTYPE html>
<html data-theme="light">
<head>
    <link rel="stylesheet" type="text/css" href="./css/global.css">
    <link rel="stylesheet" type="text/css" href="./css/index.css">
    <title>Watch - RETROTube</title>
</head>
</html>
<?php
include("header.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$video_id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

if (!$video_id) {
    exit('Invalid video ID');
}

?>

<div class="topLeft">
    <?php

        $stmt = $mysqli->prepare("SELECT * FROM videos WHERE id = ?");
        $stmt->bind_param("i", $video_id);  
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows === 0) exit('No rows');
        while($row = $result->fetch_assoc()) {
            echo '
            <h2>' . htmlspecialchars($row['videotitle'], ENT_QUOTES, 'UTF-8') . '</h2>
            <iframe id="vid-player" style="border: 0px; overflow: hidden;" src="player/lolplayer.php?id=' . htmlspecialchars($video_id, ENT_QUOTES, 'UTF-8') . '" height="360px" width="480px"></iframe> <br><br>
                <script>
                    var vid = document.getElementById(\'vid-player\').contentWindow.document.getElementById(\'video-stream\');
                    function hmsToSecondsOnly(str) {
                        var p = str.split(\':\'),
                            s = 0, m = 1;

                        while (p.length > 0) {
                            s += m * parseInt(p.pop(), 10);
                            m *= 60;
                        }

                        return s;
                    }

                    function setTimePlayer(seconds) {
                        var parsedSec = hmsToSecondsOnly(seconds);
                        document.getElementById(\'vid-player\').contentWindow.document.getElementById(\'video-stream\').currentTime = parsedSec;
                    }
                </script>';

            $videoid = $row['id'];
        }
    ?>

<div class="topRight" style="margin-left: 500px; margin-top: -336px;">
<div class="card gray">
        <?php
            $stmt = $mysqli->prepare("SELECT * FROM videos WHERE id = ?");
            $stmt->bind_param("i", $video_id);  
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows === 0) exit('No rows');
            while($row = $result->fetch_assoc()) {
                echo "Added: " . htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8') . "<br>";
                echo "" . htmlspecialchars($row['views'], ENT_QUOTES, 'UTF-8') . " views<br>";
                echo "" . htmlspecialchars($row['likes'], ENT_QUOTES, 'UTF-8') . " likes<br>";
                echo "By: " . htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8') . "<br><br>";
                echo "<br>'" . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . "'<br>";
                echo "<a href='likevideo.php?id=" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "'>Like Video</a>";
            }

        ?>  
    </div>
        <br>
        <div class="card message">     
        <?php
            $stmt = $mysqli->prepare("SELECT * FROM videos WHERE id = ?");
            $stmt->bind_param("i", $video_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows === 0) exit('No rows');
            while($row = $result->fetch_assoc()) {
                echo "URL <input value=\"https://retrotube.ml/viewvideo.php?id=" . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . "\"><br>
                Embed <input style=\"margin-right: 13px;\" value='<iframe style=\"border: 0px; overflow: hidden;\" src=\"https://retrotube.ml/player/embed.php?id=" . htmlspecialchars($video_id, ENT_QUOTES, 'UTF-8') . "\" height=\"360\" width=\"480\"></iframe>'>";
                echo "<br>";
                echo "URL to send in discord <input value=\"https://retrotube.ml/videos/" . htmlspecialchars($row["filename"], ENT_QUOTES, 'UTF-8') . "\">";
            }

        ?>  
    </div>
        </div>

</div>

        <?php
        mysqli_query($mysqli, "UPDATE videos SET views = views+1 WHERE id = '" . $videoid . "'");
        $stmt->close();
        echo '<hr style="
    margin-top: 50px;
">';
            $stmt = $mysqli->prepare("SELECT * FROM videos WHERE id = ?");
            $stmt->bind_param("i", $video_id); 
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows === 0) exit('No rows');
            while($row = $result->fetch_assoc()) {
                
                if ($row['featured'] == '1') {
                    echo "
                <div style=\"
                    color: #000;
                    background-color: var(--card-blue-1);
                    border: 1px solid var(--card-blue-2);
                    padding: 7px 15px;
                    font-size: 12px;
                    border-radius: 7px;
                    text-align: center;
                \"><strong>This video is featured on the main page!</strong></div>
                </div>
                ";
                }
            }

        echo '<h3 style="
    margin-top: 32px;
">Comments &amp; Responses</h3>';
?>


<?php

        if(isset($_POST['submit'])) {
            if(!isset($_SESSION['profileuser3'])) {
                die("Please login to comment.");
            }
            else {
              
                $comment = htmlspecialchars($_POST['bio'], ENT_QUOTES, 'UTF-8');
                $stmt = $mysqli->prepare("INSERT INTO comments (tovideoid, author, comment, date) VALUES (?, ?, ?, now())");
                $stmt->bind_param("sss", $video_id, $_SESSION['profileuser3'], $comment);
    
                $stmt->execute();
                $stmt->close();
                
                echo "<h3>comment added xd</h3>";
            }
        }
    ?>
    <form action="" method="post" enctype="multipart/form-data"><br>
        Comment: <br><textarea name="bio" rows="4" cols="40" required="required"></textarea><br><br>
        <input type="submit" value="Upload" name="submit">
    </form>
    <hr>
    <?php
        $stmt = $mysqli->prepare("SELECT * FROM comments WHERE tovideoid = ?");
        $stmt->bind_param("i", $video_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows === 0) echo('No comments.');
        while($row = $result->fetch_assoc()) {
            echo "<div class='commenttitle'>" . htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8') . " (" . htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8') . ")</div>" . nl2br(htmlspecialchars($row['comment'], ENT_QUOTES, 'UTF-8')) . "<br><br>";
        }
        $stmt->close();
    ?>
    <hr>
    <?php include("footer.php") ?>
</div>

</html>
    

<?php $mysqli->close();?>

