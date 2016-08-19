package admin

class UrlMappings {

    static mappings = {
        "/$controller/$action?/$id?(.$format)?"{
            constraints {
                // apply constraints here
            }
        }

        "/"(view:"/index")
        "/start"(view:"/start")
        "500"(view:'/error')
        "404"(view:'/notFound')
        
        // RESTService api
        "/api/temperatureEvent"(resources: 'temperatureEvent')
        "/api/switchEvent"(resources: 'switchEvent')
    }
}
