<div class="pager">

    <div class="row">
        <div class="small-24 large-4 column">
            <span ng-show="listloading === false
                    && pager.showing_from > 0
                    && pager.showing_to > 0
                    && pager.totalResults > 0">
                        <?php echo JText::_("SHOWING"); ?> {{pager.showing_from}} - {{pager.showing_to}} <?php echo JText::_("OF"); ?> {{pager.totalResults}}
            </span>
        </div>

        <div class="small-24 large-14 column">

            <ul style="margin: 1em 0;">
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

        <div class="small-24 large-6 column">
            <div style="margin: 1em 0;">
                <?php echo JText::_("COM_WEBPORTAL_PROPERTIES_PERPAGE"); ?> :
                <select ng-model="pager.size" ng-change="filter()" style="width: auto">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

    </div>


</div>
