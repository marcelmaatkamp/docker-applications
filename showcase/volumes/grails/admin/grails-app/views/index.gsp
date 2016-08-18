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

    <div id="content" role="main">
        <section class="row colset-2-its">
            <div id="controllers" role="navigation">
                <h2>Kies een item om te bewerken:</h2>
                <ul>
                    <li class="controller"> <g:link controller="persoon">persoon</g:link> </li>
                    <li class="controller"> <g:link controller="sensor">device</g:link> </li>
		<hr />
                    <li class="controller"> <g:link controller="temperatureEvent">heartbeat events</g:link> </li>
                    <li class="controller"> <g:link controller="switchEvent">alarm events</g:link> </li>
                </ul>
            </div>
        </section>
    </div>

</body>
</html>
