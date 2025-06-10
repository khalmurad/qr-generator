# QR Code Generator

A simple web application that generates QR codes and outputs them as PDF files in A4 format.

## Features

- Generate QR codes from URLs
- Add a title to your QR code
- Download the result as a PDF in A4 format
- Client-side and server-side validation
- Responsive design
- Automatic cleanup of temporary files

## Requirements

- PHP 8.3 or higher
- mbstring PHP extension
- Composer
- Web server (Apache, Nginx, etc.)

## Dependencies

- [endroid/qr-code](https://github.com/endroid/qr-code) (v4.6+) - For QR code generation
- [tecnickcom/tcpdf](https://github.com/tecnickcom/TCPDF) (v6.6+) - For PDF generation
- [setasign/fpdf](https://github.com/setasign/fpdf) (v1.8+) - PDF library dependency

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

4. The `qrcodes` directory will be created automatically when needed, but you can create it manually and set permissions if desired:
   ```
   mkdir qrcodes
   chmod 755 qrcodes
   ```

5. Configure your web server to serve the application. A sample Nginx configuration is provided in `nginx.conf`.

## Testing

You can verify that your installation is working correctly by running the test script:

```
php test.php
```

This script will:
- Check your PHP version
- Verify required directories exist (and create them if needed)
- Check that Composer dependencies are installed
- Test QR code generation functionality

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
6. The QR code is embedded in a PDF document using TCPDF along with the title.
7. The PDF is sent to the user for download.
8. Temporary files are cleaned up automatically.

## Project Structure

- `index.php` - The main entry point and user interface
- `generate.php` - Handles QR code and PDF generation
- `style.css` - CSS styles for the application
- `script.js` - Client-side JavaScript for validation and form handling
- `fonts/` - Contains Times New Roman fonts used for PDF generation
- `qrcodes/` - Directory for temporary QR code storage (created automatically)
- `nginx.conf` - Sample Nginx configuration

## Security Features

- Input validation on both client and server side
- URL validation using filter_var
- Automatic cleanup of temporary files
- Nginx configuration with security headers and access restrictions

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- [TCPDF](https://github.com/tecnickcom/TCPDF) for PDF generation
- [endroid/qr-code](https://github.com/endroid/qr-code) for QR code generation
