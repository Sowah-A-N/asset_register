<?php
require_auth();
require_role('hod_ict', 'schedule_officer', 'accountant');

$rates    = [];
$res      = mysqli_query($conn,
    'SELECT * FROM dollar_rate ORDER BY date_added DESC LIMIT 20');
while ($r = mysqli_fetch_assoc($res)) $rates[] = $r;

// Get active rate
$active_row = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT dollar_rate FROM dollar_rate WHERE rate_status='ACTIVE' LIMIT 1"));
$active_rate = $active_row ? (float)$active_row['dollar_rate'] : null;

$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Dollar Rate';
$content = function() use ($rates, $active_rate, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Dollar Rate</h4>
        <small class="text-muted">
            Active rate:
            <?= $active_rate !== null
                ? '<strong class="text-success">GH₵ ' . number_format($active_rate, 4) . '/ $1</strong>'
                : '<span class="text-danger fw-bold">NOT SET</span>' ?>
        </small>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#rateModal">
        <i class="bi bi-currency-dollar me-1"></i>Set New Rate
    </button>
</div>

<div class="table-responsive">
<table class="table table-sm table-hover align-middle">
    <thead><tr><th>Rate (GHS/$)</th><th>Status</th><th>Set By</th><th>Date</th></tr></thead>
    <tbody>
    <?php foreach ($rates as $r): ?>
    <tr>
        <td class="fw-semibold"><?= number_format((float)$r['dollar_rate'], 4) ?></td>
        <td>
            <span class="badge <?= $r['rate_status'] === 'ACTIVE' ? 'bg-success' : 'bg-secondary' ?>">
                <?= esc($r['rate_status']) ?>
            </span>
        </td>
        <td><?= esc($r['action_by']) ?></td>
        <td class="text-muted small text-nowrap"><?= esc($r['date_added']) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<div class="modal fade" id="rateModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Set Dollar Rate</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="post" action="<?= $base_url ?>/">
        <?= csrf_field() ?>
        <input type="hidden" name="_action" value="dollar_rate_save">
        <div class="modal-body">
            <label class="form-label">New Rate (GHS per USD) <span class="text-danger">*</span></label>
            <input type="number" name="dollar_rate" class="form-control"
                   step="0.0001" min="0.0001" required placeholder="e.g. 10.5">
            <div class="form-text mt-2 text-warning">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Setting a new rate will deactivate the current ACTIVE rate.
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary"
                    data-confirm="Set this as the new active rate?">Confirm</button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
        </form>
    </div></div>
</div>
<?php };
require SRC . '/templates/layouts/base.php';
