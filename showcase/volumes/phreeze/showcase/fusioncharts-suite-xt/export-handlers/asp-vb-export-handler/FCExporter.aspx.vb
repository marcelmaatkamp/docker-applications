'*
' *
' * FusionCharts Exporter is an ASP.NET C# script that handles 
' * FusionCharts (since v3.1) Server Side Export feature.
' * This in conjuncture with various export classes would 
' * process FusionCharts Export Data POSTED to it from FusionCharts 
' * and convert the data to image or PDF and subsequently save to the 
' * server or response back as http response to client side as download.
' *
' * This script might be called as the FusionCharts Exporter - main module 
' *
' *    @author FusionCharts
' *    @description FusionCharts Exporter (Server-Side - ASP.NET C#)
' *    @version 4.0 [ 21 June 2016 ]
' *  
' 

'*
' *  ChangeLog / Version History:
' *  ----------------------------
' *
' *   4.0.1 [25 Aug 2016]
' *       - fixes for throwing Null pointer Exception while exporting as jpeg in save export action
' *   4.0 [ 21 June 2016 ]
' *       - Support export if direct image base64 encoded data is provided (for FusionCharts v 3.11.0 or more).
' *       - Support for download of xls format.
' *       - Export with images suppported for every format including svg if browser is capable of sending the image data as base64 data.
' *
' *   3.0 [ 18 July 2014 ]
' *       - Support for JavaScript Chart (SVG)
' *       
' *   2.0 [ 12 February 2009 ] 
' *       - Integrated with new Export feature of FusionCharts 3.1
' *       - can save to server side directory
' *       - can provide download or open in popup window.
' *       - can report back to chart
' *       - can save as PDF/JPG/PNG/GIF
' *
' *   1.0 [ 16 August 2007 ]
' *       - can process chart data to jpg image and response back to client side as download.
' *
' 

'*
' * Copyright (c) 2016 InfoSoft Global Private Limited. All Rights Reserved
' * 
' 

'*
' *  GENERAL NOTES
' *  -------------
' *
' *  Chart would POST export data (which consists of encoded image data stream,  
' *  width, height, background color and various other export parameters like 
' *  exportFormat, exportFileName, exportAction, exportTargetWindow) to this script. 
' *  
' *  The script would process this data using appropriate resource classes & build 
' *  export binary (PDF/image) 
' *
' *  It either saves the binary as file to a server side directory or push it as
' *  Download to client side.
' *
' *
' *  ISSUES
' *  ------
' *   Q. What if someone wishes to open in the same page where the chart existed as postback
' *      replacing the old page?
' * 
' *   A. Not directly supported using any chart attribute or parameter but can do by
' *      removing/commenting the line containing 'header( content-disposition ...'
' *     
' 

'*
' * 
' *   @requires	FCIMGGenerator  Class to export FusionCharts image data to JPG, PNG, GIF binary
' *   @requires  FCPDFGenerator  Class to export FusionCharts image data to PDF binary
' *
' 


Imports System.IO
Imports System.Web
Imports System.Drawing
Imports System.Collections
Imports System.Collections.Generic
Imports System.Drawing.Imaging
Imports System.Text.RegularExpressions
Imports SharpVectors.Converters
Imports System.Web.Script.Serialization

''' <summary>
''' FusionCharts Exporter is an ASP.NET C# script that handles 
''' FusionCharts (since v3.1) Server Side Export feature.
''' This in conjuncture with other resource classses would 
''' process FusionCharts Export Data POSTED to it from FusionCharts 
''' and convert the data to an image or a PDF. Subsequently, it would save 
''' to the server or respond back as an HTTP response to client side as download.
''' 
''' This script might be called as the FusionCharts Exporter - main module
''' </summary>
''' 
Partial Public Class FCExporter
    Inherits System.Web.UI.Page


    ''' <summary>
    ''' IMPORTANT: You need to change the location of folder where 
    ''' the exported chart images/PDFs will be saved on your 
    ''' server. Please specify the path to a folder with 
    ''' write permissions in the constant SAVE_PATH below. 
    ''' 
    ''' Please provide the path as per ASP.NET path conventions. 
    ''' You can use relative or  absolute path.
    ''' 
    ''' Special Cases: 
    '''     '/' means 'wwwroot' directory.
    '''     '. /' ( without the space after .) is the directory where the FCExporter.aspx file recides.
    '''     
    ''' Absolute Path :
    ''' 
    '''     can be like this : "C:\\myFolders\\myImages" 
    '''     ( Please never use single backslash as that would stop execution of the code instantly)
    '''     or "C:/myFolders/myImages"
    ''' 
    '''     You may have a // or \ at end : "C:\\myFolders\\myImages\\"  or "C:/myFolders/myImages/"
    ''' 
    '''     You can also have mixed slashes : "C:\\myFolders/myImages" 
    '''     
    ''' 
    ''' </summary>
    ''' directory where the FCExporter.aspx file recides
    Private Const SAVE_PATH As String = "./Exported_Images/"

    ''' <summary>
    ''' IMPORTANT: This constant HTTP_URI stores the HTTP reference to 
    ''' the folder where exported charts will be saved. 
    ''' Please enter the HTTP representation of that folder 
    ''' in this constant e.g., http://www.yourdomain.com/images/
    ''' </summary>
    Private Const HTTP_URI As String = "Exported_Images/"

    ''' <summary>
    ''' OVERWRITEFILE sets whether the export handler would overwrite an existing file 
    ''' the newly created exported file. If it is set to false the export handler would
    ''' not overwrite. In this case, if INTELLIGENTFILENAMING is set to true the handler
    ''' would add a suffix to the new file name. The suffix is a randomly generated GUID.
    ''' Additionally, you add a timestamp or a random number as additional suffix.
    ''' </summary>
    Private OVERWRITEFILE As Boolean = False
    Private INTELLIGENTFILENAMING As Boolean = True
    Private FILESUFFIXFORMAT As String = "TIMESTAMP"
    ' // value can be either 'TIMESTAMP' or 'RANDOM'

    ''' <summary>
    ''' This is a constant list of the MIME types related to each export format this resource handles
    ''' The value is semicolon separated key value pair for each format
    ''' Each key is the format and value is the MIME type
    ''' </summary>
    Private Const MIMETYPES As String = "pdf=application/pdf;jpg=image/jpeg;jpeg=image/jpeg;gif=image/gif;png=image/png;svg=image/svg+xml;xls=application/vnd.ms-excel"

    ''' <summary>
    ''' This is a constant list of all the file extensions for the export formats
    ''' The value is semicolon separated key value pair for each format
    ''' Each key is the format and value is the file extension 
    ''' </summary>
    Private Const EXTENSIONS As String = "pdf=pdf;jpg=jpg;jpeg=jpg;gif=gif;png=png;svg=svg;xls=xls"

    ''' <summary>
    ''' Lists the default exportParameter values taken, if not provided by chart
    ''' </summary>
    Private Const DEFAULTPARAMS As String = "exportfilename=FusionCharts;exportformat=PDF;exportaction=download;exporttargetwindow=_self"

    ''' <summary>
    ''' Stores server notices, if any as string [ to be sent back to chart after save ] 
    ''' </summary>
    Private notices As String = ""
    ''' <summary>
    ''' Whether the export action is download. Default value. Would change as per setting retrieved from chart.
    ''' </summary>
    Private isDownload As Boolean = True

    ''' <summary>
    ''' DOMId of the chart
    ''' </summary>
    Private DOMId As String

    ''' <summary>
    ''' Where the browser is latest. Default value is false.
    ''' </summary>
    Private isLatest As Boolean = False

    Public Property IsSVGData() As Boolean
        Get
            Return m_IsSVGData
        End Get
        Set(value As Boolean)
            m_IsSVGData = Value
        End Set
    End Property
    Private m_IsSVGData As Boolean

    Public Property isImgData() As Boolean
        Get
            Return m_isImgData
        End Get
        Set(value As Boolean)
            m_isImgData = Value
        End Set
    End Property
    Private m_isImgData As Boolean

    ''' <summary>
    ''' Stores SVG information.
    ''' </summary>
    Private svgData As TextReader

    ''' <summary>
    ''' Stores SVG in-memory file.
    ''' </summary>
    Private svgStream As MemoryStream

    Private exportData As Hashtable

    ''' <summary>
    ''' The main function that handles all Input - Process - Output of this Export Architecture
    ''' </summary>
    ''' <param name="sender">FusionCharts chart SWF</param>
    ''' <param name="e"></param>
    Protected Sub Page_Load(sender As Object, e As EventArgs)

        '*
        '         * Retrieve export data from POST Request sent by chart
        '         * Parse the Request stream into export data readable by this script
        '         

        exportData = parseExportRequestStream()

        ' process export data and get the processed data (image/PDF) to be exported
        Dim exportObject As MemoryStream = Nothing
        If Not isLatest Then
            If IsSVGData Then
                If exportData("encodedImgData") IsNot Nothing AndAlso exportData("encodedImgData").ToString() <> "" AndAlso DirectCast(exportData("parameters"), Hashtable)("exportformat").ToString() = "svg" Then
                    exportObject = exportProcessor(DirectCast(exportData("parameters"), Hashtable)("exportformat").ToString(), exportData("svg").ToString(), DirectCast(exportData("parameters"), Hashtable), exportData("encodedImgData").ToString())
                Else
                    exportObject = exportProcessor(DirectCast(exportData("parameters"), Hashtable)("exportformat").ToString(), "svg", DirectCast(exportData("parameters"), Hashtable))

                End If
            Else
                If isImgData Then
                    convertRAWImageDataToFile(exportData)
                Else
                    If exportData("encodedImgData") IsNot Nothing AndAlso exportData("encodedImgData").ToString() <> "" AndAlso DirectCast(exportData("parameters"), Hashtable)("exportformat").ToString() = "svg" Then
                        exportObject = exportProcessor(DirectCast(exportData("parameters"), Hashtable)("exportformat").ToString(), "svg", DirectCast(exportData("parameters"), Hashtable), exportData("encodedImgData").ToString())
                    Else
                        exportObject = exportProcessor(DirectCast(exportData("parameters"), Hashtable)("exportformat").ToString(), "svg", DirectCast(exportData("parameters"), Hashtable))
                    End If
                End If
            End If

            '
            '         * Send the export binary to output module which would either save to a server directory
            '         * or send the export file to download. Download terminates the process while
            '         * after save the output module sends back export status 
            '         

            'object exportedStatus = IsSVGData ? outputExportObject(exportObject, exportData) : outputExportObject(exportObject, (Hashtable)exportData["parameters"]);
            Dim exportedStatus As Object = outputExportObject(exportObject, DirectCast(exportData("parameters"), Hashtable))

            ' Dispose export object
            exportObject.Close()
            exportObject.Dispose()

            '
            '         * Build Appropriate Export Status and send back to chart by flushing the  
            '         * procesed status to http response. This returns status back to chart. 
            '         * [ This is not applicable when Download action took place ]
            '         

            flushStatus(exportedStatus, DirectCast(exportData("meta"), Hashtable))
        End If

    End Sub

    ''' <summary>
    ''' Parses POST stream from chart and builds a Hashtable containing 
    ''' export data and parameters in a format readable by other functions.
    '''  The Hashtable contains keys 'stream' (contains encoded 
    ''' image data) ; 'meta' ( Hashtable with 'width', 'height' and 'bgColor' keys) ;
    ''' and 'parameters' ( Hashtable of all export parameters from chart as keys, like - exportFormat, 
    ''' exportFileName, exportAction etc.)
    ''' </summary>
    ''' <returns>Hashtable of processed export data and parameters.</returns>
    Private Function parseExportRequestStream() As Hashtable
        ' store all export data
        Dim exportData As New Hashtable()
        Dim svgStr As String = ""

        IsSVGData = False
        Dim test As String = Request("stream_type")
        If Request("stream_type") = "svg" Then
            IsSVGData = True
            exportData("svg") = Request("stream")

            ' Added custom parameter
            exportData("exporttargetwindow") = "_self"

            'Get all export parameters into a Hastable
            Dim parameters As Hashtable = parseParams(Request("parameters"))
            'parseParams("exportaction=" + exportData["exportaction"].ToString());
            exportData("parameters") = parameters

            svgStr = exportData("svg").ToString()
            svgStr = svgStr.Substring(0, svgStr.IndexOf("</svg>") + 6)
            ' fix to replace &nbsp; string occurred in some data source specifically excel
            ' need to look for a more proper method which covers all such situations
            svgStr = svgStr.Replace("&nbsp;", " ")
            exportData("svg") = svgStr
            If Request("encodedImgData") IsNot Nothing Then
                exportData("encodedImgData") = Request("encodedImgData")
            End If
            Dim svg As Byte() = System.Text.Encoding.UTF8.GetBytes(exportData("svg").ToString())
            svgStream = New MemoryStream(svg)
            svgData = New StreamReader(svgStream)

        ElseIf Request("stream_type") = "IMAGE-DATA" Then

            'for modern browser exporting
            convertRAWImageDataToFile(Request("stream"), Request("parameters"))
            isLatest = True

        ElseIf Request("stream_type") = "image-data" Then

            ' If Flash Charts
            If Request("stream_type") = "image-data" Then
                isImgData = True
                If Request("stream") Is Nothing OrElse Request("stream").Trim() = "" Then
                    raise_error("100", True)
                End If
                exportData("stream") = Request("stream").Trim().Replace(" ", "+")
                exportData("stream") = exportData("stream").ToString().Substring(exportData("stream").ToString().IndexOf(",") + 1)
                Dim parametersArray As [String]() = Request("parameters").ToString().Split("|"c)
                exportData("exportfilename") = parametersArray(0).Split("="c)(1)
                exportData("exportformat") = parametersArray(1).Split("="c)(1).ToLower()
            Else
                isImgData = False
                'String of compressed image data
                exportData("stream") = Request("stream")

                'Halt execution  if image stream is not provided.
                If Request("stream") Is Nothing OrElse Request("stream").Trim() = "" Then
                    raise_error("100", True)
                End If

                'Get all export parameters into a Hastable
                Dim parameters As Hashtable = parseParams(Request("parameters"))
                exportData("parameters") = parameters
            End If

        End If

        'get width and height of the chart
        Dim meta As New Hashtable()

        meta("width") = Request("meta_width")
        'Halt execution on error
        If Request("meta_width") Is Nothing OrElse Request("meta_width").Trim() = "" Then
            raise_error("101", True)
        End If

        meta("height") = Request("meta_height")
        'Halt execution on error
        If Request("meta_height") Is Nothing OrElse Request("meta_height").Trim() = "" Then
            raise_error("101", True)
        End If


        'Background color of chart
        meta("bgcolor") = Request("meta_bgColor")
        If meta("bgcolor") Is Nothing OrElse meta("bgcolor").ToString().Trim() = "" Then
            ' Send notice if BgColor is not provided
            raise_error(" Background color not specified. Taking White (FFFFFF) as default background color.")
            ' Set White as Default Background color            
            meta("bgcolor") = "FFFFFF"
        End If

        ' DOMId of the chart
        meta("DOMId") = If(Request("meta_DOMId") Is Nothing, "", Request("meta_DOMId"))
        DOMId = meta("DOMId").ToString()

        exportData("meta") = meta

        Return exportData
    End Function
    ''' <summary>
    ''' Decode a base64 encoded string
    ''' </summary>
    ''' <param name="data">A base64 encoded string </param>
    ''' <returns>String decoded from input </returns>
    Private Function base64Decode(data As String) As Byte()
        Return Convert.FromBase64String(data)
    End Function


    Private Sub convertRAWImageDataToFile(imageData As String, parameters As String)

        Dim fileName As String = parameters.Split("|"c)(0).Split("="c)(1), extention As String = parameters.Split("|"c)(1).Split("="c)(1), exportAction As String = parameters.Split("|"c)(2).Split("="c)(1), fullFileName As String = fileName & "." & extention, filLocation As String = HttpContext.Current.Server.MapPath("~/Exported_Images/" & fullFileName)
        Dim contentType As String = getMime(extention)
        Dim bytes As Byte() = base64Decode(imageData.Split(","c)(1))
        File.WriteAllBytes(filLocation, bytes)
        If exportAction = "download" Then
            Response.ClearContent()
            Response.AddHeader("Content-Disposition", "attachment; filename=" & fullFileName)
            Response.ContentType = contentType
            Response.TransmitFile(filLocation)
            Response.[End]()
        End If
    End Sub


    Private Sub convertRawImageDataToFile(exportData As Hashtable)
        Dim fileName As [String] = ""
        fileName = Server.MapPath(".") & SAVE_PATH & exportData("exportfilename").ToString() & "." & exportData("exportformat").ToString().ToLower()
        System.IO.File.WriteAllBytes(fileName, base64Decode(exportData("stream").ToString()))
        Dim data As Byte() = System.IO.File.ReadAllBytes(fileName)

        Dim mime As String = getMime(exportData("exportformat").ToString())
        Response.ContentType = mime
        Response.AddHeader("Content-Disposition", "attachment" & "; filename=""" & fileName & "." & exportData("exportformat").ToString().ToLower() & """")

        Dim retStatus As New Hashtable()
        retStatus("filepath") = ""

        ' set the output strem to Response stream as the file is going to be downloaded
        retStatus("outStream") = Response.OutputStream

        Dim outStream As Stream = DirectCast(retStatus("outStream"), Stream)
        outStream.Flush()
        outStream.Close()

        Response.[End]()
    End Sub
    ''' <summary>
    ''' Parse export 'parameters' string into a Hashtable 
    ''' Also synchronise default values from defaultparameterValues Hashtable
    ''' </summary>
    ''' <param name="strParams">A string with parameters (key=value pairs) separated  by | (pipe)</param>
    ''' <returns>Hashtable containing parsed key = value pairs.</returns>
    Private Function parseParams(strParams As String) As Hashtable

        'default parameter values
        Dim defaultParameterValues As Hashtable = bang(DEFAULTPARAMS)

        ' get parameters
        Dim parameters As Hashtable = bang(strParams, New Char() {"|"c, "="c})

        ' sync with default values
        ' iterate through each default parameter value
        For Each param As DictionaryEntry In defaultParameterValues
            ' if a parameter from the defaultParameterValues Hashtable is not present
            ' in the parameters hashtable take the parameter and value from default
            ' parameter hashtable and add it to params hashtable
            ' This is needed to ensure proper export
            If parameters(param.Key) Is Nothing Then
                parameters(param.Key) = param.Value.ToString()
            End If
        Next

        ' set a global flag which denotes whether the export is download or not
        ' this is needed in many a functions 
        isDownload = parameters("exportaction").ToString().ToLower() = "download"


        ' return parameters
        Return parameters


    End Function

    ''' <summary>
    ''' Get image data from the json object Request["encodedImgData"].
    ''' </summary>
    ''' <param name="imageData">(Dictionary<string, Dictionary<string, string>>) all image Image data as a combined object</param>
    ''' <param name="imageName">(string) Image Name</param>
    ''' <returns></returns> 
    Private Function getImageData(imageData As Dictionary(Of String, Dictionary(Of String, String)), imageName As String) As String
        Dim data As String = ""
        For Each key As String In imageData.Keys
            If (imageData(key)("name") & "." & imageData(key)("type")) = imageName Then
                data = imageData(key)("encodedData")
                Exit For
            End If
        Next


        Return data
    End Function
    Private Function stichImageToSVG(svgData As String, imageData As String) As String
        Dim ser As New JavaScriptSerializer()
        Dim data = ser.Deserialize(Of Dictionary(Of String, Dictionary(Of String, String)))(imageData)

        Dim rawImageDataArray As New List(Of String)()
        Dim hrefArray As New List(Of String)()

        ' /(<image[^>]*xlink:href *= *[\"']?)([^\"']*)/i
        Dim regex As New Regex("<image.+?xlink:href=""(.+?)"".+?/?>")
        Dim counter As Integer = 0
        For Each match As Match In regex.Matches(svgData)
            Dim temp1 As String() = match.Value.Split(New String() {"xlink:href="}, StringSplitOptions.None)
            hrefArray.Add(temp1(1).Split(""""c)(1))
            Dim imageNameArray As String() = hrefArray(counter).Split("/"c)
            rawImageDataArray.Add(getImageData(data, imageNameArray(imageNameArray.Length - 1)))
            counter += 1
        Next
        For index As Integer = 0 To rawImageDataArray.Count - 1
            svgData = svgData.Replace(hrefArray(index), rawImageDataArray(index))
        Next

        Return svgData
    End Function

    Private Function stichImageToSVGAndGetString(svgData As String, imageData As String) As String

        Return stichImageToSVG(svgData, imageData)
    End Function

    Private Function stichImageToSVGAndGetStream(svgData As String, imageData As String) As MemoryStream

        svgData = stichImageToSVG(svgData, imageData)
        Dim svg As Byte() = System.Text.Encoding.UTF8.GetBytes(svgData.ToString())
        Return New MemoryStream(svg)
    End Function
    ''' <summary>
    ''' Get Export data from and build the export binary/objct.
    ''' </summary>
    ''' <param name="strFormat">(string) Export format</param>
    ''' <param name="stream">(string) Export image data in FusionCharts compressed format</param>
    ''' <param name="meta">{Hastable)Image meta data in keys "width", "heigth" and "bgColor"</param>
    ''' <param name="meta">{string)Image data</param>
    ''' <returns></returns>

    Private Function exportProcessor(strFormat As String, stream As String, meta As Hashtable, imageData As String) As MemoryStream
        Return stichImageToSVGAndGetStream(stream, imageData)
    End Function
    Private Function exportProcessor(strFormat As String, stream As String, meta As Hashtable) As MemoryStream
        If exportData("encodedImgData") IsNot Nothing AndAlso exportData("encodedImgData").ToString() <> "" Then
            svgStream = stichImageToSVGAndGetStream(exportData("svg").ToString(), exportData("encodedImgData").ToString())
            svgData = New StringReader(stichImageToSVGAndGetString(exportData("svg").ToString(), exportData("encodedImgData").ToString()))
        End If
        strFormat = strFormat.ToLower()
        ' initilize memeory stream object to store output bytes
        Dim exportObjectStream As New MemoryStream()

        ' Handle Export class as per export format
        Select Case strFormat
            Case "pdf"
                If Not IsSVGData Then
                    ' Instantiate Export class for PDF, build Binary stream and store in stream object
                    Dim PDFGEN As New FCPDFGenerator(stream, meta("width").ToString(), meta("height").ToString(), meta("bgcolor").ToString())
                    exportObjectStream = PDFGEN.getBinaryStream(strFormat)
                Else
                    exportObjectStream = GetJSImage(meta, True)
                End If

                Exit Select
            Case "jpg", "jpeg", "png", "gif"
                If Not IsSVGData Then
                    ' Instantiate Export class for Images, build Binary stream and store in stream object
                    Dim IMGGEN As New FCIMGGenerator(stream, meta("width").ToString(), meta("height").ToString(), meta("bgcolor").ToString())
                    exportObjectStream = IMGGEN.getBinaryStream(strFormat)
                Else
                    exportObjectStream = GetJSImage(meta, False)
                End If
                Exit Select
            Case "svg"
                exportObjectStream = svgStream
                Exit Select
            Case Else
                ' In case the format is not recognized
                raise_error(" Invalid Export Format.", True)
                Exit Select
        End Select

        Return exportObjectStream
    End Function

    Private Function GetJSImage(exportData As Hashtable, processPdf As Boolean) As MemoryStream
        Dim exportObjectStream As New MemoryStream()

        'string filename = exportData["filename"].ToString();
        Dim type As String = exportData("exportformat").ToString().ToLower()

        If processPdf Then
            type = "jpg"
        End If

        Dim ds As New SharpVectors.Renderers.Wpf.WpfDrawingSettings()

        Dim ssc As New StreamSvgConverter(ds)
        ssc.SaveXaml = False
        ssc.SaveZaml = False

        Dim encoder As ImageEncoderType = ImageEncoderType.JpegBitmap

        Select Case type
            Case "png"
                encoder = ImageEncoderType.PngBitmap
                Exit Select
            Case "jpeg"
                encoder = ImageEncoderType.JpegBitmap
                Exit Select
        End Select

        ssc.EncoderType = encoder
        ssc.SaveXaml = False

        If ssc.Convert(svgData, exportObjectStream) Then

            If processPdf Then
                Dim PDFGEN As New FCJSPDFGenerator(True, exportObjectStream, ssc.Drawing.Bounds.Width.ToString(), ssc.Drawing.Bounds.Height.ToString())
                exportObjectStream = PDFGEN.getBinaryStream(type)
            End If
        End If

        svgData.Close()
        svgData.Dispose()
        svgStream.Close()
        svgStream.Dispose()

        Return exportObjectStream

    End Function

    ''' <summary>
    ''' Checks whether the export action is download or save.
    ''' If action is 'download', send export parameters to 'setupDownload' function.
    ''' If action is not-'download', send export parameters to 'setupServer' function.
    ''' In either case it gets exportSettings and passes the settings along with 
    ''' processed export binary (image/PDF) to the output handler function if the
    ''' export settings return a 'ready' flag set to 'true' or 'download'. The export
    ''' process would stop here if the action is 'download'. In the other case, 
    ''' it gets back success status from output handler function and returns it.
    ''' </summary>
    ''' <param name="exportObj">Export binary/object in memery stream</param>
    ''' <param name="exportParams">Hashtable of export parameters</param>
    ''' <returns>Export success status ( filename if success, false if not)</returns>
    Private Function outputExportObject(exportObj As MemoryStream, exportParams As Hashtable) As Object
        'pass export paramters and get back export settings as per export action
        Dim exportActionSettings As Hashtable = (If(isDownload, setupDownload(exportParams), setupServer(exportParams)))

        ' set default export status to true
        Dim status As Boolean = True

        ' filepath returned by server setup would be a string containing the file path
        ' where the export file is to be saved.
        ' If filepath is a boolean (i.e. false) the server setup must have failed. Hence, terminate process.
        If TypeOf exportActionSettings("filepath") Is Boolean Then
            status = False
            raise_error(" Failed to export.", True)
        Else
            ' When 'filepath' is a sting write the binary to output stream
            Try
                ' Write export binary stream to output stream
                Dim outStream As Stream = DirectCast(exportActionSettings("outStream"), Stream)
                exportObj.WriteTo(outStream)
                outStream.Flush()
                outStream.Close()
                exportObj.Close()
            Catch e As ArgumentNullException
                raise_error(" Failed to export. Error:" & e.Message)
                status = False
            Catch e As ObjectDisposedException
                raise_error(" Failed to export. Error:" & e.Message)
                status = False
            End Try


            If isDownload Then
                ' If 'download'- terminate imediately
                ' As nothing is to be written to response now.
                Response.[End]()

            End If
        End If

        ' This is the response after save action
        ' If status remains true return the 'filepath'. Otherwise return false to denote failure.
        Return (If(status, exportActionSettings("filepath"), False))


    End Function
    ''' <summary>
    ''' Flushes exported status message/or any status message to the chart or the output stream.
    ''' It parses the exported status through parser function parseExportedStatus,
    ''' builds proper response string using buildResponse function and flushes the response
    ''' string to the output stream and terminates the program.
    ''' </summary>
    ''' <param name="filename">Name of the exported file or false on failure</param>
    ''' <param name="meta">Image's meta data</param>
    ''' <param name="msg">Additional messages</param>
    Private Sub flushStatus(filename As Object, meta As Hashtable, msg As String)
        ' Process and flush message to response stream and terminate
        Response.Output.Write(buildResponse(parseExportedStatus(filename, meta, msg)))
        Response.Flush()
        Response.[End]()
    End Sub

    ''' <summary>
    ''' Flushes exported status message/or any status message to the chart or the output stream.
    ''' It parses the exported status through parser function parseExportedStatus,
    ''' builds proper response string using buildResponse function and flushes the response
    ''' string to the output stream and terminates the program.
    ''' </summary>
    ''' <param name="filename">Name of the exported file or false on failure</param>
    ''' <param name="meta">Image's meta data</param>
    ''' <param name="meta"></param>
    Private Sub flushStatus(filename As Object, meta As Hashtable)
        flushStatus(filename, meta, "")
    End Sub


    ''' <summary>
    ''' Parses the exported status and builds an array of export status information. As per
    ''' status it builds a status array which contains statusCode (0/1), statusMesage, fileName,
    ''' width, height and notice in some cases.
    ''' </summary>
    ''' <param name="filename">exported status ( false if failed/error, filename as stirng if success)</param>
    ''' <param name="meta">Hastable containing meta descriptions of the chart like width, height</param>
    ''' <param name="msg">custom message to be added as statusMessage.</param>
    ''' <returns></returns>
    Private Function parseExportedStatus(filename As Object, meta As Hashtable, msg As String) As ArrayList

        Dim arrStatus As New ArrayList()
        ' get status
        Dim status As Boolean = (If(TypeOf filename Is String, True, False))

        ' add notices 
        If notices.Trim() <> "" Then
            arrStatus.Add("notice=" & notices.Trim())
        End If

        ' DOMId of the chart
        arrStatus.Add("DOMId=" & (If(meta("DOMId") Is Nothing, DOMId, meta("DOMId").ToString())))

        ' add width and height
        ' provide 0 as width and height on failure	
        If meta("width") Is Nothing Then
            meta("width") = "0"
        End If
        If meta("height") Is Nothing Then
            meta("height") = "0"
        End If
        arrStatus.Add("height=" & (If(status, meta("height").ToString(), "0")))
        arrStatus.Add("width=" & (If(status, meta("width").ToString(), "0")))

        ' add file URI
        arrStatus.Add("fileName=" & (If(status, (Regex.Replace(HTTP_URI, "([^\/]$)", "${1}/") & Convert.ToString(filename)), "")))
        arrStatus.Add("statusMessage=" & (If(msg.Trim() <> "", msg.Trim(), (If(status, "Success", "Failure")))))
        arrStatus.Add("statusCode=" & (If(status, "1", "0")))

        Return arrStatus

    End Function


    ''' <summary>
    ''' Builds response from an array of status information. Joins the array to a string.
    ''' Each array element should be a string which is a key=value pair. This array are either joined by 
    ''' a & to build a querystring (to pass to chart) or joined by a HTML <BR> to show neat
    ''' and clean status informaton in Browser window if download fails at the processing stage. 
    ''' </summary>
    ''' <param name="arrMsg">Array of string containing status data as [key=value ]</param>
    ''' <returns>A string to be written to output stream</returns>
    Private Function buildResponse(arrMsg As ArrayList) As String
        ' Join export status array elements into querystring key-value pairs in case of 'save' action
        ' or separate with <BR> in case of 'download' action. This would make the imformation readable in browser window.
        Dim msg As String = If(isDownload, "", "&")
        msg += String.Join((If(isDownload, "<br>", "&")), DirectCast(arrMsg.ToArray(GetType(String)), String()))
        Return msg
    End Function

    ''' <summary>
    ''' Finds if a directory is writable
    ''' </summary>
    ''' <param name="path">String Path</param>
    ''' <returns></returns>
    Private Function isDirectoryWritable(path As String) As Boolean
        Dim info As New DirectoryInfo(path)
        Return (info.Attributes And FileAttributes.[ReadOnly]) <> FileAttributes.[ReadOnly]

    End Function
    ''' <summary>
    ''' check server permissions and settings and return ready flag to exportSettings 
    ''' </summary>
    ''' <param name="exportParams">Various export parameters</param>
    ''' <returns>Hashtable containing various export settings</returns>
    Private Function setupServer(exportParams As Hashtable) As Hashtable

        'get export file name
        Dim exportFile As String = exportParams("exportfilename").ToString()
        ' get extension related to specified type 
        Dim ext As String = getExtension(exportParams("exportformat").ToString())

        Dim retServerStatus As New Hashtable()

        'set server status to true by default
        retServerStatus("ready") = True

        ' Open a FileStream to be used as outpur stream when the file would be saved
        Dim fos As FileStream

        ' process SAVE_PATH : the path where export file would be saved
        ' add a / at the end of path if / is absent at the end

        Dim path As String = SAVE_PATH
        ' if path is null set it to folder where FCExporter.aspx is present
        If path.Trim() = "" Then
            path = "./"
        End If
        path = Regex.Replace(path, "([^\/]$)", "${1}/")

        Try
            ' check if the path is relative if so assign the actual path to path
            path = HttpContext.Current.Server.MapPath(path)
        Catch e As HttpException
            raise_error(e.Message)
        End Try


        ' check whether directory exists
        ' raise error and halt execution if directory does not exists
        If Not Directory.Exists(path) Then
            raise_error(" Server Directory does not exist.", True)
        End If

        ' check if directory is writable or not
        Dim dirWritable As Boolean = isDirectoryWritable(path)

        ' build filepath
        retServerStatus("filepath") = exportFile & "." & ext

        ' check whether file exists
        If Not File.Exists(path & retServerStatus("filepath").ToString()) Then
            ' need to create a new file if does not exists
            ' need to check whether the directory is writable to create a new file  
            If dirWritable Then
                ' if directory is writable return with ready flag

                ' open the output file in FileStream
                fos = File.Open(path & retServerStatus("filepath").ToString(), FileMode.Create, FileAccess.Write)

                ' set the output stream to the FileStream object
                retServerStatus("outStream") = fos
                Return retServerStatus
            Else
                ' if not writable halt and raise error
                raise_error("403", True)
            End If
        End If

        ' add notice that file exists 
        raise_error(" File already exists.")

        'if overwrite is on return with ready flag 
        If OVERWRITEFILE Then
            ' add notice while trying to overwrite
            raise_error(" Export handler's Overwrite setting is on. Trying to overwrite.")

            ' see whether the existing file is writable
            ' if not halt raising error message
            If (New FileInfo(path & retServerStatus("filepath").ToString())).IsReadOnly Then
                raise_error(" Overwrite forbidden. File cannot be overwritten.", True)
            End If

            ' if writable return with ready flag 
            ' open the output file in FileStream
            ' set the output stream to the FileStream object
            fos = File.Open(path & retServerStatus("filepath").ToString(), FileMode.Create, FileAccess.Write)
            retServerStatus("outStream") = fos
            Return retServerStatus
        End If

        ' raise error and halt execution when overwrite is off and intelligent naming is off 
        If Not INTELLIGENTFILENAMING Then
            raise_error(" Export handler's Overwrite setting is off. Cannot overwrite.", True)
        End If

        raise_error(" Using intelligent naming of file by adding an unique suffix to the exising name.")
        ' Intelligent naming 
        ' generate new filename with additional suffix
        exportFile = exportFile & "_" & generateIntelligentFileId()
        retServerStatus("filepath") = exportFile & "." & ext

        ' return intelligent file name with ready flag
        ' need to check whether the directory is writable to create a new file  
        If dirWritable Then
            ' if directory is writable return with ready flag
            ' add new filename notice
            ' open the output file in FileStream
            ' set the output stream to the FileStream object
            raise_error(" The filename has changed to " & retServerStatus("filepath").ToString() & ".")
            fos = File.Open(path & retServerStatus("filepath").ToString(), FileMode.Create, FileAccess.Write)

            ' set the output stream to the FileStream object
            retServerStatus("outStream") = fos
            Return retServerStatus
        Else
            ' if not writable halt and raise error
            raise_error("403", True)
        End If

        ' in any unknown case the export should not execute	
        retServerStatus("ready") = False
        raise_error(" Not exported due to unknown reasons.")
        Return retServerStatus

    End Function
    ''' <summary>
    ''' setup download headers and return ready flag in exportSettings 
    ''' </summary>
    ''' <param name="exportParams">Various export parameters</param>
    ''' <returns>Hashtable containing various export settings</returns>
    Private Function setupDownload(exportParams As Hashtable) As Hashtable

        'get export filename
        Dim exportFile As String = exportParams("exportfilename").ToString()
        'get extension
        Dim ext As String = getExtension(exportParams("exportformat").ToString())
        'get mime type
        Dim mime As String = getMime(exportParams("exportformat").ToString())
        ' get target window
        Dim target As String = exportParams("exporttargetwindow").ToString().ToLower()

        ' set content-type header 
        Response.ContentType = mime

        ' set content-disposition header 
        ' when target is _self the type is 'attachment'
        ' when target is other than self type is 'inline'
        ' NOTE : you can comment this line in order to replace present window (_self) content with the image/PDF  
        Response.AddHeader("Content-Disposition", (If(target = "_self", "attachment", "inline")) & "; filename=""" & exportFile & "." & ext & """")

        ' return exportSetting array. 'Ready' key should be set to 'download'
        Dim retStatus As New Hashtable()
        retStatus("filepath") = ""

        ' set the output strem to Response stream as the file is going to be downloaded
        retStatus("outStream") = Response.OutputStream
        Return retStatus

    End Function

    ''' <summary>
    '''  gets file extension checking the export type. 
    ''' </summary>
    ''' <param name="exportType">(string) export format</param>
    ''' <returns>string extension name</returns>
    Private Function getExtension(exportType As String) As String
        ' get a Hashtable array of [type=> extension] 
        ' from EXTENSIONS constant 
        Dim extensionList As Hashtable = bang(EXTENSIONS)
        exportType = exportType.ToLower()

        ' if extension type is present in $extensionList return it, otherwise return the type 
        Return (If(extensionList(exportType).ToString() IsNot Nothing, extensionList(exportType).ToString(), exportType))
    End Function
    ''' <summary>
    ''' gets mime type for an export type
    ''' </summary>
    ''' <param name="exportType">Export format</param>
    ''' <returns>Mime type as stirng</returns>
    Private Function getMime(exportType As String) As String
        ' get a Hashtable array of [type=> extension] 
        ' from MIMETYPES constant 
        Dim mimelist As Hashtable = bang(MIMETYPES)
        Dim ext As String = getExtension(exportType)

        ' get mime type asociated to extension
        Dim mime As String = If(mimelist(ext).ToString() IsNot Nothing, mimelist(ext).ToString(), "")
        Return mime
    End Function

    ''' <summary>
    ''' generates a file suffix for a existing file name to apply smart file naming 
    ''' </summary>
    ''' <returns>a string containing GUID and random number /timestamp</returns>
    Private Function generateIntelligentFileId() As String
        ' Generate Guid
        Dim guid As String = System.Guid.NewGuid().ToString("D")

        ' check FILESUFFIXFORMAT type 
        If FILESUFFIXFORMAT.ToLower() = "timestamp" Then
            ' Add time stamp with file name
            guid += "_" & DateTime.Now.ToString("ddMMyyyyHHmmssff")
        Else
            ' Add Random Number with fileName
            guid += "_" & (New Random()).[Next]().ToString()
        End If

        Return guid
    End Function


    ''' <summary>
    ''' Helper function that splits a string containing delimiter separated key value pairs 
    ''' into hashtable
    ''' </summary>
    ''' <param name="str">delimiter separated key value pairs</param>
    ''' <param name="delimiterList">List of delimiters</param>
    ''' <returns></returns>
    Private Function bang(str As String, delimiterList As Char()) As Hashtable
        Dim retArray As New Hashtable()
        ' split string as per first delimiter
        If str Is Nothing OrElse str.Trim() = "" Then
            Return retArray
        End If
        Dim tmpArray As String() = str.Split(delimiterList(0))


        ' iterate through each element of split string
        For i As Integer = 0 To tmpArray.Length - 1
            ' split each element as per second delimiter
            Dim tmp2Array As String() = tmpArray(i).Split(delimiterList(1))

            If tmp2Array.Length >= 2 Then
                ' if the secondary split creats at-least 2 array elements
                ' make the fisrt element as the key and the second as the value
                ' of the resulting array
                retArray(tmp2Array(0).ToLower()) = tmp2Array(1)
            End If
        Next
        Return retArray

    End Function
    Private Function bang(str As String) As Hashtable
        Return bang(str, New Char(1) {";"c, "="c})
    End Function
    Private Sub raise_error(msg As String)
        raise_error(msg, False)
    End Sub
    ''' <summary>
    ''' Error reporter function that has a list of error messages. It can terminate the execution
    ''' and send successStatus=0 along with a error message. It can also append notice to a global variable
    ''' and continue execution of the program. 
    ''' </summary>
    ''' <param name="msg">error code as Integer (referring to the index of the errMessages
    ''' array containing list of error messages) OR, it can be a string containing the error message/notice</param>
    ''' <param name="halt">Whether to halt execution</param>
    Private Sub raise_error(msg As String, halt As Boolean)
        Dim errMessages As New Hashtable()

        'list of defined error messages
        errMessages("100") = " Insufficient data."
        errMessages("101") = " Width/height not provided."
        errMessages("102") = " Insufficient export parameters."
        errMessages("400") = " Bad request."
        errMessages("401") = " Unauthorized access."
        errMessages("403") = " Directory write access forbidden."
        errMessages("404") = " Export Resource class not found."

        ' Find whether error message is passed in msg or it is a custom error string.
        Dim err_message As String = (If((msg Is Nothing OrElse msg.Trim() = ""), "ERROR!", (If(errMessages(msg) Is Nothing, msg, errMessages(msg).ToString()))))

        ' Halt executon after flushing the error message to response (if halt is true)
        If halt Then

            flushStatus(False, New Hashtable(), err_message)
        Else
            ' add error to notices global variable
            notices += err_message
        End If

    End Sub



End Class


''' <summary>
''' FusionCharts Image Generator Class
''' FusionCharts Exporter - 'Image Resource' handles 
''' FusionCharts (since v3.1) Server Side Export feature that
''' helps FusionCharts exported as Image files in various formats. 
''' </summary>
Public Class FCIMGGenerator
    'Array - Stores multiple chart export data
    Private arrExportData As New ArrayList()
    'stores number of pages = length of $arrExportData array
    Private numPages As Integer = 0


    ''' <summary>
    ''' Generates bitmap data for the image from a FusionCharts export format
    ''' the height and width of the original export needs to be specified
    ''' the default background color can also be specified
    ''' </summary>
    Public Sub New(imageData_FCFormat As String, width As String, height As String, bgcolor As String)
        setBitmapData(imageData_FCFormat, width, height, bgcolor)
    End Sub

    ''' <summary>
    ''' Gets the binary data stream of the image
    ''' The passed parameter determines the file format of the image
    ''' to be exported
    ''' </summary>
    Public Function getBinaryStream(strFormat As String) As MemoryStream

        ' the image object 
        Dim exportObj As Bitmap = getImageObject()

        ' initiates a new binary data sream
        Dim outStream As New MemoryStream()

        ' determines the image format
        Select Case strFormat
            Case "jpg", "jpeg"
                exportObj.Save(outStream, ImageFormat.Jpeg)
                Exit Select
            Case "png"
                exportObj.Save(outStream, ImageFormat.Png)
                Exit Select
            Case "gif"
                exportObj.Save(outStream, ImageFormat.Gif)
                Exit Select
            Case "tiff"
                exportObj.Save(outStream, ImageFormat.Tiff)
                Exit Select
            Case Else
                exportObj.Save(outStream, ImageFormat.Bmp)
                Exit Select
        End Select
        exportObj.Dispose()

        Return outStream

    End Function


    ''' <summary>
    ''' Generates bitmap data for the image from a FusionCharts export format
    ''' the height and width of the original export needs to be specified
    ''' the default background color can also be specified
    ''' </summary>
    Private Sub setBitmapData(imageData_FCFormat As String, width As String, height As String, bgcolor As String)
        Dim chartExportData As New Hashtable()
        chartExportData("width") = width
        chartExportData("height") = height
        chartExportData("bgcolor") = bgcolor
        chartExportData("imagedata") = imageData_FCFormat
        arrExportData.Add(chartExportData)
        numPages += 1
    End Sub

    ''' <summary>
    ''' Generates bitmap data for the image from a FusionCharts export format
    ''' the height and width of the original export needs to be specified
    ''' the default background color should also be specified
    ''' </summary>
    Private Function getImageObject(id As Integer) As Bitmap
        Dim rawImageData As Hashtable = DirectCast(arrExportData(id), Hashtable)

        ' create blank bitmap object which would store image pixel data
        Dim image As New Bitmap(Convert.ToInt16(rawImageData("width")), Convert.ToInt16(rawImageData("height")), System.Drawing.Imaging.PixelFormat.Format24bppRgb)

        ' drwaing surface
        Dim gr As Graphics = Graphics.FromImage(image)

        ' set background color
        gr.Clear(ColorTranslator.FromHtml("#" & rawImageData("bgcolor").ToString()))

        Dim rows As String() = rawImageData("imagedata").ToString().Split(";"c)

        For yPixel As Integer = 0 To rows.Length - 1
            'Split each row into 'color_count' columns.			
            Dim color_count As [String]() = rows(yPixel).Split(","c)
            'Set horizontal row index to 0
            Dim xPixel As Integer = 0

            For col As Integer = 0 To color_count.Length - 1
                'Now, if it's not empty, we process it				
                'Split the 'color_count' into color and repeat factor
                Dim split_data As [String]() = color_count(col).Split("_"c)

                'Reference to color
                Dim hexColor As String = split_data(0)
                'refer to repeat factor
                Dim fRepeat As Integer = Integer.Parse(split_data(1))

                'If color is not empty (i.e. not background pixel)
                If hexColor <> "" Then
                    'If the hexadecimal code is less than 6 characters, pad with 0
                    hexColor = If(hexColor.Length < 6, hexColor.PadLeft(6, "0"c), hexColor)
                    For k As Integer = 1 To fRepeat

                        'draw pixel with specified color
                        image.SetPixel(xPixel, yPixel, ColorTranslator.FromHtml("#" & hexColor))
                        'Increment horizontal row count
                        xPixel += 1
                    Next
                Else
                    'Just increment horizontal index
                    xPixel += fRepeat
                End If
            Next
        Next
        gr.Dispose()
        Return image

    End Function

    ''' <summary>
    ''' Retreives the bitmap image object
    ''' </summary>
    Private Function getImageObject() As Bitmap
        Return getImageObject(0)
    End Function

End Class

''' <summary>
''' FusionCharts Exporter - 'PDF Resource' handles 
''' FusionCharts (since v3.1) Server Side Export feature that
''' helps FusionCharts exported as PDF file.
''' </summary>
Public Class FCJSPDFGenerator

    'Array - Stores multiple chart export data
    Private arrExportData As New ArrayList()
    'stores number of pages = length of $arrExportData array
    Private numPages As Integer = 1

    Private _IsJsChart As Boolean = False
    Private _ImagePath As String = ""
    Private _ImageStream As MemoryStream
    Private _width As String = "", _height As String = ""

    Public Sub New(IsJsChart As Boolean, fileName As String, width As String, height As String)
        Me._IsJsChart = IsJsChart
        Me._ImagePath = fileName
        Me._width = width

        Me._height = height
    End Sub

    Public Sub New(IsJsChart As Boolean, ImageStream As MemoryStream, width As String, height As String)
        Me._IsJsChart = IsJsChart
        Me._ImageStream = ImageStream
        Me._width = width

        Me._height = height
    End Sub

    ''' <summary>
    ''' Gets the binary data stream of the image
    ''' The passed parameter determines the file format of the image
    ''' to be exported
    ''' </summary>
    Public Function getBinaryStream(strFormat As String) As MemoryStream
        Dim exportObj As Byte() = getPDFObjects(False)

        Dim outStream As New MemoryStream()

        outStream.Write(exportObj, 0, exportObj.Length)

        Return outStream

    End Function

    'create image PDF object containing the chart image 
    Private Function addImageToPDF(id As Integer, isCompressed As Boolean) As Byte()

        Dim imgObj As New MemoryStream()

        'PDF Object number
        Dim imgObjNo As Integer = 6 + id * 3

        'Get chart Image binary
        Dim imgBinary As Byte() = getBitmapData24(Me._ImageStream)

        'get the length of the image binary
        Dim len As Integer = imgBinary.Length

        Dim width As String = Me._width
        Dim height As String = Me._height

        'Build PDF object containing the image binary and other formats required
        'string strImgObjHead = imgObjNo.ToString() + " 0 obj\n<<\n/Subtype /Image /ColorSpace /DeviceRGB /BitsPerComponent 8 /HDPI 72 /VDPI 72 " + (isCompressed ? "" : "") + "/Width " + width + " /Height " + height + " /Length " + len.ToString() + " >>\nstream\n";
        ' Use it for JPG.
        Dim strImgObjHead As String = imgObjNo.ToString() & " 0 obj" & vbLf & "<<" & vbLf & "/Subtype /Image /Filter /DCTDecode /ColorSpace /DeviceRGB /BitsPerComponent 8 /Width " & width & " /Height " & height & " /Length " & len.ToString() & " >>" & vbLf & "stream" & vbLf

        imgObj.Write(stringToBytes(strImgObjHead), 0, strImgObjHead.Length)
        imgObj.Write(imgBinary, 0, CInt(imgBinary.Length))

        Dim strImgObjEnd As String = "endstream" & vbLf & "endobj" & vbLf
        imgObj.Write(stringToBytes(strImgObjEnd), 0, strImgObjEnd.Length)

        imgObj.Close()
        Return imgObj.ToArray()
    End Function

    Private Function getPDFObjects(isCompressed As Boolean) As Byte()
        Dim PDFBytes As New MemoryStream()

        'Store all PDF objects in this temporary string to be written to ByteArray
        Dim strTmpObj As String = ""


        'start xref array
        Dim xRefList As New ArrayList()
        xRefList.Add("xref" & vbLf & "0 ")
        xRefList.Add("0000000000 65535 f " & vbLf)
        'Address Refenrece to obj 0
        'Build PDF objects s equentially
        'version and header
        strTmpObj = "%PDF-1.3" & vbLf & "%{FC}" & vbLf
        PDFBytes.Write(stringToBytes(strTmpObj), 0, strTmpObj.Length)

        'OBJECT 1 : info (optional)
        strTmpObj = "1 0 obj<<" & vbLf & "/Author (FusionCharts)" & vbLf & "/Title (FusionCharts)" & vbLf & "/Creator (FusionCharts)" & vbLf & ">>" & vbLf & "endobj" & vbLf
        xRefList.Add(calculateXPos(CInt(PDFBytes.Length)))
        'refenrece to obj 1
        PDFBytes.Write(stringToBytes(strTmpObj), 0, strTmpObj.Length)

        'OBJECT 2 : Starts with Pages Catalogue
        strTmpObj = "2 0 obj" & vbLf & "<< /Type /Catalog /Pages 3 0 R >>" & vbLf & "endobj" & vbLf
        xRefList.Add(calculateXPos(CInt(PDFBytes.Length)))
        'refenrece to obj 2
        PDFBytes.Write(stringToBytes(strTmpObj), 0, strTmpObj.Length)

        'OBJECT 3 : Page Tree (reference to pages of the catalogue)
        strTmpObj = "3 0 obj" & vbLf & "<<  /Type /Pages /Kids ["
        For i As Integer = 0 To numPages - 1
            strTmpObj += (((i + 1) * 3) + 1) & " 0 R" & vbLf
        Next
        strTmpObj += "] /Count " & numPages & " >>" & vbLf & "endobj" & vbLf

        xRefList.Add(calculateXPos(CInt(PDFBytes.Length)))
        'refenrece to obj 3
        PDFBytes.Write(stringToBytes(strTmpObj), 0, strTmpObj.Length)

        Dim itr As Integer = 0
        Dim iWidth As String = Me._width
        Dim iHeight As String = Me._height

        'OBJECT 4..7..10..n : Page config
        strTmpObj = (((itr + 2) * 3) - 2) & " 0 obj" & vbLf & "<<" & vbLf & "/Type /Page /Parent 3 0 R " & vbLf & "/MediaBox [ 0 0 " & iWidth & " " & iHeight & " ]" & vbLf & "/Resources <<" & vbLf & "/ProcSet [ /PDF ]" & vbLf & "/XObject <</R" & (itr + 1) & " " & ((itr + 2) * 3) & " 0 R>>" & vbLf & ">>" & vbLf & "/Contents [ " & (((itr + 2) * 3) - 1) & " 0 R ]" & vbLf & ">>" & vbLf & "endobj" & vbLf
        xRefList.Add(calculateXPos(CInt(PDFBytes.Length)))
        'refenrece to obj 4,7,10,13,16...
        PDFBytes.Write(stringToBytes(strTmpObj), 0, strTmpObj.Length)

        'OBJECT 5...8...11...n : Page resource object (xobject resource that transforms the image)
        xRefList.Add(calculateXPos(CInt(PDFBytes.Length)))
        'refenrece to obj 5,8,11,14,17...
        Dim xObjR As String = getXObjResource(itr)
        PDFBytes.Write(stringToBytes(xObjR), 0, xObjR.Length)

        'OBJECT 6...9...12...n : Binary xobject of the page (image)
        Dim imgBA As Byte() = addImageToPDF(itr, isCompressed)
        xRefList.Add(calculateXPos(CInt(PDFBytes.Length)))
        'refenrece to obj 6,9,12,15,18...
        PDFBytes.Write(imgBA, 0, imgBA.Length)

        'xrefs	compilation
        xRefList(0) += ((xRefList.Count - 1) & vbLf)

        'get trailer
        Dim trailer As String = getTrailer(CInt(PDFBytes.Length), xRefList.Count - 1)

        'write xref and trailer to PDF
        Dim strXRefs As String = String.Join("", DirectCast(xRefList.ToArray(GetType(String)), String()))
        PDFBytes.Write(stringToBytes(strXRefs), 0, strXRefs.Length)
        '
        PDFBytes.Write(stringToBytes(trailer), 0, trailer.Length)

        'write EOF
        Dim strEOF As String = "%%EOF" & vbLf
        PDFBytes.Write(stringToBytes(strEOF), 0, strEOF.Length)

        PDFBytes.Close()
        Return PDFBytes.ToArray()

    End Function


    'Build Image resource object that transforms the image from First Quadrant system to Second Quadrant system
    Private Function getXObjResource() As String
        Return getXObjResource(0)
    End Function
    Private Function getXObjResource(itr As Integer) As String

        Dim width As String = Me._width
        Dim height As String = Me._height
        Return (((itr + 2) * 3) - 1) & " 0 obj" & vbLf & "<< /Length " & (24 + (width & height).Length) & " >>" & vbLf & "stream" & vbLf & "q" & vbLf & width & " 0 0 " & height & " 0 0 cm" & vbLf & "/R" & (itr + 1) & " Do" & vbLf & "Q" & vbLf & "endstream" & vbLf & "endobj" & vbLf
    End Function

    Private Function calculateXPos(posn As Integer) As String
        Return posn.ToString().PadLeft(10, "0"c) & " 00000 n " & vbLf
    End Function


    Private Function getTrailer(xrefpos As Integer) As String
        Return getTrailer(xrefpos, 7)
    End Function

    Private Function getTrailer(xrefpos As Integer, numxref As Integer) As String
        Return "trailer" & vbLf & "<<" & vbLf & "/Size " & numxref.ToString() & vbLf & "/Root 2 0 R" & vbLf & "/Info 1 0 R" & vbLf & ">>" & vbLf & "startxref" & vbLf & xrefpos.ToString() & vbLf
    End Function


    Private Function getBitmapData24(fileName As String) As Byte()
        Return File.ReadAllBytes(fileName)
    End Function

    Private Function getBitmapData24(ImageStream As MemoryStream) As Byte()
        Return ImageStream.ToArray()
    End Function

    ' converts a hexadecimal colour string to it's respective byte value
    Private Function hexToBytes(strHex As String) As Byte()
        If strHex Is Nothing OrElse strHex.Trim().Length = 0 Then
            strHex = "00"
        End If
        strHex = Regex.Replace(strHex, "[^0-9a-fA-f]", "")
        If strHex.Length Mod 2 <> 0 Then
            strHex = "0" & strHex
        End If

        Dim len As Integer = strHex.Length \ 2
        Dim bytes As Byte() = New Byte(len - 1) {}

        For i As Integer = 0 To len - 1
            Dim hex As String = strHex.Substring(i * 2, 2)
            bytes(i) = Byte.Parse(hex, System.Globalization.NumberStyles.HexNumber)
        Next
        Return bytes

    End Function

    Private Function stringToBytes(str As String) As Byte()
        If str Is Nothing Then
            str = ""
        End If
        Return System.Text.Encoding.ASCII.GetBytes(str)
    End Function
End Class

''' <summary>
''' FusionCharts Exporter - 'PDF Resource' handles 
''' FusionCharts (since v3.1) Server Side Export feature that
''' helps FusionCharts exported as PDF file.
''' </summary>
Public Class FCPDFGenerator

    'Array - Stores multiple chart export data
    Private arrExportData As New ArrayList()
    'stores number of pages = length of $arrExportData array
    Private numPages As Integer = 0

    ''' <summary>
    ''' Generates a PDF file with the given parameters
    ''' The imageData_FCFormat parameter is the FusionCharts export format data
    ''' width, height are the respective width and height of the original image
    ''' bgcolor determines the default background colour
    ''' </summary>
    Public Sub New(imageData_FCFormat As String, width As String, height As String, bgcolor As String)
        setBitmapData(imageData_FCFormat, width, height, bgcolor)
    End Sub

    ''' <summary>
    ''' Gets the binary data stream of the image
    ''' The passed parameter determines the file format of the image
    ''' to be exported
    ''' </summary>
    Public Function getBinaryStream(strFormat As String) As MemoryStream
        Dim exportObj As Byte() = getPDFObjects(True)

        Dim outStream As New MemoryStream()

        outStream.Write(exportObj, 0, exportObj.Length)

        Return outStream

    End Function

    ''' <summary>
    ''' Generates bitmap data for the image from a FusionCharts export format
    ''' the height and width of the original export needs to be specified
    ''' the default background color should also be specified
    ''' </summary>
    Private Sub setBitmapData(imageData_FCFormat As String, width As String, height As String, bgcolor As String)
        Dim chartExportData As New Hashtable()
        chartExportData("width") = width
        chartExportData("height") = height
        chartExportData("bgcolor") = bgcolor
        chartExportData("imagedata") = imageData_FCFormat
        arrExportData.Add(chartExportData)
        numPages += 1
    End Sub



    'create image PDF object containing the chart image 
    Private Function addImageToPDF(id As Integer, isCompressed As Boolean) As Byte()

        Dim imgObj As New MemoryStream()

        'PDF Object number
        Dim imgObjNo As Integer = 6 + id * 3

        'Get chart Image binary
        Dim imgBinary As Byte() = getBitmapData24(id, isCompressed)

        'get the length of the image binary
        Dim len As Integer = imgBinary.Length

        Dim width As String = getMeta("width", id)
        Dim height As String = getMeta("height", id)

        'Build PDF object containing the image binary and other formats required
        'string strImgObjHead = imgObjNo.ToString() + " 0 obj\n<<\n/Subtype /Image /ColorSpace /DeviceRGB /BitsPerComponent 8 /HDPI 72 /VDPI 72 " + (isCompressed ? "" : "") + "/Width " + width + " /Height " + height + " /Length " + len.ToString() + " >>\nstream\n";
        Dim strImgObjHead As String = imgObjNo.ToString() & " 0 obj" & vbLf & "<<" & vbLf & "/Subtype /Image /ColorSpace /DeviceRGB /BitsPerComponent 8 /HDPI 72 /VDPI 72 " & (If(isCompressed, "/Filter /RunLengthDecode ", "")) & "/Width " & width & " /Height " & height & " /Length " & len.ToString() & " >>" & vbLf & "stream" & vbLf



        imgObj.Write(stringToBytes(strImgObjHead), 0, strImgObjHead.Length)
        imgObj.Write(imgBinary, 0, CInt(imgBinary.Length))

        Dim strImgObjEnd As String = "endstream" & vbLf & "endobj" & vbLf
        imgObj.Write(stringToBytes(strImgObjEnd), 0, strImgObjEnd.Length)

        imgObj.Close()
        Return imgObj.ToArray()

    End Function
    Private Function addImageToPDF(id As Integer) As Byte()
        Return addImageToPDF(id, True)
    End Function
    Private Function addImageToPDF() As Byte()
        Return addImageToPDF(0, True)
    End Function



    'Main PDF builder function
    Private Function getPDFObjects() As Byte()
        Return getPDFObjects(True)
    End Function

    Private Function getPDFObjects(isCompressed As Boolean) As Byte()
        Dim PDFBytes As New MemoryStream()

        'Store all PDF objects in this temporary string to be written to ByteArray
        Dim strTmpObj As String = ""


        'start xref array
        Dim xRefList As New ArrayList()
        xRefList.Add("xref" & vbLf & "0 ")
        xRefList.Add("0000000000 65535 f " & vbLf)
        'Address Refenrece to obj 0
        'Build PDF objects sequentially
        'version and header
        strTmpObj = "%PDF-1.3" & vbLf & "%{FC}" & vbLf
        PDFBytes.Write(stringToBytes(strTmpObj), 0, strTmpObj.Length)

        'OBJECT 1 : info (optional)
        strTmpObj = "1 0 obj<<" & vbLf & "/Author (FusionCharts)" & vbLf & "/Title (FusionCharts)" & vbLf & "/Creator (FusionCharts)" & vbLf & ">>" & vbLf & "endobj" & vbLf
        xRefList.Add(calculateXPos(CInt(PDFBytes.Length)))
        'refenrece to obj 1
        PDFBytes.Write(stringToBytes(strTmpObj), 0, strTmpObj.Length)

        'OBJECT 2 : Starts with Pages Catalogue
        strTmpObj = "2 0 obj" & vbLf & "<< /Type /Catalog /Pages 3 0 R >>" & vbLf & "endobj" & vbLf
        xRefList.Add(calculateXPos(CInt(PDFBytes.Length)))
        'refenrece to obj 2
        PDFBytes.Write(stringToBytes(strTmpObj), 0, strTmpObj.Length)

        'OBJECT 3 : Page Tree (reference to pages of the catalogue)
        strTmpObj = "3 0 obj" & vbLf & "<<  /Type /Pages /Kids ["
        For i As Integer = 0 To numPages - 1
            strTmpObj += (((i + 1) * 3) + 1) & " 0 R" & vbLf
        Next
        strTmpObj += "] /Count " & numPages & " >>" & vbLf & "endobj" & vbLf

        xRefList.Add(calculateXPos(CInt(PDFBytes.Length)))
        'refenrece to obj 3
        PDFBytes.Write(stringToBytes(strTmpObj), 0, strTmpObj.Length)


        'Each image page
        For itr As Integer = 0 To numPages - 1
            Dim iWidth As String = getMeta("width", itr)
            Dim iHeight As String = getMeta("height", itr)
            'OBJECT 4..7..10..n : Page config
            strTmpObj = (((itr + 2) * 3) - 2) & " 0 obj" & vbLf & "<<" & vbLf & "/Type /Page /Parent 3 0 R " & vbLf & "/MediaBox [ 0 0 " & iWidth & " " & iHeight & " ]" & vbLf & "/Resources <<" & vbLf & "/ProcSet [ /PDF ]" & vbLf & "/XObject <</R" & (itr + 1) & " " & ((itr + 2) * 3) & " 0 R>>" & vbLf & ">>" & vbLf & "/Contents [ " & (((itr + 2) * 3) - 1) & " 0 R ]" & vbLf & ">>" & vbLf & "endobj" & vbLf
            xRefList.Add(calculateXPos(CInt(PDFBytes.Length)))
            'refenrece to obj 4,7,10,13,16...
            PDFBytes.Write(stringToBytes(strTmpObj), 0, strTmpObj.Length)


            'OBJECT 5...8...11...n : Page resource object (xobject resource that transforms the image)
            xRefList.Add(calculateXPos(CInt(PDFBytes.Length)))
            'refenrece to obj 5,8,11,14,17...
            Dim xObjR As String = getXObjResource(itr)
            PDFBytes.Write(stringToBytes(xObjR), 0, xObjR.Length)

            'OBJECT 6...9...12...n : Binary xobject of the page (image)
            Dim imgBA As Byte() = addImageToPDF(itr, isCompressed)
            xRefList.Add(calculateXPos(CInt(PDFBytes.Length)))
            'refenrece to obj 6,9,12,15,18...
            PDFBytes.Write(imgBA, 0, imgBA.Length)
        Next

        'xrefs	compilation
        xRefList(0) += ((xRefList.Count - 1) & vbLf)

        'get trailer
        Dim trailer As String = getTrailer(CInt(PDFBytes.Length), xRefList.Count - 1)

        'write xref and trailer to PDF
        Dim strXRefs As String = String.Join("", DirectCast(xRefList.ToArray(GetType(String)), String()))
        PDFBytes.Write(stringToBytes(strXRefs), 0, strXRefs.Length)
        '
        PDFBytes.Write(stringToBytes(trailer), 0, trailer.Length)

        'write EOF
        Dim strEOF As String = "%%EOF" & vbLf
        PDFBytes.Write(stringToBytes(strEOF), 0, strEOF.Length)

        PDFBytes.Close()
        Return PDFBytes.ToArray()

    End Function


    'Build Image resource object that transforms the image from First Quadrant system to Second Quadrant system
    Private Function getXObjResource() As String
        Return getXObjResource(0)
    End Function
    Private Function getXObjResource(itr As Integer) As String

        Dim width As String = getMeta("width", itr)
        Dim height As String = getMeta("height", itr)
        Return (((itr + 2) * 3) - 1) & " 0 obj" & vbLf & "<< /Length " & (24 + (width & height).Length) & " >>" & vbLf & "stream" & vbLf & "q" & vbLf & width & " 0 0 " & height & " 0 0 cm" & vbLf & "/R" & (itr + 1) & " Do" & vbLf & "Q" & vbLf & "endstream" & vbLf & "endobj" & vbLf
    End Function

    Private Function calculateXPos(posn As Integer) As String
        Return posn.ToString().PadLeft(10, "0"c) & " 00000 n " & vbLf
    End Function


    Private Function getTrailer(xrefpos As Integer) As String
        Return getTrailer(xrefpos, 7)
    End Function

    Private Function getTrailer(xrefpos As Integer, numxref As Integer) As String
        Return "trailer" & vbLf & "<<" & vbLf & "/Size " & numxref.ToString() & vbLf & "/Root 2 0 R" & vbLf & "/Info 1 0 R" & vbLf & ">>" & vbLf & "startxref" & vbLf & xrefpos.ToString() & vbLf
    End Function


    Private Function getBitmapData24() As Byte()
        Return getBitmapData24(0, True)
    End Function
    Private Function getBitmapData24(id As Integer, isCompressed As Boolean) As Byte()

        Dim rawImageData As String = getMeta("imagedata", id)
        Dim bgColor As String = getMeta("bgcolor", id)

        Dim imageData24 As New MemoryStream()

        ' Split the data into rows using ; as separator
        Dim rows As String() = rawImageData.Split(";"c)

        For yPixel As Integer = 0 To rows.Length - 1
            'Split each row into 'color_count' columns.			
            Dim color_count As String() = rows(yPixel).Split(","c)

            For col As Integer = 0 To color_count.Length - 1
                'Now, if it's not empty, we process it				
                'Split the 'color_count' into color and repeat factor
                Dim split_data As String() = color_count(col).Split("_"c)

                'Reference to color
                Dim hexColor As String = If(split_data(0) <> "", split_data(0), bgColor)
                'If the hexadecimal code is less than 6 characters, pad with 0
                hexColor = If(hexColor.Length < 6, hexColor.PadLeft(6, "0"c), hexColor)

                'refer to repeat factor
                Dim fRepeat As Integer = Integer.Parse(split_data(1))

                ' convert color string to byte[] array
                Dim rgb As Byte() = hexToBytes(hexColor)

                ' Set the repeated pixel in MemoryStream
                For cRepeat As Integer = 0 To fRepeat - 1
                    imageData24.Write(rgb, 0, 3)

                Next
            Next
        Next

        Dim len As Integer = CInt(imageData24.Length)
        imageData24.Close()

        'Compress image binary
        If isCompressed Then
            Return New PDFCompress(imageData24.ToArray()).RLECompress()
        Else
            Return imageData24.ToArray()
        End If
    End Function

    ' converts a hexadecimal colour string to it's respective byte value
    Private Function hexToBytes(strHex As String) As Byte()
        If strHex Is Nothing OrElse strHex.Trim().Length = 0 Then
            strHex = "00"
        End If
        strHex = Regex.Replace(strHex, "[^0-9a-fA-f]", "")
        If strHex.Length Mod 2 <> 0 Then
            strHex = "0" & strHex
        End If

        Dim len As Integer = strHex.Length \ 2
        Dim bytes As Byte() = New Byte(len - 1) {}

        For i As Integer = 0 To len - 1
            Dim hex As String = strHex.Substring(i * 2, 2)
            bytes(i) = Byte.Parse(hex, System.Globalization.NumberStyles.HexNumber)
        Next
        Return bytes

    End Function

    Private Function getMeta(metaName As String) As String
        Return getMeta(metaName, 0)
    End Function

    Private Function getMeta(metaName As String, id As Integer) As String
        If metaName Is Nothing Then
            metaName = ""
        End If
        Dim chartData As Hashtable = DirectCast(arrExportData(id), Hashtable)
        Return (If(chartData(metaName) Is Nothing, "", chartData(metaName).ToString()))
    End Function

    Private Function stringToBytes(str As String) As Byte()
        If str Is Nothing Then
            str = ""
        End If
        Return System.Text.Encoding.ASCII.GetBytes(str)
    End Function


End Class


''' <summary>
''' This is an ad-hoc class to compress PDF stream.
''' Currently this class compresses binary (byte) stream using RLE which 
''' PDF 1.3 specification has thus formulated:
''' 
''' The RunLengthDecode filter decodes data that has been encoded in a simple 
''' byte-oriented format based on run length. The encoded data is a sequence of 
''' runs, where each run consists of a length byte followed by 1 to 128 bytes of data. If 
''' the length byte is in the range 0 to 127, the following length + 1 (1 to 128) bytes 
''' are copied literally during decompression. If length is in the range 129 to 255, the 
''' following single byte is to be copied 257 - length (2 to 128) times during decompression. 
''' A length value of 128 denotes EOD.
''' 
''' The chart image compression ratio comes to around 10:3 
''' 
''' </summary>
Public Class PDFCompress

    ''' <summary>
    ''' stores the output compressed data in MemoryStream object later to be converted to byte[] array
    ''' </summary>
    Private _Compressed As New MemoryStream()

    ''' <summary>
    '''  Uncompresses data as byte[] array
    ''' </summary>
    Private _UnCompressed As Byte()


    ''' <summary>
    ''' Takes the uncompressed byte array
    ''' </summary>
    ''' <param name="UnCompressed">uncompressed data</param>
    Public Sub New(UnCompressed As Byte())
        _UnCompressed = UnCompressed
    End Sub

    ''' <summary>
    ''' Write compressed data as RunLength
    ''' </summary>
    ''' <param name="length">The length of repeated data</param>
    ''' <param name="encodee">The byte to be repeated</param>
    ''' <returns></returns>
    Private Function WriteRunLength(length As Integer, encodee As Byte) As Integer
        ' write the repeat length
        _Compressed.WriteByte(CByte(257 - length))
        ' write the byte to be repeated
        _Compressed.WriteByte(encodee)

        're-set repeat length
        length = 1
        Return length
    End Function

    Private Sub WriteNoRepeater(NoRepeatBytes As MemoryStream)
        ' write the length of non repeted data
        _Compressed.WriteByte(CByte(CInt(NoRepeatBytes.Length) - 1))
        ' write the non repeated data put literally
        _Compressed.Write(NoRepeatBytes.ToArray(), 0, CInt(NoRepeatBytes.Length))

        ' re-set non repeat byte storage stream
        NoRepeatBytes.SetLength(0)
    End Sub

    ''' <summary>
    ''' compresses uncompressed data to compressed data in byte array
    ''' </summary>
    ''' <returns></returns>
    Public Function RLECompress() As Byte()
        ' stores non repeatable data
        Dim NoRepeat As New MemoryStream()

        ' repeat counter
        Dim _RL As Integer = 1

        ' 2 consecutive bytes to compare
        Dim preByte As Byte = 0, postByte As Byte = 0

        ' iterate through the uncompressed bytes
        For i As Integer = 0 To _UnCompressed.Length - 2
            ' get 2 consecutive bytes
            preByte = _UnCompressed(i)
            postByte = _UnCompressed(i + 1)

            ' if both are same there is scope for repitition
            If preByte = postByte Then
                ' but flush the non repeatable data (if present) to compressed stream 
                If NoRepeat.Length > 0 Then
                    WriteNoRepeater(NoRepeat)
                End If

                ' increase repeat count
                _RL += 1

                ' if repeat count reaches limit of repeat i.e. 128 
                ' write the repeat data and reset the repeat counter
                If _RL > 128 Then
                    _RL = WriteRunLength(_RL - 1, preByte)

                End If
            Else
                ' when consecutive bytes do not match

                ' store non-repeatable data
                If _RL = 1 Then
                    NoRepeat.WriteByte(preByte)
                End If

                ' write repeated length and byte (if present ) to output stream
                If _RL > 1 Then
                    _RL = WriteRunLength(_RL, preByte)
                End If

                ' write non repeatable data to out put stream if the length reaches limit
                If NoRepeat.Length = 128 Then
                    WriteNoRepeater(NoRepeat)
                End If
            End If
        Next

        ' at the end of iteration 
        ' take care of the last byte

        ' if repeated 
        If _RL > 1 Then
            ' write run length and byte (if present ) to output stream
            _RL = WriteRunLength(_RL, preByte)
        Else
            ' if non repeated byte is left behind
            ' write non repeatable data to output stream 
            NoRepeat.WriteByte(postByte)
            WriteNoRepeater(NoRepeat)
        End If


        ' wrote EOD
        _Compressed.WriteByte(CByte(128))

        'close streams
        NoRepeat.Close()
        _Compressed.Close()

        ' return compressed data in byte array
        Return _Compressed.ToArray()
    End Function

End Class
