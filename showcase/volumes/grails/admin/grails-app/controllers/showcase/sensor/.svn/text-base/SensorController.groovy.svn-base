package showcase.sensor

import static org.springframework.http.HttpStatus.*
import grails.transaction.Transactional

@Transactional(readOnly = true)
class SensorController {

    static allowedMethods = [save: "POST", update: "PUT", delete: "DELETE"]

    def index(Integer max) {
        params.max = Math.min(max ?: 10, 100)
        respond Sensor.list(params), model:[sensorCount: Sensor.count()]
    }

    def show(Sensor sensor) {
        respond sensor
    }

    def create() {
        respond new Sensor(params)
    }

    @Transactional
    def save(Sensor sensor) {
        if (sensor == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (sensor.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond sensor.errors, view:'create'
            return
        }

        sensor.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.created.message', args: [message(code: 'sensor.label', default: 'Sensor'), sensor.id])
                redirect sensor
            }
            '*' { respond sensor, [status: CREATED] }
        }
    }

    def edit(Sensor sensor) {
        respond sensor
    }

    @Transactional
    def update(Sensor sensor) {
        if (sensor == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        if (sensor.hasErrors()) {
            transactionStatus.setRollbackOnly()
            respond sensor.errors, view:'edit'
            return
        }

        sensor.save flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.updated.message', args: [message(code: 'sensor.label', default: 'Sensor'), sensor.id])
                redirect sensor
            }
            '*'{ respond sensor, [status: OK] }
        }
    }

    @Transactional
    def delete(Sensor sensor) {

        if (sensor == null) {
            transactionStatus.setRollbackOnly()
            notFound()
            return
        }

        sensor.delete flush:true

        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.deleted.message', args: [message(code: 'sensor.label', default: 'Sensor'), sensor.id])
                redirect action:"index", method:"GET"
            }
            '*'{ render status: NO_CONTENT }
        }
    }

    protected void notFound() {
        request.withFormat {
            form multipartForm {
                flash.message = message(code: 'default.not.found.message', args: [message(code: 'sensor.label', default: 'Sensor'), params.id])
                redirect action: "index", method: "GET"
            }
            '*'{ render status: NOT_FOUND }
        }
    }
}
