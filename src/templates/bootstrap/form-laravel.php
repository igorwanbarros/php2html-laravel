<?php
    $session = app('request')->session();
    $hasError = $session->has('errors');
    $errors = $session->get('errors');
    $sizes = 0;
    $showRow = true;
    $current = 0;
    $last = count($form->getFields());
?>
<form action="<?php echo $form->getAction(); ?>"
      method="<?php echo $form->getMethod(); ?>"
    <?php echo $form->renderAttributes(); ?>>

    <?php foreach ($form->getFields() as $key => $field):?>
        <?php $current++;?>
        <?php if ($field->getType() !== 'hidden') {$sizes += $field->getSize();} ?>

        <?php if ($field->getType() !== 'hidden' && ($sizes <= 12 && $showRow)): ?>
            <div class="row">
            <?php $showRow = false;?>
        <?php endif;?>


        <?php if (in_array($field->getType(), ['hidden', 'html']) !== false): ?>

            <?php echo $field; ?>

        <?php else: ?>

            <?php if ($field->getType() == 'submit'): ?>
            <div class="col-sm-12">
            <?php else: ?>
            <?php $fieldError = isset($errors) && $errors ? $errors->has($field->getName()) : false; ?>
            <div class="form-group col-sm-<?php echo ($field->getSize() ?: 4) . ($fieldError ? ' has-error' : '')?>">
                <label for="<?php echo $field->getId(); ?>">
                    <?php echo $field->getLabel(); ?>
                </label>
                <?php $field->addAttribute('class', 'form-control'); ?>
            <?php endif; ?>

                <?php if (isset($fieldError) && $fieldError):?>
                    <?php $field->addAttribute('class', 'is-invalid'); ?>
                <?php endif;?>

                <?php echo $field; ?>

            <?php if (isset($fieldError) && $fieldError):?>
                <div class="list-error invalid-feedback">
                <?php foreach ($errors->get($field->getName()) as $error):?>
                    <span><?php echo $error;?></span>
                <?php endforeach;?>
                </div>
            <?php endif;?>

            </div>

        <?php endif; ?>

        <?php if ((!$showRow && $current == $last) || $sizes >= 12): ?>
            </div>
            <?php $sizes = 0; ?>
            <?php $showRow = true;?>
        <?php endif; ?>
    <?php endforeach; ?>
</form>
