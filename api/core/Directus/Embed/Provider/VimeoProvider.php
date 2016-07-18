<?php

namespace Directus\Embed\Provider;

class VimeoProvider extends AbstractProvider
{
    protected $name = 'Vimeo';

    public function validateURL($url)
    {
        return strpos($url,'vimeo.com') !== false;
    }

    protected function parseURL($url)
    {
        // Get ID from URL
        preg_match('/vimeo\.com\/([0-9]{1,10})/', $url, $matches);
        $videoID = $matches[1];

        // Can't find the video ID
        if (!$videoID) {
            throw new \Exception(__t('x_x_id_not_detected', ['type' => __t('video'), 'service' => 'Vimeo']));
        }

        $defaultInfo = [
            'embed_id' => $videoID,
            'title' => __t('x_type_x', ['service' => 'YouTube', 'type' => 'Video']).': '.$videoID,
            'size' => 0,
            'name' => 'vimeo_'.$videoID.'.jpg',
            'type' => 'embed/vimeo',
            'height' => 340,
            'width' => 560
        ];

        return array_merge($defaultInfo, $this->fetchInfo($videoID));
    }

    protected function fetchInfo($videoID)
    {
        $info = [];

        $info['title'] = __t('unable_to_retrieve_x_title', ['service' => 'YouTube']);
        $info['size'] = 0;

        // Get Data
        $url = 'http://vimeo.com/api/v2/video/' . $videoID . '.php';
        $ch = curl_init($url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
        $content = curl_exec($ch);
        curl_close($ch);

        $array = unserialize(trim($content));
        if (!$content) {
            return $info;
        }

        $info['title'] = $array[0]['title'];
        $info['caption'] = strip_tags($array[0]['description']);
        $info['size'] = $array[0]['duration'];
        $info['height'] = $array[0]['height'];
        $info['width'] = $array[0]['width'];
        $info['tags'] = $array[0]['tags'];
        $info['data'] = $this->getThumbnail($array[0]['thumbnail_large']);

        return $info;
    }

    protected function getThumbnail($thumb)
    {
        $content = file_get_contents($thumb);
        $thumbnail = '';

        if ($content) {
            $thumbnail = 'data:image/jpeg;base64,' . base64_encode($content);
        }

        return $thumbnail;
    }
}
