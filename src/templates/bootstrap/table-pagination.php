<?php
$link_limit = 8;
?>

<?php if ($paginator->lastPage() > 1): ?>
<div class="text-center">
    <ul class="pagination">
        <li class="<?php echo ($paginator->currentPage() == 1) ? 'disabled' : '' ?>">
            <a href="<?php echo $paginator->url(1) ?>">
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
        <li class="<?php echo ($paginator->currentPage() == $i) ? 'active' : '' ?>">
            <a href="<?php echo $paginator->url($i) ?>"><?php echo $i ?></a>
        </li>
        <?php endif;?>
    <?php endfor;?>
        <li class="<?php echo ($paginator->currentPage() == $paginator->lastPage()) ? 'disabled' : '' ?>">
            <a href="<?php echo $paginator->url($paginator->lastPage()) ?>">
                <i class="fa fa-angle-right"></i>
            </a>
        </li>
    </ul>
</div>
<?php endif;?>
