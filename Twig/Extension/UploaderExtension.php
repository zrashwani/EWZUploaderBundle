<?php

namespace EWZ\Bundle\UploaderBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class UploaderExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var Boolean
     */
    protected $autoInclude;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * Construct.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     * @param Boolean            $autoInclude Whether or not to automatically include assets
     * @param string             $basePath    Base path to bundle
     */
    public function __construct(ContainerInterface $container, $autoInclude, $basePath)
    {
        $this->container = $container;
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
            'ewz_uploader_add_assets' => new \Twig_Function_Method($this, 'addAssets', array('is_safe' => array('html'))),
            'ewz_uploader_is_image' => new \Twig_Function_Method($this, 'isImage', array('is_safe' => array('html'))),
        );
    }

    /**
     * Adds all related JavaScript and CSS assets.
     */
    public function addAssets()
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

    /**
     * Checks whether or not the file is an image or not.
     *
     * @param string $filename
     *
     * #return Boolean
     */
    public function isImage($filename)
    {
        $filepath = sprintf('%s/%s/%s', $this->container->getParameter('ewz_uploader.media.dir'), $this->container->getParameter('ewz_uploader.media.folder'), $filename);

        return is_array(@getimagesize($filepath));
    }
}
