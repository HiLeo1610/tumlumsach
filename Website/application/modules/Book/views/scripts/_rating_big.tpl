<div class="book_rating">
    <?php for ($x = 1; $x <= $this->item->rating; $x++): ?>
        <span class="rating_star_big_generic rating_star_big"></span>
    <?php endfor; ?>
    <?php if ((round($this->item->rating) - $this->item->rating) > 0): ?>
        <span class="rating_star_big_generic rating_star_big_half"></span>
    <?php endif; ?>
    <?php if (round($this->item->rating) == 0) :?>
        <?php for ($i = 0; $i < 5; $i++ ) : ?>
            <span class="rating_star_big_generic rating_star_big_disabled"></span>
        <?php endfor; ?>
    <?php endif; ?>
</div>