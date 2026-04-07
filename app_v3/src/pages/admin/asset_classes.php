<?php
require_auth();
require_role('hod_ict', 'schedule_officer', 'accountant');
require_once SRC . '/queries/asset_classes.php';

$classes  = get_all_asset_classes($conn);
$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Asset Classes';
$content = function() use ($classes, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Asset Classes</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#classModal">
        <i class="bi bi-plus-lg me-1"></i>Add Class
    </button>
</div>

<div class="table-responsive">
<table class="table table-hover table-sm align-middle">
    <thead>
        <tr>
            <th>Asset Class</th>
            <th class="text-center">Dep. Rate</th>
            <th class="text-center">Est. Life (yrs)</th>
            <th class="text-center">Depreciates</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($classes as $c): ?>
    <tr>
        <td><?= esc($c['asset_class']) ?></td>
        <td class="text-center"><?= number_format($c['dep_rate'] * 100, 1) ?>%</td>
        <td class="text-center"><?= (int)$c['estimated_life'] ?></td>
        <td class="text-center">
            <?= $c['depreciated']
                ? '<i class="bi bi-check-circle-fill text-success"></i>'
                : '<i class="bi bi-x-circle-fill text-secondary"></i>' ?>
        </td>
        <td class="text-center">
            <button class="btn btn-xs btn-outline-primary"
                    data-bs-toggle="modal" data-bs-target="#editClassModal"
                    data-id="<?= (int)$c['ast_id'] ?>"
                    data-name="<?= esc($c['asset_class']) ?>"
                    data-rate="<?= (float)$c['dep_rate'] ?>"
                    data-life="<?= (int)$c['estimated_life'] ?>"
                    data-dep="<?= (int)$c['depreciated'] ?>">
                <i class="bi bi-pencil"></i>
            </button>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="classModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Asset Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= $base_url ?>/">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="asset_class_save">
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label">Class Name <span class="text-danger">*</span></label>
                    <input type="text" name="asset_class" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Depreciation Rate</label>
                    <div class="input-group">
                        <input type="number" name="dep_rate" class="form-control"
                               step="0.001" min="0" max="1" value="0.1">
                        <span class="input-group-text">%×100</span>
                    </div>
                    <div class="form-text">e.g. 0.2 = 20%</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estimated Life (yrs)</label>
                    <input type="number" name="estimated_life" class="form-control"
                           min="0" value="5">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Depreciates?</label>
                    <select name="depreciated" class="form-select">
                        <option value="1">Yes</option>
                        <option value="0">No (WIP etc.)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Asset Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= $base_url ?>/">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="asset_class_save">
            <input type="hidden" name="ast_id" id="editClassId">
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label">Class Name</label>
                    <input type="text" id="editClassName" name="asset_class" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Dep. Rate (decimal)</label>
                    <input type="number" id="editClassRate" name="dep_rate"
                           class="form-control" step="0.001" min="0" max="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Est. Life (yrs)</label>
                    <input type="number" id="editClassLife" name="estimated_life"
                           class="form-control" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Depreciates?</label>
                    <select id="editClassDep" name="depreciated" class="form-select">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>

<style>.btn-xs{padding:.15rem .4rem;font-size:.75rem;}</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-bs-target="#editClassModal"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('editClassId').value   = this.dataset.id;
            document.getElementById('editClassName').value = this.dataset.name;
            document.getElementById('editClassRate').value = this.dataset.rate;
            document.getElementById('editClassLife').value = this.dataset.life;
            document.getElementById('editClassDep').value  = this.dataset.dep;
        });
    });
});
</script>
<?php };
require SRC . '/templates/layouts/base.php';
