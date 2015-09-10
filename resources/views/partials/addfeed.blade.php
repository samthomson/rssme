<form role="form" name="addfeed" class="form-horizontal" ng-keyup="$event.keyCode == 13 && addFeedSubmit()">
    {!! csrf_field() !!}
    <div class="form-group">

        <label for="addfeed_name" class="col-sm-2 control-label">Feed name</label>
        <div class="col-sm-10">
            <input ng-model="addfeed_name" type="text" name="feedname" class="form-control" id="addfeed_name" placeholder="Feed name">
        </div>

    </div>

    <div class="form-group">

        <label for="addfeed_url" class="col-sm-2 control-label">Feed Url</label>
        <div class="col-sm-10">
            <input ng-model="addfeed_url" type="text" name="feedurl" class="form-control" id="addfeed_url" placeholder="Feed name">
        </div>

    </div>



    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="button" name="submit" class="btn btn-primary form-control" id="add_button" ng-click="addFeedSubmit()"><i class="fa fa-plus"></i> add</button>
        </div>

    </div>
</form>

