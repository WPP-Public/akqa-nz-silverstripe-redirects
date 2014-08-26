<?php

namespace Heyday\Redirects;

use DataObject;
use Permission;
use PermissionProvider;
use RequiredFields;

/**
 * @package Heyday\Redirects
 */
class RedirectUrl extends DataObject implements PermissionProvider
{
    /**
     *
     */
    const PERMISSION = 'MANAGE_REDIRECTS';

    /**
     * @var array
     */
    static $db = array(
        'From' => "Text",
        'To' => 'Text'
    );

    /**
     * @var array
     */
    static $summary_fields = array(
        'From',
        'To'
    );

    /**
     * @var bool
     */
    public $refresh = true;

    /**
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return new RequiredFields('From', 'To');
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return array(self::PERMISSION => "Manage redirections");
    }


    /**
     * @param null $member
     * @return bool|int
     */
    public function canEdit($member = null)
    {
        return $this->hasPermission($member);
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canCreate($member = null)
    {
        return $this->hasPermission($member);
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canDelete($member = null)
    {
        return $this->hasPermission($member);
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canView($member = null)
    {
        return $this->hasPermission($member);
    }

    /**
     * @param null $member
     * @return bool|int
     */
    protected function hasPermission($member = null)
    {
        return Permission::checkMember($member, self::PERMISSION);
    }

    /**
     *
     */
    protected function onAfterWrite()
    {
        parent::onAfterWrite();

        // Only refresh if we have created a valid redirect i.e. model admin
        // creates a redirect as soon as you click create
        $from = trim($this->From);
        if (!empty($from) && $this->refresh) {
            UrlMap::refresh();
        }
    }

    /**
     *
     */
    protected function onAfterDelete()
    {
        parent::onAfterDelete();

        UrlMap::refresh();
    }

    /**
     * @return DateRangeSearchContext
     */
    public function getDefaultSearchContext()
    {
        return new DateRangeSearchContext(
            $this->class,
            $this->scaffoldSearchFields(),
            $this->defaultSearchFilters()
        );
    }
}