<?php

namespace Heyday\SilverStripeRedirects\Source;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;

/**
 * Class Redirector
 * @package Heyday\SilverStripeRedirects\Source
 */
class Redirector
{
    /** @var \Heyday\SilverStripeRedirects\Source\DataSourceInterface */
    protected $dataSource;

    /**
     * @param \Heyday\SilverStripeRedirects\Source\DataSourceInterface $dataSource
     */
    public function __construct(DataSourceInterface $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * @param HTTPRequest $request
     * @return \Heyday\SilverStripeRedirects\Source\Redirect
     */
    public function getRedirectForRequest(HTTPRequest $request)
    {
        // Format the URL as the key will have been formatted
        $url = Redirect::formatUrl($request->getURL());
        $dataSource = $this->dataSource->get();

        // Check if there's a key for the URL
        if (isset($dataSource[$url])) {
            $redirect = $dataSource[$url];
            if ($redirect->match($url)) {
                return $redirect;
            }
        }
        return false;
    }

    /**
     * @param HTTPRequest $request
     * @return null|HTTPResponse
     */
    public function getResponse(HTTPRequest $request)
    {
        if ($redirect = $this->getRedirectForRequest($request)) {
            $response = new HTTPResponse();
            $response->redirect($redirect->getTo(), $redirect->getStatusCode());
            
            return $response;
        }
        
        return null;
    }
}