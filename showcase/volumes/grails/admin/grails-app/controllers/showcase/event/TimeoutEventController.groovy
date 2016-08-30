package showcase.event

import static org.springframework.http.HttpStatus.*
import grails.transaction.Transactional

@Transactional(readOnly = true)
class TimeoutEventController {

    static allowedMethods = [save: "POST", update: "PUT", delete: "DELETE"]

    def index(Integer max) {
        params.max = Math.min(max ?: 10, 100)
        respond TimeoutEvent.list(params), model:[timeoutEventCount: TimeoutEvent.count()]
    }

    def show(TimeoutEvent timeoutEvent) {
        respond timeoutEvent
    }

    def create() {
        respond new TimeoutEvent(params)
    }

    @Transactional
    def save(TimeoutEvent timeoutEvent) {
        if (timeoutEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (timeoutEvent.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond timeoutEvent.errors, view:'create'
            return
        }

        timeoutEvent.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.created.message', args: [message(code: 'timeoutEvent.label', default: 'TimeoutEvent'), timeoutEvent.id])
                redirect timeoutEvent
            }
            '*' { respond timeoutEvent, [status: CREATED] }
        }
    }

    def edit(TimeoutEvent timeoutEvent) {
        respond timeoutEvent
    }

    @Transactional
    def update(TimeoutEvent timeoutEvent) {
        if (timeoutEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (timeoutEvent.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond timeoutEvent.errors, view:'edit'
            return
        }

        timeoutEvent.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.updated.message', args: [message(code: 'timeoutEvent.label', default: 'TimeoutEvent'), timeoutEvent.id])
                redirect timeoutEvent
            }
            '*'{ respond timeoutEvent, [status: OK] }
        }
    }

    @Transactional
    def delete(TimeoutEvent timeoutEvent) {

        if (timeoutEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        timeoutEvent.delete flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.deleted.message', args: [message(code: 'timeoutEvent.label', default: 'TimeoutEvent'), timeoutEvent.id])
                redirect action:"index", method:"GET"
            }
            '*'{ render status: NO_CONTENT }
        }
    }

    protected void notFound() {
        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.not.found.message', args: [message(code: 'timeoutEvent.label', default: 'TimeoutEvent'), params.id])
                redirect action: "index", method: "GET"
            }
            '*'{ render status: NOT_FOUND }
        }
    }
}
