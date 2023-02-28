.PHONY: deploy

deploy:
	rsync -vrc * servicefieldday@ogdlogger.fielddaylab.wisc.edu:/var/www/html/logger --exclude-from rsync-exclude

deploy-testing:
	rsync -vrc * servicefieldday@ogdlogger.fielddaylab.wisc.edu:/var/www/html/logger-testing --exclude-from rsync-exclude
