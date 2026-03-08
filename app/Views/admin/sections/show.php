<?= $this->extend('layout_admin') ?>

<?= $this->section('title') ?><?= esc($section['title']) ?><?= $this->endSection() ?>

<?= $this->section('header_actions') ?>
<div class="flex items-center gap-2">
    <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/edit') ?>"
       class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        <?= esc($survey['name']) ?>
    </a>
    <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results') ?>"
       class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
        Results
    </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$typeLabels = [
    'text'            => 'Short Text',
    'yesno'           => 'Yes / No',
    'scale'           => 'Scale',
    'multiple_choice' => 'Multiple Choice',
    'file_upload'     => 'File Upload',
];
?>
<div class="space-y-6 max-w-3xl">

    <!-- Section Settings -->
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center gap-2 mb-4">
            <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                <?= $section['is_respondent_info'] ? 'Respondent Info' : 'Section' ?>
            </span>
        </div>
        <form method="POST" action="<?= base_url('admin/surveys/' . $survey['id'] . '/sections/' . $section['id']) ?>" class="space-y-3">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                <input type="text" name="title" value="<?= esc($section['title']) ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Description <span class="text-gray-400">(optional)</span></label>
                <input type="text" name="description" value="<?= esc($section['description'] ?? '') ?>"
                       placeholder="Shown above questions in this section"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="bg-gray-800 text-white rounded-lg px-4 py-2 text-xs font-medium hover:bg-gray-700 transition">
                Save
            </button>
        </form>
    </div>

    <!-- Questions -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-sm font-semibold text-gray-700">
                        Questions
                        <span class="text-gray-400 font-normal ml-1">(<?= count($section['questions']) ?>)</span>
                    </h2>
                    <?php if ($hasResponses ?? false): ?>
                    <p class="text-xs text-amber-600 mt-1">
                        This survey has <?= $respondentCount ?> response<?= $respondentCount !== 1 ? 's' : '' ?> — questions cannot be modified.
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (empty($section['questions'])): ?>
        <div class="px-5 py-10 text-sm text-gray-400 text-center">No questions yet. Add one below.</div>
        <?php else: ?>
        <div class="divide-y divide-gray-100">
            <?php foreach ($section['questions'] as $q): ?>
            <div class="flex items-start justify-between gap-4 px-5 py-4 group">
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-800">
                        <?= esc($q['question_text']) ?>
                        <?php if ($q['required']): ?>
                            <span class="text-red-400 ml-0.5 text-xs">*</span>
                        <?php endif; ?>
                    </p>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                            <?= esc($typeLabels[$q['type']] ?? $q['type']) ?>
                        </span>
                        <?php if (! empty($q['options'])): ?>
                        <span class="text-xs text-gray-400"><?= count($q['options']) ?> options</span>
                        <?php endif; ?>
                    </div>
                    <?php if (! empty($q['options'])): ?>
                    <div class="mt-2 space-y-1">
                        <?php foreach ($q['options'] as $opt): ?>
                        <div class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded w-fit">
                            <?= esc($opt['option_text']) ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-2 shrink-0 opacity-0 group-hover:opacity-100 transition">
                    <?php if ($hasResponses ?? false): ?>
                    <span class="text-xs text-gray-300">Edit</span>
                    <span class="text-xs text-gray-300">Delete</span>
                    <?php else: ?>
                    <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/sections/' . $section['id'] . '/questions/' . $q['id'] . '/edit') ?>"
                       class="text-xs text-gray-500 hover:text-blue-600 border border-gray-200 hover:border-blue-300 px-2 py-1 rounded-lg transition">
                        Edit
                    </a>
                    <form method="POST"
                          action="<?= base_url('admin/surveys/' . $survey['id'] . '/sections/' . $section['id'] . '/questions/' . $q['id'] . '/delete') ?>"
                          onsubmit="return confirm('Delete this question?')">
                        <?= csrf_field() ?>
                        <button type="submit"
                                class="text-xs text-gray-500 hover:text-red-500 border border-gray-200 hover:border-red-200 px-2 py-1 rounded-lg transition">
                            Delete
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Add Question -->
        <?php if (! ($hasResponses ?? false)): ?>
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100" x-data="{ open: false }">
            <button @click="open = !open"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add question
            </button>
            <div x-show="open" x-cloak class="mt-4">
                <?php
                $action    = base_url('admin/surveys/' . $survey['id'] . '/sections/' . $section['id'] . '/questions');
                $question  = null;
                $surveyId  = $survey['id'];
                $sectionId = $section['id'];
                include APPPATH . 'Views/components/question_form.php';
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
