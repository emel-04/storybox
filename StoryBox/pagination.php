<style>
    #pagination {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 30px;
    }

    .page-btn {
        width: 36px;
        height: 36px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .page-btn:hover:not(.disabled):not(.active) {
        background: #f5f5f5;
    }

    .page-btn.active {
        background: #4285f4;
        color: white;
        border-color: #4285f4;
    }

    .page-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .page-btn.dots {
        padding: 8px;
        color: #333;
        font-size: 13;
    }
</style>
<?php
// Kiểm tra cả hai biến cùng lúc
if (!isset($total_pages, $page)) {
    return;
}

// Lấy các tham số URL hiện tại (trừ 'page')
$query_params = $_GET;
unset($query_params['page']);

// Tính toán trang bắt đầu/kết thúc
$start_page = max(1, $page - 2);
$end_page = min($total_pages, $page + 2);
?>

<div id="pagination">
    <?php // Nút Previous ?>
    <?php if ($page > 1): ?>
        <a href="?<?= htmlspecialchars(http_build_query(array_merge($query_params, ['page' => $page - 1]))) ?>" 
           class="page-btn">
            <i class="ti-angle-left"></i>
        </a>
    <?php else: ?>
        <span class="page-btn disabled">
            <i class="ti-angle-left"></i>
        </span>
    <?php endif; ?>
    
    <?php // Hiển thị trang đầu tiên và dấu ... nếu cần ?>
    <?php if ($start_page > 1): ?>
        <a href="?<?= htmlspecialchars(http_build_query(array_merge($query_params, ['page' => 1]))) ?>" 
           class="page-btn">
            1
        </a>
        <?php if ($start_page > 2): ?>
            <span class="page-btn dots">...</span>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php // Các trang chính ?>
    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
        <a href="?<?= htmlspecialchars(http_build_query(array_merge($query_params, ['page' => $i]))) ?>" 
           class="page-btn <?= $i == $page ? 'active' : '' ?>">
            <?= htmlspecialchars($i) ?>
        </a>
    <?php endfor; ?>
    
    <?php // Hiển thị trang cuối và dấu ... nếu cần ?>
    <?php if ($end_page < $total_pages): ?>
        <?php if ($end_page < $total_pages - 1): ?>
            <span class="page-btn dots">...</span>
        <?php endif; ?>
        <a href="?<?= htmlspecialchars(http_build_query(array_merge($query_params, ['page' => $total_pages]))) ?>" 
           class="page-btn">
            <?= htmlspecialchars($total_pages) ?>
        </a>
    <?php endif; ?>
    
    <?php // Nút Next ?>
    <?php if ($page < $total_pages): ?>
        <a href="?<?= htmlspecialchars(http_build_query(array_merge($query_params, ['page' => $page + 1]))) ?>" 
           class="page-btn">
            <i class="ti-angle-right"></i>
        </a>
    <?php else: ?>
        <span class="page-btn disabled">
            <i class="ti-angle-right"></i>
        </span>
    <?php endif; ?>
</div>