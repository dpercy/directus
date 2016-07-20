<?php

namespace Directus\Embed\Provider;

class VimeoProvider extends AbstractProvider
{
    protected $name = 'Vimeo';

    /**
     * @inheritDoc
     */
    public function validateURL($url)
    {
        return strpos($url,'vimeo.com') !== false;
    }

    /**
     * @inheritDoc
     */
    protected function parseURL($url)
    {
        // Get ID from URL
        preg_match('/vimeo\.com\/([0-9]{1,10})/', $url, $matches);
        $videoID = isset($matches[1]) ? $matches[1] : null;

        // Can't find the video ID
        if (!$videoID) {
            throw new \Exception(__t('x_x_id_not_detected', ['type' => __t('video'), 'service' => 'Vimeo']));
        }

        return $this->parseID($videoID);
    }

    /**
     * @inheritDoc
     */
    public function parseID($videoID)
    {
        $defaultInfo = [
            'embed_id' => $videoID,
            'title' => __t('x_type_x', ['service' => 'YouTube', 'type' => 'Video']) . ': ' . $videoID,
            'size' => 0,
            'name' => 'vimeo_' . $videoID . '.jpg',
            'type' => 'embed/vimeo',
            'height' => 340,
            'width' => 560
        ];

        $info = array_merge($defaultInfo, $this->fetchInfo($videoID));
        $info['html'] = $this->getCode($info);

        return $info;
    }

    /**
     * Fetch Video information
     * @param $videoID
     * @return array
     */
    protected function fetchInfo($videoID)
    {
        $info = [];

        $info['title'] = __t('unable_to_retrieve_x_title', ['service' => 'Vimeo']);
        $info['size'] = 0;

        // Get Data
        $url = 'http://vimeo.com/api/v2/video/' . $videoID . '.json';
        $ch = curl_init($url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
        $content = curl_exec($ch);
        curl_close($ch);

        $array = json_decode($content, true);
        if (!$array) {
            return $info;
        }

        $result = $array[0];
        $info['title'] = $result['title'];
        $info['caption'] = strip_tags($result['description']);
        $info['size'] = $result['duration'];
        $info['height'] = $result['height'];
        $info['width'] = $result['width'];
        $info['tags'] = $result['tags'];
        $info['data'] = $this->getThumbnail($result['thumbnail_large']);

        return $info;
    }

    /**
     * Fetch Video thumbnail data
     * @param $thumb - url
     * @return string
     */
    protected function getThumbnail($thumb)
    {
        $content = @file_get_contents($thumb);
        $thumbnail = '';

        if ($content) {
            $thumbnail = 'data:image/jpeg;base64,' . base64_encode($content);
        }

        return $thumbnail;
    }

    /**
     * @inheritDoc
     */
    protected function getFormatTemplate()
    {
        return '<iframe src="//player.vimeo.com/video/{{embed_id}}?title=0&amp;byline=0&amp;portrait=0&amp;color=7AC943" width="{{width}}" height="{{height}}" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }


}
