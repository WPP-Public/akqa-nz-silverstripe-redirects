<?php

namespace Heyday\SilverStripeRedirects\Source;

use SilverStripe\Control\Director;
use SilverStripe\ORM\DataModel;
use SilverStripe\Control\RequestFilter as SilverStripeRequestFilter;
use SilverStripe\Control\Session;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;

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
     * @param Session $session Request session
     * @param DataModel $model Current DataModel
     * @return boolean Whether to continue processing other filters. Null or true will continue processing (optional)
     */
    public function preRequest(HTTPRequest $request, Session $session, DataModel $model)
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
     * @param HTTPResponse $response Response output object
     * @param DataModel $model Current DataModel
     * @return boolean Whether to continue processing other filters. Null or true will continue processing (optional)
     */
    public function postRequest(HTTPRequest $request, HTTPResponse $response, DataModel $model)
    {
        //NOOP
    }
}