<?php

namespace EWZ\Bundle\UploaderBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class UploaderExtension extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'ewz_uploader_initialize' => new \Twig_Function_Method($this, 'initialize', array('is_safe' => array('html'))),
        );
    }

    public function initialize(array $parameters = array(), $name = null)
    {
        return $this->container->get('ewz_uploader_helper')->initialize($parameters, $name ?: 'EWZUploaderBundle::initialize.html.twig');
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ewz_uploader';
    }
}
