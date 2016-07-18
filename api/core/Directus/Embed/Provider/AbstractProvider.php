<?php

namespace Directus\Embed\Provider;

abstract class AbstractProvider implements ProviderInterface
{
    /**
     * Embed Service name
     * @var string
     */
    protected $name = 'unknown';

    /**
     * Config
     * @var array
     */
    protected $config = [];

    /**
     * AbstractProvider constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Parse a given URL
     * @param $url
     * @return mixed
     */
    public function parse($url)
    {
        if (!is_string($url)) {
            throw new \InvalidArgumentException(__t('url_must_be_a_string'));
        }

        if (!$this->validateURL($url)) {
            throw new \InvalidArgumentException(__t('url_x_cannot_be_parse_by_x', [
                'url' => $url,
                'class' => get_class($this)
            ]));
        }

        return $this->parseURL($url);
    }

    /**
     * Get the embed provider name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Parsing the url
     * This is a method use for the extended class
     * @param $url
     * @return mixed
     */
    abstract protected function parseURL($url);
}
