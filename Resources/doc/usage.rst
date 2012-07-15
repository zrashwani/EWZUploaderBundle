Usage
=====

Introduction
------------
In this chapter, we will explore how you can integrate EWZUploaderBundle
into your application. We will assume that you already have created a form
type object that will include the uploader field. So All you need to do is:

.. code-block :: php

    <?php

    public function buildForm(FormBuilder $builder, array $options)
    {
        // ...
        $builder->add('audio', 'ewz_uploader');
        // ...
    }

You can also set special configuration for each field:

.. code-block :: php

    <?php

    public function buildForm(FormBuilder $builder, array $options)
    {
        // ...
        $builder->add('audio', 'ewz_uploader', array(
            'max_size'     => '5120k',
            'mime_types'   => json_encode(array(
                'audio/wav',
                'audio/x-wav',
                'audio/wave',
                'audio/x-pn-wav'
            )),
            'folder'       => 'music',
            'url_upload'   => $this->router->generate('ewz_uploader_file_upload'),
            'url_remove'   => $this->router->generate('ewz_uploader_file_remove'),
            'url_download' => $this->router->generate('ewz_uploader_file_download'),
        ));
        // ...
    }

.. note ::

    Validate is simple, treat it like a hidden field.

Cool, now you are ready to add the form widget into your page:

PHP:
---

.. code-block :: php

    <?php

    $view['form']->setTheme($form, array('EWZUploaderBundle:Form'));

    echo $view['form']->widget($form['audio'], array(
        'mime_types' => json_encode(array(
            'audio/wav',
            'audio/x-wav',
            'audio/wave',
            'audio/x-pn-wav'
        )),
    ));

Twig:
----

.. code-block :: jinja

    {% form_theme form 'EWZUploaderBundle:Form:ewz_uploader_widget.html.twig' %}

    {{ form_widget(form.audio, { 'attr': {
        'mime_types': [
            'audio/wav',
            'audio/x-wav',
            'audio/wave',
            'audio/x-pn-wav'
        ]|json_encode,
    } }) }}
