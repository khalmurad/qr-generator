document.addEventListener('DOMContentLoaded', function() {
    // Get the form element
    const form = document.getElementById('qrForm');

    // Add submit event listener
    if (form) {
        form.addEventListener('submit', function(event) {
            // Prevent default form submission (no page refresh)
            event.preventDefault();

            // Get form inputs
            const title = document.getElementById('title').value.trim();
            const link = document.getElementById('link').value.trim();

            // Validate title
            if (title === '') {
                alert('Please enter a title for your QR code.');
                return;
            }

            // Validate link
            if (link === '') {
                alert('Please enter a link for your QR code.');
                return;
            }

            // Validate URL format
            try {
                new URL(link);
            } catch (e) {
                alert('Please enter a valid URL (e.g., https://example.com)');
                return;
            }

            // If we get here, form is valid
            // Show loading indicator with spinner
            const button = form.querySelector('button[type="submit"]');
            const originalButtonText = button.textContent;
            if (button) {
                button.textContent = 'В процессе...';
                button.disabled = true;
                button.classList.add('spinner');
            }

            // Create a hidden iframe to handle the file download
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.name = 'download_frame';
            document.body.appendChild(iframe);

            // Set the form's target to the iframe
            form.target = 'download_frame';

            // We'll use the iframe approach for handling file downloads
            // Set up a timeout to handle cases where the iframe might not load properly
            // const downloadTimeout = setTimeout(function() {
            //     if (button) {
            //         button.textContent = originalButtonText;
            //         button.disabled = false;
            //         button.classList.remove('spinner');
            //     }
            //     alert('The download may have failed. Please try again.');
            // }, 30000); // 30 seconds timeout

            // Set up a shorter timeout to reset the button state after a reasonable time
            // This assumes the download has started successfully
            const successTimeout = setTimeout(function() {
                // clearTimeout(downloadTimeout); // Clear the error timeout
                if (button) {
                    button.textContent = originalButtonText;
                    button.disabled = false;
                    button.classList.remove('spinner');
                }
            }, 5000); // 5 seconds should be enough for most downloads to start

            // Submit the form normally to trigger the file download
            form.submit();

            // Set up a listener to detect when the download is complete
            // Note: This may not always fire reliably for downloads
            iframe.onload = function() {
                // clearTimeout(downloadTimeout);
                clearTimeout(successTimeout);
                if (button) {
                    button.textContent = originalButtonText;
                    button.disabled = false;
                    button.classList.remove('spinner');
                }
            };
        });
    }

    // Add input event listeners for real-time validation
    const titleInput = document.getElementById('title');
    const linkInput = document.getElementById('link');

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
});
