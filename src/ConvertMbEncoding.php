<?php

namespace StreamFilter;

use php_user_filter;

class ConvertMbEncoding extends php_user_filter
{
    /**
     * @var string
     */
    const FILTERNAME = 'convert.mb.encoding';

    /**
     * @var string|string[]
     */
    private $fromEncoding;

    /**
     * @var string|null
     */
    private $toEncoding;

    /**
     * Register the class as a stream filter
     */
    public static function register()
    {
        $filterName = self::FILTERNAME . '.*';
        if (!in_array($filterName, stream_get_filters(), true)) {
            stream_filter_register($filterName, __CLASS__);
        }
    }

    /**
     * Static method to return the stream filter filtername
     *
     * @param string $toEncoding
     * @param string|string[]|null $fromEncoding
     * @return string
     */
    public static function getFilterName($toEncoding, $fromEncoding = null)
    {
        if ($fromEncoding === null) {
            return sprintf('%s.%s', self::FILTERNAME, $toEncoding);
        }

        if (is_array($fromEncoding)) {
            $fromEncoding = implode(',', $fromEncoding);
        }
        return sprintf('%s.%s:%s', self::FILTERNAME, $toEncoding, $fromEncoding);
    }

    /**
     * Return filter URL
     *
     * @param string $filename
     * @param string $toEncoding
     * @param string|string[]|null $fromEncoding
     * @return string
     */
    public static function getFilterURL($filename, $toEncoding, $fromEncoding = null)
    {
        return sprintf('php://filter/%s/resource=%s', self::getFilterName($toEncoding, $fromEncoding), $filename);
    }

    /**
     * {@inheritdoc}
     */
    public function onCreate()
    {
        $prefix = self::FILTERNAME . '.';
        if (0 !== strpos($this->filtername, $prefix)) {
            return false;
        }
        $encodings = substr($this->filtername, strlen($prefix));
        $encodings = explode(':', $encodings);

        $this->toEncoding = $encodings[0];
        $this->fromEncoding = $encodings[1] ?? null;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = mb_convert_encoding($bucket->data, $this->toEncoding, $this->fromEncoding);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }
}
