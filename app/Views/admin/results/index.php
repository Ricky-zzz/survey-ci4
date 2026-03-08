<?= $this->extend('layout_admin') ?>

<?= $this->section('title') ?>Results: <?= esc($survey['name']) ?><?= $this->endSection() ?>

<?= $this->section('header_actions') ?>
<a href="<?= base_url('admin/surveys') ?>"
   class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
    ← Back to Surveys
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Stats -->
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Total Responses</p>
        <p class="text-3xl font-semibold text-gray-800 mt-1"><?= $stats['total_respondents'] ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Completed</p>
        <p class="text-3xl font-semibold text-green-600 mt-1"><?= $stats['completed'] ?></p>
    </div>
</div>

<!-- Respondents table -->
<?php if (empty($respondents)): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-400 text-sm">
        No responses yet.
    </div>
<?php else: ?>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">#</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Submitted</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($respondents as $i => $r): ?>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3 text-gray-500"><?= $i + 1 ?></td>
                <td class="px-5 py-3 text-gray-700">
                    <?= $r['submitted_at'] ? date('M d, Y g:i a', strtotime($r['submitted_at'])) : date('M d, Y g:i a', strtotime($r['created_at'])) ?>
                </td>
                <td class="px-5 py-3">
                    <?php if ($r['submitted_at']): ?>
                        <span class="text-xs bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full">Completed</span>
                    <?php else: ?>
                        <span class="text-xs bg-yellow-50 text-yellow-700 border border-yellow-200 px-2 py-0.5 rounded-full">Incomplete</span>
                    <?php endif; ?>
                </td>
                <td class="px-5 py-3 text-right">
                    <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results/' . $r['id']) ?>"
                       class="text-xs text-blue-600 hover:underline">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?= $this->endSection() ?>
