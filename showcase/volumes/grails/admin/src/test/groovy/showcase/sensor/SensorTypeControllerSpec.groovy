package showcase.sensor

import grails.test.mixin.*
import spock.lang.*

@TestFor(SensorTypeController)
@Mock(SensorType)
class SensorTypeControllerSpec extends Specification {

    def populateValidParams(params) {
        assert params != null

        // TODO: Populate valid properties like...
        //params["name"] = 'someValidName'
        assert false, "TODO: Provide a populateValidParams() implementation for this generated test suite"
    }

    void "Test the index action returns the correct model"() {

        when:"The index action is executed"
            controller.index()

        then:"The model is correct"
            !model.sensorTypeList
            model.sensorTypeCount == 0
    }

    void "Test the create action returns the correct model"() {
        when:"The create action is executed"
            controller.create()

        then:"The model is correctly created"
            model.sensorType!= null
    }

    void "Test the save action correctly persists an instance"() {

        when:"The save action is executed with an invalid instance"
            request.contentType = FORM_CONTENT_TYPE
            request.method = 'POST'
            def sensorType = new SensorType()
            sensorType.validate()
            controller.save(sensorType)

        then:"The create view is rendered again with the correct model"
            model.sensorType!= null
            view == 'create'

        when:"The save action is executed with a valid instance"
            response.reset()
            populateValidParams(params)
            sensorType = new SensorType(params)

            controller.save(sensorType)

        then:"A redirect is issued to the show action"
            response.redirectedUrl == '/sensorType/show/1'
            controller.flash.message != null
            SensorType.count() == 1
    }

    void "Test that the show action returns the correct model"() {
        when:"The show action is executed with a null domain"
            controller.show(null)

        then:"A 404 error is returned"
            response.status == 404

        when:"A domain instance is passed to the show action"
            populateValidParams(params)
            def sensorType = new SensorType(params)
            controller.show(sensorType)

        then:"A model is populated containing the domain instance"
            model.sensorType == sensorType
    }

    void "Test that the edit action returns the correct model"() {
        when:"The edit action is executed with a null domain"
            controller.edit(null)

        then:"A 404 error is returned"
            response.status == 404

        when:"A domain instance is passed to the edit action"
            populateValidParams(params)
            def sensorType = new SensorType(params)
            controller.edit(sensorType)

        then:"A model is populated containing the domain instance"
            model.sensorType == sensorType
    }

    void "Test the update action performs an update on a valid domain instance"() {
        when:"Update is called for a domain instance that doesn't exist"
            request.contentType = FORM_CONTENT_TYPE
            request.method = 'PUT'
            controller.update(null)

        then:"A 404 error is returned"
            response.redirectedUrl == '/sensorType/index'
            flash.message != null

        when:"An invalid domain instance is passed to the update action"
            response.reset()
            def sensorType = new SensorType()
            sensorType.validate()
            controller.update(sensorType)

        then:"The edit view is rendered again with the invalid instance"
            view == 'edit'
            model.sensorType == sensorType

        when:"A valid domain instance is passed to the update action"
            response.reset()
            populateValidParams(params)
            sensorType = new SensorType(params).save(flush: true)
            controller.update(sensorType)

        then:"A redirect is issued to the show action"
            sensorType != null
            response.redirectedUrl == "/sensorType/show/$sensorType.id"
            flash.message != null
    }

    void "Test that the delete action deletes an instance if it exists"() {
        when:"The delete action is called for a null instance"
            request.contentType = FORM_CONTENT_TYPE
            request.method = 'DELETE'
            controller.delete(null)

        then:"A 404 is returned"
            response.redirectedUrl == '/sensorType/index'
            flash.message != null

        when:"A domain instance is created"
            response.reset()
            populateValidParams(params)
            def sensorType = new SensorType(params).save(flush: true)

        then:"It exists"
            SensorType.count() == 1

        when:"The domain instance is passed to the delete action"
            controller.delete(sensorType)

        then:"The instance is deleted"
            SensorType.count() == 0
            response.redirectedUrl == '/sensorType/index'
            flash.message != null
    }
}
