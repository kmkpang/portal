<div class="pager ng-cloak" ng-show="!listloading && items.length > 0">
    <div class="row">
        <div class="small-24 small-only-text-center medium-6 large-6 column no-pad-left gap-bttom-small-only">
            <span>
                  <?php echo JText::_("SHOWING"); ?> {{pager.showing_from}} - {{pager.showing_to}} <?php echo JText::_("OF"); ?> {{pager.totalResults}}
            </span>
        </div>

        <div class="small-24 small-only-text-center medium-12 large-12 column">

            <ul class="text-center">
                <li ng-show="pager.pagenumbers.indexOf(0) == -1">
                    <button ng-click="pager.setPage(0)">&laquo;</button>
                </li>
                <li ng-show="pager.totalpages > 0">
                    <button ng-disabled="pager.page <= 0 || pager.disableNav" ng-click="pager.prevPage()">&lt;</button>
                </li>
                <li ng-repeat="pagenum in pager.pagenumbers">
                    <a ng-disabled="pagenum == pager.page" ng-click="pager.setPage(pagenum)">{{pagenum + 1}}</a>
                </li>
                <li ng-show="pager.totalpages > 0">
                    <button ng-disabled="pager.page >= pager.totalpages - 1 || pager.disableNav"
                            ng-click="pager.nextPage()">
                        &gt;</button>
                </li>
                <li ng-show="pager.pagenumbers.indexOf(pager.totalpages - 1) == -1">
                    <button ng-click="pager.setPage(pager.totalpages - 1)">&raquo;</button>
                </li>
            </ul>

        </div>

        <div class="small-24 small-only-text-center medium-6 large-6 column no-pad-right">
            <div class="input-textbox--wrapper text-right">
                <?php echo JText::_("COM_WEBPORTAL_PROPERTIES_PERPAGE"); ?> :
                <select ng-model="pager.size"
                        ng-options="o as o for o in pager.available_size"
                        ng-change="filter()" style="width: auto">
                </select>
            </div>
        </div>
    </div>

    <?php if (WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->getParam('isBatch') == 'true') { ?>
    <div class="row row-batch-tools--wrapper">
        <div class="column small-24 no-pad-left side-by-side-container">
            <div class="input-send-email checkbox-one side-by-side-child" ng-hide="!sendEmail">
                <input type="checkbox" id="checkedAll" ng-model="checkedAllPropertiesForEmail" ng-click="toggleSelectAllPropertiesForEmail()"/>
                <label for="checkedAll"></label>
            </div>


            <a class="search-filters__label button-submit side-by-side-child"
               ng-hide="!sendEmail"
               ng-click="showSendMailForm()"><?php echo JText::_("SEND EMAIL") ?></a>

            <a class="search-filters__label button-submit side-by-side-child"
               ng-click="emailToggle()"><i class='fa fa-times' ng-hide="!sendEmail" aria-hidden="true"></i> <?php echo JText::_("BATCH") ?></a>
        </div>
    </div>
    <?php } ?>
</div>
