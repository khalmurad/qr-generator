document.addEventListener('DOMContentLoaded', function() {
    // Get the form element
    const form = document.getElementById('qrForm');

    // Get input elements
    const titleInput = document.getElementById('title');
    const linkInput = document.getElementById('link');
    const fileInput = document.getElementById('file');

    // Set required attribute for link input on page load since link tab is active by default
    if (linkInput) {
        linkInput.setAttribute('required', '');
    }

    // Tab switching functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    // Add click event to tab buttons
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons and panes
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));

            // Add active class to clicked button and corresponding pane
            this.classList.add('active');
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');

            // Update required attributes based on active tab

            if (tabId === 'link-tab') {
                linkInput.setAttribute('required', '');
                fileInput.removeAttribute('required');
            } else {
                fileInput.setAttribute('required', '');
                linkInput.removeAttribute('required');
            }
        });
    });

    // Check for success parameter in URL
    const urlParams = new URLSearchParams(window.location.search);
    const fileUrl = urlParams.get('file_url');
    if (fileUrl) {
        const successAlert = document.getElementById('uploadSuccess');
        const fileLink = document.getElementById('uploadedFileLink');
        fileLink.href = fileUrl;
        fileLink.textContent = fileUrl;
        successAlert.style.display = 'block';

        // Remove the parameters from URL without refreshing
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // Function to display error message
    function showError(input, message) {
        // Remove any existing error message
        const parent = input.parentElement;
        const existingError = parent.querySelector('.error-message');
        if (existingError) {
            parent.removeChild(existingError);
        }

        // Create and add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        parent.appendChild(errorDiv);

        // Add error class to input
        input.classList.add('input-error');
    }

    // Function to clear error message
    function clearError(input) {
        const parent = input.parentElement;
        const existingError = parent.querySelector('.error-message');
        if (existingError) {
            parent.removeChild(existingError);
        }
        input.classList.remove('input-error');
    }

    // Add submit event listener
    if (form) {
        form.addEventListener('submit', function(event) {
            // Prevent default form submission for validation
            event.preventDefault();

            // Clear all previous errors
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));

            // Get form inputs
            const title = document.getElementById('title').value.trim();
            const link = document.getElementById('link').value.trim();
            const file = document.getElementById('file').files[0];
            // Use existing input variables instead of redeclaring

            // Get active tab
            const activeTab = document.querySelector('.tab-btn.active').getAttribute('data-tab');

            let isValid = true;

            // Validate title
            if (title === '') {
                showError(titleInput, 'Введите название вашего QR-кода.');
                isValid = false;
            } else {
                clearError(titleInput);
            }

            // Validate based on active tab
            if (activeTab === 'link-tab') {
                // Validate link
                if (link === '') {
                    showError(linkInput, 'Введите ссылку для вашего QR-кода.');
                    isValid = false;
                } else {
                    // Validate URL format
                    try {
                        new URL(link);
                        clearError(linkInput);
                    } catch (e) {
                        showError(linkInput, 'Введите действительный URL-адрес (например, https://example.com)');
                        isValid = false;
                    }
                }
            } else {
                // Validate file
                if (!file) {
                    showError(fileInput, 'Пожалуйста, выберите файл для загрузки.');
                    isValid = false;
                } else {
                    // Validate file type
                    const allowedTypes = ['.png', '.jpg', '.jpeg', '.pdf', '.ppt', '.pptx', '.doc', '.docx'];
                    const fileName = file.name.toLowerCase();
                    const validFile = allowedTypes.some(type => fileName.endsWith(type));

                    if (!validFile) {
                        showError(fileInput, 'Пожалуйста, выберите допустимый тип файла (PNG, JPG, JPEG, PDF, PPT, PPTX, DOC, DOCX).');
                        isValid = false;
                    } else {
                        clearError(fileInput);
                    }
                }
            }

            // If form is valid, submit it
            if (isValid) {
                // Show loading indicator with spinner
                const button = form.querySelector('button[type="submit"]');
                const originalButtonText = button.textContent;
                if (button) {
                    button.textContent = 'В процессе...';
                    button.disabled = true;
                    button.classList.add('spinner');
                }

                // For file uploads, submit the form normally to allow redirect with file_url parameter
                if (activeTab === 'file-tab' && file) {
                    form.submit();
                    return;
                }

                // For link QR codes, use iframe to handle the download
                // Create a hidden iframe to handle the file download
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.name = 'download_frame';
                document.body.appendChild(iframe);

                // Set the form's target to the iframe
                form.target = 'download_frame';

                // Set up a shorter timeout to reset the button state after a reasonable time
                const successTimeout = setTimeout(function() {
                    if (button) {
                        button.textContent = originalButtonText;
                        button.disabled = false;
                        button.classList.remove('spinner');
                    }
                }, 5000); // 5 seconds should be enough for most downloads to start

                // Submit the form to trigger the file download
                form.submit();

                // Set up a listener to detect when the download is complete
                iframe.onload = function() {
                    clearTimeout(successTimeout);
                    if (button) {
                        button.textContent = originalButtonText;
                        button.disabled = false;
                        button.classList.remove('spinner');
                    }
                };
            }
        });
    }

    // Add input event listeners for real-time validation

    if (titleInput) {
        titleInput.addEventListener('input', function() {
            clearError(this);
        });
    }

    if (linkInput) {
        linkInput.addEventListener('input', function() {
            try {
                if (this.value.trim() !== '') {
                    new URL(this.value);
                    clearError(this);
                } else {
                    clearError(this);
                }
            } catch (e) {
                showError(this, 'Введите действительный URL-адрес.');
            }
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const fileName = this.files[0].name.toLowerCase();
                const allowedTypes = ['.png', '.jpg', '.jpeg', '.pdf', '.ppt', '.pptx', '.doc', '.docx'];
                const validFile = allowedTypes.some(type => fileName.endsWith(type));

                if (!validFile) {
                    showError(this, 'Пожалуйста, выберите допустимый тип файла (PNG, JPG, JPEG, PDF, PPT, PPTX, DOC, DOCX).');
                } else {
                    clearError(this);
                }
            } else {
                clearError(this);
            }
        });
    }
});
