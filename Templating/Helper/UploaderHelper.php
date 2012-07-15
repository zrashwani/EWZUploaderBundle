<?php

namespace EWZ\Bundle\UploaderBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;

class UploaderHelper extends Helper
{
    protected $templating;
    protected $loadJQuery;

    public function __construct(EngineInterface $templating, $loadJQuery = false)
    {
        $this->templating = $templating;
        $this->loadJQuery = $loadJQuery;
    }

    /**
     * Returns the HTML necessary for initializing the JavaScript SDK.
     *
     * The default template includes the following parameters:
     *
     *  - load_jquery
     *
     * @param array  $parameters An array of parameters for the initialization template
     * @param string $name       A template name
     *
     * @return string An HTML string
     */
    public function initialize(array $parameters = array(), $name = null)
    {
        $name = $name ?: 'EWZUplaoderBundle::initialize.html.php';

        return $this->templating->render($name, $parameters + array(
            'load_jquery' => $this->loadJQuery,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ewz_uploader';
    }
}
