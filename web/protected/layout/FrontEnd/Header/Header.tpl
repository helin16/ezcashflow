<com:TPanel id="topmenu" CssClass="top-head">
	<nav class="navbar navbar-default" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#top-menu-div">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/" id="logo" title="<%= $this->getPage()->getAppName() %>"><%= $this->getPage()->getAppLogo() === '' ? $this->getPage()->getAppName() : $this->getPage()->getAppLogo() %></a>
			</div>
			<div class="collapse navbar-collapse" id="top-menu-div">
				<ul class="nav navbar-nav navbar-right top-menu" >
					<li class="<%= $this->isActive('home') === true ? 'active' : '' %>"><a href="/">Home</a></li>
					<li class="<%= $this->isActive('accountentry.list') === true ? 'active' : '' %>"><a href="/accounts.html">Accounts</a></li>
					<li class="<%= $this->isActive('transaction.list') === true ? 'active' : '' %>"><a href="/transactions.html">Transactions</a></li>
					<li class="<%= $this->isActive('property.list') === true ? 'active' : '' %>"><a href="/properties.html">Properties</a></li>
					<li class="dropdown">
						<a href="javascript: void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							Reports
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="/reports/profitnlost.html"><i class="glyphicon glyphicon-tasks"></i> Profit N Lost</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="javascript: void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							<%= Core::getUser()->getPerson()->getFullName() %>
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="/users/me.html"><i class="glyphicon glyphicon-cog"></i> Change Details</a></li>
							<li><a href="/logout.html"><i class="glyphicon glyphicon-log-out"></i> LogOut</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>
</com:TPanel>