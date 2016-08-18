package showcase

import static org.springframework.http.HttpStatus.*
import grails.transaction.Transactional

@Transactional(readOnly = true)
class PersoonController {

    static allowedMethods = [save: "POST", update: "PUT", delete: "DELETE"]

    def index(Integer max) {
        params.max = Math.min(max ?: 10, 100)
        respond Persoon.list(params), model:[persoonCount: Persoon.count()]
    }

    def show(Persoon persoon) {
        respond persoon
    }

    def create() {
        respond new Persoon(params)
    }

    @Transactional
    def save(Persoon persoon) {
        if (persoon == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (persoon.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond persoon.errors, view:'create'
            return
        }

        persoon.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.created.message', args: [message(code: 'persoon.label', default: 'Persoon'), persoon.id])
                redirect persoon
            }
            '*' { respond persoon, [status: CREATED] }
        }
    }

    def edit(Persoon persoon) {
        respond persoon
    }

    @Transactional
    def update(Persoon persoon) {
        if (persoon == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (persoon.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond persoon.errors, view:'edit'
            return
        }

        persoon.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.updated.message', args: [message(code: 'persoon.label', default: 'Persoon'), persoon.id])
                redirect persoon
            }
            '*'{ respond persoon, [status: OK] }
        }
    }

    @Transactional
    def delete(Persoon persoon) {

        if (persoon == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        persoon.delete flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.deleted.message', args: [message(code: 'persoon.label', default: 'Persoon'), persoon.id])
                redirect action:"index", method:"GET"
            }
            '*'{ render status: NO_CONTENT }
        }
    }

    protected void notFound() {
        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.not.found.message', args: [message(code: 'persoon.label', default: 'Persoon'), params.id])
                redirect action: "index", method: "GET"
            }
            '*'{ render status: NOT_FOUND }
        }
    }
}
