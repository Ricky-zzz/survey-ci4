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

        <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results/analytics') ?>"
           class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm rounded-lg px-4 py-2 transition">
            Clear
        </a>
    </form>
</div>

<!-- Tabs -->
<div class="mb-6 border-b border-gray-200">
    <div class="flex gap-8">
        <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results') . (($filters['age_min'] || $filters['age_max'] || $filters['address']) ? '?age_min=' . ($filters['age_min'] ?? '') . '&age_max=' . ($filters['age_max'] ?? '') . '&address=' . urlencode($filters['address'] ?? '') : '') ?>" 
           class="px-3 py-3 border-b-2 border-transparent text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300">
            Respondents
        </a>
        <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results/analytics') ?>"
           class="px-3 py-3 border-b-2 border-blue-600 text-sm font-medium text-blue-600">
            Analytics
        </a>
    </div>
</div>

<!-- Stats -->
<div class="gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Total Responses</p>
        <p class="text-3xl font-semibold text-gray-800 mt-1"><?= $stats['total_respondents'] ?></p>
    </div>
</div>

<!-- Demographics -->
<?php
$chartColors = [
    '#2563eb', '#10b981', '#f59e0b', '#ef4444',
    '#14b8a6', '#f97316', '#84cc16', '#0ea5e9',
    '#64748b', '#22c55e', '#eab308', '#dc2626'
];
?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900">Age Distribution</h3>
            <span class="text-xs text-gray-400"><?= $ageAnalytics['total'] ?? 0 ?> respondents</span>
        </div>
        <?php if (empty($ageAnalytics['items'])): ?>
            <div class="text-sm text-gray-400">No age data available.</div>
        <?php else: ?>
            <div style="height: 500px;">
                <canvas id="agePie"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-1 gap-2 text-xs text-gray-700">
                <?php foreach ($ageAnalytics['items'] as $idx => $item): ?>
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="h-2.5 w-2.5 rounded-sm" style="background-color: <?= $chartColors[$idx % count($chartColors)] ?>"></span>
                            <span class="truncate font-semibold text-gray-800"><?= esc($item['label']) ?></span>
                        </div>
                        <span class="text-gray-600 font-semibold"><?= $item['count'] ?> (<?= $item['percent'] ?>%)</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900">Address Distribution</h3>
            <span class="text-xs text-gray-400"><?= $addressAnalytics['total'] ?? 0 ?> respondents</span>
        </div>
        <?php if (empty($addressAnalytics['items'])): ?>
            <div class="text-sm text-gray-400">No address data available.</div>
        <?php else: ?>
            <div style="height: 500px;">
                <canvas id="addressPie"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-1 gap-2 text-xs text-gray-700">
                <?php foreach ($addressAnalytics['items'] as $idx => $item): ?>
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="h-2.5 w-2.5 rounded-sm" style="background-color: <?= $chartColors[$idx % count($chartColors)] ?>"></span>
                            <span class="truncate font-semibold text-gray-800"><?= esc($item['label']) ?></span>
                        </div>
                        <span class="text-gray-600 font-semibold"><?= $item['count'] ?> (<?= $item['percent'] ?>%)</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Question Analytics -->
<div class="space-y-6">
    <?php foreach ($survey['sections'] as $section): ?>
        <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3"><?= esc($section['title']) ?></h3>
            <div class="space-y-4">
                <?php foreach ($section['questions'] as $question): ?>
                    <?php 
                    $qAnalytics = $questionAnalytics[$question['id']] ?? null;
                    if (!$qAnalytics) continue;
                    $stats = $qAnalytics['stats'];
                    ?>
                    
                    <?php if ($question['type'] === 'yesno'): ?>
                        <!-- Yes/No Question -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-4">
                                <?= esc($question['question_text']) ?>
                                <span class="text-xs text-gray-400 ml-2">(<?= $stats['total'] ?? 0 ?> responses)</span>
                            </h4>
                            <div class="space-y-3">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-sm text-gray-600">Yes</span>
                                        <span class="text-sm font-medium text-gray-900"><?= $stats['yes_percent'] ?? 0 ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: <?= $stats['yes_percent'] ?? 0 ?>%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1"><?= $stats['yes'] ?? 0 ?> person<?= ($stats['yes'] ?? 0) !== 1 ? 's' : '' ?></p>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-sm text-gray-600">No</span>
                                        <span class="text-sm font-medium text-gray-900"><?= $stats['no_percent'] ?? 0 ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-red-500 h-2 rounded-full" style="width: <?= $stats['no_percent'] ?? 0 ?>%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1"><?= $stats['no'] ?? 0 ?> person<?= ($stats['no'] ?? 0) !== 1 ? 's' : '' ?></p>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($question['type'] === 'multiple_choice'): ?>
                        <!-- Multiple Choice Question -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-4">
                                <?= esc($question['question_text']) ?>
                                <span class="text-xs text-gray-400 ml-2">(<?= $stats['count'] ?? 0 ?> responses)</span>
                            </h4>
                            <div class="space-y-3">
                                <?php foreach ($stats['distribution'] ?? [] as $option => $data): ?>
                                    <div class="<?= $data['count'] == 0 ? 'opacity-50' : '' ?>">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-sm <?= $data['count'] == 0 ? 'text-gray-400' : 'text-gray-600' ?>"><?= esc($option) ?></span>
                                            <span class="text-sm font-medium <?= $data['count'] == 0 ? 'text-gray-400' : 'text-gray-900' ?>"><?= $data['percent'] ?>%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-500 h-2 rounded-full" style="width: <?= $data['percent'] ?>%"></div>
                                        </div>
                                        <p class="text-xs <?= $data['count'] == 0 ? 'text-gray-300' : 'text-gray-500' ?> mt-1"><?= $data['count'] ?> person<?= $data['count'] !== 1 ? 's' : '' ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <?php elseif ($question['type'] === 'scale'): ?>
                        <!-- Scale Question -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-4">
                                <?= esc($question['question_text']) ?>
                                <span class="text-xs text-gray-400 ml-2">(<?= $stats['count'] ?? 0 ?> responses)</span>
                            </h4>
                            <div class="space-y-3">
                                <?php foreach ($stats['distribution'] ?? [] as $value => $count): ?>
                                    <div class="<?= $count == 0 ? 'opacity-50' : '' ?>">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-sm <?= $count == 0 ? 'text-gray-400' : 'text-gray-600' ?>">Rating <?= esc($value) ?></span>
                                            <span class="text-sm font-medium <?= $count == 0 ? 'text-gray-400' : 'text-gray-900' ?>"><?= $count > 0 ? round(($count / ($stats['count'] ?? 1)) * 100) : 0 ?>%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-purple-500 h-2 rounded-full" style="width: <?= $count > 0 ? round(($count / ($stats['count'] ?? 1)) * 100) : 0 ?>%"></div>
                                        </div>
                                        <p class="text-xs <?= $count == 0 ? 'text-gray-300' : 'text-gray-500' ?> mt-1"><?= $count ?> person<?= $count !== 1 ? 's' : '' ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (isset($stats['average']) && $stats['average'] > 0): ?>
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <p class="text-sm text-gray-600">Average: <span class="font-semibold text-gray-900"><?= round($stats['average'], 2) ?></span></p>
                                </div>
                            <?php endif; ?>
                        </div>

                    <?php elseif ($question['type'] === 'text'): ?>
                        <!-- Text Question -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">
                                <?= esc($question['question_text']) ?>
                            </h4>
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-600"><?= $stats['count'] ?? 0 ?> response<?= ($stats['count'] ?? 0) !== 1 ? 's' : '' ?></p>
                                <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results/questions/' . $question['id'] . '/text-responses') . (($filters['age_min'] || $filters['age_max'] || $filters['address']) ? '?age_min=' . ($filters['age_min'] ?? '') . '&age_max=' . ($filters['age_max'] ?? '') . '&address=' . urlencode($filters['address'] ?? '') : '') ?>"
                                   class="text-xs text-blue-600 hover:underline">View All</a>
                            </div>
                        </div>

                    <?php elseif ($question['type'] === 'file_upload'): ?>
                        <!-- File Upload Question -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">
                                <?= esc($question['question_text']) ?>
                            </h4>
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-600"><?= $stats['count'] ?? 0 ?> submission<?= ($stats['count'] ?? 0) !== 1 ? 's' : '' ?></p>
                                <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results/questions/' . $question['id'] . '/file-responses') . (($filters['age_min'] || $filters['age_max'] || $filters['address']) ? '?age_min=' . ($filters['age_min'] ?? '') . '&age_max=' . ($filters['age_max'] ?? '') . '&address=' . urlencode($filters['address'] ?? '') : '') ?>"
                                   class="text-xs text-blue-600 hover:underline">View All</a>
                            </div>
                        </div>

                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script>
const ageItems = <?= json_encode($ageAnalytics['items'] ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const addressItems = <?= json_encode($addressAnalytics['items'] ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

const chartColors = <?= json_encode($chartColors, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

function buildPieChart(canvasId, items) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || items.length === 0) return;

    const labels = items.map(item => item.label);
    const data = items.map(item => item.count);

    new Chart(canvas, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: labels.map((_, idx) => chartColors[idx % chartColors.length]),
                borderWidth: 1,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    color: '#ffffff',
                    formatter: (value, ctx) => {
                        const dataset = ctx.chart.data.datasets[0] || { data: [] };
                        const total = dataset.data.reduce((sum, item) => sum + item, 0) || 0;
                        if (total === 0) return '';
                        const percent = Math.round((value / total) * 100);
                        return `${percent}%`;
                    },
                    font: { weight: '600', size: 10 },
                    textStrokeColor: 'rgba(0,0,0,0.35)',
                    textStrokeWidth: 2,
                    clip: true
                },
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `${ctx.label}: ${ctx.parsed}`
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
}

buildPieChart('agePie', ageItems);
buildPieChart('addressPie', addressItems);
</script>

<?= $this->endSection() ?>
