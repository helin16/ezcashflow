<div class="menuWrapper">
	<div class="menuRow">
		<span class="topMenuDiv"></span>
	    <span class="topMenuUser"><com:TLabel ID="user" CssClass="welcomeMsg"/></span>
	</div>
	<div class="menuRow">
	    <span class="topMenuDiv">
	        <ul>
	            <li><a href="/" <%= $this->changeId('home') %>>Home</a></li>
	            <li><a href="/accounts.html" <%= $this->changeId('accounts') %>>Accounts</a></li>
	            <li><a href="/reports.html" <%= $this->changeId('reports') %>>Reports</a></li>
	            <li><a href="/statics.html" <%= $this->changeId('statics') %>>Statics</a></li>
	            <li><com:TLinkButton ID="logout" OnClick="logout" Text="Logout" CssClass="menuItem" /></li>
	        </ul>
	    </span>
	    <span class="topMenuUser"></span>
	</div>
</div>