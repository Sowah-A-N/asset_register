<?php
// helpers/pagination.php — reusable pagination renderer
function render_pagination(int $page, int $total_pages, string $qs_base = ''): void {
    if ($total_pages <= 1) return;
    if ($qs_base && !str_ends_with($qs_base, '&')) $qs_base .= '&';
    echo '<nav aria-label="Pagination"><ul class="pagination pagination-sm flex-wrap">';
    echo '<li class="page-item ' . ($page <= 1 ? 'disabled' : '') . '">';
    echo '<a class="page-link" href="?' . $qs_base . 'page=' . ($page - 1) . '">&laquo;</a></li>';
    $start = max(1, $page - 3);
    $end   = min($total_pages, $page + 3);
    if ($start > 1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
    for ($p = $start; $p <= $end; $p++) {
        echo '<li class="page-item ' . ($p === $page ? 'active' : '') . '">';
        echo '<a class="page-link" href="?' . $qs_base . 'page=' . $p . '">' . $p . '</a></li>';
    }
    if ($end < $total_pages) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
    echo '<li class="page-item ' . ($page >= $total_pages ? 'disabled' : '') . '">';
    echo '<a class="page-link" href="?' . $qs_base . 'page=' . ($page + 1) . '">&raquo;</a></li>';
    echo '</ul></nav>';
}
