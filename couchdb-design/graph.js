{
  "_id": "_design/graph",
  "_rev": "1-cb906dd3f7976ad13e7d54456c8a3c66",
  "views": {
    "triples": {
      "map": "// get an identifier for this document\nfunction get_identifier(doc) {\n  var identifier = '';\n  var i = '';\n\n  if (doc.document) {\n \n    if (identifier === '') {\n      // highwire\n      if (doc.document.highwire) {\n        for (i in doc.document.highwire) {\n          switch (i) {\n            case 'doi':\n              identifier = doc.document.highwire[i][0];\n              break;\n            case 'pmid':\n              if (identifier === '') {\n                identifier = doc.document.highwire[i][0];\n              }\n              break;\n            default:\n              break;\n          }\n        }\n      }\n    }\n\n   if (identifier === '') {\n      // Dublin Core\n      if (doc.document.dc) {\n        for (i in doc.document.dc) {\n          switch (i) {\n            case 'Identifier':\n              identifier = doc.document.dc[i][0];\n              break;\n            default:\n              break;\n          }\n        }\n      }\n    }\n\n\n   if (identifier === '') {\n      // URI\n      if (doc.uri) {\n        identifier = doc.uri;\n       }\n    }\n \n\n  }\n\n  return identifier;\n} \n\n\nfunction(doc) {\n  var identifier = get_identifier(doc);\n  if (identifier !== '') {\n   // triple \n   if (doc.text) {\n     if (doc.text !== '') {\n\n       if (doc.tags) {\n         for (var i in doc.tags) {\n\n           var triple = [];\n           triple.push(identifier);\n           triple.push(doc.tags[i]);\n           triple.push(doc.text);\n\n           emit(doc._id, triple);\n           }\n       }  \n     }\n   }\n  }\n}"
    }
  },
  "language": "javascript"
}