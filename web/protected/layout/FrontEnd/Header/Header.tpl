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
					<li><a href="/">Home</a></li>
					<li><a href="/accounts.html">Accounts</a></li>
					<li><a href="/properties.html">Properties</a></li>
					<li><a href="/logout.html">Logout</a></li>
				</ul>
			</div>
		</div>
	</nav>
</com:TPanel>