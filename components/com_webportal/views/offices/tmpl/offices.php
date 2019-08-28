<?php
// Load service
$offices = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOfficeAll();

// die(var_dump($offices));

//print_r($offices[0]['office_name']);
//exit();
function sortArray($a, $b)
{
    return strcmp($a['office_name'], $b['office_name']);
}

usort($offices, "sortArray");
?>

<script type="text/javascript">
    localStorage.clear();
    function filterByText(element) {
        var needle = jQuery(element).val().toUpperCase();
        jQuery(".office-list__name").each(function () {
            var heystack = jQuery(this).text().toUpperCase();
            //alert(heystack);
            if (heystack.match(needle)) {
                jQuery(this).closest(".office-list__item").show();
            }
            else {
                jQuery(this).closest(".office-list__item").hide();
            }
        });

        jQuery(".office-list__item:visible").each(function () {
            jQuery(this).css('list');
        });
        updateAgentsCount();
    }

    function updateAgentsCount() {
        var agents_listed = "Offices";
        var count = jQuery(".office-list__item:visible").length;
        var text = count + " " + agents_listed;

        jQuery("#office_count").text(text);

    }
</script>

<div class="office-list--wrapper large-24 small-24 search-filters">
    <div class="row">
        <!--<h1 class="office-list__header"><?php //echo JText::_("REAL ESTATE AGENCIES") ?></h1>-->

        <div class="row collapse office--counter--row">
            <!-- count query -->
            <div class="columns large-12 small-24">
                <span id="office_count" class="office--counter"><?php echo count($offices)." ".JText::_("OFFICES");?></span>
            </div>

            <!-- search -->
            <div class="columns large-12 small-24">
                <div class="input-textbox--wrapper">
                    <input type="text" name="search" placeholder="<?php echo JText::_("SEARCH")?>" onkeyup="filterByText(this)">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="office-list--wrapper large-24">
    <div class="row">
        <div class="row--office-list">
            <div class="row">
            <?php
            $rows = array_chunk($offices,3);
            foreach ($rows as $offices) {
                echo '<div class="clear-both">';

                foreach ($offices as $num => $office) {
                    require "singleOffice.php";

                }

                echo '</div>';
            } ?>
            </div>
        </div>
    </div>
</div>