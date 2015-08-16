<div class="modal-body ng-scope" id="siteLoginRegister">

	<div id="loginRegisterDialog" class="ng-scope">

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist" id="login_register_tablist">
			<li role="presentation" ng-class="{active: tab == 'login'}" class="active" style=""><a role="tab" data-toggle="tab" ng-click="setActiveTab('login')">login</a></li>
			<li role="presentation" ng-class="{active: tab == 'register'}"><a role="tab" data-toggle="tab" ng-click="setActiveTab('register')">register</a></li>
			<li role="presentation" class=""></li>
		</ul>


		<!-- Tab panes -->
		<div class="tab-content">
			<!---- login tab ---->
			<div role="tabpanel" class="tab-pane active" ng-class="{active: tab == 'login'}" id="login">











				<form>
					<div class="feedback"></div>
					<div class="loading alert app"><i class="fa fa-spinner fa-spin"></i> loading</div>
					<div class="form-group">
						<label for="exampleInputEmail1">Email address</label>
						<input ng-model="email" name="email" type="email" class="form-control ng-pristine ng-untouched ng-valid ng-valid-email" id="login_email" placeholder="Email">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword1">Password</label>
						<input ng-model="password" name="password" type="password" class="form-control ng-pristine ng-untouched ng-valid" id="login_password" placeholder="Password">
					</div>
					<button type="button" class="btn btn-default main-color flat btn-lg btn-block" ng-click="login()">login</button>
				</form>






				<p>
					<a href="#reset_password_tab" role="tab" data-toggle="tab" id="reset_password_button" class="btn btn-inverse form-control" ng-click="setActiveTab('reset_password')">forgot password</a>
				</p>
			</div>














			<!---- register tab ---->
			<div role="tabpanel" class="tab-pane" ng-class="{active: tab == 'register'}" id="register">
				<!-- ngIf: alert.register -->
				<form role="form" id="register_form" method="post" ng-submit="register()" class="ng-pristine ng-valid ng-valid-email">
					<div class="feedback"></div>
					<div class="loading alert app"><i class="fa fa-spinner fa-spin"></i> loading</div>
					<div class="form-group">
						<label for="exampleInputEmail1">Email address</label>
						<input ng-model="email" name="email" type="email" class="form-control ng-pristine ng-untouched ng-valid ng-valid-email" id="register_email" placeholder="Enter email">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword1">Password</label>
						<input ng-model="password" name="password" type="password" class="form-control ng-pristine ng-untouched ng-valid" id="register_password" placeholder="Password">
					</div>
					<button type="submit" class="btn btn-default main-color flat btn-lg btn-block">register</button>
				</form>

			</div>

			<!---- reset password tab ---->
			<div role="tabpanel" class="tab-pane" id="reset_password_tab" ng-class="{active: tab == 'reset_password'}">

				<form role="form" id="password_reset_form" method="post" ng-submit="resetPassword()" class="ng-pristine ng-valid ng-valid-email">
					<div class="feedback"></div>
					<div class="loading alert app"><i class="fa fa-spinner fa-spin"></i> loading</div>
					<div class="form-group">
						<label for="exampleInputEmail1">Email address</label>
						<input ng-model="email" name="email" type="email" class="form-control ng-pristine ng-untouched ng-valid ng-valid-email" id="reset_email" placeholder="Enter email">
					</div>
					<button type="submit" class="btn btn-default main-color flat btn-lg btn-block">reset password</button>
				</form>

			</div>
		</div>
	</div>

</div>