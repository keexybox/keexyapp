#!/usr/bin/perl
use strict;
use warnings;
use Proc::Daemon;
use File::Pid;

my $continue = 1;
my $pidfile = "/var/run/keexybox/keexybox.pid";

if($ARGV[0] eq 'start') {
	system('/sbin/runuser -l keexybox -c "/opt/keexybox/keexyapp/bin/cake init start"');
}

elsif($ARGV[0] eq 'stop') {
	system('/opt/keexybox/keexyapp/bin/cake init stop');
}

elsif($ARGV[0] eq 'daemon') {
	Proc::Daemon::Init;
	$SIG{TERM} = sub { $continue = 0 };
	my $pidfile = File::Pid->new({
		file => $pidfile,
		});
  
	$pidfile->write;

	# Run keexybox background task every 30s
	while ($continue) {
		system('/sbin/runuser -l keexybox -c "/opt/keexybox/keexyapp/bin/cake daemon"');
		sleep 30;
	}
}
else {
	print "help";
}
