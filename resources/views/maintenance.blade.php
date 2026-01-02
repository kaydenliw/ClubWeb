<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
    @vite(['resources/css/app.css'])
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full text-center">
        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12">
            <!-- Icon -->
            <div class="float-animation mb-8">
                <svg class="w-24 h-24 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>

            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Under Maintenance
            </h1>

            <!-- Message -->
            <p class="text-lg text-gray-600 mb-8">
                {{ $message }}
            </p>

            <!-- Additional Info -->
            <div class="bg-blue-50 rounded-lg p-6 mb-8">
                <p class="text-sm text-gray-700">
                    We apologize for any inconvenience. Our team is working hard to improve your experience.
                </p>
            </div>

            <!-- Contact -->
            <p class="text-sm text-gray-500">
                Need urgent assistance? Contact us at
                <a href="mailto:support@example.com" class="text-blue-600 hover:text-blue-700 font-medium">
                    support@example.com
                </a>
            </p>
        </div>

        <!-- Footer -->
        <p class="text-sm text-gray-600 mt-6">
            &copy; {{ date('Y') }} Car Community Portal. All rights reserved.
        </p>
    </div>
</body>
</html>
