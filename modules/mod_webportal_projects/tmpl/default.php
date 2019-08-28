<?php

//$template = $params->get("template");
//$office_id = $params->get("office_id");
//
//$office = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($office_id);
//
//$projects = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROJECTS)->getProjects($office_id);
//require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');
//
//require $template;


$projects = WFactory::getSqlService()->select("SELECT * FROM `jos_portal_projects` where is_deleted=0 order by created_date desc LIMIT 8");
foreach ($projects as $i=>$p) {
    $x = "SELECT * FROM `jos_portal_project_image` where project_id = " . $p["id"];
    $projectImages = WFactory::getSqlService()->select("SELECT * FROM `jos_portal_project_image` where project_id = " . $p["id"]);
    $image = count($projectImages)>0 ? $projectImages[0]:"";
    foreach ($projectImages as $pi){
        if($pi["description"]==='banner'){
            $image=$pi["server_url"];
        }
    }
    $projects[$i]["image"] = $image;
}

function getDataByLanguage($data)
{
    $currentLang = WFactory::getHelper()->getCurrentlySelectedLanguage();
    $data = get_object_vars(json_decode($data));
    if (!WFactory::getHelper()->isNullOrEmptyString($data[$currentLang])) {
        return $data[$currentLang];
    } else {
        foreach ($data as $d)
            if (!WFactory::getHelper()->isNullOrEmptyString($d))
                return $d;
    }

}



?>

<div class="blog--row items-row row row-0 clearfix">
    <?php for ($i = 0; $i < count($projects); $i++ ){ ?>
        <div class="columns small-24 medium-6">
            <div class="blog--item item column-1" itemprop="blogPost" itemscope="" itemtype="http://schema.org/BlogPosting">
                <div class="pull-left item-image">
                    <a href="<?php echo JUri::base()."index.php?option=com_webportal&view=projectdetail&project-id={$projects[$i]["id"]}"?>"><img
                                src="<?php echo $projects[$i]["image"]?>" alt="" itemprop="thumbnailUrl"></a>
                </div>


                <div class="page-header">
                    <h2 itemprop="name">
                        <a href="<?php echo JUri::base()."index.php?option=com_webportal&view=projectdetail&project-id={$projects[$i]["id"]}"?>"" itemprop="url">
                            <?php echo getDataByLanguage($projects[$i]["name"])?></a>
                    </h2>


                </div>


                <div class="icons">


                </div>


<!--                <span class="mod-articles-category-date">26 October 2018</span>-->
            </div>
            <!-- end item -->
        </div>
    <?php } ?>
    <!-- Add blanks -->
    <?php for ($i = 0; $i < count($projects)%4; $i++ ){ ?>
        <div class="columns small-24 medium-6 show-large-only">
        </div>
    <?php } ?>
</div>
