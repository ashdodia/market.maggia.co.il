<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
<xsl:output method="xml" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>  
  <xsl:include href="../common/topmenu.xsl" />
  <xsl:include href="../common/manage.xsl" />  
  <xsl:template match="/entry_details">
    <div class="SPDetails">
 
        <div>
          <xsl:apply-templates select="menu" />
        </div>  
      
         
 
<div class="prod_title">
	<h2><xsl:value-of select="entry/name" /></h2>
</div> 
<div class="comp_logo">
    	<xsl:copy-of select="entry/fields/field_shop_comp_logo/data" />
  </div>
         
<div class="shop_top_warp">  
    
	<div class="shop_top_right">
        <div class="shop_Field short_description">  
         <b> <xsl:value-of select="entry/fields/field_short_description/data"   disable-output-escaping="yes" /></b>  
        </div>        
        
        <div class="shop_Field">  
         <xsl:value-of select="entry/fields/field_shop_hikashop/data"   disable-output-escaping="yes" /> 
        </div>        
        
        <xsl:if test="string-length(entry/fields/field_long_description/data)!=0">
		<div class="shop_Field long_description"> 
        <h3>תיאור</h3> 
			<xsl:value-of select="entry/fields/field_long_description/data"    disable-output-escaping="yes" />
        </div>
	</xsl:if> 
    
          	<xsl:if test="string-length(entry/fields/field_package_details/data)!=0">
       
		<div class="shop_Field package_details ">  
			<xsl:value-of select="entry/fields/field_package_details/data" disable-output-escaping="yes" />
        </div>
		</xsl:if> 
	</div>


        


 
	<div class="shop_top_left">
    	
     	<div class="shop_big_img"> 
        <xsl:if test="string-length(entry/fields/field_shop_gallery/data)!=0"> 
        <xsl:value-of select="entry/fields/field_shop_gallery/data" disable-output-escaping="yes" />
        	</xsl:if>
         
             <xsl:copy-of select="entry/fields/field_shop_big_img/data" />
           
     	</div> 
        
       
        
<div class="details_warp">        
        	<xsl:if test="string-length(entry/fields/field_product_location/data)!=0">
		<div class="shop_item_product_location item_details">
			<b>ישוב: </b>  <xsl:value-of select="entry/fields/field_product_location/data" />
        </div>
	</xsl:if>
    
    	<xsl:if test="string-length(entry/fields/field_shop_address/data)!=0">
		<div class="shop_item_product_location item_details">
			<b>כתובת: </b>  <xsl:value-of select="entry/fields/field_shop_address/data" />
        </div>
	</xsl:if>      
   
	<xsl:if test="string-length(entry/fields/field_product_phone/data)!=0">
		<div class="shop_item_product_phone item_details">
			<b>טלפון: </b>  <xsl:value-of select="entry/fields/field_product_phone/data" />
        </div>
	</xsl:if> 
    
    	<xsl:if test="string-length(entry/fields/field_restaurants_website/data)!=0">
		<div class="shop_item_product_phone item_details">
        <b>אתר הבית: </b> 
<a target="new">
								<xsl:attribute name="href">
									<xsl:value-of select="entry/fields/field_restaurants_website/data/a/@href" />
								</xsl:attribute>
								<strong><xsl:value-of select="entry/fields/field_restaurants_website/data" /></strong>
							</a>
        </div>
	</xsl:if> 
  </div>      
     	<xsl:if test="string-length(entry/fields/field_shop_terms/data)!=0">
        <h3>תנאי השובר</h3>
		<div class="shop_Field long_description">  
			<xsl:value-of select="entry/fields/field_shop_terms/data" disable-output-escaping="yes" />
        </div>
		</xsl:if> 
        
        
	</div>
 
</div>
 



<div style="clear:both;"/> 
</div>

  </xsl:template>
</xsl:stylesheet>