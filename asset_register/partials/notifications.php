<?php
/**
 * partials/notifications.php
 *
 * Include this once per page — ideally just before </body>.
 * It renders any queued flash messages as Bootstrap 4 toasts and outputs the
 * notify.js polyfill that replaces native alert() with toasts.
 *
 * Usage in a page:
 *   <?php include [path_to] 'partials/notifications.php'; ?>
 */
$_flashMsgs = get_flash_messages();
?>

<!-- ── Toast container ────────────────────────────────────────────────────── -->
<div id="toast-container"
     style="position:fixed;top:20px;right:20px;z-index:9999;min-width:280px;">
<?php foreach ($_flashMsgs as $_fm): ?>
  <div class="toast show mb-2" role="alert" aria-live="assertive"
       data-autohide="true" data-delay="4000">
    <div class="toast-header bg-<?= esc($_fm['type']) ?> text-white">
      <strong class="mr-auto">
        <?= $_fm['type'] === 'danger' ? 'Error' : ucfirst($_fm['type']) ?>
      </strong>
      <button type="button" class="ml-2 mb-1 close text-white"
              data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="toast-body"><?= esc($_fm['message']) ?></div>
  </div>
<?php endforeach; ?>
</div>

<!-- ── notify.js inline polyfill ──────────────────────────────────────────── -->
<script src="<?= str_repeat('../', substr_count(str_replace(
    '/home/user/asset_register/asset_register/', '', __DIR__ . '/'),
    '/')) ?>assets/js/notify.js"></script>
