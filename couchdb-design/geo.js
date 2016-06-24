{
  "_id": "_design/geo",
  "_rev": "4-9f029d0a73284c772a8af9992891c14a",
  "views": {},
  "language": "javascript",
  "st_indexes": {
    "newGeoIndex": {
      "index": "function (doc) {\n  if (doc.geometry && doc.geometry.coordinates) {\n    st_index(doc.geometry);\n  }\n}"
    }
  }
}