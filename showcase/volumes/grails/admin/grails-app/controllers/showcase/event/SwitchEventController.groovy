package showcase.event

import static org.springframework.http.HttpStatus.*
import grails.transaction.Transactional

@Transactional(readOnly = true)
class SwitchEventController {

    static allowedMethods = [save: "POST", update: "PUT", delete: "DELETE"]

    def index(Integer max) {
        params.max = Math.min(max ?: 10, 100)
        respond SwitchEvent.list(params), model:[switchEventCount: SwitchEvent.count()]
    }

    def show(SwitchEvent switchEvent) {
        respond switchEvent
    }

    def create() {
        respond new SwitchEvent(params)
    }

    @Transactional
    def save(SwitchEvent switchEvent) {
        if (switchEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (switchEvent.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond switchEvent.errors, view:'create'
            return
        }

        switchEvent.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.created.message', args: [message(code: 'switchEvent.label', default: 'SwitchEvent'), switchEvent.id])
                redirect switchEvent
            }
            '*' { respond switchEvent, [status: CREATED] }
        }
    }

    def edit(SwitchEvent switchEvent) {
        respond switchEvent
    }

    @Transactional
    def update(SwitchEvent switchEvent) {
        if (switchEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (switchEvent.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond switchEvent.errors, view:'edit'
            return
        }

        switchEvent.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.updated.message', args: [message(code: 'switchEvent.label', default: 'SwitchEvent'), switchEvent.id])
                redirect switchEvent
            }
            '*'{ respond switchEvent, [status: OK] }
        }
    }

    @Transactional
    def delete(SwitchEvent switchEvent) {

        if (switchEvent == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        switchEvent.delete flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.deleted.message', args: [message(code: 'switchEvent.label', default: 'SwitchEvent'), switchEvent.id])
                redirect action:"index", method:"GET"
            }
            '*'{ render status: NO_CONTENT }
        }
    }

    protected void notFound() {
        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.not.found.message', args: [message(code: 'switchEvent.label', default: 'SwitchEvent'), params.id])
                redirect action: "index", method: "GET"
            }
            '*'{ render status: NOT_FOUND }
        }
    }
}
