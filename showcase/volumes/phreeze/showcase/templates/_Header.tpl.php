<!DOCTYPE html>
<html lang="en">
	<head>
	
		 <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
     <meta http-equiv="Pragma" content="no-cache"/>
     <meta http-equiv="Expires" content="0"/>
	
		<meta charset="utf-8">
		<meta http-equiv="X-Frame-Options" content="deny">
		<base href="<?php $this->eprint($this->ROOT_URL); ?>" />
		<title><?php $this->eprint($this->title); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="description" content="SHOWCASE" />
		<meta name="author" content="phreeze builder | phreeze.com" />

		<!-- Le styles -->
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />
		<link href="styles/style.css" rel="stylesheet" />
		<link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
		<link href="bootstrap/css/font-awesome.min.css" rel="stylesheet" />
		<!--[if IE 7]>
		<link rel="stylesheet" href="bootstrap/css/font-awesome-ie7.min.css">
		<![endif]-->
		<link href="bootstrap/css/datepicker.css" rel="stylesheet" />
		<link href="bootstrap/css/timepicker.css" rel="stylesheet" />
		<link href="bootstrap/css/bootstrap-combobox.css" rel="stylesheet" />
		
		<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<!-- Le fav and touch icons -->
		<link rel="shortcut icon" href="images/favicon.ico" />
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/apple-touch-icon-114-precomposed.png" />
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/apple-touch-icon-72-precomposed.png" />
		<link rel="apple-touch-icon-precomposed" href="images/apple-touch-icon-57-precomposed.png" />

		<script type="text/javascript" src="scripts/libs/LAB.min.js"></script>
		<script type="text/javascript">
			$LAB.script("//code.jquery.com/jquery-1.8.2.min.js").wait()
				.script("bootstrap/js/bootstrap.min.js")
				.script("bootstrap/js/bootstrap-datepicker.js")
				.script("bootstrap/js/bootstrap-timepicker.js")
				.script("bootstrap/js/bootstrap-combobox.js")
				.script("scripts/libs/underscore-min.js").wait()
				.script("scripts/libs/underscore.date.min.js")
				.script("scripts/libs/backbone-min.js")
				.script("scripts/app.js")
				.script("scripts/model.js").wait()
				.script("scripts/view.js").wait()
		</script>
		
		<!-- Fusion Charts Scripts-->
		<script type="text/javascript" src="fusioncharts-suite-xt/js/fusioncharts.js"></script>
		<script type="text/javascript" src="fusioncharts-suite-xt/js/themes/fusioncharts.theme.ocean.js"></script>

	</head>

	<body>

			<div class="navbar navbar-inverse navbar-fixed-top">
			
			<?php if (isset($this->currentUser)) { ?>
				
						<div class="navbar-inner">
					<div class="container">
					
						<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</a>
						<a class="brand" href="">SHOWCASE</a>
						<div class="nav-collapse collapse">
							<ul class="nav">
								
								<li <?php $this->eprint($this->title); ?></title></li>
								
								
								<?php if ($this->currentUser->RoleId >= 3) { ?>	
								
								<li <?php if ($this->nav=='nodes') { echo 'class="active"'; } ?>><a href="./nodes">Nodes</a></li>
								<li <?php if ($this->nav=='sensoren') { echo 'class="active"'; } ?>><a href="./sensoren">Sensoren</a></li>
								
								<?php } ?>	
																				
								
								<?php if ($this->currentUser->RoleId == 1) { ?>	
								
									<ul class="nav">
										
									
												<li <?php if ($this->nav=='alarmen') { echo 'class="active"'; } ?>><a href="./alarmen">Alarm Rapport <b class="icon-eye-open"></b></a></li>
												</li>
											</ul>
										</li>
									</ul>	
								
								<?php } ?>	
								
								
								<?php if ($this->currentUser->RoleId > 1) { ?>	
								
									<ul class="nav">
										<li class="dropdown">
											<a href="#" class="dropdown-toggle" data-toggle="dropdown">Alarmen <b class="caret"></b></a>
											<ul class="dropdown-menu">
									
												
												<li <?php if ($this->nav=='alarm_regels') { echo 'class="active"'; } ?>><a href="./alarm_regels">Alarm Regels</a></li>
												<li <?php if ($this->nav=='alarm_notificaties') { echo 'class="active"'; } ?>><a href="./alarm_notificaties">Alarm Notificaties</a></li>
												
											</ul>
										</li>
									</ul>	
								
								<?php } ?>	
									
								<?php if ($this->currentUser->RoleId == 1) { ?>
								
									<ul class="nav">
										
											
											
											<li <?php if ($this->nav=='laatste_observaties') { echo 'class="active"'; } ?>><a href="./laatste_observaties">Observatie<b class="icon-eye-open"></b></a></li>	
											
											
										
									</ul>	
								
								<?php } ?>	
								
								<?php if ($this->currentUser->RoleId > 1) { ?>
								
									<ul class="nav">
										<li class="dropdown">
											<a href="#" class="dropdown-toggle" data-toggle="dropdown">Rapportages<b class="caret"></b></a>
											<ul class="dropdown-menu">
											<li <?php if ($this->nav=='alarmen') { echo 'class="active"'; } ?>><a href="./alarmen">Alarmen <b class="icon-eye-open"></b></a></li>
											<li <?php if ($this->nav=='observaties') { echo 'class="active"'; } ?>><a href="./observaties">Observaties <b class="icon-eye-open"></b></a></li>
											<li <?php if ($this->nav=='laatste_observaties') { echo 'class="active"'; } ?>><a href="./laatste_observaties">Laatste Observaties <b class="icon-eye-open"></b></a></li>	
											
											</ul>
										</li>
									</ul>	
								
								<?php } ?>	
								
								
								<?php if ($this->currentUser->RoleId == 4) { ?>
								
									<ul class="nav">
										<li class="dropdown">
											<a href="#" class="dropdown-toggle" data-toggle="dropdown">Toegangsbeheer<b class="caret"></b></a>
											<ul class="dropdown-menu">
									
												<li <?php if ($this->nav=='roles') { echo 'class="active"'; } ?>><a href="./roles">Rollen</a></li>
												<li <?php if ($this->nav=='users') { echo 'class="active"'; } ?>><a href="./users">Gebruikers</a></li>
											</ul>
										</li>
									</ul>	
								
							
								<?php } ?>	
							
							<ul class="nav">
								<li>
								
								<a href="./logout"><i class="icon-lock"></i> Logout
								
								<i class="caret"></i></a>
								
								<!--<li><a href="./loginform">Login/Logout <?php $this->eprint($this->currentUser->Username); ?> </a></li>-->
								</li>
							</ul>
							
							
							<ul class="nav pull-right">
						
								
								<p class="navbar-text"><code>
								<?php $this->eprint($this->currentUser->Username);?>
								<?php if ($this->currentUser->RoleId == 4) { ?>(Tech. Beheerder)<?php } ?>
								<?php if ($this->currentUser->RoleId == 3) { ?>(Func. Beheerder)<?php } ?>
								<?php if ($this->currentUser->RoleId == 1) { ?>(Gebruiker)<?php } ?>
								<span id="last-refresh"></span>	</code></p>
							
								
								
								
							
								
							
								
								
								
							
								

								
						
								
								<!--<li><a href="./loginform">Login/Logout <?php $this->eprint($this->currentUser->Username); ?> </a></li>-->
								
							</ul>
							
								
							
							
							
							
							
							
						</div><!--/.nav-collapse -->
					</div>
				</div>
			</div>
				
						
				
			










			
			
			<?php } ?>
					
					
			
				
				
			
			
			
		
			
			
			