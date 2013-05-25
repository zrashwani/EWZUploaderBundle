<?php

namespace EWZ\Bundle\UploaderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use EWZ\Bundle\UploaderBundle\DependencyInjection\Compiler\TwigFormPass;

class EWZUploaderBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TwigFormPass());
    }
}
