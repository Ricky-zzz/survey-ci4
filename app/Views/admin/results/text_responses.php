<?= $this->extend('layout_admin') ?>

<?= $this->section('title') ?>Text Responses: <?= esc($question['question_text']) ?><?= $this->endSection() ?>

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
        <p class="text-sm text-gray-600 mt-2"><?= count($responses) ?> response<?= count($responses) !== 1 ? 's' : '' ?></p>
    </div>

    <?php if (empty($responses)): ?>
        <div class="bg-white rounded-lg border border-gray-200 p-12 text-center text-gray-400">
            <p>No text responses found with current filters.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($responses as $response): ?>
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="mb-4 pb-4 border-b border-gray-100">
                        <p class="text-sm text-gray-900 font-medium"><?= esc($response['respondent']['fullname'] ?? 'Unknown') ?></p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            <?= esc($response['respondent']['email'] ?? '-') ?>
                            <?php if (!empty($response['respondent']['age'])): ?>
                                · Age <?= esc($response['respondent']['age']) ?>
                            <?php endif; ?>
                            <?php if (!empty($response['respondent']['address'])): ?>
                                · <?= esc($response['respondent']['address']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <p class="text-sm text-gray-800 whitespace-pre-wrap"><?= esc($response['answer']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
