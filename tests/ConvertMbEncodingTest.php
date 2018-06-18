<?php

namespace Test\StreamFilter;

use PHPUnit\Framework\TestCase;
use StreamFilter\ConvertMbEncoding;

class ConvertMbEncodingTest extends TestCase
{
    public function testRegister()
    {
        $this->assertNotContains(ConvertMbEncoding::FILTERNAME . '.*', stream_get_filters());
        ConvertMbEncoding::register();
        $this->assertContains('convert.mb.encoding.*', stream_get_filters());
    }
}