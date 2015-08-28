<!--
add feed
-->
<div class="modal fade" id="modalAddFeed">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add feed</h4>
            </div>
            <div class="modal-body">
                @include('partials.addfeed')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!--
manage feeds
-->

<div class="modal fade" id="modalManageFeeds">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span class="modal-title">Rename feeds</span>
            </div>
            <div class="modal-body">
                <div id="manageFeeds">

                    <div ng-repeat="(key, feed) in feeds" class="row form-group">
                        <div class="col-xs-4">
                            <span ng-show="iFeedEditing != key">@{{ feeds[key].name  }}</span>
                            <input type="text" ng-show="iFeedEditing == key" ng-model="feeds[key].name  "/>
                        </div>
                        <div class="col-xs-4 limit ellipsis">
                            <span>@{{ feed.feed.url  }}</span>
                        </div>
                        <div class="col-xs-2" ng-show="iFeedUpdating != key">
                            <!-- edit feed -->
                            <a ng-click="editFeed(key)" ng-show="iFeedEditing != key" class="btn btn-default">edit</a>
                            <!-- update/save [changes] -->
                            <a ng-show="iFeedEditing == key" ng-click="updateFeed(key)" class="btn btn-primary">save</a>
                        </div>
                        <div class="col-xs-2" ng-show="iFeedUpdating != key">
                            <!-- delete -->
                            <a ng-click="deleteFeed(key)" ng-show="iFeedEditing != key" class="btn btn-danger">delete</a>
                            <!-- cancel edit -->
                            <a ng-show="iFeedEditing == key" ng-click="cancelEdit()" class="btn btn-default">cancel</a>
                        </div>

                        <div class="col-xs-4" ng-show="iFeedUpdating == key">
                            <i class="fa fa-spinner fa-spin"></i> updating
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<!--
-->

<div class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                <p>One fine body&hellip;</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
