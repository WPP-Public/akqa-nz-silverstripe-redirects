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
        'To' => 'Varchar(2560)',
        'Type' => 'Enum("Permanent,Vanity","Permanent")'
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'FromRelation' => 'SiteTree',
        'ToRelation' => 'SiteTree'
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'FromLink',
        'ToLink',
        'Type'
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'From',
        'To',
        'Type'
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
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = new FieldList();

        $fields->push($manual = new ToggleCompositeField(
            'TextLinks',
            'Enter urls',
            [
                new TextField('From', 'From url (e.g. "/my-page/")'),
                new TextField('To', 'To url (e.g. "/my-page/", "http://google.com/")')
            ]
        ));

        $fields->push($page = new ToggleCompositeField(
            'SiteTree',
            'Select pages from list',
            [
                new TreeDropdownField('FromRelationID', 'From', 'SiteTree'),
                new TreeDropdownField('ToRelationID', 'To', 'SiteTree')
            ]
        ));

        $fields->push(new DropdownField(
            'Type',
            'Type',
            [
                'Permanent' => 'Permanent',
                'Vanity' => 'Vanity'
            ]
        ));

        if ($this->getField('From') || $this->getField('To')) {
            $manual->setStartClosed(false);
        }
        
        if ($this->getField('FromRelationID') || $this->getField('ToRelationID')) {
            $page->setStartClosed(false);
        }
        
        return $fields;
    }

    /**
     * @return string|bool
     */
    public function getFromLink()
    {
        return $this->getLink('From');
    }

    /**
     * @return string|bool
     */
    public function getToLink()
    {
        return $this->getLink('To');
    }

    /**
     * Returns the right status code depending on type of redirect.
     * @return int
     */
    public function getStatusCode()
    {
        switch (strtolower($this->Type)) {
            case 'Permanent':
                return 301;
            case 'Vanity':
                return 302;
            default:
                return 301;
        }
    }

    /**
     * @param string $type
     * @return string|bool
     */
    protected function getLink($type)
    {
        if (!$relation = $this->getLinkRelation($type)) {
            return $this->getField($type);
        }

        return sprintf(
            "/%s",
            ltrim($relation->RelativeLink(), '/')
        );
    }

    /**
     * @param string $type
     * @return bool|SiteTree
     */
    protected function getLinkRelation($type)
    {
        $relation = $this->getComponent(sprintf("%sRelation", $type));
        
        return $relation->exists() ? $relation : false;
    }

    /**
     * @return RedirectUrlValidator
     */
    public function getCMSValidator()
    {
        return new RedirectUrlValidator();
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
     * Clear out from and to manual links if we have a relation
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        
        if ($this->isChanged('FromRelationID') && $this->getLinkRelation('From')) {
            $this->setField('From', '');
        }

        if ($this->isChanged('ToRelationID') && $this->getLinkRelation('To')) {
            $this->setField('To', '');
        }

        if ($this->isChanged('From') && $this->getField('From')) {
            $this->setField('FromRelationID', 0);
        }

        if ($this->isChanged('To') && $this->getField('To')) {
            $this->setField('ToRelationID', 0);
        }
    }

    /**
     *
     */
    protected function onAfterWrite()
    {
        parent::onAfterWrite();
        if (isset($this->dataSource)) {
            $this->dataSource->delete();
        }
    }

    /**
     *
     */
    protected function onAfterDelete()
    {
        parent::onAfterDelete();
        if (isset($this->dataSource)) {
            $this->dataSource->delete();
        }
    }
}
