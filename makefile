.PHONY: deploy

deploy:
	rsync -vrc * mli-field@fielddaylab.wisc.edu:/httpdocs/logger --exclude-from rsync-exclude

deploy-testing:
	rsync -vrc * mli-field@fielddaylab.wisc.edu:/httpdocs/logger-testing --exclude-from rsync-exclude
