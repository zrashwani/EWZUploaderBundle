EWZUploaderBundle
=================

The `EWZUploaderBundle` enhances the Symfony2 file upload experience using the
[`AjaxUpload`](https://github.com/valums/ajax-upload) library.

## Installation

### Step 1: Using Composer (recommended)

To install EWZUploaderBundle with Composer just add the following to your
`composer.json` file:

```js
// composer.json
{
    // ...
    require: {
        // ...
        "excelwebzone/uploader-bundle": "master-dev"
    }
}
```

**NOTE**: Please replace `master-dev` in the snippet above with the latest stable
branch, for example ``1.0.*``.

Then, you can install the new dependencies by running Composer's ``update``
command from the directory where your ``composer.json`` file is located:

```bash
$ php composer.phar update
```

Now, Composer will automatically download all required files, and install them
for you. All that is left to do is to update your ``AppKernel.php`` file, and
register the new bundle:

```php
<?php

// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new EWZ\UploaderBundle\EWZUploaderBundle(),
    // ...
);
```

### Step 1 (alternative): Using the ``deps`` file (Symfony 2.0.x)

First, checkout a copy of the code. Just add the following to the ``deps``
file of your Symfony Standard Distribution:

```ini
[EWZUploaderBundle]
    git=http://github.com/excelwebzone/EWZUploaderBundle.git
    target=/bundles/EWZ/Bundle/UploaderBundle
```

Then register the bundle with your kernel:

```php
<?php

// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new EWZ\UploaderBundle\EWZUploaderBundle(),
    // ...
);
```

Make sure that you also register the namespace with the autoloader:

```php
<?php

// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'EWZ'              => __DIR__.'/../vendor/bundles',
    // ...
));
```

Now use the ``vendors`` script to clone the newly added repositories
into your project:

```bash
$ php bin/vendors install
```

### Step2: Configure the bundle's

The configuration is as easy as choosing an upload directory and setting
default file permissions (like size and mime types):

```yaml
# app/config/config.yml

ewz_uploader:
    load_jquery: false
    media:
        max_size: 5120k
        mime_types: ['audio/wav', 'audio/x-wav', 'audio/wave', 'audio/x-pn-wav']
        dir: %kernel.root_dir%/../media
        folder: uploads
    url:
        upload: ewz_uploader_file_upload
        remove: ewz_uploader_file_remove
        download: ewz_uploader_file_download
```

**NOTE**:If you enabled `load_jquery` it will automatically include the library
directly from Google API.

Congratulations! You're ready!

## Basic Usage

Now that all configurations have been made, we will explore how you can integrate
EWZUploaderBundle into your application. We will assume that you already have
created a form type object that will include the uploader field. So All you
need to do is:

```php
<?php

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('audio', 'ewz_uploader');
    // ...
}
```

You can also set special configuration for each field:

```php
<?php

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add('audio', 'ewz_uploader', array(
        'max_size'     => '5120k',
        'mime_types'   => array(
            'audio/wav',
            'audio/x-wav',
            'audio/wave',
            'audio/x-pn-wav'
        ),
        'folder'       => 'music',
        'url_upload'   => $this->router->generate('ewz_uploader_file_upload'),
        'url_remove'   => $this->router->generate('ewz_uploader_file_remove'),
        'url_download' => $this->router->generate('ewz_uploader_file_download'),
    ));
    // ...
}
```

**NOTE**: Validate is simple, treat it like a hidden field.

Cool, now you are ready to add the form widget into your page:

**PHP**:

```php
<?php

$view['form']->setTheme($form, array('EWZUploaderBundle:Form'));

echo $view['form']->widget($form['audio'], array(
    'mime_types' => array(
        'audio/wav',
        'audio/x-wav',
        'audio/wave',
        'audio/x-pn-wav'
    ),
));
```

**Twig**:

```jinja
{% form_theme form 'EWZUploaderBundle:Form:ewz_uploader_widget.html.twig' %}

{{ form_widget(form.audio, { 'attr': {
    'mime_types': [
        'audio/wav',
        'audio/x-wav',
        'audio/wave',
        'audio/x-pn-wav'
    ],
} }) }}
```
