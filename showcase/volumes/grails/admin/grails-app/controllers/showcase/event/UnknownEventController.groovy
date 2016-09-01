package showcase.event

import static org.springframework.http.HttpStatus.*
import grails.transaction.Transactional

@Transactional(readOnly = true)
class UnknownEventController {

    static allowedMethods = [save: "POST", update: "PUT", delete: "DELETE"]

    def index(Integer max) {
        params.max = Math.min(max ?: 10, 100)
        respond UnknownEvent.list(params), model:[unknownEventCount: UnknownEvent.count()]
    }

    def show(UnknownEvent unknownEvent) {
        respond unknownEvent
    }

    def create() {
        respond new UnknownEvent(params)
    }

    @Transactional
    def save(UnknownEvent unknownEvent) {
        if (unknownEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (unknownEvent.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond unknownEvent.errors, view:'create'
            return
        }

        unknownEvent.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.created.message', args: [message(code: 'unknownEvent.label', default: 'UnknownEvent'), unknownEvent.id])
                redirect unknownEvent
            }
            '*' { respond unknownEvent, [status: CREATED] }
        }
    }

    def edit(UnknownEvent unknownEvent) {
        respond unknownEvent
    }

    @Transactional
    def update(UnknownEvent unknownEvent) {
        if (unknownEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (unknownEvent.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond unknownEvent.errors, view:'edit'
            return
        }

        unknownEvent.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.updated.message', args: [message(code: 'unknownEvent.label', default: 'UnknownEvent'), unknownEvent.id])
                redirect unknownEvent
            }
            '*'{ respond unknownEvent, [status: OK] }
        }
    }

    @Transactional
    def delete(UnknownEvent unknownEvent) {

        if (unknownEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        unknownEvent.delete flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.deleted.message', args: [message(code: 'unknownEvent.label', default: 'UnknownEvent'), unknownEvent.id])
                redirect action:"index", method:"GET"
            }
            '*'{ render status: NO_CONTENT }
        }
    }

    protected void notFound() {
        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.not.found.message', args: [message(code: 'unknownEvent.label', default: 'UnknownEvent'), params.id])
                redirect action: "index", method: "GET"
            }
            '*'{ render status: NOT_FOUND }
        }
    }
}
