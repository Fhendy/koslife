<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - KosLife</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full text-center">
            <div class="w-24 h-24 rounded-full bg-primary-100 dark:bg-primary-900/20 flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-wifi-slash text-3xl text-primary-600 dark:text-primary-400"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Kamu Offline</h1>
            <p class="text-gray-500 dark:text-gray-400 mb-6">
                Sepertinya kamu tidak terhubung ke internet. Beberapa fitur mungkin tidak tersedia.
            </p>
            <button onclick="location.reload()" 
                    class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-sync mr-2"></i>Coba Lagi
            </button>
        </div>
    </div>
</body>
</html>