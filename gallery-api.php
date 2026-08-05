<?php
header('Content-Type: application/json');

function getMediaFiles($dir, $allowedExtensions) {
    $media = [];
    if (!is_dir($dir)) return $media;

    // Read items in the root folder (e.g., Photos/ or Videos/)
    $items = array_diff(scandir($dir), array('.', '..'));

    foreach ($items as $item) {
        $path = $dir . '/' . $item;

        if (is_dir($path)) {
            // It's an album subfolder! Scan inside it.
            $albumName = $item;
            $files = array_diff(scandir($path), array('.', '..'));
            
            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, $allowedExtensions)) {
                    $media[] = [
                        'src' => $path . '/' . $file,
                        'album' => $albumName,
                        'name' => pathinfo($file, PATHINFO_FILENAME)
                    ];
                }
            }
        } else {
            // It's a direct file (Uncategorized / General)
            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            if (in_array($ext, $allowedExtensions)) {
                $media[] = [
                    'src' => $path,
                    'album' => 'General',
                    'name' => pathinfo($item, PATHINFO_FILENAME)
                ];
            }
        }
    }
    return $media;
}

$imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
$videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'mkv'];

$response = [
    'photos' => getMediaFiles('Photos', $imageExtensions),
    'videos' => getMediaFiles('Videos', $videoExtensions)
];

echo json_encode($response);
?>
