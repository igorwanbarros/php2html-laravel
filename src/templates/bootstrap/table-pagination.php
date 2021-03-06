<?php
$link_limit = 8;
$queryString = http_build_query(app('request')->except('page'));
$url = url($baseUrl . ($queryString ? "?{$queryString}&" : '?'));
?>

<?php if ($paginator->lastPage() > 1): ?>
<div class="row">
    <ul class="pagination">
        <li class="page-item <?php echo ($paginator->currentPage() == 1) ? 'disabled' : '' ?>">
            <a href="<?php echo $url . "page=1" ?>" class="page-link">
                <i class="fa fa-angle-left"></i>
            </a>
        </li>
    <?php for ($i = 1, $last = $paginator->lastPage(); $i <= $last; $i++): ?>
        <?php
        $half_total_links = floor($link_limit / 2);
        $from = $paginator->currentPage() - $half_total_links;
        $to = $paginator->currentPage() + $half_total_links;

        if ($paginator->currentPage() < $half_total_links) {
            $to += $half_total_links - $paginator->currentPage();
        }
        if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
            $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
        }
        ?>
        <?php if ($from < $i && $i < $to): ?>
        <li class="page-item <?php echo ($paginator->currentPage() == $i) ? 'active' : '' ?>">
            <a href="<?php echo $url . "page={$i}" ?>" class="page-link"><?php echo $i ?></a>
        </li>
        <?php endif;?>
    <?php endfor;?>
        <li class="page-item <?php echo ($paginator->currentPage() == $paginator->lastPage()) ? 'disabled' : '' ?>">
            <a href="<?php echo $url . "page={$paginator->lastPage()}" ?>" class="page-link">
                <i class="fa fa-angle-right"></i>
            </a>
        </li>
    </ul>
</div>
<?php endif;?>
