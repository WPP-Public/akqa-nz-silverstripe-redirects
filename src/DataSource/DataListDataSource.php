<?php

namespace Heyday\SilverStripeRedirects\Source\DataSource;

use Heyday\SilverStripeRedirects\Source\CacheableDataSourceInterface;
use Heyday\SilverStripeRedirects\Source\Redirect;
use Heyday\SilverStripeRedirects\Source\TransformerInterface;
use SilverStripe\ORM\DataList;

class DataListDataSource implements CacheableDataSourceInterface
{
    /** @var DataList */
    protected $list;

    /** @var TransformerInterface */
    protected $transformer;

    /**
     * @param DataList $list
     * @param TransformerInterface $transformer
     */
    public function __construct(DataList $list, TransformerInterface $transformer)
    {
        $this->list = $list;
        $this->transformer = $transformer;
    }

    /**
     * In order to improve performance for a larger number of redirects, instead of a standard array being generated
     *      an associative array (keyed on the 'from' URL) is generated which allows a direct check rather than needing
     *      to iterate through a large list (especially considering this would be done on each page request).
     *
     * @return Redirect[]
     */
    public function get()
    {
        $data = array();

        /* @var Redirect $redirect */
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
