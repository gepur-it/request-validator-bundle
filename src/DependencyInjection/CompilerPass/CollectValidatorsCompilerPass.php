<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestValidatorBundle\DependencyInjection\CompilerPass;

use GepurIt\RequestValidatorBundle\RequestValidator\RequestValidator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class CollectValidatorsCompilerPass
 * @package GepurIt\RequestValidatorBundle\DependencyInjection\CompilerPass
 */
class CollectValidatorsCompilerPass implements CompilerPassInterface
{
    const REGISTRY_ITEM_TAG = 'request.validator';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $validators       = $container->findTaggedServiceIds(self::REGISTRY_ITEM_TAG);
        $requestValidator = $container->getDefinition(RequestValidator::class);

        foreach (array_keys($validators) as $itemName) {
            $item = $container->getDefinition($itemName);
            $requestValidator->addMethodCall('addValidator', [$itemName, $item]);
        }
    }
}
