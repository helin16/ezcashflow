<!DOCTYPE html>
<html lang="en">
<com:THead ID="titleHeader">
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="keywords" content="<%= $this->getPage()->getAppName() %>, Online Property Management, Online Property Management Platform">
	<meta name="description" content="<%= $this->getPage()->getAppName() %> is a free Online Proeprty Management Platform, that allows Owners, Agents and Tenants to communicate, share documents and track issues with each other.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</com:THead>
<body role="document">
	<com:TForm id="mainForm" Attributes.class="main-form" Attributes.onSubmit="return false;">
		<header id="header">
			<com:Application.layout.FrontEnd.Header.Header />
		</header>
		<section id="mainbody">
			<com:TContentPlaceHolder ID="MainContent" />
		</section>
		<com:Application.layout.FrontEnd.Footer.Footer />
	</com:TForm>
</body>
</html>