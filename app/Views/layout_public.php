<?php
    $session = session();
    $adminId = $session->get('admin_id');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ? $this->renderSection('title') . ' — ' : '' ?>Survey App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">
    <nav class="border-b border-gray-200 bg-white sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="<?= base_url('/') ?>" class="text-lg font-semibold text-gray-900 hover:opacity-80 transition">
                Survey App
            </a>
            <div class="flex items-center gap-3">
                <?php if ($adminId): ?>
                    <a href="<?= base_url('admin/dashboard') ?>" class="text-sm text-gray-600 hover:text-gray-900">
                        Admin Dashboard
                    </a>
                    <a href="<?= base_url('admin/logout') ?>" class="text-sm px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('admin/index') ?>" class="text-sm px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                        Sign In
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <main class="flex-grow max-w-6xl mx-auto px-6 py-8 w-full">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 bg-white mt-12">
        <div class="max-w-6xl mx-auto px-6 py-6 text-center text-sm text-gray-500">
            <p>&copy; <?= date('Y') ?> Survey App. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
