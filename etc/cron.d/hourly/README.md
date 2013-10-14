Hour Cron Directory.

Anything placed in this directory will be automaticly run every hour.

The files in this directory should be chmod 750 and the names must consist 
entirely of upper and lower case letters, digits, underscores, and hyphens.

Be careful that the script name in these does not include a dot (.), e.g. 
backup.sh, since run-parts will ignore them.
