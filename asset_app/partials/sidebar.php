<?php
/**
 * Schedule Officer — Enterprise Sidebar (v2)
 * Include from any schedule_officer/[feature]/index.php:
 *   require_once '../partials/sidebar.php';
 */

// Active-state helper — matches on URL path segment
$_uri = $_SERVER['REQUEST_URI'] ?? '';
function _sActive(string ...$segments): string {
    global $_uri;
    foreach ($segments as $s) {
        if ($s && str_contains($_uri, $s)) return ' active';
    }
    return '';
}
function _sOpen(array $segments): string {
    global $_uri;
    foreach ($segments as $s) {
        if ($s && str_contains($_uri, $s)) return ' show';
    }
    return '';
}

$_assetPages  = ['new_asset','view_assets','view_untracked','untracked_assets','asset_class_sub_class','moved_assets','reclassify','disposals','archived_assets','view_asset_class','view_asset_location','asset_type'];
$_archivePages = ['archived_assets','archived_locations','archived_suppliers'];
?>
<aside class="rmu-sidebar" id="rmsSidebar">

  <!-- Brand -->
  <div class="sidebar-brand">
    <img src="../../assets/img/rmu.png" alt="RMU"
         class="sidebar-brand-logo" style="background:#fff;object-fit:contain;padding:3px">
    <div>
      <div class="sidebar-brand-name">Asset Register</div>
      <div class="sidebar-brand-sub">Regional Maritime University</div>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="sidebar-nav">
    <ul class="list-unstyled mb-0">

      <!-- MAIN -->
      <li><span class="sidebar-section-label">Main</span></li>

      <li class="sidebar-item">
        <a href="../dashboard/" class="sidebar-link<?= _sActive('dashboard') ?>">
          <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
      </li>
      <li class="sidebar-item">
        <a href="../set_rate/" class="sidebar-link<?= _sActive('set_rate') ?>">
          <i class="bi bi-currency-dollar"></i> Dollar Rate
        </a>
      </li>
      <li class="sidebar-item">
        <a href="../supplier/" class="sidebar-link<?= _sActive('supplier') ?>">
          <i class="bi bi-truck"></i> Suppliers
        </a>
      </li>

      <!-- ASSETS -->
      <li><span class="sidebar-section-label">Assets</span></li>

      <li class="sidebar-item">
        <a class="sidebar-link<?= _sActive(...$_assetPages) ?>"
           data-bs-toggle="collapse" data-bs-target="#sideAssets"
           aria-expanded="<?= _sOpen($_assetPages) === ' show' ? 'true' : 'false' ?>"
           aria-controls="sideAssets" role="button">
          <i class="bi bi-box-seam"></i>
          <span>Asset Management</span>
          <i class="bi bi-chevron-right si-arrow"></i>
        </a>
        <ul class="sidebar-submenu collapse<?= _sOpen($_assetPages) ?>" id="sideAssets">
          <li><a href="../new_asset/" class="sidebar-link<?= _sActive('new_asset') ?>">
            <i class="bi bi-plus-circle"></i> Add New Asset
          </a></li>
          <li><a href="../view_assets/" class="sidebar-link<?= _sActive('view_assets') ?>">
            <i class="bi bi-list-ul"></i> Active Assets
          </a></li>
          <li><a href="../view_untracked/" class="sidebar-link<?= _sActive('view_untracked') ?>">
            <i class="bi bi-eye"></i> Untracked Assets
          </a></li>
          <li><a href="../untracked_assets/" class="sidebar-link<?= _sActive('untracked_assets') ?>">
            <i class="bi bi-file-earmark-plus"></i> Log Untracked
          </a></li>
          <li><a href="../moved_assets/" class="sidebar-link<?= _sActive('moved_assets') ?>">
            <i class="bi bi-arrow-left-right"></i> Moved Assets
          </a></li>
          <li><a href="../reclassify/" class="sidebar-link<?= _sActive('reclassify') ?>">
            <i class="bi bi-shuffle"></i> Reclassify Assets
          </a></li>
          <li><a href="../disposals/" class="sidebar-link<?= _sActive('disposals') ?>">
            <i class="bi bi-trash3"></i> Disposals
          </a></li>
          <li><a href="../view_asset_class/" class="sidebar-link<?= _sActive('view_asset_class') ?>">
            <i class="bi bi-tags"></i> Asset Classes
          </a></li>
          <li><a href="../asset_class_sub_class/" class="sidebar-link<?= _sActive('asset_class_sub_class') ?>">
            <i class="bi bi-diagram-2"></i> Sub-Classes
          </a></li>
          <li><a href="../view_asset_location/" class="sidebar-link<?= _sActive('view_asset_location') ?>">
            <i class="bi bi-geo-alt"></i> Locations
          </a></li>
          <li><a href="../asset_type/" class="sidebar-link<?= _sActive('asset_type') ?>">
            <i class="bi bi-layers"></i> Asset Types
          </a></li>
        </ul>
      </li>

      <!-- PEOPLE -->
      <li><span class="sidebar-section-label">People</span></li>

      <li class="sidebar-item">
        <a href="../asset_users/" class="sidebar-link<?= _sActive('asset_users') ?>">
          <i class="bi bi-people"></i> Asset Users
        </a>
      </li>

      <!-- ARCHIVES & REPORTS -->
      <li><span class="sidebar-section-label">Archives &amp; Reports</span></li>

      <li class="sidebar-item">
        <a class="sidebar-link<?= _sActive(...$_archivePages) ?>"
           data-bs-toggle="collapse" data-bs-target="#sideArchives"
           aria-expanded="<?= _sOpen($_archivePages) === ' show' ? 'true' : 'false' ?>"
           aria-controls="sideArchives" role="button">
          <i class="bi bi-archive"></i>
          <span>Archives</span>
          <i class="bi bi-chevron-right si-arrow"></i>
        </a>
        <ul class="sidebar-submenu collapse<?= _sOpen($_archivePages) ?>" id="sideArchives">
          <li><a href="../archived_assets/" class="sidebar-link<?= _sActive('archived_assets') ?>">
            <i class="bi bi-box"></i> Archived Assets
          </a></li>
          <li><a href="../archived_locations/" class="sidebar-link<?= _sActive('archived_locations') ?>">
            <i class="bi bi-pin-map"></i> Archived Locations
          </a></li>
          <li><a href="../archived_suppliers/" class="sidebar-link<?= _sActive('archived_suppliers') ?>">
            <i class="bi bi-building-x"></i> Archived Suppliers
          </a></li>
        </ul>
      </li>

      <li class="sidebar-item">
        <a href="../../reports/" class="sidebar-link<?= _sActive('/reports') ?>">
          <i class="bi bi-bar-chart-line"></i> Reports Hub
        </a>
      </li>

    </ul>
  </nav>

  <!-- Footer -->
  <div class="sidebar-footer">
    <a href="../logout/" class="sidebar-link">
      <i class="bi bi-box-arrow-right"></i> Sign Out
    </a>
  </div>

</aside>
