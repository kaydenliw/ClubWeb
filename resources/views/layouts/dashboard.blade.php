<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Car Community Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* DataTables Modern Styling */
        .dataTables_wrapper {
            padding: 1rem;
        }

        /* Hide default search and length */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            display: none;
        }

        /* Table styling */
        table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0;
        }

        table.dataTable thead th {
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            padding: 0.75rem 1rem;
            background-color: #f9fafb;
        }

        table.dataTable tbody td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
        }

        table.dataTable tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Pagination styling */
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 1rem;
            text-align: right;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5rem 0.75rem;
            margin: 0 0.125rem;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            background: white;
            color: #374151;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #3b82f6 !important;
            color: white !important;
            border-color: #3b82f6 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Info text */
        .dataTables_wrapper .dataTables_info {
            padding-top: 1rem;
            font-size: 0.875rem;
            color: #6b7280;
        }

        /* Sorting icons */
        table.dataTable thead .sorting,
        table.dataTable thead .sorting_asc,
        table.dataTable thead .sorting_desc {
            cursor: pointer;
            position: relative;
        }

        table.dataTable thead .sorting:before,
        table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_desc:before {
            right: 0.5rem;
            opacity: 0.3;
        }

        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_desc:after {
            right: 0.5rem;
            opacity: 0.3;
        }

        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 9999;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .toast-enter {
            animation: slideInRight 0.3s ease-out;
        }

        .toast-exit {
            animation: slideOutRight 0.3s ease-in;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9998;
        }

        .loading-spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Required Field Indicator */
        label:has(+ input[required])::after,
        label:has(+ textarea[required])::after,
        label:has(+ select[required])::after {
            content: ' *';
            color: #ef4444;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        @include('layouts.partials.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden">
            @include('layouts.partials.header')

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-8">
                @include('layouts.partials.breadcrumb')
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="toast-container"></div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="bg-white rounded-lg p-6 flex flex-col items-center">
            <div class="loading-spinner mb-3"></div>
            <p class="text-sm text-gray-600">Processing...</p>
        </div>
    </div>

    <!-- Confirmation Dialog -->
    <div id="confirmDialog" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2" id="confirmTitle">Confirm Action</h3>
                <p class="text-sm text-gray-500 text-center mb-6" id="confirmMessage">Are you sure you want to proceed?</p>
                <div class="flex gap-3">
                    <button onclick="closeConfirmDialog()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button id="confirmButton" class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <!-- Toast Notification Script -->
    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-blue-500',
                warning: 'bg-yellow-500'
            };

            const icons = {
                success: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                error: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                info: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                warning: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>'
            };

            toast.className = `${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 mb-3 toast-enter min-w-[300px]`;
            toast.innerHTML = `
                <div class="flex-shrink-0">${icons[type]}</div>
                <div class="flex-1 text-sm font-medium">${message}</div>
                <button onclick="this.parentElement.remove()" class="flex-shrink-0 hover:opacity-75">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Show toast on page load if there's a session message
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        @if(session('info'))
            showToast("{{ session('info') }}", 'info');
        @endif

        @if(session('warning'))
            showToast("{{ session('warning') }}", 'warning');
        @endif

        // Confirmation Dialog Functions
        let confirmCallback = null;

        function showConfirmDialog(title, message, callback, buttonText = 'Confirm', buttonColor = 'red') {
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmMessage').textContent = message;
            const confirmBtn = document.getElementById('confirmButton');
            confirmBtn.textContent = buttonText;
            confirmBtn.className = `flex-1 px-4 py-2 bg-${buttonColor}-600 text-white text-sm font-medium rounded-lg hover:bg-${buttonColor}-700 transition`;
            confirmCallback = callback;
            document.getElementById('confirmDialog').style.display = 'block';
            document.getElementById('confirmDialog').classList.remove('hidden');
        }

        function closeConfirmDialog() {
            document.getElementById('confirmDialog').style.display = 'none';
            document.getElementById('confirmDialog').classList.add('hidden');
            confirmCallback = null;
        }

        document.getElementById('confirmButton').addEventListener('click', function() {
            if (confirmCallback) {
                confirmCallback();
            }
            closeConfirmDialog();
        });

        // Helper function for delete confirmations
        function confirmDelete(formId, itemName = 'this item') {
            showConfirmDialog(
                'Confirm Deletion',
                `Are you sure you want to delete ${itemName}? This action cannot be undone.`,
                function() {
                    document.getElementById(formId).submit();
                },
                'Delete',
                'red'
            );
            return false;
        }

        // Helper function for bulk action confirmations
        function confirmBulkAction(formId, action, count) {
            showConfirmDialog(
                'Confirm Bulk Action',
                `Are you sure you want to ${action} ${count} item(s)?`,
                function() {
                    document.getElementById(formId).submit();
                },
                'Proceed',
                'blue'
            );
            return false;
        }
    </script>

    <!-- Loading State Functions -->
    <script>
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        // Auto-show loading on form submit
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form:not([data-no-loading])');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Disable all submit buttons in the form
                    const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                    submitButtons.forEach(btn => {
                        btn.disabled = true;
                        btn.classList.add('opacity-50', 'cursor-not-allowed');
                        // Store original text
                        if (btn.tagName === 'BUTTON') {
                            btn.dataset.originalText = btn.innerHTML;
                            btn.innerHTML = '<svg class="animate-spin h-4 w-4 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
                        }
                    });
                    showLoading();
                });
            });
        });
    </script>

    <!-- Unsaved Changes Warning -->
    <script>
        let formChanged = false;

        document.addEventListener('DOMContentLoaded', function() {
            // Track changes on all forms with data-warn-unsaved attribute
            const forms = document.querySelectorAll('form[data-warn-unsaved]');

            forms.forEach(form => {
                const inputs = form.querySelectorAll('input, textarea, select');

                inputs.forEach(input => {
                    input.addEventListener('change', function() {
                        formChanged = true;
                    });

                    if (input.tagName === 'INPUT' || input.tagName === 'TEXTAREA') {
                        input.addEventListener('input', function() {
                            formChanged = true;
                        });
                    }
                });

                // Reset flag on form submit
                form.addEventListener('submit', function() {
                    formChanged = false;
                });
            });

            // Warn before leaving page
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                    return e.returnValue;
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
