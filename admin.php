<?php
session_start();

$admin_password = "password"; // change this to whatever you think is secure

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] == true) {

} else {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['password']) && $_POST['password'] == $admin_password) {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $error_message = "incorrect password";
        }
    }
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] == true) {

    
    include("global.php");


    if (isset($_GET['delete_video'])) {
        $video_id = $_GET['delete_video'];
        $stmt = $mysqli->prepare("DELETE FROM videos WHERE id = ?");
        $stmt->bind_param("i", $video_id);
        $stmt->execute();
        $stmt->close();
    }


    if (isset($_GET['ban_user'])) {
        $user_id = $_GET['ban_user'];
        $ban_date = date('Y-m-d H:i:s');
        $stmt = $mysqli->prepare("UPDATE users SET banned = 1, ban_date = ? WHERE id = ?");
        $stmt->bind_param("si", $ban_date, $user_id);
        $stmt->execute();
        $stmt->close();
    }

   
    if (isset($_GET['unban_user'])) {
        $user_id = $_GET['unban_user'];
        $stmt = $mysqli->prepare("UPDATE users SET banned = 0, ban_date = NULL WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }

    
        if (isset($_GET['ban_ip'])) {
        $ip = $_GET['ban_ip'];
        $ban_date = date('Y-m-d H:i:s');
        $stmt = $mysqli->prepare("INSERT INTO banned_ips (ip, ban_date) VALUES (?, ?)");
        $stmt->bind_param("ss", $ip, $ban_date);
        $stmt->execute();
        $stmt->close();
    }

       if (isset($_GET['unban_ip'])) {
        $ip = $_GET['unban_ip'];
        $stmt = $mysqli->prepare("DELETE FROM banned_ips WHERE ip = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $stmt->close();
    }
  
    $stmt = $mysqli->prepare("SELECT videos.id, videos.videotitle, users.username FROM videos JOIN users ON videos.author = users.username ORDER BY videos.id DESC LIMIT ? OFFSET ?");
    $limit = 10; // Number of videos per page
    $offset = isset($_GET['page']) ? ($_GET['page'] - 1) * $limit : 0;
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $ip_stmt = $mysqli->prepare("SELECT * FROM banned_ips");
    $ip_stmt->execute();
    $ip_result = $ip_stmt->get_result();

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>admin panel</title>
        <link rel="stylesheet" href="assets/styles.css">
    </head>
    <body>

    <h1>admin panel</h1>

    <h2>videos</h2>
    <table>
        <thead>
            <tr>
                <th>video id</th>
                <th>title</th>
                <th>author</th>
                <th>actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['videotitle']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td>
                    <a href="admin.php?delete_video=<?php echo $row['id']; ?>">delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>banned ip adresses</h2>
    <p>here's the ip adresses and the usernames in a <A href='logins.txt'>txt file</A>.</p>
        <table>
        <thead>
            <tr>
                <th>ip</th>
                <th>banned</th>
                <th>actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $ip_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['ip']; ?></td>
                <td><?php echo $row['ban_date']; ?></td>
                <td>
                    <a href="admin.php?unban_ip=<?php echo $row['ip']; ?>">Unban</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>ban user</h2>
    <form action="admin.php" method="get">
        <label for="user_id">user id:</label>
        <input type="text" id="user_id" name="ban_user" required>
        <button type="submit">ban user</button>
    </form>

    <h2>unban user</h2>
    <form action="admin.php" method="get">
        <label for="unban_user_id">user id:</label>
        <input type="text" id="unban_user_id" name="unban_user" required>
        <button type="submit">unban user</button>
    </form>

    <h2>ban ip</h2>
    <form action="admin.php" method="get">
        <label for="ip">ip address:</label>
        <input type="text" id="ip" name="ban_ip" required>
        <button type="submit">ban ip</button>
    </form>

    <h2>unban ip</h2>
    <form action="admin.php" method="get">
        <label for="unban_ip">ip address:</label>
        <input type="text" id="unban_ip" name="unban_ip" required>
        <button type="submit">unban ip</button>
    </form>

    <h2>logs</h2>
    <a href="logs.csv" download="admin_logs.csv">download csv log</a>

    </body>
    </html>

    <?php
    $stmt->close();
    $ip_stmt->close();
} else {
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>

    <h2>admin login</h2>
    <form action="admin.php" method="POST">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>

    <?php
}
?>
<br>
<a href='/'>home</a>
