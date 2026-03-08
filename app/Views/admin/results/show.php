<?= $this->extend('layout_admin') ?>

<?= $this->section('title') ?>Response #<?= $respondent['id'] ?><?= $this->endSection() ?>

<?= $this->section('header_actions') ?>
<div class="flex items-center gap-2">
    <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results') ?>"
       class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
        ← All Responses
    </a>
    <form method="POST"
          action="<?= base_url('admin/surveys/' . $survey['id'] . '/results/' . $respondent['id'] . '/delete') ?>"
          onsubmit="return confirm('Delete this response permanently?')">
        <?= csrf_field() ?>
        <button type="submit" class="px-3 py-1.5 text-xs rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition">
            Delete
        </button>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-2xl space-y-4">
    <?php foreach ($survey['sections'] as $section): ?>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
            <h3 class="text-sm font-medium text-gray-700"><?= esc($section['title']) ?></h3>
        </div>
        <div class="divide-y divide-gray-50">
            <?php foreach ($section['questions'] as $q): ?>
            <div class="px-5 py-4">
                <p class="text-xs text-gray-500 mb-1"><?= esc($q['question_text']) ?></p>
                <?php if ($q['type'] === 'file_upload'): ?>
                    <?php $qFiles = $filesByQuestion[$q['id']] ?? []; ?>
                    <?php if (empty($qFiles)): ?>
                        <p class="text-sm text-gray-400 italic">No file uploaded</p>
                    <?php else: ?>
                        <div class="space-y-1">
                            <?php foreach ($qFiles as $file): ?>
                                <a href="<?= base_url($file['file_path']) ?>" target="_blank"
                                   class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    <?= esc($file['original_filename']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <?php $answer = $responses[$q['id']] ?? null; ?>
                    <?php if ($answer !== null && $answer !== ''): ?>
                        <p class="text-sm text-gray-800"><?= esc($answer) ?></p>
                    <?php else: ?>
                        <p class="text-sm text-gray-400 italic">No answer</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
