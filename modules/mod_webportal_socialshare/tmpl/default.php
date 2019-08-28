<?php
/**
 * Created by PhpStorm.
 * User: Lian
 * Date: 7/2/14
 * Time: 4:40 AM
 */

defined('__SITEURL'); // just in case i didnt do it already!!
$shareUrl = __SITEURL . $_SERVER['REQUEST_URI'];

?>
<script src="http://platform.twitter.com/widgets.js"></script>
<div class="share-wrapper">

    <a facebook class="facebookShare"
       data-url='<?php echo $shareUrl ?>'
       data-shares='shares'>{{ shares }}</a>

    <a twitter data-lang="en"
       data-count='horizontal'
       data-url='<?php echo $shareUrl ?>'
       data-size="medium"
       data-text=''></a>

    <div gplus class="g-plus"
         data-size="tall"
         data-annotation="bubble"
         data-href='<?php echo $shareUrl ?>'
         data-action='share'></div>

</div>
