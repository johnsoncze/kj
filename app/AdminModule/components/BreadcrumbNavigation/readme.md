**Usage:**<br />
$nameResolver = new NameResolver($this->context->getByType(Translator::class));<br />
$extension = new PresenterExtension($this, $nameResolver);<br>
$navigation = $this->navigationFactory->create();<br>
$navigation->extension($extension);
<br>
<br>
**Add parent:**<br />
For add parent link you can use annotation 
"@breadcrumb-nav-parent :Admin:Login:Default"