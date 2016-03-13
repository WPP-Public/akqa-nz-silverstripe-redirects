<?php

namespace Heyday\Redirects\DataSource;

use Heyday\Redirects\CacheableDataSourceInterface;
use Heyday\Redirects\Redirect;
use Heyday\Redirects\TransformerInterface;

class DataListDataSource implements CacheableDataSourceInterface
{
    /** @var \DataList */
    protected $list;

    /** @var \Heyday\Redirects\TransformerInterface */
    protected $transformer;

    /**
     * @param \DataList $list
     * @param \Heyday\Redirects\TransformerInterface $transformer
     */
    public function __construct(\DataList $list, TransformerInterface $transformer)
    {
        $this->list = $list;
        $this->transformer = $transformer;
    }

    /**
     * In order to improve performance for a larger number of redirects, instead of a standard array being generated
     *      an associative array (keyed on the 'from' URL) is generated which allows a direct check rather than needing
     *      to iterate through a large list (especially considering this would be done on each page request).
     *
     * @return \Heyday\Redirects\Redirect[]
     */
    public function get()
    {
        $data = array();

        /* @var \Heyday\Redirects\Redirect $redirect */
        foreach (array_map([$this->transformer, 'transform'], $this->list->toArray()) as $redirect) {
            // Format the URL so it will match the SS_HTTPRequest request URL
            $data[Redirect::formatUrl($redirect->getFrom())] = $redirect;
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return md5($this->list->sql());
    }
}