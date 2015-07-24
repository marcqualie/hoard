default: build

build:
	go build -o hoard-server *.go

run:
	go run *.go
