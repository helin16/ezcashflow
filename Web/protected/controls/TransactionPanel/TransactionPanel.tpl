<com:TPanel ID="menu" GroupingText="<%= $this->groupingText %>" DefaultButton="submitBtn">
	<com:TActiveLabel Id="errorMsg" ForeColor="red"/>
	<com:TActiveLabel Id="infoMsg" ForeColor="green"/>
	<table style="background:#cccccc;padding:15px;" width="100%">
		<tr>
			<td width="10%">
				From
			</td>
			<td>
				<com:TDropDownList Id="fromAccounts" DataValueField="id" DataTextField="name"/>&nbsp;&nbsp;
				<com:TActiveLabel Id="fromAccountsMsg" ForeColor="red"/>
			</td>
		</tr>
		<tr>
			<td>
				To
			</td>
			<td>
				<com:TDropDownList Id="toAccounts" DataValueField="id" DataTextField="name"/>
				&nbsp;&nbsp;
				<com:TActiveLabel Id="toAccountsMsg" ForeColor="red"/>
			</td>
		</tr>
		<tr>
			<td>
				Value:
			</td>
			<td>
				$<com:TTextBox Id="transValue" style="width:80px;"/>&nbsp;&nbsp;
				<com:TActiveLabel Id="valueMsg" ForeColor="red"/>
			</td>
		</tr>
		<tr>
			<td>
				Description:
			</td>
			<td>
				<com:TTextBox Id="description"  width="100%"/>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<com:TButton Id="submitBtn" Text="save" OnClick="save"/>
			</td>
		</tr>
	</table>
</com:TPanel>