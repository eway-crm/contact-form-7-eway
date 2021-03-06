<?php

/**
 * eWayConnector class helps connect and manage operation for web service.
 *
 * @copyright 2014-2015 eWay System s.r.o.
 * @version 2.0
 */
class eWayConnector
{
    private $appVersion = 'PHP2.0';
    private $sessionId;
    private $webServiceAddress;
    private $username;
    private $passwordHash;
    private $dieOnItemConflict;
    private $throwExceptionOnFail;
    private $wcfVersion = null;
    private $userGuid;

    /**
     * Initialize eWayConnector class
     *
     * @param $webServiceAddress Address of web service
     * @param $username User name
     * @param $password Plain password
     * @param $passwordAlreadyEncrypted - if true, user already encrypted password
     * @param $dieOnItemConflict If true, throws rcItemConflict when item has been changed before saving, if false, merges data
     * @param $throwExceptionOnFail If true, throws exception when the web service does not return rcSuccess
     * @throws Exception If web service address is empty
     * @throws Exception If username is empty
     * @throws Exception If password is empty
     */
    function __construct($webServiceAddress, $username, $password, $passwordAlreadyEncrypted = false, $dieOnItemConflict = false, $throwExceptionOnFail = true)
    {
        if (empty($webServiceAddress))
            throw new Exception('Empty web service address');

        if (empty($username))
            throw new Exception('Empty username');

        if (empty($password))
            throw new Exception('Empty password');

        $this->webServiceAddress = $this->formatUrl( $webServiceAddress );
        $this->username = $username;
        $this->dieOnItemConflict = $dieOnItemConflict;
        $this->throwExceptionOnFail = $throwExceptionOnFail;

        if ($passwordAlreadyEncrypted)
            $this->passwordHash = $password;
        else
            $this->passwordHash = md5($password);
    }

    /**
     * Gets all additional fields
     *
     * @return Json format with all additional fields
     */
    public function getAdditionalFields()
    {
        return $this->postRequest('GetAdditionalFields');
    }
    
    /**
     * Gets additional fields by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getAdditionalFieldsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetAdditionalFieldsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets all additional fields identifiers
     *
     * @return Json format with all additional fields identifiers
     */
    public function getAdditionalFieldsIdentifiers()
    {
        return $this->postRequest('GetAdditionalFieldsIdentifiers');
    }

    /**
     * Searches additional fields
     *
     * @param $additionalField Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If additionalField is empty
     * @return Json format with found additional fields
     */
    public function searchAdditionalFields($additionalField, $includeRelations = false)
    {
        if (empty($additionalField))
            throw new Exception('Empty additional field');

        // Any search request is defined as POST
        return $this->postRequest('SearchAdditionalFields', $additionalField, $includeRelations);
    }
    
    /**
     * Deletes cart
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteCart($guid)
    {
        return $this->deleteItem('DeleteCart', $guid);
    }

    /**
     * Gets all carts
     *
     * @return Json format with all carts
     */
    public function getCarts()
    {
        return $this->postRequest('GetCarts');
    }
    
    /**
     * Gets carts by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @param $omitGoodsInCart array of additional parameters (default: null)
     * @return Json format with items selected by guids
     */
    public function getCartsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false, $omitGoodsInCart = false)
    {
        if($omitGoodsInCart == true) {
            $additionalParameters = array('omitGoodsInCart' => true);
        } else {
            $additionalParameters = null;
        }
        
        return $this->getItemsByItemGuids('GetCartsByItemGuids', $guids, $includeForeignKeys, $includeRelations, $additionalParameters);
    }
    
    /**
     * Gets carts identifiers
     *
     * @return Json format with all carts identifiers
     */
    public function getCartsIdentifiers()
    {
        return $this->getItemIdentifiers('GetCartsIdentifiers');
    }

    /**
     * Searches carts
     *
     * @param $cart Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If cart is empty
     * @return Json format with found carts
     */
    public function searchCarts($cart, $includeRelations = false)
    {
        if (empty($cart))
            throw new Exception('Empty cart');

        // Any search request is defined as POST
        return $this->postRequest('SearchCarts', $cart, $includeRelations);
    }

    /**
     * Saves cart
     *
     * @param $cart Cart array data to save
     * @throws Exception If cart is empty
     * @return Json format with successful response
     */
    public function saveCart($cart)
    {
        if (empty($cart))
            throw new Exception('Empty cart');

        return $this->postRequest('SaveCart', $cart);
    }

    /**
     * Gets all calendars
     *
     * @return Json format with all calendars
     */
    public function getCalendars()
    {
        return $this->postRequest('GetCalendars');
    }
    
    /**
     * Gets calendars by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getCalendarsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetCalendarsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets calendars identifiers
     *
     * @return Json format with all calendars identifiers
     */
    public function getCalendarsIdentifiers()
    {
        return $this->getItemIdentifiers('GetCalendarsIdentifiers');
    }

    /**
     * Searches calendars
     *
     * @param $calendar Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If calendar is empty
     * @return Json format with found calendars
     */
    public function searchCalendars($calendar, $includeRelations = false)
    {
        if (empty($calendar))
            throw new Exception('Empty calendar');

        // Any search request is defined as POST
        return $this->postRequest('SearchCalendars', $calendar, $includeRelations);
    }

    /**
     * Saves calendar
     *
     * @param $calendar Calendar array data to save
     * @throws Exception If calendar is empty
     * @return Json format with successful response
     */
    public function saveCalendar($calendar)
    {
        if (empty($calendar))
            throw new Exception('Empty calendar');

        return $this->postRequest('SaveCalendar', $calendar);
    }

    /**
     * Deletes company
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteCompany($guid)
    {
        return $this->deleteItem('DeleteCompany', $guid);
    }
    
    /**
     * Gets all companies
     *
     * @return Json format with all companies
     */
    public function getCompanies()
    {
        return $this->postRequest('GetCompanies');
    }
    
    /**
     * Gets companies by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getCompaniesByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetCompaniesByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }

    /**
     * Gets companies identifiers
     *
     * @return Json format with all companies identifiers
     */
    public function getCompaniesIdentifiers()
    {
        return $this->getItemIdentifiers('GetCompaniesIdentifiers');
    }
    
    /**
     * Searches companies
     *
     * @param $company Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If company is empty
     * @return Json format with found companies
     */
    public function searchCompanies($company, $includeRelations = false)
    {
        if (empty($company))
            throw new Exception('Empty company');

        // Any search request is defined as POST
        return $this->postRequest('SearchCompanies', $company, $includeRelations);
    }

    /**
     * Saves company
     *
     * @param $company Company array data to save
     * @throws Exception If company is empty
     * @return Json format with successful response
     */
    public function saveCompany($company)
    {
        if (empty($company))
            throw new Exception('Empty company');

        return $this->postRequest('SaveCompany', $company);
    }

    /**
     * Deletes contact
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteContact($guid)
    {
        return $this->deleteItem('DeleteContact', $guid);
    }
    
    /**
     * Gets all contacts
     *
     * @return Json format with all contacts
     */
    public function getContacts()
    {
        return $this->postRequest('GetContacts');
    }
    
    /**
     * Gets contacts by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getContactsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetContactsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets contacts identifiers
     *
     * @return Json format with all contacts identifiers
     */
    public function getContactsIdentifiers()
    {
        return $this->getItemIdentifiers('GetContactsIdentifiers');
    }

    /**
     * Searches contacts
     *
     * @param $contact Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If contact is empty
     * @return Json format with found contacts
     */
    public function searchContacts($contact, $includeRelations = false)
    {
        if (empty($contact))
            throw new Exception('Empty contact');

        // Any search request is defined as POST
        return $this->postRequest('SearchContacts', $contact, $includeRelations);
    }

    /**
     * Saves contact
     *
     * @param $contact Contact array data to save
     * @throws Exception If contact is empty
     * @return Json format with successful response
     */
    public function saveContact($contact)
    {
        if (empty($contact))
            throw new Exception('Empty contact');

        return $this->postRequest('SaveContact', $contact);
    }

    /**
     * Deletes document
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteDocument($guid)
    {
        return $this->deleteItem('DeleteDocument', $guid);
    }
    
    /**
     * Gets all documents
     *
     * @return Json format with all documents
     */
    public function getDocuments()
    {
        return $this->postRequest('GetDocuments');
    }
    
    /**
     * Gets Documents by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getDocumentsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetDocumentsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets documents identifiers
     *
     * @return Json format with all documents identifiers
     */
    public function getDocumentsIdentifiers()
    {
        return $this->getItemIdentifiers('GetDocumentsIdentifiers');
    }

    /**
     * Searches documents
     *
     * @param $document Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If document is empty
     * @return Json format with found documents
     */
    public function searchDocuments($document, $includeRelations = false)
    {
        if (empty($document))
            throw new Exception('Empty document');

        // Any search request is defined as POST
        return $this->postRequest('SearchDocuments', $document, $includeRelations);
    }

    /**
     * Saves document
     *
     * @param $document Document array data to save
     * @throws Exception If document is empty
     * @return Json format with successful response
     */
    public function saveDocument($document)
    {
        if (empty($document))
            throw new Exception('Empty document');

        return $this->postRequest('SaveDocument', $document);
    }

    /**
     * Deletes email
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteEmail($guid)
    {
        return $this->deleteItem('DeleteEmail', $guid);
    }

    /**
     * Gets all emails
     *
     * @return Json format with all emails
     */
    public function getEmails()
    {
        return $this->postRequest('GetEmails');
    }
    
    /**
     * Gets emails by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getEmailsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetEmailsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets emails identifiers
     *
     * @return Json format with all emails identifiers
     */
    public function getEmailsIdentifiers()
    {
        return $this->getItemIdentifiers('GetEmailsIdentifiers');
    }

    /**
     * Searches emails
     *
     * @param $email Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If email is empty
     * @return Json format with found email
     */
    public function searchEmails($email, $includeRelations = false)
    {
        if (empty($email))
            throw new Exception('Empty email');

        // Any search request is defined as POST
        return $this->postRequest('SearchEmails', $email, $includeRelations);
    }

    /**
     * Saves email
     *
     * @param $email Email array data to save
     * @throws Exception If email is empty
     * @return Json format with successful response
     */
    public function saveEmail($email)
    {
        if (empty($email))
            throw new Exception('Empty email');

        return $this->postRequest('SaveEmail', $email);
    }

    /**
     * Gets all enum types
     *
     * @return Json format with all enum types
     */
    public function getEnumTypes()
    {
        return $this->postRequest('GetEnumTypes');
    }
    
    /**
     * Gets enum types by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getEnumTypesByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetEnumTypesByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets enum types identifiers
     *
     * @return Json format with all enum types identifiers
     */
    public function getEnumTypesIdentifiers()
    {
        return $this->getItemIdentifiers('GetEnumTypesIdentifiers');
    }
    
    /**
     * Searches enum types
     *
     * @param $enumType Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If enumType is empty
     * @return Json format with found enum values
     */
    public function searchEnumTypes($enumType, $includeRelations = false)
    {
        if (empty($enumType))
            throw new Exception('Empty enumType');

        // Any search request is defined as POST
        return $this->postRequest('SearchEnumTypes', $enumType, $includeRelations);
    }
    
    /**
     * Gets all enum values
     *
     * @return Json format with all enum values
     */
    public function getEnumValues()
    {
        return $this->postRequest('GetEnumValues');
    }
    
    /**
     * Gets enum values by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getEnumValuesByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetEnumValuesByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets enum values identifiers
     *
     * @return Json format with all enum values identifiers
     */
    public function getEnumValuesIdentifiers()
    {
        return $this->getItemIdentifiers('GetEnumValuesIdentifiers');
    }

    /**
     * Searches enum values
     *
     * @param $enumValue Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If enumValue is empty
     * @return Json format with found enum values
     */
    public function searchEnumValues($enumValue, $includeRelations = false)
    {
        if (empty($enumValue))
            throw new Exception('Empty enumValue');

        // Any search request is defined as POST
        return $this->postRequest('SearchEnumValues', $enumValue, $includeRelations);
    }

    /**
     * Gets all Features
     *
     * @return Json format with all features
     */
    public function getFeatures()
    {
        return $this->postRequest('GetFeatures');
    }
    
    /**
     * Gets features by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getFeaturesByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetFeaturesByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets features identifiers
     *
     * @return Json format with all features identifiers
     */
    public function getFeaturesIdentifiers()
    {
        return $this->getItemIdentifiers('GetFeaturesIdentifiers');
    }

    /**
     * Searches Features
     *
     * @param $features Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If features is empty
     * @return Json format with found features
     */
    public function searchFeatures($features, $includeRelations = false)
    {
        if (empty($features))
            throw new Exception('Empty features');

        // Any search request is defined as POST
        return $this->postRequest('SearchFeatures', $features, $includeRelations);
    }

    /**
     * Gets all Flows
     *
     * @return Json format with all flows
     */
    public function getFlows()
    {
        return $this->postRequest('GetFlows');
    }
    
    /**
     * Gets additional flows by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getFlowsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetFlowsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }

    /**
     * Gets flows identifiers
     *
     * @return Json format with all flows identifiers
     */
    public function getFlowsIdentifiers()
    {
        return $this->getItemIdentifiers('GetFlowsIdentifiers');
    }
    
    /**
     * Searches Flows
     *
     * @param $flow Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If flow is empty
     * @return Json format with found flows
     */
    public function searchFlows($flow, $includeRelations = false)
    {
        if (empty($flow))
            throw new Exception('Empty flow');

        // Any search request is defined as POST
        return $this->postRequest('SearchFlows', $flow, $includeRelations);
    }

    /**
     * Gets all Global settings
     *
     * @return Json format with all global settings
     */
    public function getGlobalSettings()
    {
        return $this->postRequest('GetGlobalSettings');
    }
    
    /**
     * Gets global settings by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getGlobalSettingsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetGlobalSettingsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets global settings identifiers
     *
     * @return Json format with all global settings identifiers
     */
    public function getGlobalSettingsIdentifiers()
    {
        return $this->getItemIdentifiers('GetGlobalSettingsIdentifiers');
    }

    /**
     * Searches Global settings
     *
     * @param $globalSetting Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If globalSetting is empty
     * @return Json format with found global settings
     */
    public function searchGlobalSettings($globalSetting, $includeRelations = false)
    {
        if (empty($globalSetting))
            throw new Exception('Empty global setting');

        // Any search request is defined as POST
        return $this->postRequest('SearchGlobalSettings', $globalSetting, $includeRelations);
    }

    /**
     * Deletes good
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteGood($guid)
    {
        return $this->deleteItem('DeleteGood', $guid);
    }
    
    /**
     * Gets all goods
     *
     * @return Json format with all goods
     */
    public function getGoods()
    {
        return $this->postRequest('GetGoods');
    }
    
    /**
     * Gets additional goods by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getGoodsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetGoodsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets goods identifiers
     *
     * @return Json format with all goods identifiers
     */
    public function getGoodsIdentifiers()
    {
        return $this->getItemIdentifiers('GetGoodsIdentifiers');
    }

    /**
     * Searches goods
     *
     * @param $good Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If good is empty
     * @return Json format with found goods
     */
    public function searchGoods($good, $includeRelations = false)
    {
        if (empty($good))
            throw new Exception('Empty good');

        // Any search request is defined as POST
        return $this->postRequest('SearchGoods', $good, $includeRelations);
    }

    /**
     * Saves good
     *
     * @param $good Good array data to save
     * @throws Exception If good is empty
     * @return Json format with successful response
     */
    public function saveGood($good)
    {
        if (empty($good))
            throw new Exception('Empty good');

        return $this->postRequest('SaveGood', $good);
    }

    
    /**
     * Deletes good in cart
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteGoodInCart($guid)
    {
        return $this->deleteItem('DeleteGoodInCart', $guid);
    }
    
    /**
     * Gets all goods in cart
     *
     * @return Json format with all goods in cart
     */
    public function getGoodsInCart()
    {
        return $this->postRequest('GetGoodsInCart');
    }
    
    /**
     * Gets goods in cart by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getGoodsInCartByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetGoodsInCartByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets goods in cart identifiers
     *
     * @return Json format with all goods in cart identifiers
     */
    public function getGoodsInCartIdentifiers()
    {
        return $this->getItemIdentifiers('GetGoodsInCartIdentifiers');
    }

    /**
     * Searches goods in cart
     *
     * @param $goodInCart Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If goodInCart is empty
     * @return Json format with found good in cart
     */
    public function searchGoodsInCart($goodInCart, $includeRelations = false)
    {
        if (empty($goodInCart))
            throw new Exception('Empty goodInCart');

        // Any search request is defined as POST
        return $this->postRequest('SearchGoodsInCart', $goodInCart, $includeRelations = false);
    }

    /**
     * Saves good in cart
     *
     * @param $goodInCart GoodInCart array data to save
     * @throws Exception If goodInCart is empty
     * @return Json format with successful response
     */
    public function saveGoodInCart($goodInCart)
    {
        if (empty($goodInCart))
            throw new Exception('Empty goodInCart');

        return $this->postRequest('SaveGoodInCart', $goodInCart);
    }

    /**
     * Gets all groups
     *
     * @return Json format with all groups
     */
    public function getGroups()
    {
        return $this->postRequest('GetGroups');
    }
    
    /**
     * Gets additional groups by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getGroupsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetGroupsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets groups identifiers
     *
     * @return Json format with all groups identifiers
     */
    public function getGroupsIdentifiers()
    {
        return $this->getItemIdentifiers('GetGroupsIdentifiers');
    }

    /**
     * Searches groups
     *
     * @param $group Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If group is empty
     * @return Json format with found groups
     */
    public function searchGroups($group, $includeRelations = false)
    {
        if (empty($group))
            throw new Exception('Empty group');

        // Any search request is defined as POST
        return $this->postRequest('SearchGroups', $group, $includeRelations);
    }

    /**
     * Saves group
     *
     * @param $group Group array data to save
     * @throws Exception If group is empty
     * @return Json format with successful response
     */
    public function saveGroup($group)
    {
        if (empty($group))
            throw new Exception('Empty group');

        return $this->postRequest('SaveGroup', $group);
    }

    /**
     * Deletes journal
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteJournal($guid)
    {
        return $this->deleteItem('DeleteJournal', $guid);
    }
    
    /**
     * Gets all journals
     *
     * @return Json format with all journals
     */
    public function getJournals()
    {
        return $this->postRequest('GetJournals');
    }
    
    /**
     * Gets journals by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getJournalsItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetJournalsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }

    /**
     * Gets journals identifiers
     *
     * @return Json format with all journals identifiers
     */
    public function getJournalsIdentifiers()
    {
        return $this->getItemIdentifiers('GetJournalsIdentifiers');
    }
    
    /**
     * Searches journals
     *
     * @param $journal Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If journal is empty
     * @return Json format with found journals
     */
    public function searchJournals($journal, $includeRelations = false)
    {
        if (empty($journal))
            throw new Exception('Empty journal');

        // Any search request is defined as POST
        return $this->postRequest('SearchJournals', $journal, $includeRelations);
    }

    /**
     * Saves journal
     *
     * @param $journal Journal array data to save
     * @throws Exception If journal is empty
     * @return Json format with successful response
     */
    public function saveJournal($journal)
    {
        if (empty($journal))
            throw new Exception('Empty journal');

        return $this->postRequest('SaveJournal', $journal);
    }

    /**
     * Deletes lead
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteLead($guid)
    {
        return $this->deleteItem('DeleteLead', $guid);
    }
    
    /**
     * Gets all leads
     *
     * @return Json format with all leads
     */
    public function getLeads()
    {
        return $this->postRequest('GetLeads');
    }
    
    /**
     * Gets leads by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getLeadsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetLeadsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets leads identifiers
     *
     * @return Json format with all leads identifiers
     */
    public function getLeadsIdentifiers()
    {
        return $this->getItemIdentifiers('GetLeadsIdentifiers');
    }

    /**
     * Searches leads
     *
     * @param $lead Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If lead is empty
     * @return Json format with found leads
     */
    public function searchLeads($lead, $includeRelations = false)
    {
        if (empty($lead))
            throw new Exception('Empty lead');

        // Any search request is defined as POST
        return $this->postRequest('SearchLeads', $lead, $includeRelations);
    }

    /**
     * Saves lead
     *
     * @param $lead Lead array data to save
     * @throws Exception If lead is empty
     * @return Json format with successful response
     */
    public function saveLead($lead)
    {
        if (empty($lead))
            throw new Exception('Empty lead');

        return $this->postRequest('SaveLead', $lead);
    }
    
    /**
     * Deletes marketing campaign
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteMarketingCampaign($guid)
    {
        return $this->deleteItem('DeleteMarketingCampaign', $guid);
    }
    
    /**
     * Gets all marketing campaigns
     *
     * @return Json format with all marketing campaigns
     */
    public function getMarketingCampaigns()
    {
        return $this->postRequest('GetMarketingCampaigns');
    }
    
    /**
     * Gets marketing campaigns by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getMerketingCampaignsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetMarketingCampaignsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets marketing campaigns identifiers
     *
     * @return Json format with all marketing campaigns identifiers
     */
    public function getMarketingCampaignsIdentifiers()
    {
        return $this->getItemIdentifiers('GetMarketingCampaignsIdentifiers');
    }

    /**
     * Searches marketing campaigns
     *
     * @param $marketingCampaign Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If marketing campaign is empty
     * @return Json format with found marketing campaigns
     */
    public function searchMarketingCampaigns($marketingCampaign, $includeRelations = false)
    {
        if (empty($marketingCampaign))
            throw new Exception('Empty marketing campaign');

        // Any search request is defined as POST
        return $this->postRequest('SearchMarketingCampaigns', $marketingCampaign, $includeRelations);
    }
    
    /**
     * Saves marketing campaign
     *
     * @param $marketingCampaign marketing campaign array data to save
     * @throws Exception If marketing campaign is empty
     * @return Json format with successful response
     */
    public function saveMarketingCampaign($marketingCampaign)
    {
        if (empty($marketingCampaign))
            throw new Exception('Empty marketing campaign');

        return $this->postRequest('SaveMarketingCampaign', $marketingCampaign);
    }
    
    /**
     * Deletes marketing list record
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteMarketingListRecord($guid)
    {
        return $this->deleteItem('DeleteMarketingListRecord', $guid);
    }
    
    /**
     * Gets all marketing lists records
     *
     * @return Json format with all marketing lists records
     */
    public function getMarketingListsRecords()
    {
        return $this->postRequest('GetMarketingListsRecords');
    }
    
    /**
     * Gets marketing lists by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getMarketingListsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetMarketingListsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets marketing lists records identifiers
     *
     * @return Json format with all marketing lists records identifiers
     */
    public function getMarketingListsRecordsIdentifiers()
    {
        return $this->getItemIdentifiers('GetMarketingListsRecordsIdentifiers');
    }

    /**
     * Searches marketing lists records
     *
     * @param $marketingListRecord Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If marketing list record is empty
     * @return Json format with found marketing list records
     */
    public function searchMarketingListsRecords($marketingListRecord, $includeRelations = false)
    {
        if (empty($marketingListRecord))
            throw new Exception('Empty marketing list record');

        // Any search request is defined as POST
        return $this->postRequest('SearchMarketingListsRecords', $marketingListRecords, $includeRelations);
    }
    
    /**
     * Saves marketing list record
     *
     * @param $marketingListRecord marketing list record array data to save
     * @throws Exception If marketing list record is empty
     * @return Json format with successful response
     */
    public function saveMarketingListRecord($marketingListRecord)
    {
        if (empty($marketingListRecord))
            throw new Exception('Empty marketing list record');

        return $this->postRequest('SaveMarketingListRecord', $marketingListRecord);
    }
    
    /**
     * Deletes project
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteProject($guid)
    {
        return $this->deleteItem('DeleteProject', $guid);
    }
    
    /**
     * Gets all projects
     *
     * @return Json format with all projects
     */
    public function getProjects()
    {
        return $this->postRequest('GetProjects');
    }
    
    /**
     * Gets projects by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getProjectsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetProjectsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets projects identifiers
     *
     * @return Json format with all projects identifiers
     */
    public function getProjectsIdentifiers()
    {
        return $this->getItemIdentifiers('GetProjectsIdentifiers');
    }

    /**
     * Searches projects
     *
     * @param $projects Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If project is empty
     * @return Json format with found projects
     */
    public function searchProjects($project, $includeRelations = false)
    {
        if (empty($project))
            throw new Exception('Empty project');

        // Any search request is defined as POST
        return $this->postRequest('SearchProjects', $project, $includeRelations);
    }

    /**
     * Saves project
     *
     * @param $project project array data to save
     * @throws Exception If project is empty
     * @return Json format with successful response
     */
    public function saveProject($project)
    {
        if (empty($project))
            throw new Exception('Empty Project');

        return $this->postRequest('SaveProject', $project);
    }

    /**
     * Gets all sale prices
     *
     * @return Json format with all sale prices
     */
    public function getSalePrices()
    {
        return $this->postRequest('GetSalePrices');
    }
    
    /**
     * Gets sale prices by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getSalePricesByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetSalePricesByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets sale prices identifiers
     *
     * @return Json format with all sale prices identifiers
     */
    public function getSalePricesIdentifiers()
    {
        return $this->getItemIdentifiers('GetSalePricesIdentifiers');
    }

    /**
     * Searches sale prices
     *
     * @param $salePrice Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If salePrice is empty
     * @return Json format with found sale prices
     */
    public function searchSalePrices($salePrice, $includeRelations = false)
    {
        if (empty($salePrice))
            throw new Exception('Empty salePrice');

        // Any search request is defined as POST
        return $this->postRequest('SearchSalePrices', $salePrice, $includeRelations);
    }

    /**
     * Saves relation
     *
     * @param $relation Relation array data to save
     * @throws Exception If relation is empty
     * @return Json format with successful response
     */
    public function saveRelation($relation)
    {
        if (empty($relation))
            throw new Exception('Empty relation');

        return $this->postRequest('SaveRelation', $relation);
    }

    /**
     * Gets all tasks
     *
     * @return Json format with all tasks
     */
    public function getTasks()
    {
        return $this->postRequest('GetTasks');
    }
    
    /**
     * Gets tasks by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getTasksByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetTasksByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets tasks identifiers
     *
     * @return Json format with all tasks identifiers
     */
    public function getTasksIdentifiers()
    {
        return $this->getItemIdentifiers('GetTasksIdentifiers');
    }

    /**
     * Searches tasks
     *
     * @param $task Array with specified properties for search
     * @throws Exception If task is empty
     * @return Json format with found tasks
     */
    public function searchTasks($task)
    {
        if (empty($task))
            throw new Exception('Empty task');

        // Any search request is defined as POST
        return $this->postRequest('SearchTasks', $task);
    }

    /**
     * Saves task
     *
     * @param $task Task array data to save
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If task is empty
     * @return Json format with successful response
     */
    public function saveTask($task, $includeRelations = false)
    {
        if (empty($task))
            throw new Exception('Empty task');

        return $this->postRequest('SaveTask', $task, $includeRelations);
    }

    /**
     * Gets all users
     *
     * @return Json format with all users
     */
    public function getUsers()
    {
        return $this->postRequest('GetUsers');
    }
    
    /**
     * Gets users by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getUsersByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetUsersByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets users identifiers
     *
     * @return Json format with all users identifiers
     */
    public function getUsersIdentifiers()
    {
        return $this->getItemIdentifiers('GetUsersIdentifiers');
    }

    /**
     * Searches users
     *
     * @param $user Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If user is empty
     * @return Json format with found users
     */
    public function searchUsers($user, $includeRelations = false)
    {
        if (empty($user))
            throw new Exception('Empty user');

        // Any search request is defined as POST
        return $this->postRequest('SearchUsers', $user, $includeRelations);
    }


    /**
     * Gets all work flow models
     *
     * @return Json format with all work flows
     */
    public function getWorkFlowModels()
    {
        return $this->postRequest('GetWorkFlowModels');
    }
    
    /**
     * Gets workflow models by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getWorkflowModelsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetWorkflowModelsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets workflow models identifiers
     *
     * @return Json format with all sale workflow models identifiers
     */
    public function getWorkflowModelsIdentifiers()
    {
        return $this->getItemIdentifiers('GetWorkflowModelsIdentifiers');
    }

    /**
     * Searches work flow models
     *
     * @param $workFlowModel Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If workFlowModel is empty
     * @return Json format with found work flow models
     */
    public function searchWorkFlowModels($workFlowModel, $includeRelations = false)
    {
        if (empty($workFlowModel))
            throw new Exception('Empty workFlowModel');

        // Any search request is defined as POST
        return $this->postRequest('SearchWorkFlowModels', $workFlowModel, $includeRelations);
    }

    /**
     * Deletes work report
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteWorkReport($guid)
    {
        return $this->deleteItem('DeleteWorkReport', $guid);
    }
    
    /**
     * Gets all work reports
     *
     * @return Json format with all work reports
     */
    public function getWorkReports()
    {
        return $this->postRequest('GetWorkReports');
    }
    
    /**
     * Gets work reports by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getWorkReportsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetWorkReportsByItemGuids', $guids, $includeForeignKeys, $includeRelations);
    }
    
    /**
     * Gets work reports identifiers
     *
     * @return Json format with all sale work reports identifiers
     */
    public function getWorkReportsIdentifiers()
    {
        return $this->getItemIdentifiers('GetWorkReportsIdentifiers');
    }

    /**
     * Searches work reports
     *
     * @param $workReport Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @throws Exception If workReport is empty
     * @return Json format with found work reports
     */
    public function searchWorkReports($workReport, $includeRelations = false)
    {
        if (empty($workReport))
            throw new Exception('Empty workReport');

        // Any search request is defined as POST
        return $this->postRequest('SearchWorkReports', $workReport, $includeRelations);
    }

    /**
     * Saves work report
     *
     * @param $workReport work report array data to save
     * @throws Exception If workReport is empty
     * @return Json format with successful response
     */
    public function saveWorkReport($workReport)
    {
        if (empty($workReport))
            throw new Exception('Empty workReport');

        return $this->postRequest('SaveWorkReport', $workReport);
    }
    
    /**
     * Deletes user settings
     *
     * @param $guid guid of item to be deleted
     * @return Json format with deletion status
     */
    public function deleteUserSettings($guid)
    {
        return $this->deleteItem('DeleteUserSetting', $guid, '5.3.1.68');
    }
    
    /**
     * Gets all user settings
     *
     * @return Json format with all user settings
     */
    public function getUserSettings()
    {
        return $this->postRequest('GetUserSettings', null, '5.3.1.68');
    }
    
    /**
     * Gets user settings by item guids
     *
     * @param $guids guids of items to get
     * @param $includeFoeignKeys indicator wether you want to include foreign keys (default: true)
     * @param $includeRelations indicator wether you want to include relations (default: false)
     * @return Json format with items selected by guids
     */
    public function getUserSettingsByItemGuids($guids, $includeForeignKeys = true, $includeRelations = false)
    {
        return $this->getItemsByItemGuids('GetUserSettingsByItemGuids', $guids, $includeForeignKeys, $includeRelations, '5.3.1.68');
    }
    
    /**
     * Gets user settings identifiers
     *
     * @return Json format with all sale user settings identifiers
     */
    public function getUserSettingsIdentifiers()
    {
        return $this->getItemIdentifiers('GetUserSettingsIdentifiers', '5.3.1.68');
    }
    
    /**
     * Searches user settings
     *
     * @param $workReport Array with specified properties for search
     * @param $includeRelations indicator wether you want to include relations (default: false) 
     * @throws Exception If userSettings is empty
     * @return Json format with found user settings
     */
    public function searchUserSettings($userSettings, $includeRelations = false)
    {
        if (empty($userSettings))
            throw new Exception('Empty userSettings');

        // Any search request is defined as POST
        return $this->postRequest('SearchUserSettings', $userSettings, $includeRelations = false, '5.3.1.68');
    }
    
    /**
     * Saves user settings
     *
     * @param $workReport work report array data to save
     * @throws Exception If userSettings is empty
     * @return Json format with successful response
     */
    public function saveUserSettings($userSettings)
    {
        if (empty($userSettings))
            throw new Exception('Empty userSettings');

        return $this->postRequest('SaveUserSetting', $userSettings, '5.3.1.68');
    }
    
    /**
     * Gets the last item change id (the latest, the highest)
     *
     * @return The last item change id
     */
    public function getLastItemChangeId()
    {
        return $this->doRequest(array('sessionId' => $this->sessionId), 'GetLastItemChangeId');
    }
    
    /**
     * Gets the item change identifiers for the given module and changes interval
     *
     * @param $folderName The module name - object type identifier
     * @param $baseChangeId The base change id
     * @param $targetChangeId The target change id
     * @return The item change identifiers for the given module and changes interval
     */
    public function getItemChangeIdentifiers($folderName, $baseChangeId, $targetChangeId)
    {
        $completeTransmitObject = array(
                'sessionId' => $this->sessionId,
                'folderName' => $folderName,
                'baseChangeId' => $baseChangeId,
                'targetChangeId' => $targetChangeId
            );
        
        return $this->doRequest($completeTransmitObject, 'GetItemChangeIdentifiers');
    }
    
    /**
     * Gets the changed items form the given folders. This method is a combination of calling GetItemChangeIdentifiers and Get[FolderName]ByItemGuids
     *
     * @param $folderNames The folder names
     * @param $baseChangeId The base change id
     * @param $targetChangeId The target change id
     * @param $includeForeignKeys If set to True, the JSON result will contain foreign keys/items fields made from the 1:N relations as well
     * @return The item changes for the given module and changes interval
     */
    public function getChangedItems($folderNames, $baseChangeId, $targetChangeId, $includeForeignKeys = false)
    {
        $completeTransmitObject = array(
                'sessionId' => $this->sessionId,
                'folderNames' => $folderNames,
                'baseChangeId' => $baseChangeId,
                'targetChangeId' => $targetChangeId,
                'includeForeignKeys' => $includeForeignKeys
            );

        return $this->doRequest($completeTransmitObject, 'GetChangedItems', '5.3.0.1');
    }
    
    /**
     * Gets module permissions of the current user
     *
     * @return Json format with permissions
     */
    public function getMyModulePermissions()
    {
        return $this->postRequest('GetMyModulePermissions');
    }
    
    /**
     * Uploads binary attachement against eWay-CRM API
     *
     * @param $filePath Path to file to be attached
     * @param $itemGuid Guid of the attached item (will generate new if empty)
     * @return Json format with successful response
     */
    public function saveBinaryAttachment($filePath, &$itemGuid = null)
    {
        if (empty($itemGuid))
            $itemGuid = trim(com_create_guid(), '{}');
        
        return $this->upload($itemGuid, $filePath);
    }

    /**
     * Formats date and time for the API calls
     *
     * @param $date Date to be formatted
     * @throws Exception If date is empty
     * @return Formatted date and time as string
     */
    public function formatDate($date)
    {
        if (empty($date))
            throw new Exception('Empty date');

        return date('Y-m-d H:i:s', $date);
    }

    public function getUserGuid()
    {
        if ($this->userGuid == NULL)
        {
            $this->reLogin();
        }
        return $this->userGuid;
    }
    
    private function reLogin()
    {
        $login = array(
            'userName' => $this->username,
            'passwordHash' => $this->passwordHash,
            'appVersion' => $this->appVersion
        );

        $jsonObject = json_encode($login, true);
        $ch = $this->createPostRequest($this->createWebServiceUrl('Login'), $jsonObject);

        $result = $this->executeCurl($ch);
        $jsonResult = json_decode($result);
        $returnCode = $jsonResult->ReturnCode;
        $this->wcfVersion = $jsonResult->WcfVersion;
        $this->userGuid = $jsonResult->UserItemGuid;
        
        // Check if web service has returned success.
        if ($returnCode != 'rcSuccess') {
            throw new Exception('Login failed: '.$jsonResult->Description);
        }

        // Save this sessionId for next time
        $this->sessionId = $jsonResult->SessionId;
    }
    
    private function formatUrl($url)
    {
        if( substr_compare( $url, '.svc', -4 ) === 0 || substr_compare( $url, '.svc/', -5 ) === 0 )
        {
            return $url;
        }
        elseif( substr_compare( $url, '/', -1 ) === 0 )
        {
            return $url.'WcfService/Service.svc';
        }
        else
        {
            return $url.'/WcfService/Service.svc';
        }
    }

    private function createWebServiceUrl($action)
    {
        return $this->joinPaths($this->webServiceAddress, $action);
    }
    
    private function createFileUploadUrl($itemGuid, $fileName)
    {
        return $this->createWebServiceUrl('SaveBinaryAttachment?sessionId='.$this->sessionId.'&itemGuid='.$itemGuid.'&fileName='.$fileName);
    }

    private function joinPaths()
    {
        $args = func_get_args();
        $paths = array();

        foreach ($args as $arg) {
            $paths = array_merge($paths, (array)$arg);
        }

        $paths = array_map(function($p) {return trim($p, "/"); }, $paths);
        $paths = array_filter($paths);
        return join('/', $paths);
    }

    private function postRequest($action, $transmitObject = null, $includeRelations = false, $version = null)
    {
        if ($transmitObject == null) {
            $completeTransmitObject = array(
                'sessionId' => $this->sessionId,
                'includeRelations' => $includeRelations
            );
        } else {
            $completeTransmitObject = array(
                'sessionId' => $this->sessionId,
                'transmitObject' => $transmitObject,
                'includeRelations' => $includeRelations,
                'dieOnItemConflict' => $this->dieOnItemConflict
            );
        }

        return $this->doRequest($completeTransmitObject, $action, $version);
    }
    
    private function getItemIdentifiers($action, $version = null ) {
        $completeTransmitObject = array(
            'sessionId' => $this->sessionId
        );
            
        return $this->doRequest($completeTransmitObject, $action, $version);
    }
    
    private function getItemsByItemGuids($action, $guids, $includeForeignKeys = true, $includeRelations = false, $additionalParameters = null, $version = null ) {
        if ($guids == null) {
            throw new Exception('Action '.$action.' requires an array of searched item guids to be executed on.');
        }
		
		$completeTransmitObject = array(
			'sessionId' => $this->sessionId,
			'itemGuids' => $guids,
			'includeForeignKeys' => $includeForeignKeys,
			'includeRelations' => $includeRelations
		);
		
		if($additionalParameters != null){
			foreach($additionalParameters as $key => $parameter)
				$completeTransmitObject[$key] = $parameter;
		}
        
        return $this->doRequest($completeTransmitObject, $action, $version);
    }
    
    private function deleteItem($action, $guid, $version = null ) {
        if ($guid == null) {
            throw new Exception('Action '.$action.' requires item to be executed on.');
        } else {
            $completeTransmitObject = array(
                'sessionId' => $this->sessionId,
                'itemGuid' => $guid
            );
        }
        
        return $this->doRequest($completeTransmitObject, $action, $version);
    }

    private function executeCurl($ch)
    {
        $result = curl_exec($ch);
        // Check if request has been executed successfully.
        if ($result === false) {
            throw new Exception('Error occurred while communicating with service: '.curl_error($ch));
        }

        // Also Check if return code is OK.
        $curlReturnInfo = curl_getinfo($ch);
        if ($curlReturnInfo['http_code'] != 200) {
            throw new Exception('Error occurred while communicating with service with http code: '.$curlReturnInfo['http_code']);
        }

        return $result;
    }

    private function doRequest($completeTransmitObject, $action, $version = null, $repeatSession = true)
    {   
        // This is first request, login before
        if (empty($this->sessionId)) {
            $this->reLogin();
            
            $completeTransmitObject['sessionId'] = $this->sessionId;
            return $this->doRequest($completeTransmitObject, $action, $version);
        }
        
        if ( $version != null ){
            if ( version_compare( $this->wcfVersion, $version ) == -1){
                throw new Exception('This function is available from version '.$version.' ! Your version is '.$this->wcfVersion.' .');
            }
        }
        
        $url = $this->createWebServiceUrl($action);
        $jsonObject = json_encode($completeTransmitObject, true);
        $ch = $this->createPostRequest($url, $jsonObject);
        
        $result = $this->executeCurl($ch);
        $jsonResult = json_decode($result);
        $returnCode = $jsonResult->ReturnCode;

        // Session timed out, re-log again
        if ($returnCode == 'rcBadSession') {
            $this->reLogin();
            $completeTransmitObject['sessionId'] = $this->sessionId;
        }
        
        if ($returnCode == 'rcBadSession' || $returnCode == 'rcDatabaseTimeout') {
            // For rcBadSession and rcDatabaseTimeout types of return code we'll try to perform action once again
            if($repeatSession == true) {
                return $this->doRequest($completeTransmitObject, $action, $version, false);
            }
        }
        
        if ($this->throwExceptionOnFail && $returnCode != 'rcSuccess') {
            throw new Exception($returnCode.': '.$jsonResult->Description);
        }
        
        return $jsonResult;
    }
    
    private function upload($itemGuid, $filePath)
    {
        // This is first request, login before
        if (empty($this->sessionId)) {
            $this->reLogin();
            
            return $this->upload($itemGuid, $filePath);
        }
        
        $url = $this->createFileUploadUrl($itemGuid, basename($filePath));
        $ch = $this->createUploadRequest($url, $filePath);
        
        $result = $this->executeCurl($ch);
        $jsonResult = json_decode($result);
        $returnCode = $jsonResult->ReturnCode;
        
        // Session timed out, re-log again
        if ($returnCode == 'rcBadSession') {
            $this->reLogin();
            $completeTransmitObject['sessionId'] = $this->sessionId;
        }
        
        if ($returnCode == 'rcBadSession' || $returnCode == 'rcDatabaseTimeout') {
            // For rcBadSession and rcDatabaseTimeout types of return code we'll try to perform action once again
            if($repeatSession == true) {
                return $this->doRequest($completeTransmitObject, $action, $version, false);
            }
        }
        
        if ($this->throwExceptionOnFail && $returnCode != 'rcSuccess') {
            throw new Exception($returnCode.': '.$jsonResult->Description);
        }
        
        return $jsonResult;
    }
    
    private function createPostRequest($url, $jsonObject)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonObject);
        
        return $ch;
    }

    private function createUploadRequest($url, $filePath)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/octet-stream', 'Accept: application/json', 'Content-Length: '.filesize($filePath)));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_INFILE, fopen($filePath, 'r'));
        
        return $ch;
    }
}
?>