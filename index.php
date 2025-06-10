<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генератор QR-кода</title>

    <!-- Primary Meta Tags -->
    <meta name="title" content="Генератор QR-кода">
    <meta name="description" content="Бесплатный генератор QR-кодов для создания QR-кодов из URL-адресов и сохранения их в формате PDF с пользовательскими заголовками.">
    <meta name="keywords" content="QR код, генератор QR кода, PDF, URL в QR код, создать QR код, бесплатный QR код">
    <meta name="author" content="QR Code Generator">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Russian">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://qr.xservice.uz/">
    <meta property="og:title" content="Генератор QR-кода">
    <meta property="og:description" content="Бесплатный генератор QR-кодов для создания QR-кодов из URL-адресов и сохранения их в формате PDF с пользовательскими заголовками.">
    <meta property="og:image" content="assets/img/qr-generator-preview.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://qr.xservice.uz/">
    <meta property="twitter:title" content="Генератор QR-кода">
    <meta property="twitter:description" content="Бесплатный генератор QR-кодов для создания QR-кодов из URL-адресов и сохранения их в формате PDF с пользовательскими заголовками.">
    <meta property="twitter:image" content="assets/img/qr-generator-preview.png">

    <!-- Canonical URL -->
    <meta rel="canonical" href="https://qr.xservice.uz/">

    <link rel="stylesheet" href="assets/css/style.css">
<!--    <link rel="icon" href="assets/img/favicon.ico"/>-->
    <link rel="apple-touch-icon" sizes="57x57" href="assets/img/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="assets/img/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/img/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/img/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="assets/img/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="assets/img/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="assets/img/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="assets/img/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/img/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon-16x16.png">
    <link rel="manifest" href="assets/js/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/img/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>
<body>
    <div class="container">
        <h1>Генератор QR-кода</h1>
        <div id="uploadSuccess" class="alert-success" style="display: none; padding: 15px; margin-bottom: 20px; border: 1px solid #d6e9c6; border-radius: 4px; color: #3c763d; background-color: #dff0d8;">
            Файл успешно загружен.
            <br>
            Ссылка: <a id="uploadedFileLink" href="#" target="_blank"></a>
        </div>
        <form id="qrForm" action="generate.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Название QR-кода:</label>
                <input type="text" id="title" name="title" required>
            </div>

            <!-- Tab navigation -->
            <div class="tab-container">
                <div class="tab-nav">
                    <button type="button" class="tab-btn active" data-tab="link-tab">Ссылка</button>
                    <button type="button" class="tab-btn" data-tab="file-tab">Файл</button>
                </div>

                <!-- Tab content -->
                <div class="tab-content">
                    <div id="link-tab" class="tab-pane active">
                        <div class="form-group">
                            <label for="link">Ссылка на QR-код:</label>
                            <input type="url" id="link" name="link" required>
                        </div>
                    </div>

                    <div id="file-tab" class="tab-pane">
                        <div class="form-group">
                            <label for="file">Загрузить файл:</label>
                            <input type="file" id="file" name="file" accept=".png,.jpg,.jpeg,.pdf,.ppt,.pptx,.doc,.docx">
                            <small>Допустимые форматы: PNG, JPG, JPEG, PDF, PPT, PPTX, DOC, DOCX</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit">Сгенерировать QR-код</button>
            </div>
        </form>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>
