<?php

use Directus\Embed\Provider\YoutubeProvider;

class EmbedTest extends PHPUnit_Framework_TestCase
{
    protected $youtubeURL = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
    protected $vimeoURL = 'https://vimeo.com/45196609';

    public function testYoutubeEmbed()
    {
        $youtube = new YoutubeProvider([]);

        $this->assertTrue($youtube->validateURL($this->youtubeURL));

        $data = $youtube->parse($this->youtubeURL);
        $this->assertInternalType('array', $data);

        $this->assertEquals($data['id'], 'dQw4w9WgXcQ');
    }

    public function testExceptionYoutubeEmbedInvalidParseURL()
    {
        $youtube = new YoutubeProvider([]);
        $this->setExpectedException('InvalidArgumentException');
        $data = $youtube->parse($this->vimeoURL);
    }

    public function testExceptionYoutubeEmbedNonStringURL()
    {
        $youtube = new YoutubeProvider([]);
        $this->setExpectedException('InvalidArgumentException');
        $data = $youtube->parse(true);
    }

    public function testExceptionYoutubeEmbedNoIDInURL()
    {
        $youtube = new YoutubeProvider([]);
        $this->setExpectedException('Exception');
        $data = $youtube->parse('https://youtube.com');
    }
}
