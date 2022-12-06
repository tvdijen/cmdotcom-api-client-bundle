<?php

namespace tvdijen\CMDotCom\ApiClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder;

        $treeBuilder
            ->root('tvdijen_cmdotcom_api_client')
                ->children()
                    ->scalarNode('base_url')
                        ->defaultValue('https://gw.cmtelecom.com/v1.0/message')
                        ->validate()
                            ->ifTrue(function (string $url) {
                                $parts = parse_url($url);

                                return $parts === false
                                    || empty($parts['scheme'])
                                    || empty($parts['host']);
                            })
                            ->thenInvalid("Invalid base URL '%s': scheme and host are required.")
                        ->end()
                    ->end()
                    ->scalarNode('authorization')
                        ->validate()
                            ->ifTrue(function (string $headerValue) {
                                return strpos($headerValue, 'AccessKey ') !== 0;
                            })
                                ->thenInvalid(
                                    "Authorization value '%s' should be in the format 'AccessKey your_access_key_here'."
                                )
                            ->end()
                        ->isRequired()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
