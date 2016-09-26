<?php
	$this->assign('title','SHOWCASE | Home');
	$this->assign('nav','home');

	$this->display('_Header.tpl.php');
?>

	<div class="modal hide fade" id="getStartedDialogNode">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>Getting Started With Phreeze</h3>
		</div>
		<div class="modal-body" style="max-height: 300px">
			<p>This site has been generated by Phreeze classbuilder and contains basic DB
			read and write capability.  One UI page has been created for each table in your
			database.  Click on the links in the top navigation bar to view the pages.</p>

			<p>The application is not intended to use as-is unless you only want
			a simple web interface to your data tables and you require some type
			of authorization to access the app.  In order to convert this into
			a real working application you will need to customize each page as needed.
			The philosophy behind the auto-generated code is to
			generate more code than you need.  You can and should delete the controllers,
			methods and views that you don't need.</p>

			<h4>UI Controls</h4>

			<p>The UI controls for editing fields are generated based on the database column types.
			The generator doesn't know the <i>purpose</i> of each field, though.  For example an INT
			field may be best displayed as a regular input, a slider or an on/off switch.  It's
			possible that the field shouldn't be editable by the user at all.
			The generator doesn't know these things and so it makes a best guess based on
			column types and sizes.  You will most likely have to switch out UI controls that
			are best for your application.  Bootstrap provides a lot of great UI controls
			for you to use.</p>

			<h4>Controllers</h4>

			<p>One controller has been created for each table in the application.
			The controllers are located in /libs/Controller/.
			If a particular table is not directly editable then the controller and
			view templates should be deleted.  An example might be a table
			used in a many-to-many assignment.</p>

			<h4>Models</h4>

			<p>Several Model files have been created for each table in the application.
			The model files are located in /libs/Model/.
			If your schema changes you can re-generate only the DAO (data-access object)
			files by selecting the DAO-Only package in class builder.  As long as you
			don't touch files in the /libs/Model/DAO/ folder then you can safely make
			changes to your database schema and regenerate code without losing any
			of your customizations.</p>

		</div>
		<div class="modal-footer">
			<button id="okButton" data-dismiss="modal" class="btn btn-primary">Let's Rock...</button>
		</div>
	</div>

		<div class="container">

		<!-- Main hero unit for a primary marketing message or call to action -->
		

		
		<div class="hero-unit">
			 <div style="width: 60%; float:left">
			<h1>Welkom</h1>
			 <br>
			 
			
			<p>Welkom bij de Showcase backend.</p>
			
			<p>Via deze webapp beheer je Nodes, Sensoren, AlarmRegels en AlarmNotificaties. Tevens krijg je toegang tot Observaties en Alarmen.</p>
			
			<p>Maak in het bovenstaande menu of via de buttons beneden je keuze.</p>
			</div>
			
			<div style="width: 30%; float:right">
			 <img src="images\Pelicase-1400.jpg" class="img-circle" alt="Cinque Terre">
			 </div>

			<br style="clear:both;"/>
			<!-- <p>The default Bootstrap style of this application can be easily customized and extended with
			a drop-in replacement theme from
			<a href="https://wrapbootstrap.com/?ref=phreeze">{wrap}bootstrap</a>
			and <a href="http://www.google.com/search?q=bootstrap+themes">many others resources</a>.</p>-->
			
			<!--<p><em>Generated with Phreeze 3.3.8 HEAD.
			Running on Phreeze <?php $this->eprint($this->PHREEZE_VERSION); ?><?php if ($this->PHREEZE_PHAR) { $this->eprint(' (' . basename($this->PHREEZE_PHAR) . ')'); } ?>.</em></p>-->
			
			<!-- <a class="btn btn-primary btn-large" data-toggle="modal" href="#getStartedDialog">Get Started &raquo;</a></p> -->
		</div>

		<!-- Example row of columns -->
		<div class="row">
			<div class="span4">
				<h2><i class="icon-screenshot"></i> <a href="./nodes"> Nodes</a></h2>
				<p>Een node is de fysieke printplaat waarop de diverse sensoren zijn aangesloten. Node is verantwoordelijk voor de communicatie van sensor
				observaties naar de Showcase Backend. <a class="btn btn-link" data-toggle="modal" href="#getStartedDialogNode">Meer info &raquo;</a></p>
				
			</div>
			
			
			
			<div class="span4">
				<h2><i class="icon-camera"></i> <a href="./sensoren"> Sensoren</a></h2>
				<p>Een sensor is op node aangesloten stuk elektronisch zintuig. Denk aan temperatuur, luchtvochtigheid, microswitch, PIR sensor enz.<a class="btn btn-link" data-toggle="modal" href="#getStartedDialogNode">Meer info &raquo;</a></p>
				
			</div>
			
			<div class="span4">
				<h2><i class="icon-th-list"></i> <a href="./alarm_regels"> Alarm Regels</a></h2>
				<p>Alarm regels bepalen wanneer de aan de Node gekoppelde Sensoren in Alarm gaan. <a class="btn btn-link" data-toggle="modal" href="#getStartedDialogNode">Meer info &raquo;</a></p>
				
			</div>
		
			
			
		</div>	
		
		<div class="row">	
			<div class="span4">
				<h2><i class="icon-fire"></i> <a href="./alarmen"> Alarmen</a></h2>
				<p>Lijst van alle alarmeringen. Alarmering is een observatie welke een alarm regel overtreed.<a class="btn btn-link" data-toggle="modal" href="#getStartedDialogNode">Meer info &raquo;</a></p>
				
			</div>
			
			<div class="span4">
				<h2><i class="icon-bullhorn"></i> <a href="./alarm_notificaties"> Alarm Notificaties</a></h2>
				<p>AlarmNotificaties bepalen wie voor een AlarmRegel een notificatie ontvangt..<a class="btn btn-link" data-toggle="modal" href="#getStartedDialogNode">Meer info &raquo;</a></p>
				
			</div>
			
			<div class="span4">
				<h2><i class="icon-eye-open"></i> <a href="./observaties"> Observaties</a></h2>
				<p>Lijst van alle observaties. Observatie is waarneming door een Sensor <a class="btn btn-link" data-toggle="modal" href="#getStartedDialogNode">Meer info &raquo;</a></p>
				
			</div>
			
		
			
		
		</div>

	</div> <!-- /container -->

<?php
	$this->display('_Footer.tpl.php');
?>