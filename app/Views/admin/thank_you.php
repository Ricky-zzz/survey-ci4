<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You — <?= esc($survey['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen py-12 px-4">
<div class="max-w-2xl mx-auto space-y-4">

    <!-- Success card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 text-center">
        <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-xl font-semibold text-gray-900 mb-1">Thank you!</h1>
        <p class="text-sm text-gray-500">
            Your response to <strong class="text-gray-700"><?= esc($survey['name']) ?></strong> has been recorded.
        </p>
        <a href="<?= base_url('s') ?>"
           class="inline-block mt-5 text-sm text-blue-600 hover:text-blue-700 hover:underline">
            ← Back to surveys
        </a>
    </div>

    <?php if ($respondentId > 0): ?>
    <!-- Demographics Summary -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Your Information</h2>
        </div>
        <?php  
        // Fetch respondent demographics
        $respondentModel = new \App\Models\RespondentModel();
        $respondent = $respondentModel->find($respondentId);
        ?>
        <div class="px-6 py-5 space-y-4">
            <div>
                <p class="text-xs text-gray-500 mb-1">Full Name</p>
                <p class="text-sm font-medium text-gray-800"><?= esc($respondent['fullname'] ?? '-') ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Email</p>
                <p class="text-sm font-medium text-gray-800"><?= esc($respondent['email'] ?? '-') ?></p>
            </div>
            <?php if (!empty($respondent['address'])): ?>
            <div>
                <p class="text-xs text-gray-500 mb-1">Address</p>
                <p class="text-sm font-medium text-gray-800"><?= esc($respondent['address']) ?></p>
            </div>
            <?php endif; ?>
            <?php if (!empty($respondent['age'])): ?>
            <div>
                <p class="text-xs text-gray-500 mb-1">Age</p>
                <p class="text-sm font-medium text-gray-800"><?= esc($respondent['age']) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($answers) && !empty($survey['sections'])): ?>
    <!-- Response summary -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Your responses</h2>
            <span class="text-xs text-gray-400">Read-only copy</span>
        </div>

        <?php foreach ($survey['sections'] as $section): ?>
            <?php
            // Check if this section has any answered questions
            $hasAnswers = false;
            foreach ($section['questions'] as $q) {
                if (isset($answers[$q['id']])) { $hasAnswers = true; break; }
            }
            if (!$hasAnswers) continue;
            ?>
            <div class="px-6 py-5 border-b border-gray-100 last:border-b-0">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-4">
                    <?= esc($section['title']) ?>
                </p>
                <div class="space-y-5">
                    <?php foreach ($section['questions'] as $q): ?>
                        <?php
                        $answer = $answers[$q['id']] ?? null;
                        $hasAnswer = ($answer !== null && $answer !== '');
                        $hasFiles = isset($files[$q['id']]) && ! empty($files[$q['id']]);
                        
                        if (! $hasAnswer && ! $hasFiles) continue;
                        ?>
                        <div>
                            <p class="text-xs text-gray-500 mb-1"><?= esc($q['question_text']) ?></p>
                            <p class="text-sm font-medium text-gray-800">
                                <?php if ($q['type'] === 'file_upload' && $hasFiles): ?>
                                    <div class="space-y-2">
                                        <?php foreach ($files[$q['id']] as $file): ?>
                                        <div class="flex items-center gap-2 bg-blue-50 px-3 py-2 rounded-lg border border-blue-200">
                                            <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs text-blue-700 truncate"><?= esc($file['original_filename']) ?></p>
                                                <p class="text-xs text-blue-600"><?= round($file['file_size'] / 1024, 1) ?> KB</p>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php elseif ($hasAnswer): ?>
                                    <?= esc($answer) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
</body>
</html>
