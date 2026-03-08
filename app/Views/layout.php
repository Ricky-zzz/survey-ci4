<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?? 'Survey' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .form-input {
            @apply w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    <?= $this->include('layout/navbar') ?>
    <main class="min-h-screen">
        <?= $this->renderSection('content') ?>
    </main>
    <?= $this->include('layout/footer') ?>
</body>
</html>
