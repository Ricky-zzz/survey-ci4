<?= $this->extend('layout_admin') ?>

<?= $this->section('title') ?>Results: <?= esc($survey['name']) ?><?= $this->endSection() ?>

<?= $this->section('header_actions') ?>
<a href="<?= base_url('admin/surveys') ?>"
   class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
    ← Back to Surveys
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Filter Section -->
<div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
    <h2 class="text-sm font-semibold text-gray-900 mb-4">Filters</h2>
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-2">Age Range</label>
            <div class="flex gap-2">
                <input type="number" name="age_min" placeholder="Min"
                       value="<?= esc($filters['age_min'] ?? '') ?>"
                       class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="number" name="age_max" placeholder="Max"
                       value="<?= esc($filters['age_max'] ?? '') ?>"
                       class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-700 mb-2">Address</label>
            <input type="text" name="address" placeholder="Search address..."
                   value="<?= esc($filters['address'] ?? '') ?>"
                   class="w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg px-4 py-2 transition">
            Apply Filters
        </button>

        <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results') ?>"
           class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm rounded-lg px-4 py-2 transition">
            Clear
        </a>
    </form>
</div>

<!-- Tabs -->
<div class="mb-6 border-b border-gray-200">
    <div class="flex gap-8">
        <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results') ?>" 
           class="px-3 py-3 border-b-2 border-blue-600 text-sm font-medium text-blue-600">
            Respondents
        </a>
        <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results/analytics') . (($filters['age_min'] || $filters['age_max'] || $filters['address']) ? '?age_min=' . ($filters['age_min'] ?? '') . '&age_max=' . ($filters['age_max'] ?? '') . '&address=' . urlencode($filters['address'] ?? '') : '') ?>"
           class="px-3 py-3 border-b-2 border-transparent text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300">
            Analytics
        </a>
    </div>
</div>

<!-- Stats -->
<div class=" gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Total Responses</p>
        <p class="text-3xl font-semibold text-gray-800 mt-1"><?= $stats['total_respondents'] ?></p>
    </div>
</div>

<!-- Respondents table -->
<?php if (empty($respondents)): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-400 text-sm">
        No responses found with current filters.
    </div>
<?php else: ?>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">#</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Name</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Email</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Age</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Submitted</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($respondents as $i => $r): ?>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3 text-gray-500"><?= $i + 1 ?></td>
                <td class="px-5 py-3 text-gray-700"><?= esc($r['fullname'] ?? '-') ?></td>
                <td class="px-5 py-3 text-gray-700"><?= esc($r['email'] ?? '-') ?></td>
                <td class="px-5 py-3 text-gray-700"><?= esc($r['age'] ?? '-') ?></td>
                <td class="px-5 py-3 text-gray-700">
                    <?= $r['submitted_at'] ? date('M d, Y g:i a', strtotime($r['submitted_at'])) : date('M d, Y g:i a', strtotime($r['created_at'])) ?>
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
