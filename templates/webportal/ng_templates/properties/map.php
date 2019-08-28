<?php
require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

?>

<div id="gmap-popup">
    <div class="info-window clearfix">
        <a href="#" target="_blank" id="gmap-property-link">
            <label id="gmap-<?php echo getParam(propertyTitle) == true ? "title" : "address" ?>" class="gmap-popup-bold"></label>

            <div id="gmap-popup-tab">
                <table>
                    <tr>
                        <td>
                            <img id="gmap-image" src="" class="info-window-thumb">
                        </td>
                        <td style="vertical-align: top;padding-left: 20px">
                            <label class="gmap-popup-bold"><?php echo JText::_("TYPE") ?>
                                : </label><label id="gmap-type"></label>
                            <br />
                            <label class="gmap-popup-bold"><?php echo JText::_("PRICE") ?> : </label>
                            <label id="gmap-price"></label>
                            <label id="gmap-salerent"></label>
                            <br />
                            <label class="gmap-popup-bold"><?php echo JText::_("BEDROOMS") ?>
                                : </label><label id="gmap-bedrooms"></label>
                            <br />
                            <label class="gmap-popup-bold"><?php echo JText::_("BATHROOMS") ?>
                                : </label><label id="gmap-bathrooms"></label>
                            <br />
                            <label class="gmap-popup-bold"><?php echo JText::_("SIZE") ?>
                                : </label><label id="gmap-size"></label> <?php echo JText::_("SQM") ?>
                            <br />
                        </td>
                    </tr>
                </table>
            </div>
        </a>
    </div>

</div>
