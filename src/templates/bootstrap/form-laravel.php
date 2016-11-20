<?php
    $session = app('request')->session();
    $hasError = $session->has('errors');
    $errors = $session->get('errors');
?>
<form action="<?php echo $form->getAction(); ?>"
      method="<?php echo $form->getMethod(); ?>"
    <?php echo $form->renderAttributes(); ?>>

    <?php foreach ($form->getFields() as $field) : ?>
        <?php if ($field->getType() == 'hidden'): ?>

            <?php echo $field; ?>

        <?php else: ?>

            <?php if ($field->getType() == 'submit'): ?>
            <div class="col-sm-12">
            <?php else: ?>
            <?php $fieldError = $errors ? $errors->has($field->getName()) : false; ?>
            <div class="form-group col-sm-<?php echo ($field->getSize() ?: 4) . ($fieldError ? ' has-error' : '')?>">
                <label for="<?php echo $field->getId(); ?>">
                    <?php echo $field->getLabel(); ?>
                </label>
                <?php $field->addAttribute('class', 'form-control'); ?>
            <?php endif; ?>

                <?php echo $field; ?>

            <?php if ($fieldError):?>
                <div class="list-error">
                <?php foreach ($errors->get($field->getName()) as $error):?>
                    <span><?php echo $error;?></span>
                <?php endforeach;?>
                </div>
            <?php endif;?>

            </div>

        <?php endif; ?>

    <?php endforeach; ?>
</form>
