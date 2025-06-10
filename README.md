# QR Code Generator

A simple web application that generates QR codes from URLs and outputs them as PDF files in A4 format. The application features both client-side and server-side validation, ensuring that only valid URLs are processed.

## Project Overview

This project is a PHP-based QR code generator that allows users to create QR codes from URLs and download them as PDF files. The application uses the Endroid QR Code library for generating QR codes and TCPDF for creating PDF documents with embedded QR codes and custom fonts.

## Features

- Generate QR codes from URLs
- Upload files (PNG, JPG, JPEG, PDF, PPT, PPTX, DOC, DOCX) and get a shareable link
- Add a title to your QR code
- Download the result as a PDF in A4 format
- Client-side and server-side validation
- Responsive design
- Custom Times New Roman font embedding in PDFs

## Requirements

- PHP 8.3 or higher
- PHP mbstring extension
- Composer
- Web server (Apache, Nginx, etc.)
- Write permissions for the `qrcodes` and `uploads` directories

## Installation

1. Clone this repository to your web server directory:
   ```
   git clone https://github.com/yourusername/qr-generator.git
   ```

2. Navigate to the project directory:
   ```
   cd qr-generator
   ```

3. Install dependencies using Composer (this step is required):
   ```
   composer install
   ```

   **Important**: The application will not work without installing dependencies. If you see an error about missing vendor/autoload.php, it means you need to run this command.

4. Make sure the `qrcodes` and `uploads` directories are writable by the web server:
   ```
   sudo mkdir qrcodes uploads
   sudo chown www-data:www-data qrcodes uploads
   sudo chmod 755 qrcodes uploads
   ```

5. Make sure the `fonts` directory exists and contains the required font files:
   ```
   # Check if fonts directory exists
   ls -la fonts

   # If not, create it
   sudo mkdir -p fonts
   ```

6. Configure your web server to serve the application. A sample Nginx configuration file (`nginx.conf`) is included in the repository. You can use it as a reference:
   ```
   # For Nginx
   sudo cp nginx.conf /etc/nginx/sites-available/qr-generator.conf
   sudo ln -s /etc/nginx/sites-available/qr-generator.conf /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo nginx -s reload
   ```

## Usage

### For QR Code Generation:

1. Open the application in your web browser.
2. Enter a title for your QR code in the "Название QR-кода" field.
3. Select the "Ссылка" (Link) tab.
4. Enter the URL you want to encode in the QR code in the "Ссылка на QR-код" field.
5. Click "Сгенерировать QR-код" to create and download the PDF.
6. The PDF will be automatically downloaded with the QR code centered on an A4 page.

### For File Upload:

1. Open the application in your web browser.
2. Enter a title for your file in the "Название QR-кода" field.
3. Select the "Файл" (File) tab.
4. Click on the file upload field and select a file (supported formats: PNG, JPG, JPEG, PDF, PPT, PPTX, DOC, DOCX).
5. Click "Сгенерировать QR-код" to upload the file.
6. After successful upload, you'll receive a shareable link to the file.

## Testing

The project includes a test script (`test.php`) that verifies your environment is properly set up:

```
php test.php
```

This script checks:
- PHP version compatibility
- Required directories existence
- Composer dependencies installation
- QR code generation functionality

Run this script after installation to ensure everything is working correctly.

## How It Works

### QR Code Generation:

1. The user enters a title and URL in the form.
2. Client-side JavaScript validates the input in real-time, ensuring a valid URL format.
3. When the form is submitted, JavaScript prevents the default form submission and handles it via a hidden iframe for better download handling.
4. The server validates the input again, checking for required fields and valid URL format.
5. A QR code is generated using the endroid/qr-code library and saved as a temporary PNG file.
6. The QR code is embedded in a PDF document using TCPDF, along with the title in Times New Roman font.
7. The PDF is sent to the user for download with a unique filename based on the timestamp.
8. Temporary QR code image files are cleaned up after the PDF is generated.

### File Upload:

1. The user enters a title and selects a file to upload.
2. When the form is submitted, the file is sent to the server.
3. The server validates the file type, ensuring it's one of the supported formats (PNG, JPG, JPEG, PDF, PPT, PPTX, DOC, DOCX).
4. The file is saved to a directory structure based on the current date (year/month/day).
5. A unique filename is generated based on the title, timestamp, and original filename.
6. The server returns a shareable link to the uploaded file.
7. The link is displayed to the user for copying and sharing.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- [TCPDF](https://github.com/tecnickcom/TCPDF) for PDF generation with font embedding
- [endroid/qr-code](https://github.com/endroid/qr-code) for QR code generation
