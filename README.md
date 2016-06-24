# recon16-annotation

The hypothes.is project provides great tools to annotate web pages and PDFs, but what can we do with those annotations? This hack project aims to collect those annotations into a central database so that we can analyse them. For example, we could use annotations to build a citation network (what papers does this paper cite? what papers cite it?), we could create a map of the geographic localities mentioned in the papers, we could link entities mentioned to the corresponding Wikipedia pages, etc.

This hack tries to use existing cloud-based tools as much as possible so that you won’t need to do any programming on your laptop or device (unless, of course, you want to). 

You can participate in several ways.

1. Create a hypothes.is account and add some annotations to one or more papers.
2. Develop standard ways of annotating, perhaps using tags
3. Connect your hypothes.is account to the central database using https://ifttt.com
4. Propose queries you’d like the database to answer
5. Create those queries in the database itself.

## Overview

The hack glues together several tools:

1. https://hypothes.is to make annotations
2. https://ifttt.com to send the annotations to a central store
3. http://cloudant.com to analyse the annotations


## Using hypothes.is to annotate papers

![logo](https://github.com/rdmpage/recon16-annotation/raw/master/images/7iUlfzBp.jpg)

If you’ve not used hypothes.is before you will need to create a free account at https://hypothes.is. If you use Chrome you can get the hypothes.is extension, if you use another web browser you can still annotate content using the proxy https://via.hypothes.is

There are various ways to annotate documents. Here are some possibilities:

1. If a paper mentioned a latitude and longitude pair, select that text, click “annotate” and add the tag “geo”
2. In the list of literature cited, select a reference, click “annotate” and in the annotation paste in the DOI of the reference and add the tag “cites”
3. If the paper contains a scientific name, select it, click annotate, and add the tag “name”

There is lots of scope for thinking about what to annotate, and how to use tags and URLs to make the annotations easy to read by machines.

## Your annotation stream

Your annotations are stored in hypothes.is. To see them you can visit https://hypothes.is/stream?user=**username** where **username** is your hypothes.is username.

hypothes.is also provides the stream in a form we can use together with other services. For example, the stream can be obtained as a RSS feed:

https://hypothes.is/stream.rss?user=**username**

If you have a RSS reader you can view this stream, if you don’t there are free cloud-based RSS readers such as http://www.inoreader.com

## Connecting your annotations to a central store

![ifttt](https://github.com/rdmpage/recon16-annotation/raw/master/images/sC-uTVdd.png)

We want to be able to collect annotations and analyse them. To do this we need to connect the annotations streams to a database where we can play with them. One way to do this is to use IFTTT (If This Then That), a wonderful tool where we can connect pairs of services online to do new things.

Go to https://ifttt.com and either log in or sign up for a free account.

To connect your annotations to the central store, we need to create a recipe that links your annotation RSS feed to a “web hook” that talks to the central store. Click on “My Recipes” then “Create recipe”. Click on “this” and type “rss”. Click on the RSS icon (“Feed”). IFTTT will ask you for a “trigger”, click “New feed item” so that each annotation will trigger and event. It will then ask you for the Feed URL, paste in your feed (https://hypothes.is/stream.rss?user=<username> ) and click “Create Trigger”.

IFTTT will now ask for “that”. We are going to use the [Maker Channel](http://blog.ifttt.com/post/121786069098/introducing-the-maker-channel). In the box saying “Choose Action Channel” type in “maker” and click on the Maker icon. You will see a page like this: 

![screenshot](https://github.com/rdmpage/recon16-annotation/raw/master/images/Screenshot 2016-06-23 14.22.59.png)

Set the method to “POST”, content type to “application/json”, and body to “EntryUrl”. For the URL paste in: http://bionames.org/~rpage/recon16-annotation/webhook.php . 

This URL is for a service that reads the annotations received via IFTTT, tries to interpret them where possible (e.g., extract latitude and longitude from geographical coordinates) then sends the annotations to the central store.

Note that you can also use https://ifttt.com/recipes/433627-hypothesis-is-annotations-web-hook as a template.

## Central database

The central database for this project is CouchDB, in this case hosted by https://cloudant.com (other hosting options are available, such as http://www.iriscouch.com although these may lack some of the features this hack relies on).

An account on Cloudant has been created especially for this project with the user name “recon16” https://recon16.cloudant.com/dashboard.html . We can create some “views”  to index the data, including a geospatial index to search by geography (Cloudant has an built-in map to display this data on a map).

![map](https://github.com/rdmpage/recon16-annotation/raw/master/images/Screenshot 2016-06-23 15.53.07.png)

## Visualising annotations

As well as exploring the CouchDB database (not for the faint-hearted) we can create some simple visualisations. For example, adding the DOI of a paper to the URL http://bionames.org/~rpage/recon16-annotation/www/index.html?id= gives a crude summary of what the annotations say about a paper. Try it with:

http://bionames.org/~rpage/recon16-annotation/www/index.html?id=10.1644/10-MAMM-A-002.1

A locally edited PDF (see https://github.com/hypothesis/pdf.js-hypothes.is ).s

http://bionames.org/~rpage/recon16-annotation/www/index.html?id=urn:x-pdf:38703a7d2a507f45218b2f76aa75ff95

## Example papers to annotate

**A new species of shrew (Soricomorpha: Crocidura) from West Java, Indonesia** http://dx.doi.org/10.1644/13-MAMM-A-215 (if you are not using Chrome you can use the proxy https://via.hypothes.is/http://dx.doi.org/10.1644/13-MAMM-A-215

**A new species of tapir from the Amazon** http://dx.doi.org/10.1644/12-MAMM-A-169.1 [hypothes.is proxy](https://via.hypothes.is/http://dx.doi.org/10.1644/12-MAMM-A-169.1) (open access)

**Potamotrygon boesemani (Chondrichthyes: Myliobatiformes: Potamotrygonidae), a new species of Neotropical freshwater stingray from Surinam** http://dx.doi.org/10.1590/S1679-62252008000100001 [hypothes.is proxy](https://via.hypothes.is/http://dx.doi.org/10.1590/S1679-62252008000100001) (open access)


## Things to think about

### Multiple representations of the same article
The same article may exist as a PDF, as a web page, as an ePub, etc. If two people annotate two different representations of the same article (e.g., one person annotates the PDF, the other the web version in PubMed Central) how discover that those annotations are on the “same” article?

### Processing annotations
How much post-processing of annotations should we do? For example, should we try and parse a string and convert it to a link to the corresponding entity in a database?

