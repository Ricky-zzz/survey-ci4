<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Survey App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <div class="text-center mb-8">
                <a href="/" class="inline-block hover:opacity-80 transition">
                    <h1 class="text-2xl font-semibold text-gray-800">Survey App</h1>
                </a>
                <p class="text-sm text-gray-500 mt-1">Sign in to your admin account</p>
            </div>

            <?php if ($error = session()->getFlashdata('error')): ?>
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <?= esc($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('admin/login') ?>" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username"
                           value="<?= esc(old('username')) ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="admin" autofocus>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="••••••••">
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-700 transition mt-2">
                    Sign in
                </button>
            </form>
        </div>
    </div>
</body>
</html>
