<?php
// $q — question array with keys: id, question_text, type, required, options, allow_multiple_files
if (!isset($q) || !is_array($q)) {
    throw new \Exception('Invalid question data passed to question_field component');
}

$required     = $q['required'] ? 'required' : '';
$requiredStar = $q['required'] ? '<span class="text-red-500 ml-0.5">*</span>' : '';
$name         = 'responses[' . $q['id'] . ']';
$old          = old($name);
$qId          = 'question-' . $q['id'];  // Add ID for error linking
?>

<div id="<?= $qId ?>">
<label class="block text-sm text-gray-800 mb-2 font-medium">
    <?= esc($q['question_text']) ?> <?= $requiredStar ?>
</label>

<?php if ($q['type'] === 'text'): ?>
    <input type="text"
           name="<?= $name ?>"
           value="<?= esc($old ?? '') ?>"
           <?= $required ?>
           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

<?php elseif ($q['type'] === 'yesno'): ?>
    <div class="flex gap-3">
        <?php foreach (['Yes' => 'yes', 'No' => 'no'] as $label => $val): ?>
        <label class="cursor-pointer flex-1">
            <input type="radio"
                   name="<?= $name ?>"
                   value="<?= $val ?>"
                   class="sr-only peer"
                   <?= ($old === $val ? 'checked' : '') ?>
                   <?= $required ?>>
            <span class="block text-center py-2.5 text-sm border border-gray-200 rounded-xl text-gray-600
                         peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600
                         hover:border-blue-300 transition select-none">
                <?= $label ?>
            </span>
        </label>
        <?php endforeach; ?>
    </div>

<?php elseif ($q['type'] === 'scale'): ?>
    <div class="flex flex-wrap gap-2">
        <?php foreach ($q['options'] as $opt): ?>
        <label class="cursor-pointer">
            <input type="radio"
                   name="<?= $name ?>"
                   value="<?= esc($opt['value']) ?>"
                   class="sr-only peer"
                   <?= ($old === $opt['value'] ? 'checked' : '') ?>
                   <?= $required ?>>
            <span class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg text-gray-600
                         peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600
                         hover:border-blue-300 transition select-none">
                <?= esc($opt['option_text']) ?>
            </span>
        </label>
        <?php endforeach; ?>
    </div>

<?php elseif ($q['type'] === 'multiple_choice'): ?>
    <div class="space-y-2">
        <?php foreach ($q['options'] as $opt): ?>
        <label class="flex items-center gap-3 cursor-pointer group">
            <input type="radio"
                   name="<?= $name ?>"
                   value="<?= esc($opt['value']) ?>"
                   class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                   <?= ($old === $opt['value'] ? 'checked' : '') ?>
                   <?= $required ?>>
            <span class="text-sm text-gray-700 group-hover:text-gray-900 transition"><?= esc($opt['option_text']) ?></span>
        </label>
        <?php endforeach; ?>
    </div>

<?php elseif ($q['type'] === 'file_upload'): ?>
    <div x-data="filePreview()" class="space-y-2">
        <label class="flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-xl py-8 px-4 cursor-pointer hover:border-blue-400 transition bg-gray-50"
               @dragover.prevent @drop.prevent="handleDrop($event)">
            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <span class="text-sm text-gray-500">Drop file here or <span class="text-blue-600">browse</span></span>
            <span class="text-xs text-gray-400 mt-0.5">PDF, Word, Excel, Image — max 5 MB</span>
            <input type="file"
                   name="file_response[<?= $q['id'] ?>]"
                   class="hidden"
                   <?= ($q['allow_multiple_files'] ?? false) ? 'multiple' : '' ?>
                   <?= $required ?>
                   @change="handleChange($event)">
        </label>
        <template x-if="filename">
            <p class="text-xs text-gray-600 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span x-text="filename"></span>
            </p>
        </template>
    </div>

<?php endif; ?>

<script>
function filePreview() {
    return {
        filename: '',
        handleChange(event) {
            const files = event.target.files;
            if (files.length === 1) {
                this.filename = files[0].name;
            } else if (files.length > 1) {
                this.filename = files.length + ' files selected';
            }
        },
        handleDrop(event) {
            const input = event.currentTarget.querySelector('input[type=file]');
            input.files = event.dataTransfer.files;
            this.handleChange({ target: input });
        },
    };
}
</script>
</div>
