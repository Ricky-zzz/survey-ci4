<?= $this->extend('layout_admin') ?>

<?= $this->section('title') ?>Answer Surveys<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Available Surveys</h1>
        <p class="text-gray-600 mt-1">Select a survey to complete</p>
    </div>

    <!-- Passcode Form -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-2">Have a Survey Passcode?</h2>
        <p class="text-sm text-gray-600 mb-4">Enter your passcode to access a private survey</p>
        <form method="POST" action="<?= base_url('survey/access') ?>" class="flex gap-3">
            <?= csrf_field() ?>
            <input type="text"
                   name="passcode"
                   placeholder="Enter your survey passcode..."
                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required>
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition whitespace-nowrap text-sm">
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

    <?php if (empty($surveys)): ?>
        <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-600">No surveys available at this time.</p>
        </div>
    <?php else: ?>
        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Available Surveys</h2>
            <div class="grid gap-4 grid-cols-1 md:grid-cols-2">
                <?php foreach ($surveys as $survey): ?>
                    <a href="<?= base_url('s/' . $survey['id']) ?>"
                       class="block p-6 bg-white rounded-lg border border-gray-200 hover:border-blue-400 hover:shadow-md transition">
                        <h3 class="text-lg font-semibold text-gray-900"><?= esc($survey['name']) ?></h3>
                        <?php if (!empty($survey['description'])): ?>
                            <p class="text-sm text-gray-600 mt-2 line-clamp-2"><?= esc($survey['description']) ?></p>
                        <?php endif; ?>
                        <div class="mt-4 flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Start Survey
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
