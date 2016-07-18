<?php

namespace Directus\Embed\Provider;

class YoutubeProvider extends AbstractProvider
{
    protected $name = 'YouTube';

    public function validateURL($url)
    {
        return strpos($url, 'youtube.com') !== false;
    }

    protected function parseURL($url)
    {
        // Get ID from URL
        parse_str(parse_url($url, PHP_URL_QUERY), $urlParameters);
        $videoID = isset($urlParameters['v']) ? $urlParameters['v'] : false;

        // Can't find the video ID
        if (!$videoID) {
            throw new \Exception(__t('x_x_id_not_detected', ['type' => __t('video'), 'service' => 'YouTube']));
        }

        $defaultInfo = [
            'embed_id' => $videoID,
            'data' => $this->getThumbnail($videoID),
            'title' => __t('x_type_x', ['service' => 'YouTube', 'type' => 'Video']).': '.$videoID,
            'size' => 0,
            'name' => 'youtube_'.$videoID.'.jpg',
            'type' => 'embed/youtube',
            'height' => 340,
            'width' => 560
        ];

        return array_merge($defaultInfo, $this->fetchInfo($videoID));
    }

    protected function fetchInfo($videoID)
    {
        $info = [];

        if (!isset($this->config['youtube_api_key'])) {
            return $info;
        }

        $youtubeFormatUrlString = "https://www.googleapis.com/youtube/v3/videos?id=%s&key=%s&part=snippet,contentDetails";
        $url = sprintf($youtubeFormatUrlString, $videoID, $this->config['youtube_api_key']);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);

        $content = json_decode($response);
        if (!$content) {
            return $info;
        }

        if (property_exists($content, 'error')) {
            throw new \Exception(__t('bad_x_api_key', ['service' => 'YouTube']));
        }

        $info['title'] = __t('unable_to_retrieve_x_title', ['service' => 'YouTube']);
        $info['size'] = 0;

        if (property_exists($content, 'items') && count($content->items) > 0) {
            $videoDataSnippet = $content->items[0]->snippet;

            $info['title'] = $videoDataSnippet->title;
            $info['caption'] = $videoDataSnippet->description;
            $tags = '';
            if (property_exists($videoDataSnippet, 'tags')) {
                $tags = implode(',', $videoDataSnippet->tags);
            }
            $info['tags'] = $tags;

            $videoContentDetails = $content->items[0]->contentDetails;
            $videoStart = new \DateTime('@0'); // Unix epoch
            $videoStart->add(new \DateInterval($videoContentDetails->duration));
            $info['size'] = $videoStart->format('U');
        }

        return $info;
    }

    protected function getThumbnail($videoID)
    {
        $content = file_get_contents('http://img.youtube.com/vi/'.$videoID.'/0.jpg');

        $thumbnail = '';
        if ($content) {
            $thumbnail = 'data:image/jpeg;base64,' . base64_encode($content);
        }

        return $thumbnail;
    }
}
