# QR Code Generator

A simple web application that generates QR codes and outputs them as PDF files in A4 format.

## Features

- Generate QR codes from URLs
- Add a title to your QR code
- Download the result as a PDF in A4 format
- Client-side and server-side validation
- Responsive design

## Requirements

- PHP 8.3 or higher
- Composer
- Web server (Apache, Nginx, etc.)

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
   mkdir qrcodes
   chmod 755 qrcodes
   ```

5. Configure your web server to serve the application.

## Usage

1. Open the application in your web browser.
2. Enter a title for your QR code.
3. Enter the URL you want to encode in the QR code.
4. Click "Generate QR Code PDF" to create and download the PDF.

## How It Works

1. The user enters a title and URL in the form.
2. Client-side JavaScript validates the input.
3. The form is submitted to the server.
4. The server validates the input again.
5. A QR code is generated using the endroid/qr-code library.
6. The QR code is embedded in a PDF document along with the title and URL.
7. The PDF is sent to the user for download.
8. Temporary files are cleaned up.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- [FPDF](http://www.fpdf.org/) for PDF generation
- [endroid/qr-code](https://github.com/endroid/qr-code) for QR code generation
