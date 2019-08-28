<?php

/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 7:51 PM
 * To change this template use File | Settings | File Templates.
 */
class ArticleService
{


    public function __construct()
    {

    }

    /**
     * @param null $categoryId
     * @return array
     */
    public function getArticles($categoryId = null, $limit = 10)
    {
        if (!WFactory::getHelper()->isNullOrEmptyString($categoryId)) {
            $category = explode(',', $categoryId);
            foreach ($category as &$c)
                $c = "jos_content.catid = $c";

            $category = implode(' or ', $category);
        } else
            $category = "jos_content.catid = $categoryId";

        $mysqlTime = WFactory::getSqlService()->getMySqlDateTime();
        $query = "SELECT jos_content.*
                  FROM calltivation.jos_content jos_content
                  WHERE     ($category)
                       AND (jos_content.publish_up < '$mysqlTime' )
                       AND (jos_content.state = 1)
                       ORDER BY jos_content.publish_up desc
                        LIMIT $limit
                       ";


        $result = WFactory::getSqlService()->select($query);

        return $result;
    }


}