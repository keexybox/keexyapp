#!/usr/bin/python

# ***** BEGIN LICENSE BLOCK *****
# @author Benoit Saglietto <bsaglietto[AT]keexybox.org>
#
# @copyright Copyright (c) 2020, Benoit SAGLIETTO
# @license GPLv3
#
# This file is part of Keexybox project.
#
# Keexybox is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Keexybox is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Keexybox.	If not, see <http://www.gnu.org/licenses/>.
# ***** END LICENSE BLOCK *****

import mysql.connector, getopt, sys, os.path, re, wget, urllib

#--------- Load settings --------
script_dir = os.path.dirname(os.path.realpath(__file__))
config = script_dir + '/config.py'
execfile(config)
category = "default"
#------ end Default values --------

#--------- SQL query settings --------
start_query = "REPLACE INTO blacklist (category, zone, host) VALUES "
end_query = ";"
#------ end Default values --------

#------- HELP FONCTION -----------
def usage(script):
	print '''DESCRIPTION : 
  This script is part of Keexybox project. It is used to imports domains to Blacklist from URL.

USAGE : 
  -u : URL of list
  -c : Category to set in Blacklist for this list
  -d : Destination directory where to extract files
  -n : Number of URLS to import per SQL query

EXAMPLES : '''
	print "  " + script + " -u https://www.domain.com/list.txt"
	print "  " + script + " -u https://www.domain.com/list.txt -w path/to/workdir -n 1000\n"

#--------- END HELP -----------

#------ Catch options from CLI ------
try:
	opts, args = getopt.getopt(sys.argv[1:], 'hu:w:n:c:')
except getopt.GetoptError as err:
	print str(err)
	sys.exit(2)

# Reading options and set values
for o, a in opts:

	# Help option
	if o == "-h":
		usage(sys.argv[0])
		sys.exit(2)

	# URL of list
	if o == "-u":
		url_list = a

	# Category
	if o == "-c":
		category = a

	# Extract dir option
	if o == "-w":
		working_dir = a

	# URLs to import per query options
	if o == "-n":
		try:
			domains_per_query = int(a)
		except:
			print a + "is not an integer !"
			sys.exit(255)

# Check -f options been specified
if url_list == '':
	usage(sys.argv[0])
	sys.exit(255)

# Check if path to tar file is valid
url_regex = re.compile(
	r'^(?:http|ftp)s?://' # http:// or https://
	r'(?:(?:[A-Z0-9](?:[A-Z0-9-]{0,61}[A-Z0-9])?\.)+(?:[A-Z]{2,6}\.?|[A-Z0-9-]{2,}\.?)|' #domain...
	r'localhost|' #localhost...
	r'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})' # ...or ip
	r'(?::\d+)?' # optional port
	r'(?:/?|[/?]\S+)$', re.IGNORECASE)

if not (re.match(url_regex, url_list)):
	print "url " + a + " is wrong !"
	sys.exit(255)

# Check if extract directory is valid
if not (os.path.isdir(working_dir)):
	print working_dir + " is not a directory or does not exists !"
	sys.exit(255)

#if not (category.isalnum()):
if not (re.match(r'[A-Za-z0-9_-]*$', category)):
	print "Error: Category name must be alphanumeric and may contain hyphens (-) and underscores (_)"
	sys.exit(255)

#---- end Catch options from CLI ------


## Connect to MySQL 
conn = mysql.connector.connect(host=mysql_host,user=mysql_user,password=mysql_pass, database=mysql_bldb)
cursor = conn.cursor()

#----- Function to run SQL import query  -----
def importurls(urls, category):
	query = ''
	for line in urls:
		# Remove space arround domain
		domain = line.strip()
		# Append data in query
		#query = query + ",('{0}', '" + category + "', '" + d + "')".format(domain)
		query = query + ",('{0}', '{1}', '{2}')".format(category, domain, '@*')
	# build final query	([1:] removes the first "," in query)
	final_query = start_query + query[1:] + end_query
	# Exectute final query
	cursor.execute(final_query)
	# Commit data
	conn.commit()

# Hostname validation REGEX
hostname_regex = re.compile("^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$", re.IGNORECASE)

# Download file if HTTP request is 200 OK, else give up
if(urllib.urlopen(url_list).getcode() == 200):
	import_file = wget.download(url_list,working_dir)
else:
	sys.exit(1)

if (os.path.isfile(import_file)):
	# import only domain or url, so bltype must be set
	print "importing " + import_file + "list in Blacklist with " + category + " category..."
	# Open file that contains domains or urls
	with open(import_file, 'r') as infile:
		# set lines
		lines = []
		for line in infile:
			# Append domain is match hostname string
			if (re.match(hostname_regex, line.split()[0])):
				lines.append(line.split()[0])

			# Load N domains in the file (domains_per_query value)
			if len(lines) > domains_per_query:
				# Run importurls function to write SQL queries
				importurls(lines, category)
				# clear value lines
				lines = []

		# Load last domains that remains to import
		if len(lines) > 0:
			# Run importurls function to import N domains
			importurls(lines, category)
			# clear value lines
			lines = []

os.remove(import_file)

conn.close()

