<?php

namespace Tests\RestAPI;

use Tests\TestCase;

class RestAPITestCase extends TestCase
{
    /**
     * Base API uri.
     */
    const API_ROUTE = 'api/v1/';

    /**
     * Version route.
     *
     * @param  string  $uri
     * @return string
     */
    public function versionRoute($uri)
    {
        return url(self::API_ROUTE . $uri);
    }
}
