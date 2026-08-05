<?php
header('Content-Type: application/json');

$baseDir = __DIR__;
$allowedPhotos = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$allowedVideos = ['mp4', 'webm', 'mov', 'mkv'];

$albums = [];

function scanMediaFolder($folderName, $type, $allowedExtensions, $baseDir, &$albums) {
    $dir = $baseDir . '/' . $folderName;
    if (!is_dir($dir)) return;

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isDir()) continue;
        
        $ext = strtolower($file->getExtension());
        if (in_array($ext, $allowedExtensions)) {
            // Normalize slashes
            $relativePath = str_replace('\\', '/', substr($file->getPathname(), strlen($baseDir) + 1));
            $pathParts = explode('/', $relativePath);

            // Determine album name from subfolder
            $albumName = (count($pathParts) > 2) ? $pathParts[1] : 'General';

            $albums[$albumName][] = [
                'type' => $type,
                'url'  => $relativePath,
                'name' => pathinfo($file->getFilename(), PATHINFO_FILENAME)
            ];
        }
    }
}

scanMediaFolder('photos', 'photo', $allowedPhotos, $baseDir, $albums);
scanMediaFolder('videos', 'video', $allowedVideos, $baseDir, $albums);

echo json_encode($albums);
?>