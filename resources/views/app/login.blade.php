<div class="modal-body ng-scope" id="siteLoginRegister">

	<div id="loginRegisterDialog" class="ng-scope">

		<!-- Nav tabs 
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#login" aria-controls="home" role="tab" data-toggle="tab">Login</a></li>
			<li role="presentation"><a href="#register" aria-controls="profile" role="tab" data-toggle="tab">Register</a></li>
		</ul>
		-->
		<!-- 
		<div class="tab-content">

			<div role="tabpanel" class="tab-pane active"id="login">


-->
				
				<form {{-- ng-keyup="$event.keyCode == 13 && login()"--}} ng-submit="login()" >
					<div class="feedback"></div>
					<div class="register_feedback"></div>


					<div class="form-group">
						<label for="exampleInputEmail1">Email address</label>
						<input ng-model="email" name="email" type="email" class="form-control ng-pristine ng-untouched ng-valid ng-valid-email" id="login_email" placeholder="Email">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword1">Password</label>
						<input ng-model="password" name="password" type="password" class="form-control ng-pristine ng-untouched ng-valid" id="login_password" placeholder="Password">
					</div>
					<div class="row">
						<div class="col-xs-6">
							<button type="button" class="btn btn-secondary main-color flat btn-lg btn-block" ng-click="register()">register</button>
						</div>
						<div class="col-xs-6">
							<button type="submit" class="btn btn-primary btn-lg btn-block">login</button>
						</div>
					</div>
				</form>



                    <!--
					<a href="#reset_password_tab" role="tab" data-toggle="tab" id="reset_password_button" class="pull-right" ng-click="setActiveTab('reset_password')">forgot password</a>-->

			<!--</div>-->









			<!-- register tab 
			<div role="tabpanel" class="tab-pane" id="register">

				<form role="form" id="register_form" ng-keyup="$event.keyCode == 13 && register()">
					-->
					<!--
					<div class="form-group">
						<label for="exampleInputEmail1">Email address</label>
						<input ng-model="register_email" name="email" type="email" class="form-control " id="register_email" placeholder="Enter email">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword1">Password</label>
						<input ng-model="register_password" name="password" type="password" class="form-control" id="register_password" placeholder="Password">
					</div>
					
				</form>

			</div>-->

		</div>
	</div>

</div>