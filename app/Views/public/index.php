<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Surveys</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<!-- Header -->
<header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Surveys</h1>
            <p class="text-sm text-gray-600 mt-0.5">Survey of the survying surveys</p>
        </div>
        <a href="<?= base_url('admin/login') ?>" class="text-blue-600 hover:text-blue-700 font-medium">
            Admin Login
        </a>
    </div>
</header>

<!-- Main Content -->
<main class="flex-1">
    <div class="max-w-6xl mx-auto px-4 py-12">
        
        <!-- Passcode Form -->
        <div class="mb-12 bg-blue-50 border border-blue-200 rounded-2xl p-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Have a Survey Passcode?</h2>
            <p class="text-sm text-gray-600 mb-4">Enter your passcode to access a private survey</p>
            <form method="POST" action="<?= base_url('survey/access') ?>" class="flex gap-3">
                <?= csrf_field() ?>
                <input type="text"
                       name="passcode"
                       placeholder="Enter your survey passcode..."
                       class="flex-1 border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-8 py-3 rounded-xl transition whitespace-nowrap">
                    Access Survey
                </button>
            </form>
            <?php if (session()->getFlashdata('passcode_error')): ?>
            <p class="text-red-600 text-sm mt-3 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                </svg>
                <?= esc(session()->getFlashdata('passcode_error')) ?>
            </p>
            <?php endif; ?>
        </div>

        <!-- Public Surveys Grid -->
        <?php if (empty($surveys)): ?>
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No public surveys available</p>
            <p class="text-gray-400 text-sm mt-1">Check back later or use a passcode to access private surveys</p>
        </div>
        <?php else: ?>
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-1">Public Surveys</h2>
            <p class="text-sm text-gray-600">Click on any survey below to get started</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($surveys as $survey): ?>
            <a href="<?= base_url('s/' . $survey['id']) ?>"
               class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-lg hover:border-blue-300 transition-all duration-200 p-6 group">
                
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition">
                        <?= esc($survey['name']) ?>
                    </h3>
                </div>

                <?php if ($survey['description']): ?>
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                    <?= esc($survey['description']) ?>
                </p>
                <?php endif; ?>

                <div class="flex items-center text-blue-600 font-medium text-sm group-hover:translate-x-1 transition">
                    Start Survey
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Footer -->
<footer class="border-t border-gray-200 bg-white mt-12">
    <div class="max-w-6xl mx-auto px-4 py-8 text-center text-sm text-gray-500">
        <p>Questions? Contact the survey administrator</p>
    </div>
</footer>

</body>
</html>

