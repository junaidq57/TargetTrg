<?php
return [
    'scopes' => [
        'websites' => [
            'admin' => [
                'website_id' => '0',
                'code' => 'admin',
                'name' => 'Admin',
                'sort_order' => '0',
                'default_group_id' => '0',
                'is_default' => '0'
            ],
            'base' => [
                'website_id' => '1',
                'code' => 'base',
                'name' => 'Main Website',
                'sort_order' => '0',
                'default_group_id' => '1',
                'is_default' => '1'
            ]
        ],
        'groups' => [
            [
                'group_id' => '0',
                'website_id' => '0',
                'code' => 'default',
                'name' => 'Default',
                'root_category_id' => '0',
                'default_store_id' => '0'
            ],
            [
                'group_id' => '1',
                'website_id' => '1',
                'code' => 'main_website_store',
                'name' => 'Main Website Store',
                'root_category_id' => '2',
                'default_store_id' => '1'
            ]
        ],
        'stores' => [
            'admin' => [
                'store_id' => '0',
                'code' => 'admin',
                'website_id' => '0',
                'group_id' => '0',
                'name' => 'Admin',
                'sort_order' => '0',
                'is_active' => '1'
            ],
            'default' => [
                'store_id' => '1',
                'code' => 'default',
                'website_id' => '1',
                'group_id' => '1',
                'name' => 'Default Store View',
                'sort_order' => '0',
                'is_active' => '1'
            ]
        ]
    ],
    'themes' => [
        'frontend/Magento/blank' => [
            'parent_id' => null,
            'theme_path' => 'Magento/blank',
            'theme_title' => 'Magento Blank',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Magento/blank'
        ],
        'adminhtml/Magento/backend' => [
            'parent_id' => null,
            'theme_path' => 'Magento/backend',
            'theme_title' => 'Magento 2 backend',
            'is_featured' => '0',
            'area' => 'adminhtml',
            'type' => '0',
            'code' => 'Magento/backend'
        ],
        'frontend/Magento/luma' => [
            'parent_id' => 'Magento/blank',
            'theme_path' => 'Magento/luma',
            'theme_title' => 'Magento Luma',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Magento/luma'
        ],
        'frontend/targettraining/default' => [
            'parent_id' => 'Magento/luma',
            'theme_path' => 'targettraining/default',
            'theme_title' => 'Target Training',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'targettraining/default'
        ]
    ],
    'system' => [
        'default' => [
            'web' => [
                'seo' => [
                    'use_rewrites' => '1'
                ],
                'secure' => [
                    'use_in_frontend' => null,
                    'use_in_adminhtml' => null
                ]
            ],
            'general' => [
                'locale' => [
                    'code' => 'en_GB',
                    'timezone' => 'UTC'
                ],
                'region' => [
                    'display_all' => '1',
                    'state_required' => 'AT,BR,CA,CH,EE,ES,FI,HR,IN,LT,LV,RO,US'
                ]
            ],
            'currency' => [
                'options' => [
                    'base' => 'GBP',
                    'default' => 'GBP',
                    'allow' => 'GBP'
                ]
            ],
            'catalog' => [
                'category' => [
                    'root_id' => '2'
                ]
            ],
            'analytics' => [
                'subscription' => [
                    'enabled' => '1'
                ]
            ],
            'design' => [
                'theme' => [
                    'theme_id' => 'frontend/targettraining/default'
                ],
                'pagination' => [
                    'pagination_frame' => '5',
                    'pagination_frame_skip' => null,
                    'anchor_text_for_previous' => null,
                    'anchor_text_for_next' => null
                ],
                'head' => [
                    'default_title' => 'Magento Commerce',
                    'title_prefix' => null,
                    'title_suffix' => null,
                    'default_description' => null,
                    'default_keywords' => null,
                    'includes' => null,
                    'demonotice' => '0'
                ],
                'header' => [
                    'logo_width' => null,
                    'logo_height' => null,
                    'logo_alt' => null,
                    'welcome' => 'Default welcome msg!'
                ],
                'footer' => [
                    'copyright' => 'Copyright © 2013-present Magento, Inc. All rights reserved.',
                    'absolute_footer' => null
                ],
                'search_engine_robots' => [
                    'default_robots' => 'INDEX,FOLLOW',
                    'custom_instructions' => null
                ],
                'watermark' => [
                    'image_size' => null,
                    'image_imageOpacity' => null,
                    'image_position' => 'stretch',
                    'small_image_size' => null,
                    'small_image_imageOpacity' => null,
                    'small_image_position' => 'stretch',
                    'thumbnail_size' => null,
                    'thumbnail_imageOpacity' => null,
                    'thumbnail_position' => 'stretch',
                    'swatch_image_size' => null,
                    'swatch_image_imageOpacity' => null,
                    'swatch_image_position' => 'stretch'
                ],
                'email' => [
                    'logo_alt' => null,
                    'logo_width' => null,
                    'logo_height' => null,
                    'header_template' => 'design_email_header_template',
                    'footer_template' => 'design_email_footer_template'
                ]
            ],
            'wordpress' => [
                'setup' => [
                    'enabled' => '1',
                    'path' => 'wordpress'
                ]
            ],
            'dev' => [
                'js' => [
                    'minify_files' => '1'
                ]
            ]
        ]
    ],
    'i18n' => [

    ],
    'modules' => [
        'Magento_Store' => 1,
        'Magento_Config' => 1,
        'Magento_AdminAnalytics' => 1,
        'Magento_AdminNotification' => 1,
        'Magento_AdobeIms' => 1,
        'Magento_AdobeImsApi' => 1,
        'Magento_AdobeStockAdminUi' => 1,
        'Magento_MediaGallery' => 1,
        'Magento_AdobeStockAssetApi' => 1,
        'Magento_AdobeStockClient' => 1,
        'Magento_AdobeStockClientApi' => 1,
        'Magento_AdobeStockImage' => 1,
        'Magento_Directory' => 1,
        'Magento_AdobeStockImageApi' => 1,
        'Magento_AdvancedPricingImportExport' => 1,
        'Magento_Theme' => 1,
        'Magento_Amqp' => 1,
        'Magento_Backend' => 1,
        'Magento_User' => 1,
        'Magento_Authorization' => 1,
        'Magento_Eav' => 1,
        'Magento_Customer' => 1,
        'Magento_AdminAdobeIms' => 1,
        'Magento_Backup' => 1,
        'Magento_Indexer' => 1,
        'Magento_GraphQl' => 1,
        'Magento_BundleImportExport' => 1,
        'Magento_CacheInvalidate' => 1,
        'Magento_Variable' => 1,
        'Magento_Cms' => 1,
        'Magento_Rule' => 1,
        'Magento_Security' => 1,
        'Magento_CmsGraphQl' => 1,
        'Magento_EavGraphQl' => 1,
        'Magento_Search' => 1,
        'Magento_CatalogImportExport' => 1,
        'Magento_Catalog' => 1,
        'Magento_CatalogInventory' => 1,
        'Magento_CatalogPageBuilderAnalytics' => 1,
        'Magento_CatalogRule' => 1,
        'Magento_Msrp' => 1,
        'Magento_CatalogRuleGraphQl' => 1,
        'Magento_CatalogSearch' => 1,
        'Magento_CatalogUrlRewrite' => 1,
        'Magento_StoreGraphQl' => 1,
        'Magento_MediaStorage' => 1,
        'Magento_Quote' => 1,
        'Magento_SalesSequence' => 1,
        'Magento_CheckoutAgreementsGraphQl' => 1,
        'Magento_MediaGalleryUi' => 1,
        'Magento_CatalogGraphQl' => 1,
        'Magento_CmsPageBuilderAnalytics' => 1,
        'Magento_CmsUrlRewrite' => 1,
        'Magento_CmsUrlRewriteGraphQl' => 1,
        'Magento_CompareListGraphQl' => 1,
        'Magento_Integration' => 1,
        'Magento_Payment' => 1,
        'Magento_Sales' => 1,
        'Magento_QuoteGraphQl' => 1,
        'Magento_Checkout' => 1,
        'Magento_Contact' => 1,
        'Magento_Cookie' => 1,
        'Magento_Cron' => 1,
        'Magento_Csp' => 1,
        'Magento_Widget' => 1,
        'Magento_Robots' => 1,
        'Magento_Analytics' => 1,
        'Magento_Downloadable' => 1,
        'Magento_CustomerGraphQl' => 1,
        'Magento_CustomerImportExport' => 1,
        'Magento_Deploy' => 1,
        'Magento_Developer' => 1,
        'Magento_Dhl' => 1,
        'Magento_AdvancedSearch' => 1,
        'Magento_DirectoryGraphQl' => 1,
        'Magento_DownloadableGraphQl' => 1,
        'Magento_CustomerDownloadableGraphQl' => 1,
        'Magento_ImportExport' => 1,
        'Magento_Bundle' => 1,
        'Magento_CatalogCustomerGraphQl' => 1,
        'Magento_Elasticsearch' => 1,
        'Magento_Elasticsearch7' => 1,
        'Magento_Email' => 1,
        'Magento_EncryptionKey' => 1,
        'Magento_Fedex' => 1,
        'Magento_GiftMessage' => 1,
        'Magento_GiftMessageGraphQl' => 1,
        'Magento_GoogleAdwords' => 1,
        'Magento_GoogleAnalytics' => 1,
        'Magento_GoogleGtag' => 1,
        'Magento_Ui' => 1,
        'Magento_GoogleShoppingAds' => 1,
        'Magento_BundleGraphQl' => 1,
        'Magento_PageCache' => 1,
        'Magento_GroupedProduct' => 1,
        'Magento_GroupedImportExport' => 1,
        'Magento_GroupedCatalogInventory' => 1,
        'Magento_GroupedProductGraphQl' => 1,
        'Magento_DownloadableImportExport' => 1,
        'Magento_Captcha' => 1,
        'Magento_InstantPurchase' => 1,
        'Magento_CatalogAnalytics' => 1,
        'Magento_Inventory' => 0,
        'Magento_InventoryAdminUi' => 0,
        'Magento_InventoryAdvancedCheckout' => 0,
        'Magento_InventoryApi' => 0,
        'Magento_InventoryBundleImportExport' => 0,
        'Magento_InventoryBundleProduct' => 0,
        'Magento_InventoryBundleProductAdminUi' => 0,
        'Magento_InventoryBundleProductIndexer' => 0,
        'Magento_InventoryCatalog' => 0,
        'Magento_InventorySales' => 0,
        'Magento_InventoryCatalogAdminUi' => 0,
        'Magento_InventoryCatalogApi' => 0,
        'Magento_InventoryCatalogFrontendUi' => 0,
        'Magento_InventoryCatalogSearch' => 0,
        'Magento_InventoryCatalogSearchBundleProduct' => 0,
        'Magento_InventoryCatalogSearchConfigurableProduct' => 0,
        'Magento_ConfigurableProduct' => 1,
        'Magento_ConfigurableProductGraphQl' => 1,
        'Magento_InventoryConfigurableProduct' => 0,
        'Magento_InventoryConfigurableProductIndexer' => 0,
        'Magento_InventoryConfiguration' => 0,
        'Magento_InventoryConfigurationApi' => 0,
        'Magento_InventoryDistanceBasedSourceSelection' => 0,
        'Magento_InventoryDistanceBasedSourceSelectionAdminUi' => 0,
        'Magento_InventoryDistanceBasedSourceSelectionApi' => 0,
        'Magento_InventoryElasticsearch' => 0,
        'Magento_InventoryExportStockApi' => 0,
        'Magento_InventoryIndexer' => 0,
        'Magento_InventorySalesApi' => 0,
        'Magento_InventoryGroupedProduct' => 0,
        'Magento_InventoryGroupedProductAdminUi' => 0,
        'Magento_InventoryGroupedProductIndexer' => 0,
        'Magento_InventoryImportExport' => 0,
        'Magento_InventoryInStorePickupApi' => 0,
        'Magento_InventoryInStorePickupAdminUi' => 0,
        'Magento_InventorySourceSelectionApi' => 0,
        'Magento_InventoryInStorePickup' => 0,
        'Magento_InventoryInStorePickupGraphQl' => 0,
        'Magento_Shipping' => 1,
        'Magento_InventoryInStorePickupShippingApi' => 0,
        'Magento_InventoryInStorePickupQuoteGraphQl' => 0,
        'Magento_InventoryInStorePickupSales' => 0,
        'Magento_InventoryInStorePickupSalesApi' => 0,
        'Magento_InventoryInStorePickupQuote' => 0,
        'Magento_InventoryInStorePickupShipping' => 0,
        'Magento_InventoryInStorePickupShippingAdminUi' => 0,
        'Magento_Multishipping' => 1,
        'Magento_Webapi' => 1,
        'Magento_InventoryCache' => 0,
        'Magento_InventoryLowQuantityNotification' => 0,
        'Magento_Reports' => 1,
        'Magento_InventoryLowQuantityNotificationApi' => 0,
        'Magento_InventoryMultiDimensionalIndexerApi' => 0,
        'Magento_InventoryProductAlert' => 0,
        'Magento_InventoryQuoteGraphQl' => 0,
        'Magento_InventoryRequisitionList' => 0,
        'Magento_InventoryReservations' => 0,
        'Magento_InventoryReservationCli' => 0,
        'Magento_InventoryReservationsApi' => 0,
        'Magento_InventoryExportStock' => 0,
        'Magento_InventorySalesAdminUi' => 0,
        'Magento_CatalogInventoryGraphQl' => 1,
        'Magento_InventorySalesAsyncOrder' => 0,
        'Magento_InventorySalesFrontendUi' => 0,
        'Magento_InventorySetupFixtureGenerator' => 0,
        'Magento_InventoryShipping' => 0,
        'Magento_InventoryShippingAdminUi' => 0,
        'Magento_InventorySourceDeductionApi' => 0,
        'Magento_InventorySourceSelection' => 0,
        'Magento_InventoryInStorePickupFrontend' => 0,
        'Magento_InventorySwatchesFrontendUi' => 0,
        'Magento_InventoryVisualMerchandiser' => 0,
        'Magento_InventoryWishlist' => 0,
        'Magento_JwtFrameworkAdapter' => 1,
        'Magento_JwtUserToken' => 1,
        'Magento_LayeredNavigation' => 1,
        'Magento_LoginAsCustomer' => 1,
        'Magento_LoginAsCustomerAdminUi' => 1,
        'Magento_LoginAsCustomerApi' => 1,
        'Magento_LoginAsCustomerAssistance' => 1,
        'Magento_LoginAsCustomerFrontendUi' => 1,
        'Magento_LoginAsCustomerGraphQl' => 1,
        'Magento_LoginAsCustomerLog' => 1,
        'Magento_LoginAsCustomerPageCache' => 1,
        'Magento_LoginAsCustomerQuote' => 1,
        'Magento_LoginAsCustomerSales' => 1,
        'Magento_Marketplace' => 1,
        'Magento_MediaContent' => 1,
        'Magento_MediaContentApi' => 1,
        'Magento_MediaContentCatalog' => 1,
        'Magento_MediaContentCms' => 1,
        'Magento_MediaContentSynchronization' => 1,
        'Magento_MediaContentSynchronizationApi' => 1,
        'Magento_MediaContentSynchronizationCatalog' => 1,
        'Magento_MediaContentSynchronizationCms' => 1,
        'Magento_AdobeStockAsset' => 1,
        'Magento_MediaGalleryApi' => 1,
        'Magento_MediaGalleryCatalog' => 1,
        'Magento_MediaGalleryCatalogIntegration' => 1,
        'Magento_MediaGalleryCatalogUi' => 1,
        'Magento_MediaGalleryCmsUi' => 1,
        'Magento_MediaGalleryIntegration' => 1,
        'Magento_MediaGalleryMetadata' => 1,
        'Magento_MediaGalleryMetadataApi' => 1,
        'Magento_MediaGalleryRenditions' => 1,
        'Magento_MediaGalleryRenditionsApi' => 1,
        'Magento_MediaGallerySynchronization' => 1,
        'Magento_MediaGallerySynchronizationApi' => 1,
        'Magento_MediaGallerySynchronizationMetadata' => 1,
        'Magento_AdobeStockImageAdminUi' => 1,
        'Magento_MediaGalleryUiApi' => 1,
        'Magento_CatalogWidget' => 1,
        'Magento_MessageQueue' => 1,
        'Magento_ConfigurableImportExport' => 1,
        'Magento_MsrpConfigurableProduct' => 1,
        'Magento_MsrpGroupedProduct' => 1,
        'Magento_InventoryInStorePickupMultishipping' => 0,
        'Magento_MysqlMq' => 1,
        'Magento_NewRelicReporting' => 1,
        'Magento_Newsletter' => 1,
        'Magento_NewsletterGraphQl' => 1,
        'Magento_OfflinePayments' => 1,
        'Magento_SalesRule' => 1,
        'Magento_OpenSearch' => 1,
        'Magento_Sitemap' => 1,
        'Magento_PageBuilder' => 1,
        'Magento_PageBuilderAnalytics' => 1,
        'Magento_GraphQlCache' => 1,
        'Magento_CardinalCommerce' => 1,
        'Magento_PaymentGraphQl' => 1,
        'Magento_Vault' => 1,
        'Magento_Paypal' => 1,
        'Magento_PaypalGraphQl' => 1,
        'Magento_Persistent' => 1,
        'Magento_ProductAlert' => 1,
        'Magento_ProductVideo' => 1,
        'Magento_CheckoutAgreements' => 1,
        'Magento_QuoteAnalytics' => 1,
        'Magento_QuoteBundleOptions' => 1,
        'Magento_QuoteConfigurableOptions' => 1,
        'Magento_QuoteDownloadableLinks' => 1,
        'Magento_InventoryConfigurableProductAdminUi' => 0,
        'Magento_ReCaptchaAdminUi' => 1,
        'Magento_ReCaptchaCheckout' => 1,
        'Magento_ReCaptchaCheckoutSalesRule' => 1,
        'Magento_ReCaptchaContact' => 1,
        'Magento_ReCaptchaCustomer' => 1,
        'Magento_ReCaptchaFrontendUi' => 1,
        'Magento_ReCaptchaMigration' => 1,
        'Magento_ReCaptchaNewsletter' => 1,
        'Magento_ReCaptchaPaypal' => 1,
        'Magento_ReCaptchaReview' => 1,
        'Magento_ReCaptchaSendFriend' => 1,
        'Magento_ReCaptchaStorePickup' => 1,
        'Magento_ReCaptchaUi' => 1,
        'Magento_ReCaptchaUser' => 1,
        'Magento_ReCaptchaValidation' => 1,
        'Magento_ReCaptchaValidationApi' => 1,
        'Magento_ReCaptchaVersion2Checkbox' => 1,
        'Magento_ReCaptchaVersion2Invisible' => 1,
        'Magento_ReCaptchaVersion3Invisible' => 1,
        'Magento_ReCaptchaWebapiApi' => 1,
        'Magento_ReCaptchaWebapiGraphQl' => 1,
        'Magento_ReCaptchaWebapiRest' => 1,
        'Magento_ReCaptchaWebapiUi' => 1,
        'Magento_RelatedProductGraphQl' => 1,
        'Magento_ReleaseNotification' => 1,
        'Magento_RemoteStorage' => 1,
        'Magento_InventoryLowQuantityNotificationAdminUi' => 0,
        'Magento_RequireJs' => 1,
        'Magento_Review' => 1,
        'Magento_ReviewAnalytics' => 1,
        'Magento_ReviewGraphQl' => 1,
        'Magento_AwsS3' => 1,
        'Magento_Rss' => 1,
        'Magento_PageBuilderAdminAnalytics' => 1,
        'Magento_CatalogRuleConfigurable' => 1,
        'Magento_SalesAnalytics' => 1,
        'Magento_SalesGraphQl' => 1,
        'Magento_SalesInventory' => 1,
        'Magento_OfflineShipping' => 1,
        'Magento_ConfigurableProductSales' => 1,
        'Magento_UrlRewrite' => 1,
        'Magento_UrlRewriteGraphQl' => 1,
        'Magento_CustomerAnalytics' => 1,
        'Magento_Securitytxt' => 1,
        'Magento_SendFriend' => 1,
        'Magento_SendFriendGraphQl' => 1,
        'Magento_InventoryInStorePickupSalesAdminUi' => 0,
        'Magento_AwsS3PageBuilder' => 1,
        'Magento_InventoryGraphQl' => 0,
        'Magento_CatalogCmsGraphQl' => 1,
        'Magento_Swagger' => 1,
        'Magento_SwaggerWebapi' => 1,
        'Magento_SwaggerWebapiAsync' => 1,
        'Magento_Swatches' => 1,
        'Magento_SwatchesGraphQl' => 1,
        'Magento_SwatchesLayeredNavigation' => 1,
        'Magento_Tax' => 1,
        'Magento_TaxGraphQl' => 1,
        'Magento_TaxImportExport' => 1,
        'Magento_TwoFactorAuth' => 0,
        'Magento_ThemeGraphQl' => 1,
        'Magento_Translation' => 1,
        'Magento_AdminAdobeImsTwoFactorAuth' => 0,
        'Magento_GoogleOptimizer' => 1,
        'Magento_Ups' => 1,
        'Magento_SampleData' => 1,
        'Magento_CatalogUrlRewriteGraphQl' => 1,
        'Magento_AsynchronousOperations' => 1,
        'Magento_Usps' => 1,
        'Magento_InventoryConfigurableProductFrontendUi' => 0,
        'Magento_PaypalCaptcha' => 1,
        'Magento_VaultGraphQl' => 1,
        'Magento_Version' => 1,
        'Magento_InventoryInStorePickupWebapiExtension' => 0,
        'Magento_WebapiAsync' => 1,
        'Magento_WebapiSecurity' => 1,
        'Magento_Weee' => 1,
        'Magento_WeeeGraphQl' => 1,
        'Magento_CurrencySymbol' => 1,
        'Magento_Wishlist' => 1,
        'Magento_WishlistAnalytics' => 1,
        'Magento_WishlistGraphQl' => 1,
        'Amasty_Base' => 1,
        'Amasty_Reindex' => 1,
        'Experius_WysiwygDownloads' => 1,
        'FishPig_WordPress' => 1,
        'FishPig_WordPress_Yoast' => 1,
        'FutureActivities_FixCategoryImages' => 1,
        'MageWorx_OptionBase' => 1,
        'MageWorx_OptionDependency' => 1,
        'MageWorx_OptionFeatures' => 1,
        'MageWorx_OptionInventory' => 1,
        'MageWorx_OptionLink' => 1,
        'MageWorx_OptionSkuPolicy' => 1,
        'MageWorx_OptionSwatches' => 1,
        'MageWorx_OptionTemplates' => 1,
        'Mageants_AllSlider' => 1,
        'Mageants_ExtensionVersionInformation' => 1,
        'Magecomp_Adminactivity' => 1,
        'Magenest_Ticket' => 0,
        'Mageplaza_Core' => 1,
        'Mageplaza_DeleteOrders' => 1,
        'Mageplaza_Productslider' => 1,
        'Mageplaza_Smtp' => 1,
        'PayPal_Braintree' => 1,
        'PayPal_BraintreeGraphQl' => 1,
        'Snmportal_SyntaxHighlighter' => 1,
        'StripeIntegration_Payments' => 1,
        'TargetTraining_Backend' => 1,
        'TargetTraining_CatalogCategory' => 1,
        'TargetTraining_CoachingForm' => 1,
        'TargetTraining_CustomOptions' => 1,
        'TargetTraining_CustomizedReport' => 1,
        'TargetTraining_EventFinder' => 1,
        'TargetTraining_FeaturedCourse' => 1,
        'TargetTraining_WordPress' => 1,
        'TargetTraining_ProductAttribute' => 1,
        'TargetTraining_Ticket' => 0,
        'TargetTraining_Homepage' => 1,
        'Temando_ShippingRemover' => 1,
        'ZV_SeoCompatible' => 1
    ]
];
