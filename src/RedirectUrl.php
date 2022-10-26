<?php

namespace Heyday\SilverStripeRedirects\Source;

use Heyday\SilverStripeRedirects\Source\DataSource\CachedDataSource;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;

/**
 * Class RedirectUrl
 * @package Heyday\SilverStripeRedirects\Source
 */
class RedirectUrl extends DataObject implements PermissionProvider
{
    /**
     * Permission for managing redirects
     */
    const PERMISSION = 'MANAGE_REDIRECTS';

    /**
     * @var string
     */
    private static $table_name = 'RedirectUrl';

    private static $singular_name = 'Redirect';

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
        'FromRelation' => SiteTree::class,
        'ToRelation' => SiteTree::class
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Created' => 'Created',
        'LastEdited.Nice' => 'Last Edited',
        'FromLink' => 'From',
        'ToLink' => 'To',
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

        $fields->push(new DropdownField(
            'Type',
            'Type of redirect',
            [
                'Vanity' => 'Vanity (302)',
                'Permanent' => 'Permanent (301)'

            ]
        ));

        /**
         * FROM
         */
        $fromTitle = new HeaderField('FromTitle', 'FROM');
        $fromExplanation = new LiteralField(
            'FromExplanation',
            "<p>Please either enter a url by choosing 'manual' or select a page and choose the type 'page'</p>"
        );
        $fromType = new OptionsetField(
            'FromType',
            'Type',
            [
                'manual' => 'manual',
                'page' => 'page'
            ],
            $this->FromRelationID == 0 ? 'manual' : 'page'
        );
        $from = new TextField('From', 'From (manual)');
        $from->setDescription('(e.g "/my-page/")- always include the /');
        $fromPage = new TreeDropdownField('FromRelationID', 'From (page)', 'SilverStripe\CMS\Model\SiteTree');

        $fields->push($fromTitle);
        $fields->push($fromExplanation);
        $fields->push($fromType);
        $fields->push($from);
        $fields->push($fromPage);

        /**
         * TO
         */

        $toTitle = new HeaderField('ToTitle', 'TO');
        $toExplanation = new LiteralField(
            'ToExplanation',
            "<p>Please either enter a url by choosing 'manual' or select a page and choose the type 'page'</p>"
        );
        $toType = new OptionsetField(
            'ToType',
            'Type',
            [
                'manual' => 'manual',
                'page' => 'page'
            ],
            $this->ToRelationID == 0 ? 'manual' : 'page'
        );
        $to = new TextField('To', 'To (manual)');
        $to->setDescription('e.g "/my-page/" for internal pages or "http://google.com/" for external websites (and include the scheme - http:// or https://)');
        $toPage = new TreeDropdownField('ToRelationID', 'To (page)', 'SilverStripe\CMS\Model\SiteTree');

        $fields->push($toTitle);
        $fields->push($toExplanation);
        $fields->push($toType);
        $fields->push($to);
        $fields->push($toPage);


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



    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if ($this->FromType == 'page') {
            $this->setField('From', '');
        } elseif ($this->FromType == 'manual') {
            $this->setField('FromRelationID', 0);
        }

        if ($this->ToType == 'page') {
            $this->setField('To', '');
        } elseif ($this->ToType == 'manual') {
            $this->setField('ToRelationID', 0);
        }
    }


    protected function onAfterWrite()
    {
        parent::onAfterWrite();

        if (isset($this->dataSource)) {
            $this->dataSource->delete();
        }
    }


    protected function onAfterDelete()
    {
        parent::onAfterDelete();

        if (isset($this->dataSource)) {
            $this->dataSource->delete();
        }
    }

}
