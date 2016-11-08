#!c:\Python27\python.exe
# Script:   kpnlora2map.py
# Author:   Erik den Boggende
# Purpose:  Convert locations from KPN LoRa JSON to KML or GeoJSON
# Example input (line wrapped for readability, actual input is 1 JSON object per line):
#   {"LrrSNR":"-3.000000","Lrrid":"080E035E","SpFact":12,"SubBand":"G0",
#   "CustomerData":"{\"alr\":{\"pro\":\"SMTC/LoRaMote\",\"ver\":\"1\"}}",
#   "FPort":1,"Channel":"LC2","FCntUp":7,"Time":1475241903159,"DevEUI":"0059AC0000181135",
#   "payload_hex":"ae67ee576e183f8d071f2575230326000000000802","CustomerID":100006356,
#   "LrrRSSI":"-117.000000","ADRbit":1,"ModelCfg":0,"mic_hex":"6ac0822d",
#   "LrrLON":"5.304718","LrrLAT":"52.085815","FCntDn":6,"Lrcid":"0059AC01","DevLrrCnt":2}

import sys
import re
import struct
from datetime import datetime
from math import radians, cos, sin, asin, sqrt, atan2, degrees

def haversine(lon1, lat1, lon2, lat2):
  """
  Calculate the great circle distance between two points 
  on the earth (specified in decimal degrees)
  """
  # convert decimal degrees to radians 
  lon1, lat1, lon2, lat2 = map(radians, [lon1, lat1, lon2, lat2])
  # haversine formula 
  dlon = lon2 - lon1 
  dlat = lat2 - lat1 
  a = sin(dlat/2)**2 + cos(lat1) * cos(lat2) * sin(dlon/2)**2
  c = 2 * asin(sqrt(a)) 
  km = 6367 * c
  return km
    
def calculate_bearing(lon1deg, lat1deg, lon2deg, lat2deg):
  """
  Calculates the bearing between two points.
  The formulae used is the following:
      a = atan2(sin(deltalong).cos(lat2),
                cos(lat1).sin(lat2) - sin(lat1).cos(lat2).cos(deltalong))
  :Parameters:
    - `lon1deg/lat1deg: Represents the latitude/longitude for the
      first point. Latitude and longitude must be in decimal degrees
    - `lon2deg/lat2deg: Represents the latitude/longitude for the
      second point. Latitude and longitude must be in decimal degrees
  :Returns:
    The bearing in degrees
  :Returns Type:
    float
  """
  lat1 = radians(lat1deg)
  lat2 = radians(lat2deg)

  diffLong = radians(lon2deg - lon1deg)

  x = sin(diffLong) * cos(lat2)
  y = cos(lat1) * sin(lat2) - (sin(lat1)
          * cos(lat2) * cos(diffLong))

  initial_bearing = atan2(x, y)

  # Now we have the initial bearing but atan2 return values
  # from -180 to + 180 which is not what we want for a compass bearing
  # The solution is to normalize the initial bearing as shown below
  initial_bearing = degrees(initial_bearing)
  compass_bearing = (initial_bearing + 360) % 360

  return compass_bearing

# Function:   usage()
# Purpose:    displays help message
def usage(message):
  print message
  print "Usage: python " + sys .argv[0] + " <flag (optional)> <JSON file> <output file (optional)>"
  print "Flag: -kml      output markers in KML format(default)"
  print "      -geojson  output markers in GeoJSON format"
  print "      -geojson2 output lines in GeoJSON format"
  sys.exit()

# No parameters -> default help
if (len(sys.argv) < 2):
  usage("Convert locations from KPN LoRa JSON to KML or GeoJSON")

# Only JSON file
if (len(sys.argv) == 2):
  jsonFile = sys.argv[1]
  outputFile = ''
  flag = "-kml"

# flag + JSON file
# JSON file + output file
if (len(sys.argv) == 3):
  if (sys.argv[1][0] == '-'):
    jsonFile = sys.argv[2]
    outputFile = ''
    flag = sys.argv[1]
  else:
    jsonFile = sys.argv[1]
    outputFile = sys.argv[2]
    flag = "-kml"

# flag + JSON file + output file
if (len(sys.argv) == 4):    
  jsonFile = sys.argv[2]
  outputFile = sys.argv[3]
  flag = sys.argv[1]

# Check validity of the flag
if (flag != "-kml" and flag != "-geojson" and flag != "-geojson2"):
  usage("Illegal flag: " + flag)

print "Processing " + jsonFile

# Read all entries from the JSON file
f = open (jsonFile, "r")
entries = f.readlines()
f.close()

# Analyse input file

# Pattern for a valid KPN LoRa JSON object
# Group 1: Gateway ID
# Group 2: time
# Group 3: payload
# Group 4: lon
# Group 5: lat
jsonPattern = r'^{.*?,"Lrrid":"(.*?)",.*?"Time":(.*?),.*?"payload_hex":"(.*?)",.*?"LrrRSSI":"(.*?)",.*?"LrrLON":"(.*?)","LrrLAT":"(.*?)",.*?}'

# Dictionaries with relevant data retrieved from the JSON file
dictEui = {}
dictLat = {}
dictLon = {}
dictLastSeen = {}
dictPayload = {}
dictRssi = {}

def calcKeyFromPayload(euiIn, lonIn, latIn, payloadHexIn):
  payloadIn = bytearray.fromhex(payloadHexIn)
  if (len(payloadIn) == 21):
    plLat = float(struct.unpack_from('<i', buffer(payloadIn,6,4))[0])/10000000.0
    plLon = float(struct.unpack_from('<i', buffer(payloadIn,10,4))[0])/10000000.0
    bearing = calculate_bearing(float(lonIn), float(latIn), plLon, plLat)
    distance = haversine(float(lonIn), float(latIn), plLon, plLat)
    keyBearing = str(int(bearing/2)) # Group bearings by 2 degrees 
    keyDistance = str(int(distance*1000/200)) # Group distances by 200 meters
    calcKey = euiIn + ':' + keyDistance + ':' + keyBearing
    print calcKey
  else:
    calcKey = 0
  return calcKey

totalLines = 0
validLines = 0
usedLines = 0
while (totalLines != len(entries)):
  jsonEntry = re.search(jsonPattern, entries[totalLines])
  if (jsonEntry):
    validLines = validLines + 1

    # Retrieve the relevant data from the JSON entry
    eui = jsonEntry.group(1)
    time = jsonEntry.group(2)
    payload = jsonEntry.group(3)
    rssi = jsonEntry.group(4)
    lon = jsonEntry.group(5)
    lat = jsonEntry.group(6)

    if (flag == "-geojson2"):
      pkey = calcKeyFromPayload(eui, lon, lat, payload)
    else:
      pkey = eui
 
    # Update dictionaries
    if (pkey in dictLastSeen.keys()):
      if(time > dictLastSeen[pkey]):
        dictEui[pkey] = eui
        dictLastSeen[pkey] = time
        dictLon[pkey] = lon
        dictLat[pkey] = lat
        dictPayload[pkey] = payload
        dictRssi[pkey] = rssi
    else:
      dictEui[pkey] = eui
      dictLastSeen[pkey] = time
      dictLon[pkey] = lon
      dictLat[pkey] = lat
      dictPayload[pkey] = payload
      dictRssi[pkey] = rssi
      usedLines = usedLines + 1

  totalLines = totalLines + 1

# Report reading of the JSON file
print 'Total number of input lines:', totalLines
print 'Valid number of input lines:', validLines
print 'Number of lines used for output:', usedLines

# Create KML file
def createKml(fname):
  f = open (fname, "w")
  
  f.write('<?xml version="1.0" encoding="UTF-8"?>\n')
  f.write('<kml xmlns="http://earth.google.com/kml/2.2">\n')
  f.write('<Document>\n')
  f.write('	<name>' + 'LoRa KPN' + '</name>\n')
  f.write('	<Style id="tr">\n')
  f.write('		<LineStyle>\n')
  f.write('			<color>63eeee17</color>\n')
  f.write('			<width>4</width>\n')
  f.write('		</LineStyle>\n')
  f.write('	</Style>\n')
  f.write('	<Placemark id="segment1">\n')
  f.write('	<name>segment 1</name>\n')
  f.write('		<visibility>1</visibility>\n')
  f.write('		<styleUrl>#tr</styleUrl>\n')
  f.write('    </Placemark>\n')
  f.write('    <Folder id="trackpoints">\n')
  f.write('        <name>Track points with timestamps</name>\n')
  f.write('        <visibility>1</visibility>\n')
  f.write('        <open>0</open>\n')
  
  for id in dictEui:
    # Ignore gateways with coordinates (0,0)
    if (not(float(dictLon[id]) == 0.0 and float(dictLat[id]) == 0.0)):
      f.write('		<Placemark id="tp' + str(dictEui[id]) + '"><visibility>1</visibility>\n')
      f.write('			<TimeStamp><when>' + dictLastSeen[id] + '</when></TimeStamp><Point><coordinates>' + dictLon[id] + ',' + dictLat[id] + ',0.0</coordinates></Point><description>' + datetime.utcfromtimestamp(int(dictLastSeen[id])/1000).strftime('%Y-%m-%dT%H:%M:%SZ')  + '<br/>' + dictEui[id] + '</description>\n')
      f.write('		</Placemark>\n')
  
  f.write('    </Folder>\n')
  f.write('</Document>\n')
  f.write('</kml>\n')
      
  f.close()
  return

# Check for valid position
def isValidPosition(checkLat, checkLon):
  if (checkLat < -90.0 or checkLat > 90.0):
    return False
  if (checkLon < -180.0 or checkLon > 180.0):
    return False
  if (checkLat == 0.0 and checkLon == 0.0):
    return False
  return True
 
# Create GeoJSON file
def createGeojson(fname, flag):
  f = open (fname, "w")
  validFeature = False
  
  f.write('{ "type": "FeatureCollection",\n')
  f.write('    "properties": {\n')
  if (flag == "-geojson"):
    f.write('      "name": "' + 'KPN gateways' + '",\n')
    f.write('      "marker": {\n')
    f.write('        "icon": "' + 'android-wifi' + '",\n')
    f.write('        "prefix": "' + 'ion' + '",\n')
    f.write('        "markercolor": "' + 'green' + '",\n')
    f.write('        "iconcolor": "' + 'white' + '",\n')
    f.write('        "spin": ' + 'false' + ',\n')
    f.write('        "opacity": ' + '0.7' + '\n')
    f.write('        }\n')
  else:
    f.write('      "name": "' + 'KPN metingen' + '",\n')
    f.write('      "line": {\n')
    f.write('        "color": "' + 'blue' + '",\n')
    f.write('        "weight": ' + '3' + ',\n')
    f.write('        "opacity": ' + '0.5' + '\n')
    f.write('        }\n')  
  
  f.write('      },\n')
  f.write('  "features": [\n')

  for id in dictEui:
    # Ignore gateways with coordinates (0,0)
    if (not(float(dictLon[id]) == 0.0 and float(dictLat[id]) == 0.0)):
      # Start with a comma after a new feature was added to the output file
      if (validFeature):
        f.write(',\n')

      propEui = dictEui[id]
      propTimestamp = datetime.utcfromtimestamp(int(dictLastSeen[id])/1000).strftime('%Y-%m-%dT%H:%M:%SZ')
      propTimestampText = datetime.fromtimestamp(int(dictLastSeen[id])/1000).strftime('%d-%m-%Y %H:%M:%S')
      popupMarkerHtml = 'EUI: <b>' + propEui + '</b><br>Laatst gezien: <b>' + propTimestampText + '</b>'
      
      payloadArray = bytearray.fromhex(dictPayload[id])
      if (len(payloadArray) == 21):
        plLat = float(struct.unpack_from('<i', buffer(payloadArray,6,4))[0])/10000000.0
        plLon = float(struct.unpack_from('<i', buffer(payloadArray,10,4))[0])/10000000.0
        plTtf = payloadArray[20]
        propRssi = dictRssi[id].split('.')[0]
        propDist = "{0:.1f}".format((haversine(float(dictLon[id]), float(dictLat[id]), plLon, plLat)))
        popupLineHtml = 'Laatst gemeten: <b>' + propTimestampText + '</b><br>Afstand: <b>' + propDist + ' km</b><br>RSSI: <b>' + propRssi + ' dBm</b>'

      if (flag == "-geojson"):
        f.write('    { "type": "Feature",\n')
        f.write('      "geometry": {"type": "Point", "coordinates": [' + dictLon[id] + ',' + dictLat[id] + ']},\n')
        f.write('      "properties": {\n')
        f.write('        "eui": "' + propEui + '",\n')
        f.write('        "lastSeen": "' + propTimestamp + '",\n')
        f.write('        "popup": "' + popupMarkerHtml + '"\n')      
        f.write('        }\n')
        f.write('      }')
        validFeature = True
  
      if (flag == "-geojson2"):
        if (len(payloadArray) == 21 and isValidPosition(float(dictLat[id]), float(dictLon[id])) and isValidPosition(plLat, plLon) and plTtf != 255):
          f.write('    { "type": "Feature",\n')
          f.write('      "geometry": {"type": "LineString", "coordinates": [[' + dictLon[id] + ',' + dictLat[id] + '],[' + str(plLon) + ',' + str(plLat) + ']]},\n')
          f.write('      "properties": {\n')
          f.write('        "rssi": "' + propRssi + ' dBm",\n')
          f.write('        "Afstand": "' + propDist + ' km",\n')
          f.write('        "popup": "' + popupLineHtml + '"\n')
          f.write('        }\n')
          f.write('      }')
          validFeature = True
        else:
          validFeature = False
  
  f.write('\n    ]\n')
  f.write('  }\n')

  f.close()
  return

if (flag == "-kml"):
  if (len(outputFile) == 0):
    outputFile = "kpngateways.kml"
  createKml(outputFile)

if (flag == "-geojson" or flag == "-geojson2"):
  if (len(outputFile) == 0):
    if (flag == "-geojson"):
      outputFile = "kpngateways.geojson"
    else:
      outputFile = "kpncoverage.geojson"
  createGeojson(outputFile, flag)

