Edu_CmsCreateStore
===================

#EDU270 scandiweb

##4. Updating database data (migration scripts e.g. Configure store)

Configure store according to requirements using migration scripts

###Requirements

    Store available in 2 different languages via store switcher in the header - Store switcher example (Just example. The styling does not have to match)

    Base currency is EUR, but English store uses GBP

    English store uses scandi/default theme, but the german store uses scandi/german theme. (Both are should be created in the skin folder, but no modification necessary yet)

    Product and category pages should not have .html suffix

###use - php bin/magento setup:upgrade

    url http://<host>/slick

