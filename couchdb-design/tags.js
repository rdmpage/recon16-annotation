{
  "_id": "_design/tags",
  "_rev": "6-db99d6c20eb1e2ae346d760dfff49fc7",
  "views": {
    "all": {
      "map": "function get_text(doc) {\n  var text = '';\n\n  if (doc.text) {\n    text = doc.text;\n  } else {\n    for (var i in doc.target[0].selector) {\n      if (doc.target[0].selector[i].type == 'TextQuoteSelector') {\n        text = doc.target[0].selector[i].exact;\n      }\n    }\n  }\n \n\n  return text;\n\n}\n\n\nfunction(doc) {\n  if (doc.tags) {\n    for (var i in doc.tags) {\n      emit (doc.tags[i], get_text(doc));\n    }\n  }  \n}"
    },
    "identifier": {
      "map": "// get an identifier for this document\nfunction get_identifier(doc) {\n  var identifier = '';\n  var i = '';\n\n  if (doc.document) {\n \n    if (identifier === '') {\n      // highwire\n      if (doc.document.highwire) {\n        for (i in doc.document.highwire) {\n          switch (i) {\n            case 'doi':\n              identifier = doc.document.highwire[i][0];\n              break;\n            case 'pmid':\n              if (identifier === '') {\n                identifier = doc.document.highwire[i][0];\n              }\n              break;\n            default:\n              break;\n          }\n        }\n      }\n    }\n\n   if (identifier === '') {\n      // Dublin Core\n      if (doc.document.dc) {\n        for (i in doc.document.dc) {\n          switch (i) {\n            case 'Identifier':\n              identifier = doc.document.dc[i][0];\n              break;\n            default:\n              break;\n          }\n        }\n      }\n    }\n\n\n   if (identifier === '') {\n      // URI\n      if (doc.uri) {\n        identifier = doc.uri;\n       }\n    }\n \n\n  }\n\n  return identifier;\n} \n\n\nfunction(doc) {\n  var identifier = get_identifier(doc);\n  if (identifier !== '') {\n    emit(doc._id, identifier );\n  }\n}"
    },
    "geo": {
      "map": "function get_text(doc) {\n  var text = '';\n\n  if (doc.text) {\n    text = doc.text;\n  } else {\n    for (var i in doc.target[0].selector) {\n      if (doc.target[0].selector[i].type == 'TextQuoteSelector') {\n        text = doc.target[0].selector[i].exact;\n      }\n    }\n  }\n \n\n  return text;\n\n}\n\n\nfunction(doc) {\n  if (doc.tags) {\n    if (doc.tags.indexOf('geo') !== -1) {\n      emit (doc._id, get_text(doc));\n    }\n  }  \n}"
    },
    "geo-bad": {
      "map": "function get_text(doc) {\n  var text = '';\n\n  if (doc.text) {\n    text = doc.text;\n  } else {\n    for (var i in doc.target[0].selector) {\n      if (doc.target[0].selector[i].type == 'TextQuoteSelector') {\n        text = doc.target[0].selector[i].exact;\n      }\n    }\n  }\n \n\n  return text;\n\n}\n\n\nfunction(doc) {\n  if (doc.tags) {\n    if (doc.tags.indexOf('geo') !== -1) {\n      if (!doc.geometry) {\n        emit (doc._id, get_text(doc));\n      }\n    }\n  }  \n}"
    }
  },
  "language": "javascript"
}