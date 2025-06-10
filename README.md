# QR Code Generator

A simple web application that generates QR codes from URLs and outputs them as PDF files in A4 format. The application features both client-side and server-side validation, ensuring that only valid URLs are processed.

## Project Overview

This project is a PHP-based QR code generator that allows users to create QR codes from URLs and download them as PDF files. The application uses the Endroid QR Code library for generating QR codes and TCPDF for creating PDF documents with embedded QR codes and custom fonts.

## Features

- Generate QR codes from URLs
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
- Write permissions for the `qrcodes` directory

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

4. Make sure the `qrcodes` directory is writable by the web server:
   ```
   sudo mkdir qrcodes
   sudo chown www-data:www-data /var/www/qr-generator/
   sudo chmod 755 qrcodes
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

1. Open the application in your web browser.
2. Enter a title for your QR code in the "Название QR-кода" field.
3. Enter the URL you want to encode in the QR code in the "Ссылка на QR-код" field.
4. Click "Сгенерировать QR-код" to create and download the PDF.
5. The PDF will be automatically downloaded with the QR code centered on an A4 page.

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

1. The user enters a title and URL in the form.
2. Client-side JavaScript validates the input in real-time, ensuring a valid URL format.
3. When the form is submitted, JavaScript prevents the default form submission and handles it via a hidden iframe for better download handling.
4. The server validates the input again, checking for required fields and valid URL format.
5. A QR code is generated using the endroid/qr-code library and saved as a temporary PNG file.
6. The QR code is embedded in a PDF document using TCPDF, along with the title in Times New Roman font.
7. The PDF is sent to the user for download with a unique filename based on the timestamp.
8. Temporary QR code image files are cleaned up after the PDF is generated.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- [TCPDF](https://github.com/tecnickcom/TCPDF) for PDF generation with font embedding
- [endroid/qr-code](https://github.com/endroid/qr-code) for QR code generation
