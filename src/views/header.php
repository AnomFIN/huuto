<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? SITE_NAME, ENT_QUOTES, 'UTF-8'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .countdown { font-feature-settings: 'tnum'; }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-blue-600">Huuto</a>
                    <span class="ml-2 text-gray-500 text-sm">Suomalainen Huutokauppa</span>
                </div>
                <div class="flex items-center space-x-4">
                    <form action="/search.php" method="GET" class="hidden md:block">
                        <input type="text" name="q" placeholder="Etsi kohteita..." 
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </form>
                    <a href="/auctions.php" class="text-gray-700 hover:text-blue-600">Kohteet</a>
                    <a href="/categories.php" class="text-gray-700 hover:text-blue-600">Kategoriat</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
