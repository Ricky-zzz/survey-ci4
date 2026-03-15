<?= $this->extend('layout_admin') ?>

<?= $this->section('title') ?>File Uploads: <?= esc($question['question_text']) ?><?= $this->endSection() ?>

<?= $this->section('header_actions') ?>
<a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results/analytics') . (($filters['age_min'] || $filters['age_max'] || $filters['address']) ? '?age_min=' . ($filters['age_min'] ?? '') . '&age_max=' . ($filters['age_max'] ?? '') . '&address=' . urlencode($filters['address'] ?? '') : '') ?>"
   class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
    ← Back to Analytics
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900"><?= esc($question['question_text']) ?></h1>
        <p class="text-sm text-gray-600 mt-2"><?= count($files) ?> file<?= count($files) !== 1 ? 's' : '' ?> submitted</p>
    </div>

    <?php if (empty($files)): ?>
        <div class="bg-white rounded-lg border border-gray-200 p-12 text-center text-gray-400">
            <p>No files uploaded with current filters.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($files as $item): ?>
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="mb-4 pb-4 border-b border-gray-100">
                        <p class="text-sm text-gray-900 font-medium"><?= esc($item['respondent']['fullname'] ?? 'Unknown') ?></p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            <?= esc($item['respondent']['email'] ?? '-') ?>
                            <?php if (!empty($item['respondent']['age'])): ?>
                                · Age <?= esc($item['respondent']['age']) ?>
                            <?php endif; ?>
                            <?php if (!empty($item['respondent']['address'])): ?>
                                · <?= esc($item['respondent']['address']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="flex items-center gap-3 bg-blue-50 px-4 py-3 rounded-lg border border-blue-200">
                        <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <a href="<?= base_url($item['file']['file_path']) ?>" target="_blank"
                               class="text-sm text-blue-700 hover:underline truncate block font-medium">
                                <?= esc($item['file']['original_filename']) ?>
                            </a>
                            <p class="text-xs text-blue-600"><?= round($item['file']['file_size'] / 1024, 1) ?> KB</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
