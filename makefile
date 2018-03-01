.PHONY: deploy

deploy:
	rsync -vrc * mli-field@fielddaylab.wisc.edu:/httpdocs/logger --exclude-from rsync-exclude
