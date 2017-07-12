<?php

namespace Heyday\SilverStripeRedirects\Code;

use Heyday\SilverStripeRedirects\Source\DataSource\CachedDataSource;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;

/**
 * Class RedirectUrl
 * @package Heyday\SilverStripeRedirects\Code
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
        'FromRelation' => 'SilverStripe\CMS\Model\SiteTree',
        'ToRelation' => 'SilverStripe\CMS\Model\SiteTree'
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Created' => 'Created',
        'LastEdited.Nice' => 'Last Edited',
        'FromLink' => 'From Link',
        'ToLink' => 'To Link',
        'Type' => 'Type'
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'From' => [
            'title' => 'From URL',
            'type' => 'PartialMatchFilter'
        ],
        'To' => [
            'title' => 'To URL',
            'type' => 'PartialMatchFilter'
        ],
        'Type' => 'PartialMatchFilter'
    ];

    /**
     * @return string
     */
    public function getTitle()
    {
        $from = $this->getFromLink();
        $to = $this->getToLink();

        return "From \"$from\" to \"$to\"";
    }

    /**
     * @var CachedDataSource
     */
    protected $dataSource;

    /**
     * @param CachedDataSource $dataSource
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
        $fields->push(
            new LiteralField(
                'Explanation',
                "<p>Pages selected from the list have precedence over manually entered URLs. A Vanity redirect is temporary(HTTP 302). A permanent is a HTTP 301.</p>"
            )
        );

        $from = new TextField('From', 'From');
        $from->setRightTitle('(e.g "/my-page/")- always include the /');
        $to = new TextField('To', 'To');
        $to->setRightTitle('e.g "/my-page/" for internal pages or "http://google.com/" for external websites (and include the scheme - http:// or https://)');

        $fields->push($manual = new ToggleCompositeField(
            'TextLinks',
            'Enter urls',
            [
                $from,
                $to

            ]
        ));

        $fields->push($page = new ToggleCompositeField(
            'SiteTree',
            'Select pages from list',
            [
                new TreeDropdownField('FromRelationID', 'From', 'SilverStripe\CMS\Model\SiteTree'),
                new TreeDropdownField('ToRelationID', 'To', 'SilverStripe\CMS\Model\SiteTree')
            ]
        ));

        $fields->push(new DropdownField(
            'Type',
            'Type',
            [
                'Vanity' => 'Vanity',
                'Permanent' => 'Permanent'

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
            case 'permanent':
                return 301;
            case 'vanity':
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
     * @param array $context
     * @return bool|int
     */
    public function canCreate($member = null, $context = [])
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
       $te3st = Injector::inst()->get('Heyday\SilverStripeRedirects\Code\RedirectUrl');
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
