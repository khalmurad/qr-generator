<?php
declare(strict_types=1);
ob_start();
ini_set('display_errors', '0');

if (PHP_VERSION_ID < 80300) { die('Requires PHP 8.3+'); }
if (!extension_loaded('mbstring')) { die('mbstring required'); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php'); exit;
}

// Required for both file upload and QR code generation
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('Run composer install.');
}
require_once __DIR__ . '/vendor/autoload.php';

// Import required classes
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$title = $_POST['title'] ?? '';

// Check if we're handling a file upload or a link
if (!empty($_FILES['file']['name'])) {
    // File upload handling
    $file = $_FILES['file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die('File upload error: ' . $file['error']);
    }

    if (!$title) {
        die('Title is required.');
    }

    // Validate file type
    $allowedTypes = ['image/png', 'image/jpeg', 'application/pdf', 'application/vnd.ms-powerpoint',
                     'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                     'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $fileType = $file['type'];
    $fileName = $file['name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $validExtension = in_array($fileExt, ['png', 'jpg', 'jpeg', 'pdf', 'ppt', 'pptx', 'doc', 'docx']);

    if (!$validExtension) {
        die('Invalid file type. Allowed types: PNG, JPG, JPEG, PDF, PPT, PPTX, DOC, DOCX');
    }

    // Create directory structure based on current date
    $year = date('Y');
    $month = date('m');
    $day = date('d');

    $uploadDir = __DIR__ . "/uploads/$year/$month/$day";

    // Create directories if they don't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $uniqueFileName = md5($title . time() . $fileName) . '.' . $fileExt;
    $uploadPath = "$uploadDir/$uniqueFileName";

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        die('Failed to move uploaded file.');
    }

    // Generate file URL
    $fileUrl = "uploads/$year/$month/$day/$uniqueFileName";
    $absoluteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
                  "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['REQUEST_URI']) . "/$fileUrl";

    // Redirect back to form with success message
    header("Location: index.php?file_url=" . urlencode($absoluteUrl));
    exit;
} else {
    // Original link handling
    $link = $_POST['link'] ?? '';
    if (!$title || !$link || !filter_var($link, FILTER_VALIDATE_URL)) {
        die('Title and valid URL required.');
    }

    $qrDir  = __DIR__ . '/qrcodes';
    $qrFile = $qrDir . '/' . md5($title . $link . time()) . '.png';
    if (!is_dir($qrDir)) { mkdir($qrDir, 0755, true); }

    // Generate QR PNG
    $qr = QrCode::create($link)->setSize(400)->setMargin(10);
    file_put_contents($qrFile, (new PngWriter())->write($qr)->getString());

    // Build PDF
    try {
        // 1) Instantiate PDF
        $pdf = new \TCPDF('P','mm','A4',true,'UTF-8',false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15,27,15);
        $pdf->SetAutoPageBreak(true,10);

        // 2) Convert & register TTF (once)
        $regularFont = \TCPDF_FONTS::addTTFfont(
            __DIR__ . '/assets/fonts/Times-New-Roman.ttf',
            'TrueTypeUnicode','',96
        );
        $boldFont = \TCPDF_FONTS::addTTFfont(
            __DIR__ . '/assets/fonts/Times-New-Roman-Bold.ttf',
            'TrueTypeUnicode','',96
        );

        // 3) Draw page
        $pdf->AddPage();

        // 4) Title in TNR Bold
        $pdf->SetFont($boldFont,'B',16);
        $pdf->MultiCell(0, 8, $title, 0, 'C', 0, 1, '', '', true);
        $pdf->Ln(20);

        // 5) QR code enlarged
        $imgW  = 150;
        $xPos  = ($pdf->GetPageWidth() - $imgW) / 2;
        $yPos  = $pdf->GetY();
        $pdf->Image($qrFile, $xPos, $yPos, $imgW, $imgW, 'PNG');

        // 6) Output
        ob_end_clean();
        $pdf->Output('QR_Code_' . time() . '.pdf', 'D');
        unlink($qrFile);

    } catch (Exception $e) {
        die('PDF error: ' . $e->getMessage());
    }
}
