.PHONY: build push

build:
	(cd themes/david-bohn; npm run production)
	hugo
	docker build -t dbohn/portfolio:latest .

push:
	docker push dbohn/portfolio:latest

test:
	docker run --name portfolio-test -d -p 8080:80 dbohn/portfolio:latest

deploy:
	envoy run deploy

all: build push deploy

dev:
	hugo server --theme=david-bohn --buildDrafts
