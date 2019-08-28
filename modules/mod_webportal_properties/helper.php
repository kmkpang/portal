<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 8/2/15
 * Time: 4:42 PM
 */


defined('_JEXEC') or die;

/**
 * Helper for wp_menu
 *
 * @package     Joomla.Site
 * @subpackage  wp_menu
 * @since       1.5
 */
class WpPropertiesModuleHelper
{

    private static $exclusionList = array();

    /**
     * @param $params $params Joomla\Registry\Registry
     * @return bool|mixed|string
     */
    public static function getProperties(&$params)
    {

        $rows = (int)$params->get('rows');
        $columns = (int)$params->get('columns');
        $type = $params->get('property_type');
        $officeId = (int)$params->get('office_id');
        $agentId = (int)$params->get('agent_id');
        $regionId = (int)$params->get('region_id');
        $cityTownId = (int)$params->get('city_town_id');
        $postalCodeId = (int)$params->get('zip_code_id');
        $categoryId = (int)$params->get('category_id');


        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();

        if ($type == 'newest') {
            $searchModel->order = array(ORDER_BY_NEWEST_FIRST);
        } else if ($type == 'random') {
            $searchModel->order = array(ORDER_BY_RANDOM);
        } else if ($type == 'featured') {
            $searchModel->is_featured = 1;
        } else if ($type == 'investment') {
            $searchModel->investment = 1;
        } else if ($type == 'open_house') {
            $searchModel->order = array(ORDER_BY_OPENHOUSE_FIRST);
        } else if ($type == 'next_previous') {
            $searchModel =

                WFactory::getServices()
                    ->getServiceClass(__PROPPERTY_PORTAL_SEARCH)
                    ->generateSearchModelFromSearchHash(
                        JFactory::getApplication()->input->getBase64('hash')
                    );

            $searchModel->is_next_previous = true;
            $searchModel->next_previous_center_property_id = JFactory::getApplication()->input->getInt('propertyid', 0);
            $searchModel->next_previous_max_length = 4;
        }

        if ($officeId > 0) {
            $searchModel->office_id = $officeId;
        }
        if ($agentId > 0) {
            $searchModel->sale_id = $agentId;
        }
        if ($regionId > 0) {
            $searchModel->region_id = $regionId;
        }
        if ($cityTownId > 0) {
            $searchModel->city_town_id = $cityTownId;
        }
        if ($postalCodeId > 0) {
            $searchModel->zip_code_id = $postalCodeId;
        }
        if ($categoryId > 0) {
            $searchModel->category_id = $categoryId;
        }

        $searchModel->limit_start = 0;
        $searchModel->limit_length = $rows * $columns;
        $searchModel->returnType = RETURN_TYPE_LIST;
        $searchModel->return_properties_with_no_address = true;
        $searchModel->exclusion_list = WpPropertiesModuleHelper::$exclusionList;

        $properties = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->getProperties($searchModel->limit_length, RETURN_TYPE_LIST, $searchModel->order[0], $searchModel);

        //open house does NOT always return stuffs..so make sure we fill the rest up with newest properties

        if ($type === 'open_house' && count($properties) < $searchModel->limit_length) {

            if (is_array($properties[0]) && $properties[0]['search_key_only'] !== null) {
                $properties = array();
            } else {
                /**
                 * @var $p PropertyListModel
                 */
                foreach ($properties as $p) {
                    if ($p->property_id !== null)
                        WpPropertiesModuleHelper::$exclusionList[] = $p->property_id;
                }
                $searchModel->exclusion_list = WpPropertiesModuleHelper::$exclusionList;
            }


            //fill rest up!!
            $searchModel->order = array(ORDER_BY_NEWEST_FIRST);
            $searchModel->limit_length = $rows * $columns - count($properties);
            $restOfProperties = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->getProperties($searchModel->limit_length, RETURN_TYPE_LIST, $searchModel->order[0], $searchModel);

            foreach ($restOfProperties as $r)
                $properties[] = $r;

        } else if ($type !== 'next_previous') {
            /**
             * @var $p PropertyListModel
             */
            foreach ($properties as $p) {
                if ($p->property_id !== null)
                    WpPropertiesModuleHelper::$exclusionList[] = $p->property_id;
            }
        }


        return $properties;
    }
}