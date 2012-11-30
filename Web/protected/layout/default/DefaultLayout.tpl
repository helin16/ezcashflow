<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<com:THead ID="titleHeader" Title="<%$ AppTitle %>">
	<meta http-equiv="Pragma" content="no-cache"/>
	<meta http-equiv="Cache-Control" content="no-cache"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta http-equiv="content-language" content="en"/>
	<meta name="description" content="Ezcashflow">
	<meta name="keywords" content="cashflow">
</com:THead>
<body>
    <com:TForm>
        <div id="bodyWrapper">
            <div id="header">
                <div class="widthWrapper">
					<com:Application.controls.Menu.Menu Id="topMenu"/>
				</div>
            </div>
            <div id="content">
                <div class="widthWrapper">
					<com:TActiveLabel Id="InfoMsg" ForeColor="green" style="width:100%"/>
					<com:TActiveLabel Id="ErrorMsg" ForeColor="red" style="width:100%"/>
					<com:TContentPlaceHolder ID="MainContent" />
				</div>
            </div>
            <div id="footer">
                <div class="widthWrapper">
                </div>
            </div>
		</div>
	</com:TForm>
</body>
</html>