<?= $this->extend('layout_admin') ?>

<?= $this->section('title') ?>Edit Question<?= $this->endSection() ?>

<?= $this->section('header_actions') ?>
<div class="flex items-center gap-2 text-xs text-gray-500">
    <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/edit') ?>"
       class="hover:text-blue-600 transition"><?= esc($survey['name']) ?></a>
    <span class="text-gray-300">/</span>
    <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/sections/' . $section['id']) ?>"
       class="hover:text-blue-600 transition"><?= esc($section['title']) ?></a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-700">Edit Question</span>
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
$hasOptions = in_array($question['type'], ['scale', 'multiple_choice']);
?>
<div class="max-w-2xl">
    <form method="POST"
          action="<?= base_url('admin/surveys/' . $survey['id'] . '/sections/' . $section['id'] . '/questions/' . $question['id']) ?>"
          class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        <?= csrf_field() ?>

        <!-- Question text -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Question <span class="text-red-500">*</span>
            </label>
            <input type="text" name="question_text"
                   value="<?= esc($question['question_text']) ?>"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Type (read-only) -->
        <div>
            <span class="block text-sm font-medium text-gray-700 mb-1">Type</span>
            <span class="inline-block text-sm text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg">
                <?= esc($typeLabels[$question['type']] ?? $question['type']) ?>
            </span>
            <p class="text-xs text-gray-400 mt-1">Delete and re-add the question to change type.</p>
        </div>

        <!-- Required -->
        <div>
            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                <input type="checkbox" name="required" value="1"
                       <?= $question['required'] ? 'checked' : '' ?>
                       class="rounded border-gray-300 text-blue-600">
                Required
            </label>
        </div>

        <?php if ($question['type'] === 'file_upload'): ?>
        <div>
            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                <input type="checkbox" name="allow_multiple_files" value="1"
                       <?= $question['allow_multiple_files'] ? 'checked' : '' ?>
                       class="rounded border-gray-300 text-blue-600">
                Allow multiple files
            </label>
        </div>
        <?php endif; ?>

        <?php if ($hasOptions): ?>
        <!-- Options — existing ones rendered as plain inputs, new ones appended via Alpine -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
            <div id="options-list" class="space-y-2">
                <?php foreach ($question['options'] as $i => $opt): ?>
                <div class="flex gap-2 items-center option-row">
                    <input type="text"
                           name="option_text[<?= $i ?>]"
                           value="<?= esc($opt['option_text']) ?>"
                           placeholder="Option label"
                           class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="hidden"
                           name="option_value[<?= $i ?>]"
                           value="<?= esc($opt['option_text']) ?>"
                           class="option-value-mirror">
                    <button type="button" onclick="removeOption(this)"
                            class="text-gray-300 hover:text-red-500 transition px-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>

            <button type="button" onclick="addOption()"
                    class="mt-3 text-xs text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add option
            </button>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
            <button type="submit"
                    class="bg-blue-600 text-white rounded-lg px-5 py-2 text-sm font-medium hover:bg-blue-700 transition">
                Save
            </button>
            <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/sections/' . $section['id']) ?>"
               class="text-sm text-gray-500 hover:text-gray-700 transition">
                Cancel
            </a>
            <form method="POST"
                  action="<?= base_url('admin/surveys/' . $survey['id'] . '/sections/' . $section['id'] . '/questions/' . $question['id'] . '/delete') ?>"
                  onsubmit="return confirm('Delete this question?')"
                  class="ml-auto">
                <?= csrf_field() ?>
                <button type="submit" class="text-sm text-red-400 hover:text-red-600 transition">
                    Delete question
                </button>
            </form>
        </div>
    </form>
</div>

<?php if ($hasOptions): ?>
<script>
function nextIndex() {
    return document.querySelectorAll('#options-list .option-row').length;
}

function addOption() {
    const i    = nextIndex();
    const list = document.getElementById('options-list');
    const row  = document.createElement('div');
    row.className = 'flex gap-2 items-center option-row';
    row.innerHTML = `
        <input type="text" name="option_text[${i}]" placeholder="Option label"
               oninput="this.nextElementSibling.value = this.value"
               class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input type="hidden" name="option_value[${i}]" value="" class="option-value-mirror">
        <button type="button" onclick="removeOption(this)" class="text-gray-300 hover:text-red-500 transition px-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;
    list.appendChild(row);
}

function removeOption(btn) {
    btn.closest('.option-row').remove();
    // Re-index remaining rows so names stay sequential
    document.querySelectorAll('#options-list .option-row').forEach((row, i) => {
        row.querySelector('input[type="text"]').name   = `option_text[${i}]`;
        row.querySelector('input[type="hidden"]').name = `option_value[${i}]`;
    });
}

// Mirror existing option labels → hidden value fields on edit
document.querySelectorAll('#options-list .option-row input[type="text"]').forEach(input => {
    input.addEventListener('input', () => {
        input.nextElementSibling.value = input.value;
    });
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>
