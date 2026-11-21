<?php
require __DIR__ . '/config/database.php';

$books = [
    [
        'kode' => 'BK-001',
        'file' => 'cover_bk001.jpg',
        'tag' => 'SOFTWARE CRAFT',
        'title' => "CLEAN\nCODE",
        'sub' => 'A Handbook of Agile Software Craftsmanship',
        'author' => 'ROBERT C. MARTIN',
        'bg' => [18, 14, 20],
        'accent' => [180, 42, 60],
        'icon' => '< / >'
    ],
    [
        'kode' => 'BK-002',
        'file' => 'cover_bk002.jpg',
        'tag' => 'WEB DEVELOPMENT',
        'title' => "PHP 8 &\nMARIADB",
        'sub' => 'Pemrograman Web Dinamis Modern',
        'author' => 'BUDI RAHARJO',
        'bg' => [14, 18, 26],
        'accent' => [195, 75, 90],
        'icon' => 'PHP'
    ],
    [
        'kode' => 'BK-003',
        'file' => 'cover_bk003.jpg',
        'tag' => 'DISTRIBUTED SYSTEMS',
        'title' => "DATA-\nINTENSIVE",
        'sub' => 'Designing Applications for Scale',
        'author' => 'MARTIN KLEPPMANN',
        'bg' => [20, 16, 22],
        'accent' => [218, 150, 60],
        'icon' => 'O RLY'
    ],
    [
        'kode' => 'BK-004',
        'file' => 'cover_bk004.jpg',
        'tag' => 'BUSINESS & IT',
        'title' => "SISTEM\nINFORMASI",
        'sub' => 'Analisis dan Perancangan Terstruktur',
        'author' => 'JOGIYANTO HM',
        'bg' => [18, 20, 24],
        'accent' => [140, 35, 52],
        'icon' => '[ S I ]'
    ],
    [
        'kode' => 'BK-005',
        'file' => 'cover_bk005.jpg',
        'tag' => 'VENTURE & STARTUP',
        'title' => "THE LEAN\nSTARTUP",
        'sub' => 'Build - Measure - Learn Feedback Loop',
        'author' => 'ERIC RIES',
        'bg' => [24, 16, 18],
        'accent' => [200, 50, 65],
        'icon' => 'LOOP'
    ],
    [
        'kode' => 'BK-006',
        'file' => 'cover_bk006.jpg',
        'tag' => 'SASTRA INDONESIA',
        'title' => "LAUT\nBERCERITA",
        'sub' => 'Sebuah Novel Pergulatan & Harapan',
        'author' => 'LEILA S. CHUDORI',
        'bg' => [12, 18, 28],
        'accent' => [165, 45, 60],
        'icon' => '~ ~ ~'
    ],
    [
        'kode' => 'BK-007',
        'file' => 'cover_bk007.jpg',
        'tag' => 'SEJARAH KLASIK',
        'title' => "BUMI\nMANUSIA",
        'sub' => 'Tetralogi Buru Karya Monumental',
        'author' => 'PRAMOEDYA A. TOER',
        'bg' => [26, 18, 14],
        'accent' => [190, 80, 50],
        'icon' => 'BURU'
    ],
    [
        'kode' => 'BK-008',
        'file' => 'cover_bk008.jpg',
        'tag' => 'SELF IMPROVEMENT',
        'title' => "ATOMIC\nHABITS",
        'sub' => 'Tiny Changes, Remarkable Results',
        'author' => 'JAMES CLEAR',
        'bg' => [20, 20, 22],
        'accent' => [225, 175, 65],
        'icon' => '1% +'
    ],
    [
        'kode' => 'BK-009',
        'file' => 'cover_bk009.jpg',
        'tag' => 'PRODUCTIVITY',
        'title' => "DEEP\nWORK",
        'sub' => 'Rules for Focused Success in a Distracted World',
        'author' => 'CAL NEWPORT',
        'bg' => [16, 14, 20],
        'accent' => [175, 40, 55],
        'icon' => 'FOCUS'
    ],
    [
        'kode' => 'BK-010',
        'file' => 'cover_bk010.jpg',
        'tag' => 'COMPUTER SCIENCE',
        'title' => "MATEMATIKA\nDISKRIT",
        'sub' => 'Fondasi Algoritma & Struktur Data Komputer',
        'author' => 'RINALDI MUNIR',
        'bg' => [15, 20, 25],
        'accent' => [185, 48, 65],
        'icon' => 'f(x)=n'
    ],
    [
        'kode' => 'BK-011',
        'file' => 'cover_bk011.jpg',
        'tag' => 'POPULAR SCIENCE',
        'title' => "BRIEF\nHISTORY",
        'sub' => 'Of Time: From the Big Bang to Black Holes',
        'author' => 'STEPHEN HAWKING',
        'bg' => [14, 12, 22],
        'accent' => [135, 30, 48],
        'icon' => 'SPACE'
    ],
    [
        'kode' => 'BK-012',
        'file' => 'cover_bk012.jpg',
        'tag' => 'BIOGRAFI TOKOH',
        'title' => "STEVE\nJOBS",
        'sub' => 'The Exclusive Biography of an Innovator',
        'author' => 'WALTER ISAACSON',
        'bg' => [18, 18, 20],
        'accent' => [210, 50, 65],
        'icon' => 'THINK'
    ]
];

$targetDir = __DIR__ . '/uploads/covers/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$w = 480;
$h = 680;

foreach ($books as $b) {
    $img = imagecreatetruecolor($w, $h);

    // Base colors
    $bgColor = imagecolorallocate($img, $b['bg'][0], $b['bg'][1], $b['bg'][2]);
    $accentColor = imagecolorallocate($img, $b['accent'][0], $b['accent'][1], $b['accent'][2]);
    $accentDim = imagecolorallocatealpha($img, $b['accent'][0], $b['accent'][1], $b['accent'][2], 85);
    $white = imagecolorallocate($img, 242, 239, 234);
    $gold = imagecolorallocate($img, 233, 196, 120);
    $muted = imagecolorallocate($img, 160, 155, 150);
    $border = imagecolorallocate($img, 55, 55, 65);
    $panelBg = imagecolorallocatealpha($img, 255, 255, 255, 120);

    // Fill background
    imagefill($img, 0, 0, $bgColor);

    // Geometric background artwork
    imagefilledellipse($img, $w - 40, 80, 260, 260, $accentDim);
    imagefilledellipse($img, 60, $h - 100, 220, 220, $accentDim);

    // Top Maroon Header Bar
    imagefilledrectangle($img, 0, 0, $w, 8, $accentColor);

    // Inner border line (book frame)
    imagerectangle($img, 24, 24, $w - 24, $h - 24, $border);
    imagerectangle($img, 28, 28, $w - 28, $h - 28, $accentDim);

    // Category Badge
    imagefilledrectangle($img, 45, 50, $w - 45, 78, $panelBg);
    imagerectangle($img, 45, 50, $w - 45, 78, $border);
    $tagStr = '· ' . $b['tag'] . ' ·';
    $tagX = (int) (($w - (strlen($tagStr) * 9)) / 2);
    imagestring($img, 4, $tagX, 56, $tagStr, $gold);

    // Center emblem / icon box
    $boxW = 120;
    $boxH = 60;
    $boxX = (int) (($w - $boxW) / 2);
    $boxY = 125;
    imagefilledrectangle($img, $boxX, $boxY, $boxX + $boxW, $boxY + $boxH, $accentColor);
    imagerectangle($img, $boxX - 4, $boxY - 4, $boxX + $boxW + 4, $boxY + $boxH + 4, $gold);
    $iconStr = $b['icon'];
    $iconX = (int) (($w - (strlen($iconStr) * 9)) / 2);
    imagestring($img, 5, $iconX, $boxY + 22, $iconStr, $white);

    // Book Title (multi-line large text emulation)
    $titleLines = explode("\n", $b['title']);
    $startY = 240;
    foreach ($titleLines as $line) {
        $charW = 9;
        $lineX = (int) (($w - (strlen($line) * $charW * 2)) / 2);
        // Shadow
        imagestring($img, 5, $lineX + 2, $startY + 2, $line, $border);
        // Main title text
        imagestring($img, 5, $lineX, $startY, $line, $white);
        $startY += 32;
    }

    // Divider line
    imageline($img, 70, $startY + 20, $w - 70, $startY + 20, $gold);
    imagefilledellipse($img, (int)($w / 2), $startY + 20, 8, 8, $accentColor);

    // Subtitle
    $sub = $b['sub'];
    if (strlen($sub) > 42) {
        $sub1 = substr($sub, 0, 40) . '...';
    } else {
        $sub1 = $sub;
    }
    $subX = (int) (($w - (strlen($sub1) * 7.5)) / 2);
    imagestring($img, 3, max(40, $subX), $startY + 42, $sub1, $muted);

    // Bottom Footer (Author & Library Badge)
    imagefilledrectangle($img, 45, $h - 110, $w - 45, $h - 52, $panelBg);
    imagerectangle($img, 45, $h - 110, $w - 45, $h - 52, $border);

    $authLabel = 'PENULIS';
    $authLabelX = (int) (($w - (strlen($authLabel) * 6.5)) / 2);
    imagestring($img, 2, $authLabelX, $h - 102, $authLabel, $gold);

    $authorName = $b['author'];
    $authorX = (int) (($w - (strlen($authorName) * 8)) / 2);
    imagestring($img, 4, max(46, $authorX), $h - 84, $authorName, $white);

    // Bottom Brand Signature
    $brandStr = 'PERPUSTAKAANN FARREL · KOLEKSI DIGITAL';
    $brandX = (int) (($w - (strlen($brandStr) * 6)) / 2);
    imagestring($img, 1, $brandX, $h - 40, $brandStr, $muted);

    // Save image
    $destPath = $targetDir . $b['file'];
    imagejpeg($img, $destPath, 92);
    imagedestroy($img);

    // Update Database
    $stmt = mysqli_prepare($koneksi, "UPDATE buku SET cover = ? WHERE kode_buku = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $b['file'], $b['kode']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo "Generated & Linked: " . $b['file'] . " -> " . $b['kode'] . "\n";
}

echo "Semua 12 cover buku berhasil dibuat dan diupdate di database perpustakaan_3569!\n";
