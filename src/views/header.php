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
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center">
                        <img src="/logo.png" alt="Huuto - Verkkohuutokauppa" 
                             class="h-12 md:h-14 lg:h-16 w-auto object-contain">
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <form action="/" method="GET" class="hidden md:block">
                        <input type="text" name="q" placeholder="Etsi kohteita..." 
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </form>
                    <a href="auction.php" class="text-gray-700 hover:text-blue-600">Kohteet</a>
                    <a href="category.php" class="text-gray-700 hover:text-blue-600">Kategoriat</a>
                    
                    <?php if (function_exists('is_logged_in') && is_logged_in()): ?>
                        <?php $user = current_user(); ?>
                        <span class="text-gray-600 hidden sm:inline">Hei, <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>!</span>
                        <a href="admin.php" class="text-gray-700 hover:text-blue-600">Admin</a>
                        <a href="add_product.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors font-semibold">
                            + Lisää tuote
                        </a>
                        <a href="/auth/logout.php" class="text-gray-700 hover:text-red-600">Kirjaudu ulos</a>
                    <?php else: ?>
                        <a href="/auth/login.php" class="text-gray-700 hover:text-blue-600">Kirjaudu</a>
                        <a href="/auth/register.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Rekisteröidy
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
