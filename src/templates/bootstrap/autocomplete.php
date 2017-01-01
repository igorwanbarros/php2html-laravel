<div class="typeahead__container">
    <div class="typeahead__field">
        <span class="typeahead__query">
            <?php echo $autocomplete->getInput(); ?>
            <?php if ($autocomplete->getInputHidden()): ?>
                <?php echo $autocomplete->getInputHidden(); ?>
            <?php endif; ?>
        </span>
        <span class="input-group-btn typeahead__button">
            <?php
                $disabled =
                    $autocomplete->getAttribute('disabled') || $autocomplete->getAttribute('readonly')
                        ? 'disabled'
                        : ''
            ?>
            <button type="button" class="<?php echo 'button_' . $autocomplete->getName()?> btn btn-default <?php echo $disabled?>">
                <span class="typeahead__search-icon"></span>
            </button>
        </span>
    </div>
</div>
