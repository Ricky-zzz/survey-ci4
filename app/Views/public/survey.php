<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($survey['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<div x-data="surveyForm(<?= count($survey['sections']) ?>)"
     x-init="init()">

    <!-- Header with Progress -->
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-2xl mx-auto px-4 py-5">
            <div class="mb-4">
                <h1 class="text-2xl font-semibold text-gray-900"><?= esc($survey['name']) ?></h1>
                <?php if ($survey['description']): ?>
                    <p class="text-sm text-gray-500 mt-1"><?= esc($survey['description']) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <div class="flex items-center justify-between text-xs text-gray-400 mb-1.5">
                    <span x-text="sectionTitle()"></span>
                    <span x-text="(currentStep + 1) + ' / ' + totalSteps"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-300"
                         :style="`width: ${((currentStep + 1) / totalSteps) * 100}%`"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 py-10">

    <!-- Validation Errors (from server) -->
    <?php if ($ve = session()->getFlashdata('validation_errors')): ?>
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm" id="errorBox">
        <p class="font-medium mb-2">Please answer the following required fields:</p>
        <ul class="list-disc list-inside space-y-1">
            <?php foreach ($ve as $qid => $err): ?>
                <li><a href="#question-<?= $qid ?>" class="hover:underline"><?= esc($err) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script>
        // Scroll to first error and set currentStep to the section containing the error
        document.addEventListener('DOMContentLoaded', function() {
            const firstErrorLink = document.querySelector('#errorBox a');
            if (firstErrorLink) {
                firstErrorLink.click();
                const firstError = document.querySelector(firstErrorLink.hash);
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    </script>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('s/' . $survey['id'] . '/submit') ?>"
          enctype="multipart/form-data" id="surveyForm">
        <?= csrf_field() ?>

        <?php foreach ($survey['sections'] as $sIndex => $section): ?>
        <!-- Section <?= $sIndex ?> -->
        <div x-show="currentStep === <?= $sIndex ?>" x-cloak
             class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800"><?= esc($section['title']) ?></h2>
                <?php if ($section['description']): ?>
                    <p class="text-sm text-gray-500 mt-0.5"><?= esc($section['description']) ?></p>
                <?php endif; ?>
            </div>

            <div class="divide-y divide-gray-50">
                <?php
                // Group scale questions with the same matrix_group_id together
                $renderedGroups = [];
                $questions      = $section['questions'];
                $rendered       = [];
                ?>
                <?php foreach ($questions as $qi => $q): ?>
                    <?php if (!is_array($q) || in_array($qi, $rendered, true)) continue; ?>
                    <?php
                    $matrixGroup = $q['matrix_group_id'];
                    if ($matrixGroup && $q['type'] === 'scale'):
                        // Collect all members of this matrix group in this section
                        $matrixQuestions = array_filter($questions, fn($mq) => $mq['matrix_group_id'] === $matrixGroup && $mq['type'] === 'scale');
                        foreach (array_keys($matrixQuestions) as $mk) { $rendered[] = $mk; }
                    ?>
                        <!-- Matrix group — table layout -->
                        <div class="px-6 py-5" id="question-<?= $matrixGroup ?>">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-4">Rate each of the following</p>
                            <?php $matrixOptions = array_values($matrixQuestions)[0]['options']; ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border-collapse">
                                    <thead>
                                        <tr>
                                            <th class="text-left pb-3 pr-6 text-xs font-medium text-gray-400 w-2/5"></th>
                                            <?php foreach ($matrixOptions as $opt): ?>
                                            <th class="pb-3 px-3 text-center text-xs font-medium text-gray-500 whitespace-nowrap">
                                                <?= esc($opt['option_text']) ?>
                                            </th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($matrixQuestions as $mq): ?>
                                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                                            <td class="py-3 pr-6 text-sm text-gray-800">
                                                <?= esc($mq['question_text']) ?>
                                                <?php if ($mq['required']): ?><span class="text-red-500 ml-0.5">*</span><?php endif; ?>
                                            </td>
                                            <?php foreach ($matrixOptions as $opt): ?>
                                            <td class="py-3 px-3 text-center">
                                                <label class="cursor-pointer inline-flex items-center justify-center">
                                                    <input type="radio"
                                                           name="responses[<?= $mq['id'] ?>]"
                                                           value="<?= esc($opt['value']) ?>"
                                                           class="w-4 h-4 accent-blue-600"
                                                           <?= ($mq['required'] ? 'required' : '') ?>>
                                                </label>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php $rendered[] = $qi; ?>
                        <!-- Regular question -->
                        <div class="px-6 py-5">
                            <?php include APPPATH . 'Views/components/question_field.php'; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Navigation -->
        <div class="mt-6 flex items-center justify-between">
            <button type="button"
                    @click="prev()"
                    x-show="currentStep > 0"
                    x-cloak
                    class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-100 transition">
                Back
            </button>
            <div x-show="currentStep === 0" class="invisible w-1"></div>

            <button type="button"
                    @click="next()"
                    x-show="currentStep < totalSteps - 1"
                    class="ml-auto px-6 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition">
                Next
            </button>

            <button type="submit"
                    x-show="currentStep === totalSteps - 1"
                    class="ml-auto px-6 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition">
                Submit
            </button>
        </div>
    </form>
</div>

<script>
function surveyForm(total) {
    return {
        currentStep: 0,
        totalSteps:  total,
        sectionTitles: <?= json_encode(array_column($survey['sections'], 'title')) ?>,
        errorQuestionIds: <?= json_encode(array_keys(session()->getFlashdata('validation_errors') ?? [])) ?>,

        init() {
            // If there are validation errors, go to the first section with an error
            if (this.errorQuestionIds.length > 0) {
                // Find the section index for the first error question
                const sections = document.querySelectorAll('[x-show]');
                for (let sIdx = 0; sIdx < sections.length; sIdx++) {
                    const section = sections[sIdx];
                    const hasError = this.errorQuestionIds.some(qid => 
                        section.querySelector(`#question-${qid}`) !== null
                    );
                    if (hasError) {
                        this.currentStep = sIdx;
                        this.$nextTick(() => {
                            const firstError = document.querySelector('#errorBox a');
                            if (firstError) {
                                firstError.click();
                                document.querySelector(firstError.hash)?.scrollIntoView({ 
                                    behavior: 'smooth', 
                                    block: 'center' 
                                });
                            }
                        });
                        break;
                    }
                }
            }
        },

        sectionTitle() {
            return this.sectionTitles[this.currentStep] ?? '';
        },

        next() {
            // Basic client-side required check for current step
            const section = document.querySelectorAll('[x-show]')[this.currentStep];
            const invalid = section ? section.querySelectorAll('[required]') : [];
            for (const el of invalid) {
                if (!el.value) {
                    el.focus();
                    el.reportValidity();
                    return;
                }
            }
            if (this.currentStep < this.totalSteps - 1) {
                this.currentStep++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        prev() {
            if (this.currentStep > 0) {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
    };
}
</script>
</body>
</html>
