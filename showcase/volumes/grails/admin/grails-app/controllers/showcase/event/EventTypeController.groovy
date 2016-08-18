package showcase.event

import static org.springframework.http.HttpStatus.*
import grails.transaction.Transactional

@Transactional(readOnly = true)
class EventTypeController {

    static allowedMethods = [save: "POST", update: "PUT", delete: "DELETE"]

    def index(Integer max) {
        params.max = Math.min(max ?: 10, 100)
        respond EventType.list(params), model:[eventTypeCount: EventType.count()]
    }

    def show(EventType eventType) {
        respond eventType
    }

    def create() {
        respond new EventType(params)
    }

    @Transactional
    def save(EventType eventType) {
        if (eventType == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (eventType.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond eventType.errors, view:'create'
            return
        }

        eventType.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.created.message', args: [message(code: 'eventType.label', default: 'EventType'), eventType.id])
                redirect eventType
            }
            '*' { respond eventType, [status: CREATED] }
        }
    }

    def edit(EventType eventType) {
        respond eventType
    }

    @Transactional
    def update(EventType eventType) {
        if (eventType == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (eventType.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond eventType.errors, view:'edit'
            return
        }

        eventType.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.updated.message', args: [message(code: 'eventType.label', default: 'EventType'), eventType.id])
                redirect eventType
            }
            '*'{ respond eventType, [status: OK] }
        }
    }

    @Transactional
    def delete(EventType eventType) {

        if (eventType == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        eventType.delete flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.deleted.message', args: [message(code: 'eventType.label', default: 'EventType'), eventType.id])
                redirect action:"index", method:"GET"
            }
            '*'{ render status: NO_CONTENT }
        }
    }

    protected void notFound() {
        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.not.found.message', args: [message(code: 'eventType.label', default: 'EventType'), params.id])
                redirect action: "index", method: "GET"
            }
            '*'{ render status: NOT_FOUND }
        }
    }
}
