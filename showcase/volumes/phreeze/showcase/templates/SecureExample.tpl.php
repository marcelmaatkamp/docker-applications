<?php
	$this->assign('title','AUTHEXAMPLE Secure Example');
	$this->assign('nav','secureexample');
	$this->display('_Header.tpl.php');
?>

<div class="container">

	<?php if ($this->feedback) { ?>
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<?php $this->eprint($this->feedback); ?>
		</div>
	<?php } ?>
	
	<!-- #### this view/tempalate is used for multiple pages.  the controller sets the 'page' variable to display differnet content ####  -->
	<?php if ($this->page == 'login') { ?>
		<div class="hero-unit">
		<div style="width: 10%; float:right">
			 <img src="images\Pelicase-1400.jpg" class="img-circle" alt="Case">
			 </div>
		
			<h1><b class="icon-lock"> Showcase Login</b></h1>
			
			
			
			<p>
				<!--<a href="secureuser" class="btn btn-primary btn-large">Visit User Page</a>
				<a href="secureadmin" class="btn btn-primary btn-large">Visit Admin Page</a> -->
				<?php if (isset($this->currentUser)) { ?>
					<a href="logout" class="btn btn-primary btn-large">Logout</a>
				<?php } ?>
			</p>
		</div>
		<form class="well" method="post" action="login">
			<fieldset>
			<legend>Login met gebruikersnaam en wachtwoord:</legend>
				<div class="control-group">
				<input id="username" name="username" type="text" placeholder="Gebruikersnaam..." />
				</div>
				<div class="control-group">
				<input id="password" name="password" type="password" placeholder="Wachtwoord..." />
				</div>
				<div class="control-group">
				<button type="submit" class="btn btn-primary">Login</button>
				</div>
			</fieldset>
		</form>
	<?php } else { ?>
	
		<div class="hero-unit">
			<h1>Welkom <strong><?php $this->eprint($this->currentUser->Username); ?></strong></h1>
			
						<br style="clear:both;"/>
			
			<!--<p>This page is accessible only to <?php $this->eprint($this->page == 'userpage' ? 'authenticated users' : 'administrators'); ?>.  -->
			Maak je keuze in de menubalk.</p>
			<!--<p>
				<a href="secureuser" class="btn btn-primary btn-large">Visit User Page</a>
				<a href="secureadmin" class="btn btn-primary btn-large">Visit Admin Page</a>
				<a href="logout" class="btn btn-primary btn-large">Logout</a>
			</p>-->
		</div>
	<?php } ?>

</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>