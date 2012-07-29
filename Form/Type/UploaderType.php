<?php

namespace EWZ\Bundle\UploaderBundle\Form\Type;

use Symfony\Component\Routing\Router;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Options;

/**
 * A field for uploading a file.
 */
class UploaderType extends AbstractType
{
    /**
     * Holds the Router instance
     *
     * @var Router
     */
    protected $router;

    /**
     * The media settings
     *
     * @var array
     */
    protected $media;

    /**
     * The url's settings
     *
     * @var array
     */
    protected $url;

    /**
     * Construct.
     *
     * @param Router $router An Router instance
     * @param array  $media  Media configuration values
     * @param array  $url    Url's configuration values
     */
    public function __construct(Router $router, array $media = array(), array $url = array())
    {
        $this->router = $router;
        $this->media  = $media;
        $this->url    = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'max_size'     => $options['max_size'],
            'mime_types'   => $options['mime_types'],
            'folder'       => $options['folder'],
            'url_upload'   => $options['url_upload'],
            'url_remove'   => $options['url_remove'],
            'url_download' => $options['url_download'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'compound'     => false,
            'max_size'     => null, //$this->media['max_size'],
            'mime_types'   => null, //$this->media['mime_types'],
            'folder'       => null, //$this->media['folder'],
            'url_upload'   => $this->router->generate($this->url['upload']),
            'url_remove'   => $this->router->generate($this->url['remove']),
            'url_download' => $this->url['download'] ? $this->router->generate($this->url['download']) : false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ewz_uploader';
    }
}
