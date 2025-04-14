var vid = document.getElementById("video-stream");
var playpause = document.getElementById("playpause");
var playbut = document.getElementById("playbut");
var buffic = document.getElementById("buffic");
var fullscreen = document.getElementById("fullscreenButton");
var scrubber = document.getElementById("scrubber");
var progress = document.getElementById("position");
var buffer = document.getElementById("buffer");

var selected = 0;
var lastPlayPos = 0;
var currentPlayPos = 0;
var bufferingDetected = false;

function gTimeFormat(seconds) {
    var m = Math.floor(seconds / 60) < 10 ? "0" + Math.floor(seconds / 60) : Math.floor(seconds / 60);
    var s = Math.floor(seconds - (m * 60)) < 10 ? "0" + Math.floor(seconds - (m * 60)) : Math.floor(seconds - (m * 60));
    return m + ":" + s;
}

function playVid() {
    if (playpause.className == "playButton") {
        vid.play();
        playpause.className = "pauseButton";
        $("#playbut").fadeOut(70);
    } else {
        vid.pause();
        playpause.className = "playButton";
        $("#playbut").fadeIn(70);
    }
}

function openFullscreen() {
    var docElm = document.getElementById("07player");
    if (!document.fullscreenElement) {
        if (docElm.requestFullscreen) {
            docElm.requestFullscreen();
            fullscreen.style.backgroundImage = "url(./assets/close.png)";
        } else if (docElm.mozRequestFullScreen) {
            docElm.mozRequestFullScreen();
            fullscreen.style.backgroundImage = "url(./assets/close.png)";
        } else if (docElm.webkitRequestFullScreen) {
            docElm.webkitRequestFullScreen();
            fullscreen.style.backgroundImage = "url(./assets/close.png)";
        } else if (docElm.msRequestFullscreen) {
            docElm.msRequestFullscreen();
            fullscreen.style.backgroundImage = "url(./assets/close.png)";
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
            fullscreen.style.backgroundImage = "url(./assets/fullscreen.png)";
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
            fullscreen.style.backgroundImage = "url(./assets/fullscreen.png)";
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
            fullscreen.style.backgroundImage = "url(./assets/fullscreen.png)";
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
            fullscreen.style.backgroundImage = "url(./assets/fullscreen.png)";
        }
    }
}

function timeUpdate() {
    document.getElementById("cur").innerHTML = gTimeFormat(vid.currentTime);
    document.getElementById("dur").innerHTML = gTimeFormat(vid.duration);
    $('#progress').slider("option", "max", vid.duration);
    scrubber.style.left = (vid.currentTime / vid.duration) * 100 + "%";
    progress.style.width = (vid.currentTime / vid.duration) * 100 + "%";

    volumeval = $('#volbar').slider("option", "value");
    vid.volume = volumeval / 100;

    if (vid && vid.buffered && vid.buffered.length > 0 && vid.buffered.end && vid.duration) {
        var buffered = vid.buffered.end(0);
        var buffered_percentage = (buffered / vid.duration) * 100;
        buffer.style.width = buffered_percentage + "%";
    }
}

setInterval(function () {
    currentPlayPos = vid.currentTime;

    if (!bufferingDetected && currentPlayPos < (lastPlayPos + 0.05) && !vid.paused) {
        buffic.style.display = "block";
        bufferingDetected = true;
    }

    if (bufferingDetected && currentPlayPos > (lastPlayPos + 0.05) && !vid.paused) {
        buffic.style.display = "none";
        bufferingDetected = false;
    }

    lastPlayPos = currentPlayPos;
}, 50);

vid.ontimeupdate = timeUpdate;

$("#progress").on("mousedown", function () {
    selected = 1;
});

$("#progress").on("mouseup", function () {
    selected = 0;
    vid.currentTime = $('#progress').slider("option", "value");
});

window.onkeydown = function (e) {
    if (e.code === 'ArrowLeft') {
        vid.currentTime -= 5;
    } else if (e.code === 'ArrowRight') {
        vid.currentTime += 5;
    } else if (e.code === 'Space') {
        playVid();
    }
};

vid.addEventListener('contextmenu', function (e) {
    e.preventDefault();
});
