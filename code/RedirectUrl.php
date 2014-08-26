<?php

use Heyday\Redirects\DataSource\CachedDataSource;

/**
 * @package Heyday\Redirects
 */
class RedirectUrl extends DataObject implements PermissionProvider
{
    /**
     * Permission for managing redirects
     */
    const PERMISSION = 'MANAGE_REDIRECTS';

    /**
     * @var array
     */
    private static $db = [
        'From' => 'Varchar(2560)',
        'To' => 'Varchar(2560)'
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'From',
        'To'
    ];

    /**
     * @var \Heyday\Redirects\DataSource\CachedDataSource
     */
    protected $dataSource;

    /**
     * @param \Heyday\Redirects\DataSource\CachedDataSource $dataSource
     */
    public function setDataSource(CachedDataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

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
        return [
            self::PERMISSION => "Manage redirections"
        ];
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
        
        $this->dataSource->delete();
    }

    /**
     *
     */
    protected function onAfterDelete()
    {
        parent::onAfterDelete();

        $this->dataSource->delete();
    }
}