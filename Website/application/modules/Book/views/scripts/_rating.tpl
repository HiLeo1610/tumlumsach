<?php for ($x = 1; $x <= $this->item->rating; $x++): ?>
    <span class="rating_star_big_generic rating_star"></span>
<?php endfor; ?>
<?php if ((round($this->item->rating) - $this->item->rating) > 0): ?>
    <span class="rating_star_generic rating_star_half"></span>
<?php endif; ?>