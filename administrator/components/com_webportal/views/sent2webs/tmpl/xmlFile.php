<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 8/22/15
 * Time: 8:13 PM
 */


$id = JFactory::getApplication()->input->getInt('xmlId');
$xml = htmlspecialchars(WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SENTTOWEB)->getXml($id));


?>
<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js?skin=desert"></script>
<body style="background: #333">

<pre class="prettyprint"
     style="border: 1px solid darkgray;padding: 5px;border-radius: 5px;"><?php echo $xml ?></pre>

</body>



