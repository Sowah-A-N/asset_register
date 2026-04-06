/**
 * Asset Register v3 — App JS
 * Vanilla JS only; no jQuery.
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Sidebar toggle ──────────────────────────────────────────────────────
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar       = document.getElementById('sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
        });
    }

    // ── Auto-dismiss alerts after 5 s ──────────────────────────────────────
    document.querySelectorAll('.alert.alert-success, .alert.alert-info').forEach(function (el) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        }, 5000);
    });

    // ── Confirm before delete/dispose actions ──────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const msg = el.dataset.confirm || 'Are you sure?';
            if (!window.confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    // ── Number formatting: add comma separators to .fmt-number elements ────
    document.querySelectorAll('.fmt-number').forEach(function (el) {
        const val = parseFloat(el.textContent.replace(/,/g, ''));
        if (!isNaN(val)) {
            el.textContent = val.toLocaleString('en-GH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    });

});
