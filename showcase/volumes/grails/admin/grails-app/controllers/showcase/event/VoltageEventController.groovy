package showcase.event

import static org.springframework.http.HttpStatus.*
import grails.transaction.Transactional

@Transactional(readOnly = true)
class VoltageEventController {

    static allowedMethods = [save: "POST", update: "PUT", delete: "DELETE"]

    def index(Integer max) {
        params.max = Math.min(max ?: 10, 100)
        respond VoltageEvent.list(params), model:[voltageEventCount: VoltageEvent.count()]
    }

    def show(VoltageEvent voltageEvent) {
        respond voltageEvent
    }

    def create() {
        respond new VoltageEvent(params)
    }

    @Transactional
    def save(VoltageEvent voltageEvent) {
        if (voltageEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (voltageEvent.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond voltageEvent.errors, view:'create'
            return
        }

        voltageEvent.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.created.message', args: [message(code: 'voltageEvent.label', default: 'VoltageEvent'), voltageEvent.id])
                redirect voltageEvent
            }
            '*' { respond voltageEvent, [status: CREATED] }
        }
    }

    def edit(VoltageEvent voltageEvent) {
        respond voltageEvent
    }

    @Transactional
    def update(VoltageEvent voltageEvent) {
        if (voltageEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (voltageEvent.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond voltageEvent.errors, view:'edit'
            return
        }

        voltageEvent.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.updated.message', args: [message(code: 'voltageEvent.label', default: 'VoltageEvent'), voltageEvent.id])
                redirect voltageEvent
            }
            '*'{ respond voltageEvent, [status: OK] }
        }
    }

    @Transactional
    def delete(VoltageEvent voltageEvent) {

        if (voltageEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        voltageEvent.delete flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.deleted.message', args: [message(code: 'voltageEvent.label', default: 'VoltageEvent'), voltageEvent.id])
                redirect action:"index", method:"GET"
            }
            '*'{ render status: NO_CONTENT }
        }
    }

    protected void notFound() {
        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.not.found.message', args: [message(code: 'voltageEvent.label', default: 'VoltageEvent'), params.id])
                redirect action: "index", method: "GET"
            }
            '*'{ render status: NOT_FOUND }
        }
    }
}
