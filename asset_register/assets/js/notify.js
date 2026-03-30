/**
 * notify.js — replaces native alert() with Bootstrap 4 toast notifications.
 *
 * Drop this file in and include it once per layout. No page-level changes needed.
 *
 * Existing PHP code that does:
 *   echo '<script>alert("Asset saved."); window.location="index.php";</script>';
 *
 * …will automatically show a toast instead of a blocking dialog.
 * The window.location redirect still fires; the toast appears briefly before
 * the navigation.
 *
 * For programmatic use from new code:
 *   notify('Asset saved successfully.', 'success');
 *   notify('Duplicate GRV number.', 'danger');
 */

(function () {
  'use strict';

  // ── Toast container ────────────────────────────────────────────────────────
  var container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.setAttribute('aria-live', 'polite');
    container.style.cssText =
      'position:fixed;top:20px;right:20px;z-index:9999;min-width:300px;max-width:380px;';
    document.body.appendChild(container);
  }

  // ── Helpers ────────────────────────────────────────────────────────────────
  function escapeHtml(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(String(str)));
    return d.innerHTML;
  }

  var TYPE_CLASSES = {
    success: 'bg-success text-white',
    danger:  'bg-danger  text-white',
    warning: 'bg-warning text-dark',
    info:    'bg-info    text-white',
  };

  /**
   * Show a Bootstrap 4 toast.
   *
   * @param {string} message  Plain-text message.
   * @param {string} [type]   Bootstrap colour key: success | danger | warning | info.
   *                          Defaults to 'info'.
   * @param {number} [delay]  Auto-hide delay in ms (default 4000).
   */
  function notify(message, type, delay) {
    type  = TYPE_CLASSES[type] ? type : 'info';
    delay = (typeof delay === 'number') ? delay : 4000;

    var headerClass = TYPE_CLASSES[type];
    var label = { success: 'Success', danger: 'Error', warning: 'Warning', info: 'Notice' }[type];

    var toast = document.createElement('div');
    toast.className = 'toast mb-2';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML =
      '<div class="toast-header ' + headerClass + '">' +
        '<strong class="mr-auto">' + label + '</strong>' +
        '<button type="button" class="ml-2 mb-1 close" style="color:inherit" ' +
                'data-dismiss="toast" aria-label="Close">' +
          '<span aria-hidden="true">&times;</span>' +
        '</button>' +
      '</div>' +
      '<div class="toast-body">' + escapeHtml(message) + '</div>';

    container.appendChild(toast);

    // Bootstrap 4 jQuery-based toast
    if (typeof $ !== 'undefined' && $.fn && $.fn.toast) {
      $(toast).toast({ autohide: true, delay: delay }).toast('show');
      $(toast).on('hidden.bs.toast', function () { toast.parentNode && toast.parentNode.removeChild(toast); });
    } else {
      // Fallback: plain CSS show/hide when jQuery is unavailable
      toast.style.cssText = 'display:block;opacity:1;transition:opacity 0.4s';
      setTimeout(function () {
        toast.style.opacity = '0';
        setTimeout(function () { toast.parentNode && toast.parentNode.removeChild(toast); }, 400);
      }, delay);
    }
  }

  // ── Override window.alert ──────────────────────────────────────────────────
  /**
   * Detect message "type" from common keywords so we pick the right colour
   * without touching any PHP source file.
   */
  function detectType(msg) {
    var m = String(msg).toLowerCase();
    if (/error|fail|invalid|wrong|exist|cannot|not found|expired|denied|duplicate|negative/i.test(m)) return 'danger';
    if (/warning|check|confirm/i.test(m)) return 'warning';
    if (/success|added|saved|updated|changed|moved|archived|disposed|deleted/i.test(m)) return 'success';
    return 'info';
  }

  window._nativeAlert = window.alert;   // preserve original in case needed
  window.alert = function (msg) {
    notify(String(msg), detectType(msg));
  };

  // ── Expose globally ────────────────────────────────────────────────────────
  window.notify = notify;

})();
