<?php

namespace Heyday\SilverStripeRedirects\Code;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\Search\SearchContext;

/**
 * Class RedirectsModelAdmin
 * @package Heyday\SilverStripeRedirects\Code
 */
class RedirectsModelAdmin extends ModelAdmin
{
    /**
     * @var array
     */
    private static $managed_models = [
        'Heyday\SilverStripeRedirects\Code\RedirectUrl'
    ];

    /**
     * @var string
     */
    private static $url_segment = 'redirects-management';

    /**
     * @var string
     */
    private static $menu_title = 'Redirects';

    /**
     * @param null $id
     * @param null $fields
     * @return $this|Form
     */
    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        $gridFieldName = $this->sanitiseClassName($this->modelClass);
        $gridField = $form->Fields()->fieldByName($gridFieldName);

        // order the list in Date created DESC
        $list = $gridField->getList()->sort('Created DESC');
        $gridField->setList($list);

        return $form;
    }


    /**
     * @return SearchContext
     */
    public function getSearchContext()
    {
        $context = parent::getSearchContext();

        $context->getFields()->push(new LiteralField('dateinfo', '<h3>Filter between dates</h3>'));

        $dateField = new DateField("q[FromDate]", "From Date");
        // Get the DateField portion of the DatetimeField and
        // Explicitly set the desired date format and show a date picker
        $dateField->setDateFormat('dd/MM/yyyy')
            ->setAttribute('showcalendar', true);
        $context->getFields()
            ->push($dateField);

        $dateField = new DateField("q[ToDate]", "To Date");
        // Get the DateField portion of the DatetimeField and
        // Explicitly set the desired date format and show a date picker
        $dateField->setDateFormat('dd/MM/yyyy')
            ->setAttribute('showcalendar', true);
        $context->getFields()
            ->push($dateField);

        return $context;
    }

    /**
     * @return mixed
     */
    public function getList()
    {
        $list = parent::getList();
        $params = $this->request->requestVar('q'); // use this to access search parameters

        if (isset($params['FromDate']) && $params['FromDate']) {
            $list = $list->exclude('Created:LessThan', $params['FromDate']);
        }

        if (isset($params['ToDate']) && $params['ToDate']) {
            //split  date into day month year variables
            list($day, $month, $year) = sscanf($params['ToDate'], "%d/%d/%d");
            //date functions expect US date format, create new date object
            $date = new \DateTime("$month/$day/$year");
            //create interval of Plus 1 Day (P1D)
            $interval = new \DateInterval('P1D');
            //add interval to the date
            $date->add($interval);
            //use the new date value as the GreaterThan exclusion filter
            $list = $list->filter('Created:LessThan', date_format($date, 'd/m/Y'));
        }

        return $list;
    }
}
