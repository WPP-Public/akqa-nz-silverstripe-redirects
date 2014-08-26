<?php

namespace Heyday\Redirects\DataSource;

use Heyday\Redirects\CacheableDataSourceInterface;
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
     * @return \Heyday\Redirects\Redirect[]
     */
    public function get()
    {
        return array_map([$this->transformer, 'transform'], $this->list->toArray());
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return md5($this->list->sql());
    }

    /**
     * @return bool
     */
    public function delete()
    {

    }
}