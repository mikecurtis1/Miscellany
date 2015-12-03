<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:slim="http://www.loc.gov/MARC21/slim"
  version="1.0">
    <xsl:output method="html"/>
    <xsl:template match="/">
        <body>
            <!-- re: select name of root node http://stackoverflow.com/a/368727/4223423 -->
            <xsl:value-of select="name(/*)"/><br />
            <xsl:value-of select="document('timestamp.xml')/timestamp"/><br />
            <xsl:value-of select="document('sandburg.xml')//slim:datafield[@tag='245']"/><br />
        </body>
    </xsl:template>
</xsl:stylesheet>