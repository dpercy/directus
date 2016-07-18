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

    public function parse($url)
    {
        foreach($this->providers as $provider) {
            if ($provider->validateURL($url)) {
                return $provider->parse($url);
            }
        }
    }

    public function register(ProviderInterface $provider)
    {
        if (!array_key_exists($provider->getName(), $this->providers)) {
            $this->providers[$provider->getName()] = $provider;
        }

        return $this->providers[$provider->getName()];
    }
}
