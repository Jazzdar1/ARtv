<?php
// Define default playlist URL
$default_playlist_url = "https://iptv-org.github.io/iptv/index.m3u";

// Initialize channels array
$channels = [];

function parsePlaylist($content) {
    $channels = [];
    $lines = explode("\n", $content);
    for ($i = 0; $i < count($lines); $i++) {
        $line = trim($lines[$i]);
        if (strpos($line, "#EXTINF:") === 0) {
            $info = explode(",", $line, 2)[1] ?? 'Unknown Channel';
            $logo = '';
            if (preg_match('/tvg-logo="([^"]+)"/', $line, $matches)) {
                $logo = $matches[1];
            }
            $url = trim($lines[$i + 1] ?? '');
            if (!empty($url)) {
                $channels[] = ['name' => $info, 'logo' => $logo, 'url' => $url];
            }
        }
    }
    return $channels;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $playlist_url = $_POST['playlist_url'] ?? '';
    $playlist_file = $_FILES['playlist_file']['tmp_name'] ?? '';

    $playlist_content = '';
    if (!empty($playlist_file)) {
        $playlist_content = file_get_contents($playlist_file);
    } elseif (!empty($playlist_url)) {
        $playlist_content = file_get_contents($playlist_url);
    } else {
        // Load default playlist
        $playlist_content = file_get_contents($default_playlist_url);
    }

    if (!empty($playlist_content)) {
        $channels = parsePlaylist($playlist_content);
    } else {
        echo "<div class='alert alert-danger'>No playlist found.</div>";
    }
}
?>
