<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Psalm;

use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;
use Whsv26\Functional\Collection\Psalm\CollectionFilterMethodRefinement\CollectionFilterMethodReturnTypeProvider;
use Whsv26\Functional\Collection\Psalm\Suppress\UnusedCallSuppressor;

/**
 * Plugin entrypoint
 */
class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        $register =
            /**
             * @param class-string $hook
             */
            function (string $hook) use ($registration): void {
                class_exists($hook);
                $registration->registerHooksFromClass($hook);
            };

        $register(CollectionFilterMethodReturnTypeProvider::class);
        $register(UnusedCallSuppressor::class);
    }
}
