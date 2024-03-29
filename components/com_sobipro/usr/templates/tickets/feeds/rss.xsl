<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
    <xsl:output method="xml" encoding="UTF-8"/>
    <xsl:template match="/section|/category">
        <feed xmlns="http://www.w3.org/2005/Atom">
            <title>
                <xsl:value-of select="name"/>
            </title>
            <xsl:for-each select="entries/entry">
                <xsl:variable name="url">
                    <xsl:value-of select="php:function( 'SobiPro::Cfg', 'live_site' )"/><xsl:value-of select="url"/>
                </xsl:variable>
                <entry>
                    <title>
                        <xsl:value-of select="name"/>
                    </title>
                    
                    <link rel="alternate">
                        <xsl:attribute name="href">
                            <xsl:value-of select="php:function( 'Sobi::FixPath', $url )"/>
                        </xsl:attribute>
                    </link>
                   
                    <content type="html">
                    <div style="font-size:18px; font-weight:bold;" class="title"><xsl:value-of select="name"/></div>
                    
                        <xsl:copy-of select="fields/field_product_img/data/*" /><br />
                       <xsl:value-of select="fields/field_short_description/data"/><br />
                        <xsl:value-of select="fields/field_long_description/data"/>
                    </content>
                </entry>
            </xsl:for-each>
        </feed>
    </xsl:template>
</xsl:stylesheet>
