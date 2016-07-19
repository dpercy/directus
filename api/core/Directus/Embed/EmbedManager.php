<?php

namespace Directus\Embed;

use Directus\Embed\Provider\ProviderInterface;

class EmbedManager
{
    /**
     * List of registered provider
     * @var array
     */
    protected $providers = [];

    /**
     * Parse a given url with all the registered providers
     * @param $url
     * @return array
     */
    public function parse($url)
    {
        foreach($this->providers as $provider) {
            if ($provider->validateURL($url)) {
                return $provider->parse($url);
            }
        }
    }

    /**
     * Register a provider
     * @param ProviderInterface $provider
     * @return ProviderInterface
     */
    public function register(ProviderInterface $provider)
    {
        if (!array_key_exists($provider->getName(), $this->providers)) {
            $this->providers[$provider->getName()] = $provider;
        }

        return $this->providers[$provider->getName()];
    }
}
