package main

import "net/http"

type Route struct {
  Pattern     string
  Method      string
  Name        string
  HandlerFunc http.HandlerFunc
}

type Routes []Route

var routes = Routes{
  Route{
    "/",
    "GET",
    "Index",
    Index,
  },
  Route{
    "/data",
    "POST",
    "DataCreate",
    DataCreate,
  },
}
