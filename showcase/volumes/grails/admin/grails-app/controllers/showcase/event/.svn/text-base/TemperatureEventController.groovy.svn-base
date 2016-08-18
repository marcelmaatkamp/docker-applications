package showcase.event

import static org.springframework.http.HttpStatus.*
import grails.transaction.Transactional

@Transactional(readOnly = true)
class TemperatureEventController {

    static allowedMethods = [save: "POST", update: "PUT", delete: "DELETE"]

    def index(Integer max) {
        params.max = Math.min(max ?: 10, 100)
        respond TemperatureEvent.list(params), model:[temperatureEventCount: TemperatureEvent.count()]
    }

    def show(TemperatureEvent temperatureEvent) {
        respond temperatureEvent
    }

    def create() {
        respond new TemperatureEvent(params)
    }

    @Transactional
    def save(TemperatureEvent temperatureEvent) {
        if (temperatureEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (temperatureEvent.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond temperatureEvent.errors, view:'create'
            return
        }

        temperatureEvent.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.created.message', args: [message(code: 'temperatureEvent.label', default: 'TemperatureEvent'), temperatureEvent.id])
                redirect temperatureEvent
            }
            '*' { respond temperatureEvent, [status: CREATED] }
        }
    }

    def edit(TemperatureEvent temperatureEvent) {
        respond temperatureEvent
    }

    @Transactional
    def update(TemperatureEvent temperatureEvent) {
        if (temperatureEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (temperatureEvent.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond temperatureEvent.errors, view:'edit'
            return
        }

        temperatureEvent.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.updated.message', args: [message(code: 'temperatureEvent.label', default: 'TemperatureEvent'), temperatureEvent.id])
                redirect temperatureEvent
            }
            '*'{ respond temperatureEvent, [status: OK] }
        }
    }

    @Transactional
    def delete(TemperatureEvent temperatureEvent) {

        if (temperatureEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        temperatureEvent.delete flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.deleted.message', args: [message(code: 'temperatureEvent.label', default: 'TemperatureEvent'), temperatureEvent.id])
                redirect action:"index", method:"GET"
            }
            '*'{ render status: NO_CONTENT }
        }
    }

    protected void notFound() {
        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.not.found.message', args: [message(code: 'temperatureEvent.label', default: 'TemperatureEvent'), params.id])
                redirect action: "index", method: "GET"
            }
            '*'{ render status: NOT_FOUND }
        }
    }
}
