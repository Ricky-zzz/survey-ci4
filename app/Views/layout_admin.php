<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?? 'Admin' ?> — Survey Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

<!-- Toast notifications -->
<?php
$toasts = [];
if ($msg = session()->getFlashdata('success')) $toasts[] = ['type' => 'success', 'msg' => $msg];
if ($msg = session()->getFlashdata('error'))   $toasts[] = ['type' => 'error',   'msg' => $msg];
if ($errs = session()->getFlashdata('errors')) {
    foreach ($errs as $e) $toasts[] = ['type' => 'error', 'msg' => $e];
}
?>
<?php if (!empty($toasts)): ?>
<div id="toast-stack"
     class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 w-80"
     x-data="toastStack()"
     x-init="init()">
    <?php foreach ($toasts as $i => $t): ?>
    <div x-data="{ show: true }"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         x-init="setTimeout(() => show = false, 5000)"
         class="flex items-start gap-3 rounded-xl px-4 py-3 shadow-lg border text-sm
                <?= $t['type'] === 'success'
                    ? 'bg-white border-green-200 text-green-800'
                    : 'bg-white border-red-200 text-red-800' ?>">
        <!-- Icon -->
        <?php if ($t['type'] === 'success'): ?>
        <svg class="w-4 h-4 mt-0.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <?php else: ?>
        <svg class="w-4 h-4 mt-0.5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 110 18A9 9 0 0112 3z"/>
        </svg>
        <?php endif; ?>
        <!-- Message -->
        <span class="flex-1 leading-snug"><?= esc($t['msg']) ?></span>
        <!-- Dismiss -->
        <button @click="show = false" class="ml-1 mt-0.5 text-gray-400 hover:text-gray-600 transition shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <?php endforeach; ?>
</div>
<script>
function toastStack() { return { init() {} }; }
</script>
<?php endif; ?>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-56 bg-white border-r border-gray-200 flex flex-col">
            <div class="px-6 py-5 border-b border-gray-100">
                <a href="<?= base_url('admin/dashboard') ?>" class="text-lg font-semibold text-blue-600 tracking-tight">Survey App</a>
            </div>
            <nav class="flex-1 px-4 py-4 space-y-1 text-sm">
                <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="<?= base_url('admin/surveys') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    My Surveys
                </a>
                <a href="<?= base_url('s') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"/></svg>
                    Answer Surveys
                </a>
            </nav>
            <div class="px-4 py-4 border-t border-gray-100 text-sm">
                <a href="<?= base_url('admin/logout') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout (<?= esc(session()->get('admin_user')) ?>)
                </a>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h1 class="text-base font-medium text-gray-700"><?= $this->renderSection('title') ?? 'Admin' ?></h1>
                <?= $this->renderSection('header_actions') ?>
            </header>
            <main class="flex-1 overflow-y-auto p-6">
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>
</body>
</html>
