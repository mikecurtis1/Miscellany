<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html"/>

<xsl:template match="/">

<html>

  <head>
    <title>CIRC Sorted by Rank (greatest loans)</title>
    <link rel="stylesheet" type="text/css" href="top_100.css" media="screen" />
  </head>
  
  <body>

    <h1>Top 100 Books &#64;BCC 2008</h1>

    <xsl:for-each select="printout/section-02"> <!-- PRINT ALL CHILD TAG VALUES FOR ALL SECTION-02 TAGS -->

      <xsl:sort select="z30-no-loans" data-type="number" order="descending"/>
      <xsl:sort select="z13-doc-number" data-type="number" order="descending"/>

      <xsl:if test="position()&lt;=100">

        <div class="main">

          <xsl:choose>
            <xsl:when test="z13-isbn-issn-code='020'">
              <div class="left">
                <a>
                  <xsl:attribute name="href">https://books.google.com/books?vid=ISBN0062720163<xsl:value-of select="substring(z13-isbn-issn,1,10)"/></xsl:attribute>
                  <xsl:element name="img">
                    <xsl:attribute name="src">https://books.google.com/books?printsec=frontcover&amp;img=1&amp;zoom=1&amp;vid=ISBN:<xsl:value-of select="substring(z13-isbn-issn,1,10)"/></xsl:attribute>
                    <xsl:attribute name="alt">
                      <xsl:text>Book Cover</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="style">
                      <xsl:text>border:0;</xsl:text>
                    </xsl:attribute>
                  </xsl:element>
                </a>
              </div>
            </xsl:when>
            <xsl:otherwise></xsl:otherwise>
          </xsl:choose>
          
          <div class="right">

          <span class="bib_line">Rank: <xsl:text>#</xsl:text><xsl:value-of select="position()"/><xsl:text> Score: </xsl:text><xsl:value-of select="z30-no-loans"/></span>
          <br/>

          <xsl:if test="z13-title!=''">
            <span class="bib_line">
              <a>
                <xsl:attribute name="href">
                  <xsl:text>http://bcc.sunyconnect.suny.edu:4620/F?func=item-global&amp;doc_library=BCC01&amp;doc_number=</xsl:text>
                  <xsl:value-of select="format-number(z13-doc-number,'000000000')"/>
                </xsl:attribute>
                <xsl:value-of select="z13-title"/>
              </a>
            </span>
            <br/>
          </xsl:if>

          <xsl:if test="z13-author != ''">
            <span class="bib_line">
              <xsl:value-of select="z13-author"/>
            </span>
            <br/>
          </xsl:if>

          <xsl:if test="z13-imprint != ''">
            <span class="bib_line">
              <xsl:value-of select="z13-imprint"/>
            </span>
            <br/>
          </xsl:if>

          <xsl:choose>
            <xsl:when test="z13-isbn-issn-code = '020'">
              <span class="bib_line">
                <a>
                  <xsl:attribute name="href">
                    <xsl:text>http://www.goodreads.com/search?query=</xsl:text>
                    <xsl:value-of select="substring(z13-isbn-issn,1,10)"/>
                  </xsl:attribute>
                  <xsl:text>Reviews</xsl:text>
                </a>
              </span>
              <br/>
            </xsl:when>
            <xsl:otherwise><br/></xsl:otherwise>
          </xsl:choose>

          <span class="bib_line"><xsl:value-of select="z30-call-no"/></span>
          <br/>
          
          </div>

          <hr style="clear:both;"/>

        </div>

      </xsl:if>

    </xsl:for-each>

    <br/>

  </body>
  
</html>

</xsl:template>

</xsl:stylesheet>