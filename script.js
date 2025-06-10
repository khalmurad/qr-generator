document.addEventListener('DOMContentLoaded', function() {
    // Get the form element
    const form = document.getElementById('qrForm');

    // Add submit event listener
    if (form) {
        form.addEventListener('submit', function(event) {
            // Get form inputs
            const title = document.getElementById('title').value.trim();
            const link = document.getElementById('link').value.trim();
            const pdfFile = document.getElementById('pdfFile').files[0];

            // Validate title
            if (title === '') {
                event.preventDefault();
                alert('Please enter a title for your QR code.');
                return;
            }

            // Validate link
            if (link === '') {
                event.preventDefault();
                alert('Please enter a link for your QR code.');
                return;
            }

            // Validate URL format
            try {
                new URL(link);
            } catch (e) {
                event.preventDefault();
                alert('Please enter a valid URL (e.g., https://example.com)');
                return;
            }

            // Validate PDF file if one was selected
            if (pdfFile) {
                // Check file type
                if (pdfFile.type !== 'application/pdf') {
                    event.preventDefault();
                    alert('The selected file is not a PDF. Please select a valid PDF file.');
                    return;
                }

                // Check file size (max 10MB)
                const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                if (pdfFile.size > maxSize) {
                    event.preventDefault();
                    alert('The selected PDF file is too large. Please select a file smaller than 10MB.');
                    return;
                }
            }

            // If we get here, form is valid and will submit
            // Show loading indicator with spinner
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.textContent = 'Generating...';
                button.disabled = true;
                button.classList.add('spinner');
            }

            // Set up page refresh after download
            // Create a hidden iframe to handle the form submission
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.name = 'download_frame';
            document.body.appendChild(iframe);

            // Set the form's target to the iframe
            form.target = 'download_frame';

            // Set up a timer to check if the form submission is complete
            // This is more reliable than listening for the iframe's load event
            setTimeout(function() {
                // Refresh the page after a delay to ensure the download has started
                window.location.reload();
            }, 3000); // 3 seconds should be enough for most PDF generations
        });
    }

    // Add input event listeners for real-time validation
    const titleInput = document.getElementById('title');
    const linkInput = document.getElementById('link');
    const pdfFileInput = document.getElementById('pdfFile');

    if (titleInput) {
        titleInput.addEventListener('input', function() {
            this.setCustomValidity('');
        });
    }

    if (linkInput) {
        linkInput.addEventListener('input', function() {
            this.setCustomValidity('');
            try {
                if (this.value.trim() !== '') {
                    new URL(this.value);
                }
            } catch (e) {
                this.setCustomValidity('Please enter a valid URL');
            }
        });
    }

    if (pdfFileInput) {
        pdfFileInput.addEventListener('change', function() {
            this.setCustomValidity('');

            if (this.files.length > 0) {
                const file = this.files[0];

                // Check file type
                if (file.type !== 'application/pdf') {
                    this.setCustomValidity('The selected file is not a PDF. Please select a valid PDF file.');
                    return;
                }

                // Check file size (max 10MB)
                const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                if (file.size > maxSize) {
                    this.setCustomValidity('The selected PDF file is too large. Please select a file smaller than 10MB.');
                    return;
                }
            }
        });
    }
});
