<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
<xsl:output method="xml" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>

  <xsl:template name="vcard">  
    <xsl:variable name="url">
      <xsl:value-of select="url" />
    </xsl:variable>
<div class="shop_img">
    <a href="{$url}" >
      <xsl:copy-of select="fields/field_product_img/data/*" />
    </a>
</div>

<div class="info_warp">    
    <div class="shop_product_title">
      <a href="{$url}">
        <xsl:value-of select="substring(name, 1, 30)"  disable-output-escaping="yes" />  
      </a>
    </div>
    
     <div class="shop_product_short_description">
        <xsl:value-of select="substring(fields/field_short_description/data, 1, 45)"  disable-output-escaping="yes" />...    
    </div>   
 
 <div class="shop_product_location_and_phone">
 
      <xsl:if test="string-length(fields/field_product_location/data)!=0">  
     <div class="shop_product_location">
        <xsl:value-of select="fields/field_product_location/data" />    
    </div>  
    </xsl:if>
    
      <xsl:if test="string-length(fields/field_shop_erea/data)!=0">  
     <div class="shop_product_phone">
        אזור: <xsl:value-of select="fields/field_shop_erea/data" />    
    </div>  
    </xsl:if>
    
 </div> 
    
    

    
    <div class="shop_price">
    
              <xsl:if test="string-length(fields/field_shop_starting_from/data)!=0">  
     <div class="shop_starting_from">
        <xsl:value-of select="fields/field_shop_starting_from/data" />    
    </div>  
    </xsl:if>

		<xsl:value-of select="fields/field_product_price/data" /> 
    </div>


</div>
  </xsl:template>
</xsl:stylesheet>