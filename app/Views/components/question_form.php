<?php
// Variables expected: $action, $question (array|null), $surveyId, $sectionId, $questionId
$q    = $question ?? null;
$type = $q['type'] ?? 'text';
?>
<form method="POST" action="<?= $action ?>"
      x-data="questionForm('<?= $type ?>')"
      class="space-y-3 border border-gray-200 rounded-xl p-4 bg-white">
    <?= csrf_field() ?>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Question <span class="text-red-500">*</span></label>
        <input type="text" name="question_text"
               value="<?= esc($q['question_text'] ?? '') ?>"
               placeholder="Enter your question…"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
            <select name="type" x-model="type"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="text">Short Text</option>
                <option value="yesno">Yes / No</option>
                <option value="scale">Scale (options)</option>
                <option value="multiple_choice">Multiple Choice</option>
                <option value="file_upload">File Upload</option>
            </select>
        </div>

        <div class="flex flex-col justify-end gap-2">
            <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-600">
                <input type="checkbox" name="required" value="1"
                       <?= ($q['required'] ?? 1) ? 'checked' : '' ?>
                       class="rounded border-gray-300 text-blue-600">
                Required
            </label>
            <label x-show="type === 'file_upload'" x-cloak
                   class="flex items-center gap-2 cursor-pointer text-xs text-gray-600">
                <input type="checkbox" name="allow_multiple_files" value="1"
                       <?= ($q['allow_multiple_files'] ?? 0) ? 'checked' : '' ?>
                       class="rounded border-gray-300 text-blue-600">
                Allow multiple files
            </label>
        </div>
    </div>

    <!-- Matrix group (optional) -->
    <div x-show="type === 'scale'" x-cloak>
        <label class="block text-xs font-medium text-gray-600 mb-1">Matrix Group ID <span class="text-gray-400">(optional — group scale questions into a matrix)</span></label>
        <input type="text" name="matrix_group_id"
               value="<?= esc($q['matrix_group_id'] ?? '') ?>"
               placeholder="e.g. service-quality"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <!-- Options (scale / multiple_choice) -->
    <div x-show="type === 'scale' || type === 'multiple_choice'" x-cloak>
        <label class="block text-xs font-medium text-gray-600 mb-2">Options</label>
        <div x-data="optionsEditor(<?= json_encode($q['options'] ?? []) ?>)" class="space-y-2">
            <template x-for="(opt, i) in options" :key="i">
                <div class="flex gap-2 items-center">
                    <input type="text" :name="`option_text[${i}]`" x-model="opt.option_text"
                           @input="opt.value = opt.option_text"
                           placeholder="Option label"
                           class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="hidden" :name="`option_value[${i}]`" :value="opt.option_text">
                    <button type="button" @click="remove(i)"
                            class="text-gray-300 hover:text-red-500 transition px-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>
            <button type="button" @click="add()"
                    class="text-xs text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 transition mt-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add option
            </button>
        </div>
    </div>

    <div class="pt-1">
        <button type="submit"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 text-xs font-medium hover:bg-blue-700 transition">
            <?= $q ? 'Update Question' : 'Add Question' ?>
        </button>
    </div>
</form>

<script>
function questionForm(initialType) {
    return { type: initialType };
}

function optionsEditor(initialOptions) {
    return {
        options: initialOptions.length ? initialOptions.map(o => ({ option_text: o.option_text, value: o.value })) : [],
        add()   { this.options.push({ option_text: '', value: '' }); },
        remove(i) { this.options.splice(i, 1); },
    };
}
</script>
