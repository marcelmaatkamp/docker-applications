#!c:\Python27\python.exe
# Script:   kpnlora2kml.py
# Author:   Erik den Boggende
# Purpose:  Convert locations from KPN LoRa JSON to KML
# Example input (line wrapped for readability, actual input is 1 JSON object per line):
#   {"LrrSNR":"-3.000000","Lrrid":"080E035E","SpFact":12,"SubBand":"G0",
#   "CustomerData":"{\"alr\":{\"pro\":\"SMTC/LoRaMote\",\"ver\":\"1\"}}",
#   "FPort":1,"Channel":"LC2","FCntUp":7,"Time":1475241903159,"DevEUI":"0059AC0000181135",
#   "payload_hex":"ae67ee576e183f8d071f2575230326000000000802","CustomerID":100006356,
#   "LrrRSSI":"-117.000000","ADRbit":1,"ModelCfg":0,"mic_hex":"6ac0822d",
#   "LrrLON":"5.304718","LrrLAT":"52.085815","FCntDn":6,"Lrcid":"0059AC01","DevLrrCnt":2}

import sys
import re
from datetime import datetime

# Function:   usage()
# Purpose:    displays help message
def usage():
  print "Convert locations from KPN LoRa JSON to KML"
  print "Usage: python " + sys .argv[0] + " <JSON file> <output file (optional)>"
  sys.exit()

if (len(sys.argv) < 2):
  usage()

jsonFile = sys.argv[1]
if (len(sys.argv) > 2):
  outputFile = sys.argv[2]
else:
  outputFile = 'kpngateways.kml'

print "Processing " + jsonFile

# Read all entries from the JSON file
f = open (jsonFile, "r")
entries = f.readlines()
f.close()

# Analyse input file

# Pattern for a valid KPN LoRa JSON object
# Group 1: Gateway ID
# Group 2: time
# Group 3: lon
# Group 4: lat
jsonPattern = r'^{.*?,"Lrrid":"(.*?)",.*?"Time":(.*?),.*?"LrrLON":"(.*?)","LrrLAT":"(.*?)",.*?}'

# Dictionaries with relevant data retrieved from the JSON file
dictGwId = {}
dictLat = {}
dictLon = {}
dictLastSeen = {}

lineCount = 0
i = 0
while (i != len(entries)):
  jsonEntry = re.search(jsonPattern, entries[i])
  if (jsonEntry):
    lineCount = lineCount + 1

    # Retrieve the relevant data from the JSON entry
    gwId = jsonEntry.group(1)
    time = jsonEntry.group(2)
    lon = jsonEntry.group(3)
    lat = jsonEntry.group(4)
    keyLonLat = lon + ',' + lat

    # Update dictionaries
    if (keyLonLat in dictLastSeen.keys()):
      if (time > dictLastSeen[keyLonLat]):
        dictLastSeen[keyLonLat] = time
    else:
      dictGwId[keyLonLat] = gwId
      dictLastSeen[keyLonLat] = time
      dictLon[keyLonLat] = lon
      dictLat[keyLonLat] = lat

  i = i + 1

# Report reading of the JSON file
for id in dictLastSeen:
  print 'Gateway on coordinate', id, 'last seen on', dictLastSeen[id]
  
# Create KML file(s)
f = open (outputFile, "w")

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
#f.write('		<LineString>\n')
#f.write('			<coordinates>\n')
#
#for id in dictLastSeen:
#  f.write(dictLon[id] + ',' + dictLat[id] + ',0.0 ')
#
#f.write('\n')
#f.write('            </coordinates>\n')
#f.write('        </LineString>\n')
f.write('    </Placemark>\n')
f.write('    <Folder id="trackpoints">\n')
f.write('        <name>Track points with timestamps</name>\n')
f.write('        <visibility>1</visibility>\n')
f.write('        <open>0</open>\n')

for id in dictLastSeen:
  # Ignore gateways with coordinates (0,0)
  if (not(float(dictLon[id]) == 0.0 and float(dictLat[id]) == 0.0)):
    f.write('		<Placemark id="tp' + str(id) + '"><visibility>1</visibility>\n')
    f.write('			<TimeStamp><when>' + dictLastSeen[id] + '</when></TimeStamp><Point><coordinates>' + dictLon[id] + ',' + dictLat[id] + ',0.0</coordinates></Point><description>' + datetime.utcfromtimestamp(int(dictLastSeen[id])/1000).strftime('%Y-%m-%dT%H:%M:%SZ')  + '<br/>' + dictGwId[id] + '</description>\n')
    f.write('		</Placemark>\n')

f.write('    </Folder>\n')
f.write('</Document>\n')
f.write('</kml>\n')
    
f.close()

print 'Processed', lineCount, 'lines'
