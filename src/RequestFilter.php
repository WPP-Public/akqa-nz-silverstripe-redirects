<?php

namespace Heyday\SilverStripeRedirects\Source;

use SilverStripe\Control\Director;
use SilverStripe\Control\RequestFilter as SilverStripeRequestFilter;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;

/**
 * TODO use HTTP Middleware instead of RequestFilter as it is deprecated
 * Class RequestFilter
 * @package Heyday\SilverStripeRedirects\Source
 */
class RequestFilter implements SilverStripeRequestFilter
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

    /**
     * Filter executed before a request processes
     *
     * @param HTTPRequest $request Request container object
     * @return boolean Whether to continue processing other filters. Null or true will continue processing (optional)
     */
    public function preRequest(HTTPRequest $request)
    {
        if (!Director::is_cli() && $response = $this->redirector->getResponse($request)) {
            $response->output();
            exit;
        }
    }

    /**
     * Filter executed AFTER a request
     *
     * @param HTTPRequest $request Request container object
     * @param HTTPResponse $response
     * @return boolean Whether to continue processing other filters. Null or true will continue processing (optional)
     */
    public function postRequest(HTTPRequest $request, HTTPResponse $response)
    {
        //NOOP
    }
}