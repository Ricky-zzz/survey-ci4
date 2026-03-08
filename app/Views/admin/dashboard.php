<?= $this->extend('layout_admin') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('header_actions') ?>
<a href="<?= base_url('admin/surveys/create') ?>"
   class="bg-blue-600 text-white text-sm rounded-lg px-4 py-2 hover:bg-blue-700 transition">
    + New Survey
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Total Surveys</p>
        <p class="text-3xl font-semibold text-gray-800 mt-1"><?= count($surveys) ?></p>
    </div>
</div>

<?php if (empty($surveys)): ?>
    <div class="text-center py-16 text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-sm">No surveys yet. <a href="<?= base_url('admin/surveys/create') ?>" class="text-blue-600 hover:underline">Create one</a></p>
    </div>
<?php else: ?>
    <div class="space-y-3">
        <?php foreach ($surveys as $survey): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
                <div>
                    <h3 class="font-medium text-gray-800"><?= esc($survey['name']) ?></h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <?= $survey['stats']['completed'] ?> responses
                        &middot;
                        <?= $survey['is_public'] ? '<span class="text-green-600">Public</span>' : '<span class="text-gray-400">Draft</span>' ?>
                    </p>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/edit') ?>"
                       class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Edit</a>
                    <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results') ?>"
                       class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Results</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
