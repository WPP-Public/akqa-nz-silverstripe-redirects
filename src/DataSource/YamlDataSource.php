<?php

namespace Heyday\Redirects\DataSource;

use Heyday\Redirects\DataSourceInterface;
use Heyday\Redirects\Redirect;
use Symfony\Component\Yaml\Yaml;

class YamlDataSource implements DataSourceInterface
{
    /** @var string */
    protected $file;

    /**
     * @return \Heyday\Redirects\Redirect[]
     */
    public function get()
    {
        $redirects = [];

        foreach (Yaml::parse($this->file) as $from => $to) {
            $redirects[] = new Redirect($from, $to);
        }
        
        return $redirects;
    }
}