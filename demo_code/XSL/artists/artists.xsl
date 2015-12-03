<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:template match="/">
	 <html>
	 <head>
	 <title>Artists :: XML</title>
	 </head>
	  <xsl:apply-templates/>
	 </html>
	</xsl:template>

	<xsl:template match="document">
	 <body>
	  <xsl:apply-templates/>
	 </body>
	</xsl:template>

	<xsl:template match="artist">
	 <h2><xsl:value-of select="@name"/> [<xsl:value-of select="@id"/>]</h2>
	  <ul>
	   <xsl:apply-templates/>
	  </ul>
	</xsl:template>

	<xsl:template match="resource">
	 <li>
	  <xsl:value-of select="@title"/>
	  <xsl:text> - </xsl:text>
	  <xsl:value-of select="@location"/>
	  <xsl:text> - </xsl:text>
	   <a>
	    <xsl:attribute name="href">
	    <xsl:text>http://bcc.sunyconnect.suny.edu:4620/F?func=direct&amp;doc_number=</xsl:text>
	    <xsl:value-of select="@aleph_id"/>
	    <xsl:text>&amp;current_base=BCC01</xsl:text>
	    </xsl:attribute>
	    <xsl:value-of select="@aleph_id"/>
	   </a>
  	 </li>
	</xsl:template>

</xsl:stylesheet>