<?php echo $view['form']->renderBlock('hidden_widget') ?>

<div class="uploader">
    <?php echo $view['form']->renderBlock('form_widget_simple', array(
        'type'      => 'file',
        'id'        => 'uploader_'.$id,
        'full_name' => 'uploader_'.$full_name,

        'attr' => array(
            'data-max-size'     => $max_size,
            'data-mime-types'   => $mime_types,
            'data-folder'       => $folder,
            'data-url-upload'   => $url_upload,
            'data-url-remove'   => $url_remove,
            'data-url-download' => $url_download,
        ),
    )) ?>

    <span class="file-upload loader"></span>
    <span class="file-upload success"></span>
    <span class="file-upload error"></span>

    <span class="remove" <?php if (!empty($value)): ?>style="display:inline-block;"<?php endif ?>>(Remove)</span>
    <span class="filename"><?php if (!empty($value)): ?><a href="#" target="_blank"><?php echo $view->escape($value) ?></a><?php endif ?></span>
    <span class="message"></span>
</div>
