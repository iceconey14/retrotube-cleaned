<?php
include("../global.php");
?>
<head>
    <link rel="stylesheet" href="./assets/player.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<style>
		.buffer {
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			background: url(./assets/buffer.png);
			background-size: auto;
			background-size: 1px 8px;
			width: 0%;
		}
	</style>
</head>
<body style="color:white; background-color:black;">
    <div class="player" id="07player">
    <div class="video-stream" style="
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0px;
        left: 0px;
    ">
    <?php
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $video_id = (int) $_GET['id']; 

        $stmt = $mysqli->prepare("SELECT filename FROM videos WHERE id = ?");
        $stmt->bind_param("i", $video_id);  
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            exit('No rows');
        }
        $row = $result->fetch_assoc();
        $filename = htmlspecialchars($row['filename']);  
    } else {
        exit('Invalid video ID');
    }
    ?>
    <video id="video-stream" src="../videos/<?php echo $filename; ?>" onclick="playVid()" style="width:100%; height:100%;">
    </video>
    <img src="./assets/playbutton.png" id="playbut" style="position: absolute;left: 43%;top: 38%;width: 83px;opacity: 0.9;" onclick="playVid();">
    <img src="./assets/retrologo.png" id="playbut" style="position: absolute;left: 66%;top: 76%;width: 160px;opacity: 0.9;" onclick="redirectmain();">
    <img src="./fulp_spinner.webp" id="buffic" style="display: none;position: absolute;left: 47%;top: 42%;width: 36px;opacity: 0.9;">
    <div class="controls" style="background-color:white;">
    <div style="float: left; height: 100%; text-align: left;">
    <div class="playButton" id="playpause" onclick="playVid();"></div>
    </div>
    <div style="float: right;">
    <div class="timer">
    <span class="cur" id="cur">00:00</span>
    / 
    <span class="dur" id="dur">00:00</span>
    </div>
    <div class="separator"></div>
    <div class="volbar" id="volbar">
    <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" id="slider" style="left: 0%;"></span>
    </div>
    <div class="volvisual">
    </div>
    <div class="separator"></div>
    <div class="fullscreenButton" id="fullscreenButton" onclick="openFullscreen();"></div>
    </div>
    <div style="overflow: hidden; height: 100%; text-align: left;">
    <div class="progress" id="progress">
    <div style="z-index: 1;" class="position" id="position">
    </div>
    <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" id="scrubber" style="z-index: 1;left: 0%;"></span>
    <div class="buffer" id="buffer">
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
</body>
<script>
</script>
</body>
