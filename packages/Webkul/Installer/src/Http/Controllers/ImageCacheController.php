<?php

namespace Webkul\Installer\Http\Controllers;

use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Facades\Cache;

class ImageCacheController
{
    /**
     * Cache template
     *
     * @var string
     */
    protected $template;

    /**
     * Logo
     *
     * @var string
     */
    const KRAYIN_LOGO = 'https://updates.krayincrm.com/krayin.png';

    /**
     * Get HTTP response of template applied image file
     *
     * @param  string  $filename
     * @return Illuminate\Http\Response
     */
    public function getImage($filename)
    {
        try {
            $content = Cache::remember('krayin-logo', 10080, function () {
                return $this->getImageFromUrl(self::KRAYIN_LOGO);
            });
        } catch (\Exception $e) {
            $content = '';
        }

        return $this->buildResponse($content);
    }

    /**
     * Init from given URL
     *
     * @param  string  $url
     * @return \Intervention\Image\Image
     */
    public function getImageFromUrl($url)
    {
        $domain = config('app.url');

        $options = [
            'http' => [
                'method'           => 'GET',
                'protocol_version' => 1.1, // force use HTTP 1.1 for service mesh environment with envoy
                'header'           => "Accept-language: en\r\n".
                "Domain: $domain\r\n".
                "User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36\r\n",
            ],
        ];

        $context = stream_context_create($options);

        if ($data = @file_get_contents($url, false, $context)) {
            return $data;
        }

        throw new \Exception(
            'Unable to init from given url ('.$url.').'
        );
    }

    /**
     * Builds HTTP response from given image data
     *
     * @param  string  $content
     * @return Illuminate\Http\Response
     */
    protected function buildResponse($content)
    {
        /**
         * Define mime type
         */
        $mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $content);

        /**
         * Respond with 304 not modified if browser has the image cached
         */
        $eTag = md5($content);

        $notModified = isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $eTag;

        $content = $notModified ? null : $content;

        $statusCode = $notModified ? 304 : 200;

        /**
         * Return http response
         */
        return new IlluminateResponse($content, $statusCode, [
            'Content-Type'   => $mime,
            'Cache-Control'  => 'max-age=10080, public',
            'Content-Length' => strlen($content),
            'Etag'           => $eTag,
        ]);
    }
}
