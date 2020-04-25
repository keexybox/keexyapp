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

import mysql.connector, tarfile, getopt, sys, os.path, re, shutil, glob

#-------- Load settings -------#
script_dir = os.path.dirname(os.path.realpath(__file__))
config = script_dir + '/config.py'
execfile(config)
tar_file = ''
max_row_per_query = 5000

#------- HELP FONCTION -----------
def usage(script):
	print '''DESCRIPTION : 
  This script is part of Keexybox project. It is used to export blacklist to tarball.

USAGE : 
  -f : TAR file that contains URL to imports (REQUIRED)
  -w : Working directory
  -c : Blacklist categories to export

EXAMPLES : '''
	print "  " + script + " -f path/to/file.tar.gz -c adv,violence"
	print "  " + script + " -f path/to/file.tar.gz -d path/to/workdir -c adv,violence\n"

#--------- END HELP -----------

#------ Catch options from CLI ------
try:
	opts, args = getopt.getopt(sys.argv[1:], 'hf:w:c:')
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
		#extract_dir = a
		working_dir = a
	
	if o == "-c":
		categories = a

# Check -f options been specified
if tar_file == '':
	usage(sys.argv[0])
	sys.exit(255)

# Check if extract directory is valid
if not (os.path.isdir(working_dir)):
	print working_dir + " is not a directory or does not exists !"
	sys.exit(255)

#---- end Catch options from CLI ------

## Connect to MySQL 
conn = mysql.connector.connect(host=mysql_host,user=mysql_user,password=mysql_pass, database=mysql_bldb)
cursor = conn.cursor()

categories = categories.split(',')

if os.path.isdir(working_dir + "/blacklists_export/"):
	shutil.rmtree(working_dir + "/blacklists_export/")

os.mkdir(working_dir + "/blacklists_export/")
os.chdir(working_dir + "/blacklists_export/")

tar = tarfile.open(tar_file, "w:gz")

domains_list = None





for category in categories:
	# Create dir
	os.mkdir(working_dir + "/blacklists_export/" + category)

	limit_start = 0
	continue_export = 1

	while continue_export == 1:
		# build SQL query
		query = "SELECT zone FROM `blacklist` WHERE `category`=\'" + category + "\' LIMIT " + str(limit_start) + "," + str(max_row_per_query) + ";"
		# Fetch data
		cursor.execute(query)
		rows = cursor.fetchall()
	
		for row in rows:
			with open(working_dir + "/blacklists_export/" + category + "/domains", "a") as domainsfile:
				domainsfile.write(row[0] + "\n")

		limit_start = limit_start + max_row_per_query

		if rows == []:
			continue_export = 0
		
	tar.add(category + "/domains")

tar.close()
