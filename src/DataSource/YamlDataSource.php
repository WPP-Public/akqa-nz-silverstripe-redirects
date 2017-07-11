<?php

namespace Heyday\SilverStripeRedirects\Source\DataSource;

use Heyday\SilverStripeRedirects\Source\DataSourceInterface;
use Heyday\SilverStripeRedirects\Source\Redirect;
use Symfony\Component\Yaml\Yaml;

class YamlDataSource implements DataSourceInterface
{
    /** @var string */
    protected $file;

    /**
     * @return Redirect[]
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