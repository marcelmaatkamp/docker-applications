<!doctype html>
<html>
<head>
    <meta name="layout" content="main"/>
    <title>Welcome to Showcase</title>
    <asset:link rel="icon" href="favicon.ico" type="image/x-ico" />
</head>
<body>
    <div class="svg" role="presentation">
        <div class="grails-logo-container">
            <asset:image src="iot.png" class="grails-logo" />
        </div>
    </div>



<div class="well">
<p>Maak een keuze</p>
</div>






        <section class="row colset-2-its">

<g:link controller="persoon" action="index">
    <button type="button" class="btn btn-lg btn-primary">Persoon</button>
</g:link>

<g:link controller="sensor" action="index">
    <button type="button" class="btn btn-lg btn-primary">Sensor</button>
</g:link>

<g:link controller="sensorType" action="index">
    <button type="button" class="btn btn-lg btn-primary">Sensor Type</button>
</g:link>


<g:link controller="switchEvent" action="index">
    <button type="button" class="btn btn-lg btn-primary">Alarm Event</button>
</g:link>

<g:link controller="temperatureEvent" action="index">
    <button type="button" class="btn btn-lg btn-primary">Heartbeat Event</button>
</g:link>



        </section>


<br>
<br>
<br>
<br>

</body>
</html>
