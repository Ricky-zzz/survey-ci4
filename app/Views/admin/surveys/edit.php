<?= $this->extend('layout_admin') ?>

<?= $this->section('title') ?><?= esc($survey['name']) ?><?= $this->endSection() ?>

<?= $this->section('header_actions') ?>
<div class="flex items-center gap-2" x-data="sharePanel(<?= $survey['id'] ?>)">
    <?php if ($survey['is_public']): ?>
        <a href="<?= base_url('s/' . $survey['id']) ?>" target="_blank"
           class="px-3 py-1.5 text-xs rounded-lg border border-green-300 text-green-700 bg-green-50 hover:bg-green-100 transition">
            View Live
        </a>
        <button @click="copyLink()" title="Copy link"
                class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
            Copy Link
        </button>
    <?php else: ?>
        <button @click="publish()" :disabled="loading"
                class="px-4 py-1.5 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
            <span x-show="!loading">Make Public</span>
            <span x-show="loading" x-cloak>Saving…</span>
        </button>
    <?php endif; ?>
    <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results') ?>"
       class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
        Results
    </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="surveyEditor()" class="space-y-6 max-w-3xl">

    <!-- Survey settings -->
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <details class="group">
            <summary class="cursor-pointer text-sm font-medium text-gray-700 list-none flex items-center justify-between">
                Survey Settings
                <svg class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </summary>
            <form method="POST" action="<?= base_url('admin/surveys/' . $survey['id']) ?>" class="mt-4 space-y-3">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Survey Name</label>
                    <input type="text" name="name" value="<?= esc($survey['name']) ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"><?= esc($survey['description'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Visibility</label>
                    <div class="flex gap-3">
                        <label class="flex-1 flex items-start gap-2.5 border rounded-lg px-3 py-2.5 cursor-pointer has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 transition">
                            <input type="radio" name="is_public" value="1" class="accent-blue-600 mt-0.5"
                                   <?= $survey['is_public'] ? 'checked' : '' ?>>
                            <span class="text-sm">
                                <span class="font-medium text-gray-800">Public</span>
                                <span class="block text-xs text-gray-500">Listed on the home page</span>
                            </span>
                        </label>
                        <label class="flex-1 flex items-start gap-2.5 border rounded-lg px-3 py-2.5 cursor-pointer has-[:checked]:border-gray-400 has-[:checked]:bg-gray-50 transition">
                            <input type="radio" name="is_public" value="0" class="accent-blue-600 mt-0.5"
                                   <?= ! $survey['is_public'] ? 'checked' : '' ?>>
                            <span class="text-sm">
                                <span class="font-medium text-gray-800">Private</span>
                                <span class="block text-xs text-gray-500">Accessible by passcode only</span>
                            </span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex gap-3">
                        <label class="flex-1 flex items-start gap-2.5 border rounded-lg px-3 py-2.5 cursor-pointer has-[:checked]:border-green-500 has-[:checked]:bg-green-50 transition">
                            <input type="radio" name="is_active" value="1" class="accent-green-600 mt-0.5"
                                   <?= $survey['is_active'] ? 'checked' : '' ?>>
                            <span class="text-sm">
                                <span class="font-medium text-gray-800">Active</span>
                                <span class="block text-xs text-gray-500">Open for responses</span>
                            </span>
                        </label>
                        <label class="flex-1 flex items-start gap-2.5 border rounded-lg px-3 py-2.5 cursor-pointer has-[:checked]:border-gray-400 has-[:checked]:bg-gray-50 transition">
                            <input type="radio" name="is_active" value="0" class="accent-blue-600 mt-0.5"
                                   <?= ! $survey['is_active'] ? 'checked' : '' ?>>
                            <span class="text-sm">
                                <span class="font-medium text-gray-800">Draft</span>
                                <span class="block text-xs text-gray-500">Not accessible to respondents</span>
                            </span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Passcode</label>
                    <p class="text-xs text-gray-400 mb-2">Auto-generated. Share with respondents for private access or direct links.</p>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly value="<?= esc($survey['passkey']) ?>" id="passcodeDisplay"
                               class="flex-1 border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm font-mono text-gray-700 cursor-pointer"
                               onclick="this.select()">
                        <button type="button"
                                onclick="navigator.clipboard.writeText(document.getElementById('passcodeDisplay').value).then(()=>{ this.textContent='Copied!'; setTimeout(()=>this.textContent='Copy',1500) })"
                                class="px-3 py-2 text-xs border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                            Copy
                        </button>
                        <button type="submit" name="regenerate_passkey" value="1"
                                onclick="return confirm('Generate a new passcode? The old one will stop working.')"
                                class="px-3 py-2 text-xs border border-orange-200 text-orange-600 rounded-lg hover:bg-orange-50 transition">
                            Regenerate
                        </button>
                    </div>
                </div>
                <button type="submit" class="bg-gray-800 text-white rounded-lg px-4 py-2 text-xs font-medium hover:bg-gray-700 transition">
                    Save Settings
                </button>
            </form>
        </details>
    </div>

    <!-- Default Demographics (Toggleable) -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6" x-data="{ open: false }">
        <button @click="open = !open" type="button"
                class="w-full px-5 py-4 text-left hover:bg-gray-50 flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z"/>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Default Demographics</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Auto-captured on all surveys</p>
                </div>
            </div>
            <svg :class="{'rotate-180': open}" class="w-5 h-5 text-gray-400 transition-transform"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open" x-cloak class="border-t border-gray-100 px-5 py-4 bg-blue-50 space-y-3">
            <p class="text-xs text-blue-700 mb-3">
                These fields are automatically captured from respondents—no need to create sections for them.
            </p>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center bg-white px-2.5 py-1 rounded-lg text-xs font-medium text-blue-700 border border-blue-200">
                    <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mr-1.5"></span>
                    Full Name *
                </span>
                <span class="inline-flex items-center bg-white px-2.5 py-1 rounded-lg text-xs font-medium text-blue-700 border border-blue-200">
                    <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mr-1.5"></span>
                    Email *
                </span>
                <span class="inline-flex items-center bg-white px-2.5 py-1 rounded-lg text-xs font-medium text-gray-600 border border-gray-200">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                    Address
                </span>
                <span class="inline-flex items-center bg-white px-2.5 py-1 rounded-lg text-xs font-medium text-gray-600 border border-gray-200">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                    Age
                </span>
            </div>
            <p class="text-xs text-blue-600 font-medium">* Required fields</p>
        </div>
    </div>

    <!-- Sections -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-sm font-semibold text-gray-700">Sections</h2>
                    <?php if ($hasResponses ?? false): ?>
                    <p class="text-xs text-amber-600 mt-1">
                        This survey has <?= $respondentCount ?> response<?= $respondentCount !== 1 ? 's' : '' ?> — sections and questions cannot be modified.
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (empty($survey['sections'])): ?>
        <div class="px-5 py-8 text-sm text-gray-400 text-center">No sections yet.</div>
        <?php else: ?>
        <div class="divide-y divide-gray-100">
            <?php foreach ($survey['sections'] as $section): ?>
            <?php if ($section['is_respondent_info']) continue; ?>
            <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition group">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/sections/' . $section['id']) ?>"
                           class="text-sm font-medium text-gray-800 hover:text-blue-600 transition truncate">
                            <?= esc($section['title']) ?>
                        </a>
                        <?php if ($section['is_respondent_info']): ?>
                        <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full shrink-0">Respondent Info</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <?= count($section['questions']) ?> question<?= count($section['questions']) !== 1 ? 's' : '' ?>
                        <?php if (! empty($section['description'])): ?>
                        · <?= esc($section['description']) ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/sections/' . $section['id']) ?>"
                       class="text-xs text-gray-400 hover:text-blue-600 transition opacity-0 group-hover:opacity-100 flex items-center gap-1">
                        Manage
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <?php if (! $section['is_respondent_info'] && ! ($hasResponses ?? false)): ?>
                    <form method="POST"
                          action="<?= base_url('admin/surveys/' . $survey['id'] . '/sections/' . $section['id'] . '/delete') ?>"
                          onsubmit="return confirm('Delete this section and all its questions?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="text-gray-300 hover:text-red-500 transition p-1 opacity-0 group-hover:opacity-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Add Section -->
    <?php if (! ($hasResponses ?? false)): ?>
    <div class="bg-white rounded-xl border border-dashed border-gray-300 p-5" x-data="{ open: false }">
        <button @click="open = !open"
                class="w-full text-sm text-gray-500 hover:text-blue-600 font-medium flex items-center justify-center gap-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add section
        </button>
        <div x-show="open" x-cloak class="mt-4">
            <form method="POST" action="<?= base_url('admin/surveys/' . $survey['id'] . '/sections') ?>" class="space-y-3">
                <?= csrf_field() ?>
                <input type="text" name="title" placeholder="Section title"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="text" name="description" placeholder="Description (optional)"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-700 transition">
                    Add Section
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
function surveyEditor() {
    return {};
}

function sharePanel(surveyId) {
    return {
        loading: false,

        async publish() {
            this.loading = true;
            try {
                const res  = await fetch(`<?= base_url('admin/surveys/') ?>${surveyId}/share-link`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.cookie.match(/csrf_cookie_name=([^;]+)/)?.[1] ?? '',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ '<?= csrf_token() ?>': '<?= csrf_hash() ?>' }),
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                }
            } finally {
                this.loading = false;
            }
        },

        copyLink() {
            const url = `<?= base_url('s/') ?>${surveyId}`;
            navigator.clipboard.writeText(url).then(() => alert('Link copied: ' + url));
        },

        async revoke() {
            if (!confirm('Unpublish this survey? The share link will stop working.')) return;
            await fetch(`<?= base_url('admin/surveys/') ?>${surveyId}/revoke-link`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.cookie.match(/csrf_cookie_name=([^;]+)/)?.[1] ?? '',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ '<?= csrf_token() ?>': '<?= csrf_hash() ?>' }),
            });
            window.location.reload();
        },
    };
}
</script>
<?= $this->endSection() ?>
