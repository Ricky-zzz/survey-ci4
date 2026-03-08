<?= $this->extend('layout_admin') ?>

<?= $this->section('title') ?>New Survey<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-5">Create Survey</h2>
        <form method="POST" action="<?= base_url('admin/surveys') ?>" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Survey Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="<?= esc(old('name')) ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g. Customer Feedback Survey" autofocus>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                          placeholder="Optional short description shown to respondents"><?= esc(old('description')) ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Visibility</label>
                <div class="flex gap-3">
                    <label class="flex-1 flex items-start gap-2.5 border rounded-lg px-3 py-2.5 cursor-pointer has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 transition">
                        <input type="radio" name="is_public" value="1" class="accent-blue-600 mt-0.5">
                        <span class="text-sm">
                            <span class="font-medium text-gray-800">Public</span>
                            <span class="block text-xs text-gray-500">Listed on the home page</span>
                        </span>
                    </label>
                    <label class="flex-1 flex items-start gap-2.5 border rounded-lg px-3 py-2.5 cursor-pointer has-[:checked]:border-gray-400 has-[:checked]:bg-gray-50 transition">
                        <input type="radio" name="is_public" value="0" class="accent-blue-600 mt-0.5" checked>
                        <span class="text-sm">
                            <span class="font-medium text-gray-800">Private</span>
                            <span class="block text-xs text-gray-500">Accessible by passcode only</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-blue-600 text-white rounded-lg px-5 py-2 text-sm font-medium hover:bg-blue-700 transition">
                    Create & Add Questions
                </button>
                <a href="<?= base_url('admin/dashboard') ?>"
                   class="px-5 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
