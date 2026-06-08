<?php
/**
 * FILE: asset_app/partials/chrome_close.php
 * PURPOSE: Adaptive page chrome (close). Mirrors chrome_open.php.
 */
?>
    </main>
<?php if (!empty($RMU_OP)): ?>
    <footer style="padding:.85rem 1.75rem;border-top:1px solid var(--card-border);background:white;font-size:.75rem;color:var(--text-muted);display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
      <span>RMU Asset Register</span><span><?= date('Y') ?> &nbsp;·&nbsp; Regional Maritime University</span>
    </footer>
  </div>
</div>
<?php endif; ?>
