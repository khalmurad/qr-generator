<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Генератор QR-кода</h1>
        <form id="qrForm" action="generate.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Название QR-кода:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="link">Ссылка на QR-код:</label>
                <input type="url" id="link" name="link" required>
            </div>
            <div class="form-group">
                <button type="submit">Сгенерировать QR-код</button>
            </div>
        </form>
    </div>
    <script src="script.js"></script>
</body>
</html>
