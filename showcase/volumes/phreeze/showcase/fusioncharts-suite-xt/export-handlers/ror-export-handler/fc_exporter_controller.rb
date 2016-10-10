# FusionCharts Exporter is a ruby script that handles 
# FusionCharts (since v3.5.0) Server Side Export feature.
# This in conjuncture with various export classes would 
# process FusionCharts Export Data POSTED to it from FusionCharts 
# and convert the data to image or PDF and subsequently save to the 
# server or response back as http response to client side as download.
#
# This script might be called as the FusionCharts Exporter - main module 
#
#    @author FusionCharts
#    @description FusionCharts Exporter (Server-Side - ROR)
#    @version 2.0 [ 21 June 2016 ]
#  
#
#
#  ChangeLog / Version History:
#  ----------------------------
#       
#   2.0 [ 21 June 2016 ] 
#       - Integrated with new Export feature of FusionCharts 3.11.0
#       - Support for export if direct image is base64 encoded
#         (data provided by the FusionCharts v3.11.0)
#         Add a comment to this line
#       - Support for download in the XLS format
#       - Export with images suppported for every format including svg 
#         if browser is capable of sending the image data as SVG format.
#
#   1.0 [ 06 Nov 2014 ]
#       - can process chart data to jpg image and response back to client side as download.
#       - Support for JavaScript Chart (SVG)
#       - can save to server side directory
#       - can provide download or open in popup window.
#       - can save as PDF/JPG/PNG
#
#
# Copyright (c) 2016 InfoSoft Global Private Limited. All Rights Reserved
# 
#
#  GENERAL NOTES
#  -------------
#
#  Chart would POST export data (which consists of encoded image data stream,  
#  width, height, background color and various other export parameters like 
#  exportFormat, exportFileName, exportAction, exportTargetWindow) to this script. 
#  
#  The script would process this data using appropriate resource classes & build 
#  export binary (PDF/image) 
#
#  It either saves the binary as file to a server side directory or push it as
#  Download to client side.
#
#
#
#  Dependencies to run the this controller
#    - rmagick
#    - json
#
#  Installing rmagick in ubuntu
#
#    - First, check that the universe repository is enabled by inspecting '/etc/apt/sources.list' with your favourite editor.
#
#    - You will need to use sudo to ensure that you have permissions to edit the file.
#
#    - If universe is not included then modify the file so that it does.
#
#    - deb http://us.archive.ubuntu.com/ubuntu precise main universe
#    - After any changes you should run this command to update your system.
#
#    - sudo apt-get update
#    - You can now install the package like this.
#
#    - Install librmagick-ruby
#    - sudo apt-get install librmagick-ruby
#
#    - Add this line to your application’s gemfile
#    - gem "rmagick", "~> 2.13.1" 

require "RMagick" 
require 'rubygems'
require 'json'
require "base64"

class FcExporterController < ApplicationController
  
  # following line is required thus the controller can be called from javascript without any key authentication
  protect_from_forgery with: :null_session 
  
  # settings variable declaration begins
  SAVE_PATH = "ExportedImages/"
  # settings variable declaration ends

  # this method should be called as http://localhost:<PORT-NUMBER(eg. 3000)>/fc_exporter/init/ from javascript
  def init
  	
  	checkAndCreateDirectories([SAVE_PATH])

  	requestObject = parseRequestParams(request.POST)
  	stream = requestObject['stream']

    #replacing single-quote with nothing because of linearGradient, otherwise all the exported image will be b/w
    stream = stream.gsub('\'', '')
  
    if requestObject['stream_type'] == "IMAGE-DATA"
      prepareAndSendFile(requestObject);
    else  
  	  if requestObject['imageData'] != ""
  		  stream = stichImageToSVG(stream, requestObject['imageData'])
  	  end
      convertSVGtoImageOrPdf(stream, requestObject['exportFileName'] ,requestObject['exportFormat'])  
    end 
  end

  # From here all the declared members are private in nature
  private

  # this function check for the directories needed to export image
  # if not present then it creates the directories
  def checkAndCreateDirectories(directoriesNameArray)
  	directoriesNameArray.each do |directoryName|
  		if !File.directory?(directoryName) # if the directory doesn't exists create that
  			begin
  				Dir.mkdir directoryName
  			rescue
  				raiseError("403") # raising error if directory cann't be created
  			end	
  		end	
  	end	
  end	

  # This function purse the request and create the request Data object
  def parseRequestParams(requestData)
  	# Following values are expected to have from request stream
  	stream = "" # expected to hold the SVG string coming from the chart
  	imageData = "" # if the request contains raw image data then those will go here
  	parametersArray = [] # holds the parameters
  	width = 0 # holds the width of exported image
  	height = 0 # holds the height of exported image
  	exportFileName = "" # holds the name of the exported file
  	exportFormat = "" # holds the format of the exported files
  	exportAction = ""
  	
  	if requestData["stream"]
  		stream = requestData["stream"]
  	else
  		raiseError("101")	
  	end

    if requestData["stream"]
      streamType = requestData["stream_type"]
    else
      raiseError("101") 
    end
    
  	if requestData["encodedImgData"]
  		imageData = requestData["encodedImgData"]
  	end
  	
  	if requestData["meta_width"]!="" && requestData["meta_height"] !=""
  		width = requestData["meta_width"]
  		height = requestData["meta_height"]
  	else
  		raiseError("101")	
  	end
  	
  	if requestData["parameters"] != ""
  		parametersArray = requestData["parameters"].split("|")
	else
		raiseError("100")  		
  	end

  	if 	parametersArray[0].split("=").last && parametersArray[1].split("=").last && parametersArray[2].split("=").last
	  	exportFileName = parametersArray[0].split("=").last
	  	exportFormat = parametersArray[1].split("=").last
	  	exportAction = parametersArray[2].split("=").last
    else
    	raiseError("100")  		
  	end
  	
  	# preparing the request object
  	requestObject = {
  		"stream" => stream,
      "stream_type" => streamType,  
  		"imageData" => imageData, 
  		"width" => width, 
  		"height" => height, 
  		"exportFileName" => exportFileName, 
  		"exportFormat" => exportFormat, 
  		"exportAction" => exportAction
  	}

  	return requestObject
  end	

  # this function replaces the image href with image data
  def stichImageToSVG(svgString, imageData)
  	
  	imageDataArray = JSON.parse(imageData) # parsing the image data object
  	keys = imageDataArray.keys # holds the keys like image_1, image_2 etc  
  	#matches = /xlink:href\s*=\s*"([^"]*)"\g/.match(svgString).captures # matches array holds the physical paths of images lies in the SVG
  	matches = svgString.scan(/xlink:href\s*=\s*"([^"]*)"/xmi)
  	# looping through all of the matches
  	matches.each do |match|
	    imageName = match[0].split("/").last.split(".").first
	    imageData = ""
		  #looping through the images of json data	
		  keys.each do |key|
	    	if imageDataArray[key]['name'] == imageName
	    		imageData = imageDataArray[key]['encodedData']
	    	end	
		  end
		  svgString = svgString.sub(match[0], imageData); # replacing the image href with image data
	  end

  	return svgString
	
  end	

  # this method raise the error based on the error code
  def raiseError(errorCode)
  	errorArray = {
  			"100" => " Insufficient data.", 
  			"101" => " Width/height not provided.", 
  			"102" => " Insufficient export parameters.", 
  			"400" => " Bad request.", 
  			"401" => " Unauthorized access.", 
  			"403" => " Directory write access forbidden.", 
  			"404" => " Export Resource not found."
  		}
  	send_data errorArray[errorCode], :disposition => 'inline'	
  end	

  # this function sends the provided file as downloadable to the browser
  def sendFileToDownload(fileName)
  	send_file(fileName)
  end	

  # This function coverts the provided SVG string to image or pdf file
  def convertSVGtoImageOrPdf(svgString, exportFileName, exportFileFormat)
  	completeFileName = SAVE_PATH+exportFileName+"."+exportFileFormat
  	
  	if exportFileFormat != "svg"
	  	img = Magick::Image.from_blob(svgString) {
		  self.format = "SVG"
		}
		begin
			img[0].write(completeFileName)
		rescue
			raiseError("403") # raising error if image cannot be created	
		end	
	else
		begin
			file = File.open(completeFileName, "w")
			file.write(svgString) 
			file.close()
		rescue
			raiseError("403") # raising error if file cannot be created	
		end
	end
	sendFileToDownload(completeFileName)
	
  end

  # if the stream type is IMAGE-DATA then this mewthod will be called
  def prepareAndSendFile(requestObject) 
    fileName = SAVE_PATH + requestObject["exportFileName"] + "." + requestObject["exportFormat"]
    begin
      imageData = requestObject["stream"].split(',')[1];
      file= File.new(fileName, 'wb')
      file.write(Base64.decode64(imageData)) 
      file.close()
    rescue
      raiseError("403") # raising error if file cannot be created 
    end
    sendFileToDownload(fileName)
  end  

end
