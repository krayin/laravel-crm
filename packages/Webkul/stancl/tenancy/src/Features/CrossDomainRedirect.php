<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Features;

use Illuminate\Http\RedirectResponse;
use Stancl\Tenancy\Contracts\Feature;
use Stancl\Tenancy\Tenancy;

class CrossDomainRedirect implements Feature
{
    public function bootstrap(Tenancy $tenancy): void
    {
        RedirectResponse::macro('domain', function (string $domain) {
            /** @var RedirectResponse $this */

            // replace first occurance of hostname fragment with $domain
            $url = $this->getTargetUrl();
            $hostname = parse_url($url, PHP_URL_HOST);
            $position = strpos($url, $hostname);
            $this->setTargetUrl(substr_replace($url, $domain, $position, strlen($hostname)));

            return $this;
        });
    }
}
