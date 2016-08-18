package showcase.sensor

import static org.springframework.http.HttpStatus.*
import grails.transaction.Transactional

@Transactional(readOnly = true)
class SensorTypeController {

    static allowedMethods = [save: "POST", update: "PUT", delete: "DELETE"]

    def index(Integer max) {
        params.max = Math.min(max ?: 10, 100)
        respond SensorType.list(params), model:[sensorTypeCount: SensorType.count()]
    }

    def show(SensorType sensorType) {
        respond sensorType
    }

    def create() {
        respond new SensorType(params)
    }

    @Transactional
    def save(SensorType sensorType) {
        if (sensorType == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (sensorType.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond sensorType.errors, view:'create'
            return
        }

        sensorType.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.created.message', args: [message(code: 'sensorType.label', default: 'SensorType'), sensorType.id])
                redirect sensorType
            }
            '*' { respond sensorType, [status: CREATED] }
        }
    }

    def edit(SensorType sensorType) {
        respond sensorType
    }

    @Transactional
    def update(SensorType sensorType) {
        if (sensorType == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (sensorType.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond sensorType.errors, view:'edit'
            return
        }

        sensorType.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.updated.message', args: [message(code: 'sensorType.label', default: 'SensorType'), sensorType.id])
                redirect sensorType
            }
            '*'{ respond sensorType, [status: OK] }
        }
    }

    @Transactional
    def delete(SensorType sensorType) {

        if (sensorType == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        sensorType.delete flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.deleted.message', args: [message(code: 'sensorType.label', default: 'SensorType'), sensorType.id])
                redirect action:"index", method:"GET"
            }
            '*'{ render status: NO_CONTENT }
        }
    }

    protected void notFound() {
        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.not.found.message', args: [message(code: 'sensorType.label', default: 'SensorType'), params.id])
                redirect action: "index", method: "GET"
            }
            '*'{ render status: NOT_FOUND }
        }
    }
}
