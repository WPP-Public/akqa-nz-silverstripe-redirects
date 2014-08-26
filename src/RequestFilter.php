<?php

namespace Heyday\Redirects;

use DataModel;
use RequestFilter as SilverStripeRequestFilter;
use Session;
use SS_HTTPRequest;
use SS_HTTPResponse;

class RequestFilter implements SilverStripeRequestFilter
{
    /**
     * @var \Heyday\Redirects\Redirector
     */
    protected $redirector;

    /**
     * @param \Heyday\Redirects\Redirector $redirector
     */
    public function __construct(Redirector $redirector)
    {
        $this->redirector = $redirector;
    }

    /**
     * Filter executed before a request processes
     *
     * @param SS_HTTPRequest $request Request container object
     * @param Session $session Request session
     * @param DataModel $model Current DataModel
     * @return boolean Whether to continue processing other filters. Null or true will continue processing (optional)
     */
    public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model)
    {
        if ($response = $this->redirector->getResponse($request)) {
            $response->output();
            exit;
        }
    }

    /**
     * Filter executed AFTER a request
     *
     * @param SS_HTTPRequest $request Request container object
     * @param SS_HTTPResponse $response Response output object
     * @param DataModel $model Current DataModel
     * @return boolean Whether to continue processing other filters. Null or true will continue processing (optional)
     */
    public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model)
    {
        //NOOP
    }
}