<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestValidatorBundle;

use GepurIt\RequestValidatorBundle\DependencyInjection\CompilerPass\CollectValidatorsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class RequestValidatorBundle
 * @package GepurIt\RequestValidatorBundle
 */
class RequestValidatorBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new CollectValidatorsCompilerPass());
    }
}
