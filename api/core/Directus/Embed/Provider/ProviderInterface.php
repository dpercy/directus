<?php

namespace Directus\Embed\Provider;

interface ProviderInterface
{
    /**
     * Check whether the given URL is supported
     * @param $url
     * @return bool
     */
    public function validateURL($url);

    /**
     * Parse a given url
     * @param $url
     * @return array
     */
    public function parse($url);

    /**
     * Get the provider name
     * @return string
     */
    public function getName();
}
