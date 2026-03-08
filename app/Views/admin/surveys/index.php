<?= $this->extend('layout_admin') ?>

<?= $this->section('title') ?>Surveys<?= $this->endSection() ?>

<?= $this->section('header_actions') ?>
<a href="<?= base_url('admin/surveys/create') ?>"
   class="bg-blue-600 text-white text-sm rounded-lg px-4 py-2 hover:bg-blue-700 transition">
    + New Survey
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (empty($surveys)): ?>
    <div class="text-center py-20 text-gray-400">
        <svg class="w-14 h-14 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-sm font-medium text-gray-500">No surveys yet</p>
        <p class="text-xs text-gray-400 mt-1">
            <a href="<?= base_url('admin/surveys/create') ?>" class="text-blue-600 hover:underline">Create your first survey</a>
        </p>
    </div>
<?php else: ?>
    <div class="space-y-3 max-w-4xl">
        <?php foreach ($surveys as $survey): ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between gap-4">
            <div class="min-w-0">
                <h3 class="font-medium text-gray-900 truncate"><?= esc($survey['name']) ?></h3>
                <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-2">
                    <?php if ($survey['is_active']): ?>
                        <span class="inline-flex items-center gap-1 text-green-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                            Active
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1 text-gray-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300 inline-block"></span>
                            Draft
                        </span>
                    <?php endif; ?>
                    <span class="text-gray-300">·</span>
                    <?php if ($survey['is_public']): ?>
                        <span class="text-blue-600">Public</span>
                    <?php else: ?>
                        <span class="text-gray-500">Private</span>
                    <?php endif; ?>
                    <span class="text-gray-300">·</span>
                    <span><?= $survey['stats']['completed'] ?? 0 ?> response<?= $survey['stats']['completed'] !== 1 ? 's' : '' ?></span>
                    <?php if ($survey['description']): ?>
                        <span class="text-gray-300">·</span>
                        <span class="truncate max-w-xs"><?= esc($survey['description']) ?></span>
                    <?php endif; ?>
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0 text-sm">
                <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results') ?>"
                   class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition text-xs">
                    Results
                </a>
                <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/edit') ?>"
                   class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition text-xs font-medium">
                    Edit
                </a>
                <form method="POST" action="<?= base_url('admin/surveys/' . $survey['id'] . '/delete') ?>"
                      onsubmit="return confirm('Delete this survey? This cannot be undone.')">
                    <?= csrf_field() ?>
                    <button type="submit"
                            class="px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition text-xs">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
