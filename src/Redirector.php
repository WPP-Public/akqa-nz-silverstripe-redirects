<?php

namespace Heyday\Redirects;

class Redirector
{
    /** @var \Heyday\Redirects\DataSourceInterface */
    protected $dataSource;

    /**
     * @param \Heyday\Redirects\DataSourceInterface $dataSource
     */
    public function __construct(DataSourceInterface $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * @param \SS_HTTPRequest $request
     * @return \Heyday\Redirects\Redirect
     */
    public function getRedirectForRequest(\SS_HTTPRequest $request)
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
     * @param \SS_HTTPRequest $request
     * @return null|\SS_HTTPResponse
     */
    public function getResponse(\SS_HTTPRequest $request)
    {
        if ($redirect = $this->getRedirectForRequest($request)) {
            $response = new \SS_HTTPResponse();
            $response->redirect($redirect->getTo(), $redirect->getStatusCode());
            
            return $response;
        }
        
        return null;
    }
}