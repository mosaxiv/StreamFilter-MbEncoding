<?php

namespace Test\StreamFilter;

use PHPUnit\Framework\TestCase;
use SplFileObject;
use StreamFilter\ConvertMbEncoding;

class ConvertMbEncodingTest extends TestCase
{
    public function testRegister()
    {
        $this->assertNotContains(ConvertMbEncoding::FILTERNAME . '.*', stream_get_filters());
        ConvertMbEncoding::register();
        $this->assertContains('convert.mb.encoding.*', stream_get_filters());
    }

    public function testGetFilterName()
    {
        $url = ConvertMbEncoding::getFilterURL('test.csv', 'UTF-8', 'SJIS');
        $this->assertSame('php://filter/convert.mb.encoding.UTF-8:SJIS/resource=test.csv', $url);

        $url = ConvertMbEncoding::getFilterURL('test.csv', 'UTF-8');
        $this->assertSame('php://filter/convert.mb.encoding.UTF-8/resource=test.csv', $url);

        $url = ConvertMbEncoding::getFilterURL('test.csv', 'UTF-8', ['SJIS', 'EUC-JP']);
        $this->assertSame('php://filter/convert.mb.encoding.UTF-8:SJIS,EUC-JP/resource=test.csv', $url);
    }

    public function testConvert()
    {
        ConvertMbEncoding::register();
        $url = ConvertMbEncoding::getFilterURL(__DIR__ . '/fixture/test_sjis.csv', 'UTF-8', 'SJIS-WIN');

        $file = new SplFileObject($url);
        $data = $file->fgetcsv();

        $this->assertSame('ヲ', $data[0]);
    }
}
