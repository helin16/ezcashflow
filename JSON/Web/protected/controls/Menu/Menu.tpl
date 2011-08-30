<com:TPanel ID="menu">
	<style>
		td.topmenulink
		{
		}
		.menuItem,
		a#active
		{
			text-decoration:none;
			padding: 3px 25px 2px 5px;
		}
		a#active
		{
			background:blue;
			color:white;
		}
	</style>
	<table>
		<tr>
			<td class="topmenulink">
				<a href="/" <%= $this->changeId('home') %>>Home</a>
			</td>
			<td class="topmenulink">
				<a href="/accounts.html" <%= $this->changeId('accounts') %>>Accounts</a>
			</td>
			<td class="topmenulink">
				<a href="/reports.html" <%= $this->changeId('reports') %>>Reports</a>
			</td>
			<td class="topmenulink">
				<a href="/statics.html" <%= $this->changeId('statics') %>>Statics</a>
			</td>
			<td class="topmenulink">
				<com:TLinkButton ID="logout" OnClick="logout" Text="Logout" CssClass="menuItem" />
			</td>
		</tr>
	</table>
</com:TPanel>