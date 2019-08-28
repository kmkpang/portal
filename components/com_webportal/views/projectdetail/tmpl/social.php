<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

?>

<!-- Facebook -->
<div id="fb-root"></div>
<script>(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.10&appId=427207043980298";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

<!-- Line Share button -->
<script src="https://d.line-scdn.net/r/web/social-plugin/js/thirdparty/loader.min.js" async="async"
        defer="defer"></script>


<script type="text/javascript">

    jQuery(document).ready(function () {
        LineIt.loadButton();
    })

</script>

<div class="row small-24 social">
    <div class="small-24 small-centered large-15 columns">

        <div class="row line-it-wrapper">
            <div class="line-it-button" data-lang="en" data-type="share-a"
                 data-url="http://mida.softverk.co.th/index.php?option=com_webportal&amp;view=projectdetail&amp;project-id=2"
                 style="display: none;"></div>
            <div class="line-it-button" data-lang="en" data-type="friend" data-lineid="@shroukkhan" data-count="true"
                 data-home="true" style="display: none;"></div>
            <div class="line-it-button" data-lang="en" data-type="like"
                 data-url="http://mida.softverk.co.th/th/?option=com_webportal&amp;view=projectdetail&amp;project-id=2"
                 data-share="true" data-lineid="@lineteamjp" style="display: none;"></div>
        </div>


        <div class="fb-page"
             data-href="<?php echo $projectDetail['facebookLink']?>"
             data-tabs="timeline,events,messages"
             data-small-header="false"
             data-adapt-container-width="true"
             data-hide-cover="false"
             data-width="1000"
             data-show-facepile="true">
            <blockquote cite="<?php echo $projectDetail['facebookLink']?>" class="fb-xfbml-parse-ignore"><a
                        href="<?php echo $projectDetail['facebookLink']?>"><?php echo $projectDetail['facebookLink']?></a></blockquote>
        </div>


    </div>


</div>
