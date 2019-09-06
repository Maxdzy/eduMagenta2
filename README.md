#EDU270 scandiweb

##3. FE Templating (styling default Magento functionality e.g. Related products)

Description

Update related product template and styling to match the design - Related Product design

Requirments

Need to use the Slick slider

Max number of products: 10

The related products must be in a slider. The slider should shows:

• 4 products for screens sizes of 1280px - ...         
• 3 products for screen sizes of 1024px - 1279px        
• 2 products for screen sizes of 768px - 1023px        
• 1 product for screen sizes of ... - 767px 

Each product must be clickable and link to the product page of that product

The user must be able to navigate through the slider by clicking on arrows  (as per design)

The dots below the slider represent the current location in image list (if the left-most item is the first item, the first dot is darker)

Once the slider reaches the end of the image list it should continue with the first images (infinite loop) appended to the end of the list so that there is no jump to first items and the transition is seamless.



###use - php bin/magento setup:upgrade

* url ```http://<host>/slick```
