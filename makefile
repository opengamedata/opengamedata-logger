.PHONY: deploy

deploy:
	rsync -vrc * mli-field@fielddaylab.wisc.edu:/var/www/site/fielddaylab/logger --exclude-from rsync-exclude
