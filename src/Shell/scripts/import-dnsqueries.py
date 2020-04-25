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

import mysql.connector, getopt, sys, os.path, hashlib, shlex, socket, itertools, gzip
from datetime import datetime

#--------- Load settings --------
script_dir = os.path.dirname(os.path.realpath(__file__))
config = script_dir + '/config.py'
execfile(config)
proxy_host = socket.gethostname()
log_file = ''
#------ end Default values --------

#------ Connect to MySQL 
conn = mysql.connector.connect(host=mysql_host,user=mysql_user,password=mysql_pass)
cursor = conn.cursor(buffered=True)

#------- HELP FONCTION -----------
def usage(script):
	print '''DESCRIPTION : 
  This script is part of Keexybox project. It is used to imports squid accesslog file into database.

USAGE : 
  -f : Log file to import
  -n : Number of log to import per SQL query

EXAMPLES : '''
	print "  " + script + " -f path/to/access.log"
	print "  " + script + " -f path/to/access.log -n 1000\n"

#--------- END HELP -----------

#------ Catch options from CLI ------
try:
	opts, args = getopt.getopt(sys.argv[1:], 'hf:d:n:l:t:')
except getopt.GetoptError as err:
	print str(err)
	sys.exit(2)

# Reading options and set values
for o, a in opts:

	# Help option
	if o == "-h":
		usage(sys.argv[0])
		sys.exit(2)

	# path to logfile option
	if o == "-f":
		log_file = a
		# Define if log file is gzipped or not and use the right methode to open file.
		# We trust that that file end with .gz are real gzip file
		if log_file.endswith('.gz'):
			opener = gzip.open
		else:
			opener = open

	# first line to import
	if o == "-l":
		try:
			first_line = int(a)
		except:
			print a + "is not an integer !"
			sys.exit(255)

	# last lines to import 
	if o == "-t":
		try:
			last_lines = int(a)
			with opener(log_file) as f:
				last_line = sum(1 for _ in f)
				if last_lines > last_line:
					first_line = 0
				else:
					first_line = last_line-last_lines

		except:
			print a + "is not an integer !"
			sys.exit(255)

	# URLs to import per query options
	if o == "-n":
		try:
			logs_per_query = int(a)
		except:
			print a + "is not an integer !"
			sys.exit(255)

# Check -f options been specified
if log_file == '':
	usage(sys.argv[0])
	sys.exit(255)

# Check if path to tar file is valid
if not (os.path.isfile(log_file)):
	print "file " + log_file + " does not exists !"
	sys.exit(255)

#---- end Catch options from CLI ------


# Query to create temp table that will store queried domain. It helps to give a faster result for domain that were already queried by this scripts
queried_domains_create_tmp_table='''CREATE TEMPORARY TABLE ''' + mysql_bldb + '''.tmp_queried_domains (profile_id INT(10), domain VARCHAR(255), blocked INT(1), category VARCHAR(255));'''
queried_domains_drop_tmp_table='''DROP TEMPORARY TABLE ''' + mysql_bldb + '''.tmp_queried_domains;'''

# begin of SQL Query to insert data to dns_log table
start_query = '''REPLACE INTO ''' + mysql_logdb + '''.''' + dns_log_table + ''' (
	''' + mysql_logdb + '''.''' + dns_log_table + '''.id, 
	''' + mysql_logdb + '''.''' + dns_log_table + '''.logfile_name, 
	''' + mysql_logdb + '''.''' + dns_log_table + '''.line_number, 
	''' + mysql_logdb + '''.''' + dns_log_table + '''.proxy_host, 
	''' + mysql_logdb + '''.''' + dns_log_table + '''.date_time, 
	''' + mysql_logdb + '''.''' + dns_log_table + '''.client_ip, 
	''' + mysql_logdb + '''.''' + dns_log_table + '''.profile_id,
	''' + mysql_logdb + '''.''' + dns_log_table + '''.domain,
	''' + mysql_logdb + '''.''' + dns_log_table + '''.blocked,
	''' + mysql_logdb + '''.''' + dns_log_table + '''.category)
VALUES '''

end_query = ";"

#------- Fonction to verify if domain may have been blocked
def check_blocked_domain(profile_id, domain):
	# first search in the temporary table populated by this script
	search_query='SELECT * FROM ' + mysql_bldb + '.tmp_queried_domains WHERE ' + mysql_bldb + '.tmp_queried_domains.profile_id=' + str(profile_id) + ' AND ' + mysql_bldb + '.tmp_queried_domains.domain="' + str(domain) + '" LIMIT 1;'

	cursor.execute(search_query)

	first_result = cursor.fetchone()

	# If value not found, search in big temporary blacklist table and set if domain is in blacklist for the profile ID
	if first_result is None:
		# Split fqdn
		splitted_domain = domain.split('.')
	
		# Count number of subdomain on fqdn
		count = len(splitted_domain)-1
	
		# Begin of query
		bl_query = '''SELECT ''' + mysql_kxydb + '''.profiles.id, ''' + mysql_bldb + '''.blacklist.zone, ''' + mysql_bldb + '''.blacklist.category 
		FROM ''' + mysql_kxydb + '''.profiles
  			INNER JOIN ''' + mysql_kxydb + '''.profiles_blacklists ON ''' + mysql_kxydb + '''.profiles_blacklists.profile_id=''' + mysql_kxydb + '''.profiles.id 
  			INNER JOIN ''' + mysql_bldb + '''.blacklist ON ''' + mysql_bldb + '''.blacklist.category=''' + mysql_kxydb + '''.profiles_blacklists.category 
		WHERE  ''' + mysql_kxydb + '''.profiles.id = ''' + profile_id

		# Building query to search also root domains of domains 
		bl_domains_query = []
	
		# initialize domain search string with root zone
		domain_name = splitted_domain[count]
		bl_domains_query = ' AND (' + mysql_bldb + '.blacklist.zone="' + domain_name + '" '
		count = count - 1 
	
		# Add others subdomains
		while count >= 0:
			domain_name = splitted_domain[count] + '.' + domain_name
			bl_domains_query = bl_domains_query + ' OR ' + mysql_bldb + '.blacklist.zone="' + domain_name + '" '
			count = count - 1 
	
		bl_end_query = ''') LIMIT 1;'''
	
		bl_final_query = bl_query + bl_domains_query + bl_end_query
	
		# query if domain is in blacklist for given profile ID
		cursor.execute(bl_final_query)
		second_result = cursor.fetchone()
		if second_result is None:
			blocked=0
			category=''
		else:
			blocked=1
			category=second_result[2]
	
		# Store data in the other temp table for faster search for this same domain
		insert_queried_domain='''INSERT INTO ''' + mysql_bldb + '''.tmp_queried_domains (`profile_id`, `domain`, `blocked`, `category`) values (''' + str(profile_id) + ', "' + domain + '", ' + str(blocked) + ', "' + category + '");' 
		cursor.execute(insert_queried_domain)
		conn.commit()

	# Else set if domain is in blacklist for the profile ID
	else:
		blocked=first_result[2]
		if blocked == 1:
			category=first_result[3]
		else:
			category=''

	return category


#------- Fonction to set data
def setlogdata(log_line, line_number):
	sql_values = {}

	sql_values["proxy_host"] = proxy_host
	sql_values["logfile_name"] = log_file.split("/")[-1]

	# split data in log line, posix=False is used in case of single quote inside URL 
	l = shlex.split(line, posix=False)

	#datetime_object = datetime.strptime('05-Dec-2017 09:51:24', '%d-%b-%Y %H:%M:%S')

	sql_values["line_number"] = line_number
	date = l[0]
	time = l[1].split('.')[0]
	dtime = datetime.strptime(date + " " + time, '%d-%b-%Y %H:%M:%S')

	# Date time use for id hashing, seconds are removed to have 1 min granularity to reduce log in database
	dtime_for_hash = dtime.strftime('%Y-%m-%d %H:%M')

	dtime = dtime.strftime('%Y-%m-%d %H:%M:%S')
	sql_values["date_time"] = dtime

	#sql_values["datetime"] = datetime.strptime(datetime, '%d-%b-%Y %H:%M:%S')
	sql_values["client_ip"] = l[4].split('#')[0]
	sql_values["profile_id"] = l[7].replace('view_profile_','').replace(':','')
	sql_values["domain"] = l[9]

	# LOG ID is sha256 HASH of log line
	str_to_hash = dtime_for_hash +  " " + sql_values["client_ip"] + " " + sql_values["profile_id"] + " " + sql_values["domain"]
	sql_values["id"] = hashlib.sha256(str_to_hash).hexdigest()

	# Determine if domain may have been blocked
	#sql_values["blocked"] = check_blocked_domain(sql_values["profile_id"], sql_values["domain"])
	block_status=check_blocked_domain(sql_values["profile_id"], sql_values["domain"])
	if block_status is '':
		sql_values["blocked"]=0
	else:
		sql_values["blocked"]=1

	sql_values["category"]=block_status

	return sql_values

#------- Function to import data
def import_data_db(lines):
	query = ''
	for line in lines:
		query = query + ",('{0}', '{1}', '{2}', '{3}', '{4}', '{5}', '{6}', '{7}', '{8}', '{9}')".format(line["id"], line["logfile_name"], line["line_number"], line["proxy_host"], line["date_time"], line["client_ip"], line["profile_id"], line["domain"], line["blocked"], line["category"])

	# build final query	([1:] removes the first "," in query)
	final_query = start_query + query[1:] + end_query

	# Exectute final query
	cursor.execute(final_query)

	# Commit data
	conn.commit()

#------- Main
# Create temporary table to store queried domains
cursor.execute(queried_domains_create_tmp_table)
conn.commit()

with opener(log_file, 'r') as infile:
	lines = []
	line_count = first_line
	for line in itertools.islice(infile, first_line, None):
		# split field of log line and save into array
		line_count = line_count + 1
		try:
			data = setlogdata(line, line_count)
			# Append array
			lines.append(data)
		except:
			print "Error import log line " + str(line_count) + " : " + line 
			
		if len(lines) > logs_per_query:
			import_data_db(lines)
			lines = []
	if len(lines) > 0:
		import_data_db(lines)
		lines = []

debug_temp_table = '''SELECT * FROM ''' + mysql_bldb + '''.tmp_queried_domains;'''
cursor.execute(debug_temp_table)
rows = cursor.fetchall()
for row in rows:
	print row

cursor.execute(queried_domains_drop_tmp_table)
conn.commit()
	
cursor.close
