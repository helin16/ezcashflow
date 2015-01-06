<com:TPanel id="topmenu" CssClass="top-head">
	<div class="container">
		<nav class="navbar navbar-default" role="navigation">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#top-menu-div">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/" id="logo" title="<%= $this->getPage()->getAppName() %>"></a>
			</div>
			<div class="collapse navbar-collapse" id="top-menu-div">
				<ul class="nav navbar-nav navbar-right top-menu" >
					<li><a href="/#header">Home</a></li>
					<li><a href="/#why">Why</a></li>
					<li><a href="/#about">About</a></li>
					<li><a href="/#contactus">Contact</a></li>
				</ul>
			</div>
		</nav>
	</div>
</com:TPanel>