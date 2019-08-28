<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 12/18/14
 * Time: 8:27 PM
 */


// This one just validates if the dtd and xml conforms with each other


$dtdPath = '/home/khan/www/softverk-webportal-remaxth/cli/ddfeed.dtd';
$xmlPath = '/home/khan/www/softverk-webportal-remaxth/cli/DDProperty.xml';


$root = 'listing-data';

$old = new DOMDocument;
$old->load($xmlPath);

$creator = new DOMImplementation;
$doctype = $creator->createDocumentType($root, null, $dtdPath);
$new = $creator->createDocument(null, null, $doctype);
$new->encoding = "utf-8";

$oldNode = $old->getElementsByTagName($root)->item(0);
$newNode = $new->importNode($oldNode, true);
$new->appendChild($newNode);


$result = $new->validate();

echo $result;


