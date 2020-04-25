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

# To install mysql-connector for python :
#  apt-get install python-pip libmysqlclient-dev python-dev python-mysqldb
#  pip install mysql
#  pip install MySQL-python
#  pip install mysql-connector

#http://smallenvelop.com/display-loading-icon-page-loads-completely/

import mysql.connector, tarfile, getopt, sys, os.path, re

#--------- Load settings --------
script_dir = os.path.dirname(os.path.realpath(__file__))
config = script_dir + '/config.py'
execfile(config)
tar_file = ''
#------ end Default values --------

#--------- SQL query settings --------
start_query = "REPLACE INTO blacklist (category, zone, host) VALUES "
end_query = ";"
#------ end Default values --------

#------- HELP FONCTION -----------
def usage(script):
	print '''DESCRIPTION : 
  This script is part of Keexybox project. It is used to imports domains to Blacklist from tarball.

USAGE : 
  -f : TAR file that contains URL to imports (REQUIRED)
  -d : Destination directory where to extract files
  -n : Number of URLS to import per SQL query

EXAMPLES : '''
	print "  " + script + " -f path/to/file.tar.gz"
	print "  " + script + " -f path/to/file.tar.gz -w path/to/workdir -n 1000\n"

#--------- END HELP -----------

#------ Catch options from CLI ------
try:
	opts, args = getopt.getopt(sys.argv[1:], 'hf:w:n:')
except getopt.GetoptError as err:
	print str(err)
	sys.exit(2)

# Reading options and set values
for o, a in opts:

	# Help option
	if o == "-h":
		usage(sys.argv[0])
		sys.exit(2)

	# path to tar option
	if o == "-f":
		tar_file = a

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
if tar_file == '':
	usage(sys.argv[0])
	sys.exit(255)

# Check if path to tar file is valid
if not (os.path.isfile(tar_file)):
	print "file " + a + " does not exists !"
	sys.exit(255)

# Check if extract directory is valid
if not (os.path.isdir(working_dir)):
	print working_dir + " is not a directory or does not exists !"
	sys.exit(255)

#---- end Catch options from CLI ------

# a subdir will be created when extrating files
working_dir = working_dir + "/keexybox_extract/"

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

#----- end Function to run SQL import query  -----

# Check if file is tar file then ...
if(tarfile.is_tarfile(tar_file)):
	# Open tar file
	mytar = tarfile.open(tar_file, 'r')
	# extract files
	print "Extracting " + tar_file + " in " + working_dir + "..."
	mytar.extractall(working_dir)
	# Get list of files stored in tar file
	tar_files_list = mytar.getnames()

	# Filter only files that end with /domains
	pattern = re.compile(r".*\/domains$")
	files_list = []
	for f in tar_files_list:
		print f
		if pattern.match(f):
			files_list.append(f)

	# close tar file
	mytar.close()

	for blfile in files_list:
		# Domain or URL category is directory name inside tar file
		filetype = blfile.split("/")[-1]
		category = blfile.split("/")[-2]
		category = category.lower()
		import_file = working_dir + blfile

		# import only domain or url, so bltype must be set
		print "importing " + filetype + " with " + category + " category..."
		# Open file that contains domains or urls
		with open(import_file, 'r') as infile:
			# set lines
			lines = []
			for line in infile:
				# Append domain or url
				lines.append(line)
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

else:
	print tar_file + " is not a tar file ! Please try with another file"
	sys.exit(1)

conn.close()
