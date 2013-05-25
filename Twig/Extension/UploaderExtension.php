<?php

namespace EWZ\Bundle\UploaderBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class UploaderExtension extends \Twig_Extension
{
    /**
     * @var boolean
     */
    protected $autoInclude;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    public function __construct($autoInclude, $basePath)
    {
        $this->autoInclude = $autoInclude;
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ewz_uploader';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'ewz_include_uploader' => new \Twig_Function_Method($this, 'includeUploader', array('is_safe' => array('html'))),
        );
    }

    public function includeUploader()
    {
        if (!$this->environment->hasExtension('assets')) {
            return;
        }

        if ($this->autoInclude) {
            $path = $this->environment
                ->getExtension('assets')
                ->getAssetUrl($this->basePath);

            echo sprintf('<link href="%s/css/doc.css" media="all" rel="stylesheet" type="text/css" />', $path);
            echo sprintf('<script src="%s/js/external/ajaxupload/ajaxupload.min.js" type="text/javascript"></script>', $path);
            echo sprintf('<script src="%s/js/application.js" type="text/javascript"></script>', $path);

            $this->autoInclude = false;
        }
    }
}
