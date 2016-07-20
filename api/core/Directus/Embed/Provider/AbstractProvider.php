<?php

namespace Directus\Embed\Provider;

use Directus\Util\StringUtils;

abstract class AbstractProvider implements ProviderInterface
{
    /**
     * Embed Service name
     * @var string
     */
    protected $name = 'unknown';

    /**
     * Files Config
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
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(__t('invalid_unsupported_url'));
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
        return strtolower($this->name);
    }

    /**
     * Get the embed type
     * @return string
     */
    public function getType()
    {
        return 'embed/' . $this->getName();
    }

    /**
     * @inheritDoc
     */
    public function getCode($data)
    {
        return StringUtils::replacePlaceholder($this->getFormatTemplate(), $data);
    }

    /**
     * Get the HTML embed format template
     * @return mixed
     */
    protected function getFormatTemplate()
    {
        return '';
    }

    /**
     * Parse an embed ID
     * @param $embedID
     * @return array
     */
    abstract public function parseID($embedID);

    /**
     * Parsing the url
     * This is a method use for the extended class
     * @param $url
     * @return array
     * @throws \Exception
     */
    abstract protected function parseURL($url);
}
