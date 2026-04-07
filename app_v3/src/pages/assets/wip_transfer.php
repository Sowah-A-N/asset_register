<?php
require_auth();
require_role('hod_ict', 'schedule_officer', 'accountant');
require_once SRC . '/queries/assets.php';

// Fetch all active Building Works in Progress assets
$result = mysqli_query($conn,
    "SELECT asset_id, asset_name, id_number, additions, acquisition_date
     FROM assets
     WHERE asset_class = 'Building Works in Progress'
       AND disposed = 0 AND archived = 0
     ORDER BY asset_name ASC");
$wip_assets = [];
while ($row = mysqli_fetch_assoc($result)) $wip_assets[] = $row;

// Already-transferred log
$transferred = [];
$tr = mysqli_query($conn,
    "SELECT a.asset_id, a.asset_name, a.id_number, a.additions,
            a.transferred_at, b.asset_name AS dest_name, b.id_number AS dest_id
     FROM assets a
     LEFT JOIN assets b ON b.asset_id = a.transferred_to_asset_id
     WHERE a.asset_class = 'Building Works in Progress'
       AND a.transferred_at IS NOT NULL
     ORDER BY a.transferred_at DESC LIMIT 50");
while ($row = mysqli_fetch_assoc($tr)) $transferred[] = $row;

$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'WIP Capitalisation';
$content = function() use ($wip_assets, $transferred, $base_url) {
?>
<div class="mb-4">
    <h4 class="fw-bold mb-0">Building WIP → Capitalisation</h4>
    <p class="text-muted small mb-0">
        Transfer a Building Works in Progress asset into the Land &amp; Buildings class
        once construction is complete.
    </p>
</div>

<?php if ($wip_assets): ?>
<div class="card mb-4">
    <div class="card-header fw-semibold">
        <i class="bi bi-arrow-left-right me-2 text-warning"></i>
        Pending WIP Assets (<?= count($wip_assets) ?>)
    </div>
    <div class="table-responsive">
    <table class="table table-hover table-sm align-middle mb-0">
        <thead>
            <tr>
                <th>Asset Name</th>
                <th>ID No.</th>
                <th class="text-end">Additions (GHS)</th>
                <th>Acquisition Date</th>
                <th class="text-center">Transfer</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($wip_assets as $a): ?>
        <tr>
            <td><?= esc($a['asset_name']) ?></td>
            <td><code class="small"><?= esc($a['id_number']) ?></code></td>
            <td class="text-end"><?= number_format((float)$a['additions'], 2) ?></td>
            <td><?= esc($a['acquisition_date']) ?></td>
            <td class="text-center">
                <button class="btn btn-sm btn-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#transferModal"
                        data-id="<?= (int)$a['asset_id'] ?>"
                        data-name="<?= esc($a['asset_name']) ?>"
                        data-amount="<?= number_format((float)$a['additions'], 2) ?>">
                    <i class="bi bi-arrow-right-circle me-1"></i>Capitalise
                </button>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<?php else: ?>
<div class="alert alert-info">No pending Building WIP assets.</div>
<?php endif; ?>

<!-- Completed transfers log -->
<?php if ($transferred): ?>
<div class="card">
    <div class="card-header fw-semibold">
        <i class="bi bi-check-circle me-2 text-success"></i>Completed Transfers
    </div>
    <div class="table-responsive">
    <table class="table table-sm align-middle mb-0">
        <thead>
            <tr>
                <th>WIP Asset</th>
                <th>Capitalised Into</th>
                <th class="text-end">Value (GHS)</th>
                <th>Transferred At</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($transferred as $t): ?>
        <tr>
            <td><?= esc($t['asset_name']) ?> <code class="small"><?= esc($t['id_number']) ?></code></td>
            <td><?= $t['dest_name'] ? esc($t['dest_name']) : '<span class="text-muted">—</span>' ?></td>
            <td class="text-end"><?= number_format((float)$t['additions'], 2) ?></td>
            <td class="text-muted small text-nowrap"><?= esc($t['transferred_at']) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<?php endif; ?>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-right-circle me-2"></i>Capitalise WIP Asset
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= $base_url ?>/">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="wip_transfer">
            <input type="hidden" name="wip_asset_id" id="modalAssetId">
            <div class="modal-body">
                <p class="mb-1">
                    <strong id="modalAssetName"></strong><br>
                    <span class="text-muted small">Additions: GH₵ <span id="modalAmount"></span></span>
                </p>
                <hr>
                <p class="text-muted small mb-3">
                    This will reclassify the asset to <strong>Land And Buildings</strong>
                    and record the capitalisation date. The WIP asset record will be
                    retained and linked to the new Buildings entry.
                </p>
                <div class="mb-3">
                    <label class="form-label">New Asset Name (in Land &amp; Buildings) <span class="text-danger">*</span></label>
                    <input type="text" name="new_asset_name" class="form-control" required
                           placeholder="e.g. Construction of Dormitory Block A">
                </div>
                <div class="mb-3">
                    <label class="form-label">New ID Number</label>
                    <input type="text" name="new_id_number" class="form-control"
                           placeholder="Leave blank to auto-generate">
                </div>
                <div class="mb-3">
                    <label class="form-label">Sub-Class</label>
                    <input type="text" name="new_sub_class" class="form-control"
                           value="Land And Buildings">
                </div>
                <div class="mb-0">
                    <label class="form-label">Transfer Notes</label>
                    <textarea name="notes" class="form-control" rows="2"
                              placeholder="Reason for capitalisation…"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning"
                        data-confirm="Capitalise this WIP asset? This cannot be undone.">
                    <i class="bi bi-check-lg me-1"></i>Confirm Transfer
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-target="#transferModal"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('modalAssetId').value   = this.dataset.id;
            document.getElementById('modalAssetName').textContent = this.dataset.name;
            document.getElementById('modalAmount').textContent    = this.dataset.amount;
        });
    });
});
</script>
<?php };
require SRC . '/templates/layouts/base.php';
