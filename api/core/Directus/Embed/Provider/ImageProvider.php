<?php

namespace Directus\Embed\Provider;

class ImageProvider extends AbstractProvider
{
    protected $name = 'Image';

    public function getProviderType()
    {
        return 'image';
    }

    public function validateURL($url)
    {
        $urlHeaders = $this->getHeaders($url);

        return strpos($urlHeaders['Content-Type'], 'image/') === 0;
    }

    protected function getHeaders($url)
    {
        stream_context_set_default([
            'http' => [
                'method' => 'HEAD'
            ]
        ]);

        $headers = get_headers($url, 1);

        stream_context_set_default([
            'http' => [
                'method' => 'GET'
            ]
        ]);

        return $headers;
    }

    protected function parseURL($url)
    {
        $defaultInfo = [
            'provider_id' => null,
            'type' => 'image/unknown',
            'title' => '',
            'size' => 0
        ];

        return array_merge($defaultInfo, $this->fetchInfo($url));
    }

    public function parseID($url)
    {
        return $this->parseURL($url);
    }

    protected function fetchInfo($url)
    {
        $info = [];

        $urlInfo = pathinfo($url);
        list($width, $height) = getimagesize($url);
        $urlHeaders = $this->getHeaders($url);

        $content = file_get_contents($url);

        if (!$content) {
            return $info;
        }

        $data = 'data:' . $urlHeaders['Content-Type'] . ';base64,' . base64_encode($content);

        $info['title'] = $urlInfo['filename'];
        $info['name'] = $urlInfo['basename'];
        $info['size'] = isset($urlHeaders['Content-Length']) ? $urlHeaders['Content-Length'] : 0;
        $info['type'] = $urlHeaders['Content-Type'];
        $info['width'] = $width;
        $info['height'] = $height;
        $info['data'] = $data;
        $info['charset'] = 'binary';

        return $info;
    }
}
