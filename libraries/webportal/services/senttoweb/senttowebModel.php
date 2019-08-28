<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 8/20/15
 * Time: 11:26 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';

define("DIRECTION_INCOMING", "INCOMING");
define("DIRECTION_OUTGOING", "OUTGOING");
define("COMMAND_CREATE", "CREATE");
define("COMMAND_UPDATE", "UPDATE");
define("COMMAND_DELETE", "DELETE");
define("SENTTOWEB_OFFICE", "OFFICE");
define("SENTTOWEB_AGENT", "AGENT");
define("SENTTOWEB_PROPERTY", "PROPERTY");
define("SENTTOWEB_PROJECT", "PROJECT");

class SenttowebModel extends ModelBase
{

    /**
     * Id of a particular sent to web xml
     * @var int
     */
    var $id;
    /**
     * Mysql formatted date time
     * @var String
     */
    var $date;
    /**
     * use defined constant DIRECTION_INCOMING or DIRECTION_OUTGOING
     * or if string, use "INCOMING" or "OUTGOING"
     * @var String
     */
    var $direction;
    /**
     * use defined constant COMMAND_CREATE,COMMAND_UPDATE or COMMAND_DELETE
     * or if string, use "CREATE","UPDATE" or "DELETE"
     * @var String
     */
    var $command;
    /**
     * use defined constant SENTTOWEB_OFFICE,SENTTOWEB_AGENT or SENTTOWEB_PROPERTY
     * or if string, use "OFFICE","AGENT" or "PROPERTY"
     * @var
     */
    var $type;

    var $data;
    /**
     * property uniqueID
     * @var String
     */
    var $propertyUniqueId;

    /**
     * agent uniqueID
     * @var String
     */
    var $agentUniqueId;

    /**
     * office uniqueID
     * @var String
     */
    var $officeUniqueId;

    /**
     * @var PortalPortalSenttowebLogSql
     */
    var $rawSqlClass;

    /*--------------------------------------*/

    /**
     * Command ( in case OUTGOING ) or Reply ( in case INCOMING ) that caused this msg
     * @var SenttowebModel
     */
    var $rawAssociatedSqlClass;
    /**
     * @var PortalPortalSenttowebLogSql
     */
    var $associatedSentToWeb;
    /**
     * do you want to get or NOT get the associated xml ?
     * @var bool
     */
    var $getAssociated = true;


    var $officeAgentPropertyId = 0;
    var $officeAgentPropertyLink = 0;


}
