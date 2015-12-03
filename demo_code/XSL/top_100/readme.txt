Aleph 500 ILS

Follow these steps to run the Top 100 report and load it to the Web

aleph circ module
service -> items -> general retrieval form (ret-adm-01)
collection = CIRC
loan = 10 - 999

see screen shot top_100.gif

output file is in C:\AL500prod\circ\files\xxx50\print
remove first line "## - XML_XSL" which is not XML compliant

replace "<?xml version="1.0"?>" with: 
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="top_100.xsl"?>