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
    
</div>
