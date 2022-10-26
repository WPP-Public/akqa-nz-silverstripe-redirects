<?php

namespace Heyday\SilverStripeRedirects\Source;

use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Middleware\HTTPMiddleware;

/**
 * Class RequestFilter
 * @package Heyday\SilverStripeRedirects\Source
 */
class RequestFilter implements HTTPMiddleware
{
    /**
     * @var Redirector
     */
    protected $redirector;

    /**
     * @param Redirector $redirector
     */
    public function __construct(Redirector $redirector)
    {
        $this->redirector = $redirector;
    }

    public function process(HTTPRequest $request, callable $delegate)
    {
        if (!Director::is_cli() && $response = $this->redirector->getResponse($request)) {
            $response->output();
            exit;
        }

        $response = $delegate($request);

        if (!$response) {
            return null;
        }

        return $response;
    }
}
