<?php
$changelog = array(
     array(
        'version'  => 'Version 2.9.9',
        'released' => '2019-05-15',
        'changes'  => array(
            array(
                'title'         => 'Translation issue',
                'type'          => 'Fix',
                'description'   => 'Make coupon strings translatable'
            ),
            array(
                'title'         => 'Report Abuse Module thumbnail',
                'type'          => 'Improvement',
                'description'   => 'Add thumbnail and description of report abuse module'
            ),
            array(
                'title'         => 'Social login and vendor verification',
                'type'          => 'Improvement',
                'description'   => 'Refactor social login and vendor verification module'
            ),
            array(
                'title'         => 'Change Moip brand to wirecard',
                'type'          => 'Improvement',
                'description'   => 'Rename Moip to Wirecard payment gateway'
            ),
        )
    ),
     array(
        'version'  => 'Version 2.9.8',
        'released' => '2019-05-07',
        'changes'  => array(
            array(
                'title'         => 'Report Abuse',
                'type'          => 'New',
                'description'   => 'Customer will be able to report againts product.'
            ),
            array(
                'title'         => 'Vendor Add Edit',
                'type'          => 'New',
                'description'   => 'Admin will be able to create new Vendor from the backend'
            ),
            array(
                'title'         => 'Dokan Booking',
                'type'          => 'New',
                'description'   => 'Add restricted days functionality in dokan booking module'
            ),
            array(
                'title'         => 'Single Product Multi Vendor',
                'type'          => 'New',
                'description'   => 'Enable SPMV for admins to duplicate products from admin panel'
            ),
            array(
                'title'         => 'Store Category',
                'type'          => 'Fix',
                'description'   => 'Fix store category list table search form'
            ),
            array(
                'title'         => 'Duplicate Subscription Form',
                'type'          => 'Fix',
                'description'   => 'Subscription form is rendering twice in registration form'
            ),
            array(
                'title'         => 'Subscription Cancellation',
                'type'          => 'Fix',
                'description'   => 'Cancel subscription doesn\'t work for manually assigned subscription'
            ),
            array(
                'title'         => 'Vendor Shipping',
                'type'          => 'new',
                'description'   => 'Add wilecard and range matching for vendor shipping zone'
            ),
            array(
                'title'         => 'Depricated Functions',
                'type'          => 'Improvement',
                'description'   => 'Replace get_woocommerce_term_meta with get_term_meta as it was deprecated'
            ),
        )
    ),
    array(
        'version'  => 'Version 2.9.7',
        'released' => '2019-03-25',
        'changes'  => array(
            array(
                'title'         => 'Store Category',
                'type'          => 'New',
                'description'   => 'Vendor will be able to register under specefic cateogry. ei(Furniture, Mobile)'
            ),
            array(
                'title'         => 'YITH WC Brand Compatible',
                'type'          => 'New',
                'description'   => 'Make Dokan YITH WC Brand add-on compatible'
            ),
            array(
                'title'         => 'Date and refund column in admin logs area',
                'type'          => 'New',
                'description'   => 'Add date and refund column in admin logs area to get more detaild overview.'
            ),
            array(
                'title'         => 'Product Status',
                'type'          => 'New',
                'description'   => 'Change product status according to subscription status '
            ),
            array(
                'title'         => 'Show button for non logged-in user',
                'type'          => 'Fix',
                'description'   => 'Show button for non logged-in user'
            ),
            array(
                'title'         => 'Refund Calculation Issue',
                'type'          => 'Fix',
                'description'   => 'Send refund admin commission to customer '
            ),
            array(
                'title'         => 'Error on subscription cancellation email ',
                'type'          => 'Fix',
                'description'   => 'There was an error on subscription cancellation, which has been fixed in this release.'
            ),
            array(
                'title'         => 'Trial Subscription',
                'type'          => 'Improvement',
                'description'   => 'When a vendor subscribe to a trial subscription, make all other trial to non-trial subscription for that vendor'
            ),
            array(
                'title'         => 'Social Login Issue',
                'type'          => 'Fix',
                'description'   => 'Update social login and vendor verification API'
            ),
        )
    ),
    array(
        'version'  => 'Version 2.9.5',
        'released' => '2019-02-18',
        'changes'  => array(
            array(
                'title'         => 'Automate order refund process via stripe',
                'type'          => 'New',
                'description'   => 'Vendor can now send automatic refund to their customer from vendor order dashboard'
            ),
            array(
                'title'         => 'Add trial subscription (Subscription Module)',
                'type'          => 'New',
                'description'   => 'Admin can now offer trail subscription for vendors'
            ),
            array(
                'title'         => 'Product type & gallery image restriction',
                'type'          => 'New',
                'description'   => 'Admin can now restrict product type & gallery image upload for vendor subscription'
            ),
            array(
                'title'         => 'Privacy and Policy',
                'type'          => 'New',
                'description'   => 'Admin can configure privacy policy info for frontend product enquiry form'
            ),
            array(
                'title'         => 'Email notification for store follow',
                'type'          => 'Fix',
                'description'   => 'Now vendor can get email notification on store follows and unfollows'
            ),
            array(
                'title'         => 'Unable to select country or state in vendor shipping',
                'type'          => 'Fix',
                'description'   => 'Country dropdown not working in shipping and announcement'
            ),
            array(
                'title'         => 'Admin report logs calculation issue is fixed in admin dashboard',
                'type'          => 'Fix',
                'description'   => 'Some calculation issue fixed in admin reports'
            ),
        )
    ),
    array(
        'version'  => 'Version 2.9.4',
        'released' => '2019-01-23',
        'changes'  => array(
            array(
                'title'         => 'Wholesale Module(Business, Enterprise Package)',
                'type'          => 'New',
                'description'   => 'Added new Wholesale module. Vendor can offer wholesale price for his/her products.'
            ),
            array(
                'title'         => 'Return and Warranty Module(Professional, Business, Enterprise Package)',
                'type'          => 'New',
                'description'   => 'Vendor can offer warranty and return system for their products and customer can take this warranty offers'
            ),
            array(
                'title'         => 'Subscription cancellation email',
                'type'          => 'New',
                'description'   => 'Now admin can get email if any subscription is cancelled by vendor'
            ),
            array(
                'title'         => 'Subscription Unlimited pack',
                'type'          => 'New',
                'description'   => 'Admin can offer unlimited package for vendor subscription'
            ),
            array(
                'title'         => 'MOIP Gateway connection issue',
                'type'          => 'Fix',
                'description'   => 'Change some gateway api params for connection moip gateway'
            ),
        )
    ),
    array(
        'version'  => 'Version 2.9.3',
        'released' => '2018-12-18',
        'changes'  => array(
            array(
                'title'         => 'ShipStation Module(Business, Enterprise Package)',
                'type'          => 'New',
                'description'   => 'Added new ShipStation module'
            ),
            array(
                'title'         => 'Follow Store Module(Professional, Business, Enterprise Package)',
                'type'          => 'New',
                'description'   => 'Added Follow Store module'
            ),
            array(
                'title'         => 'Product Quick Edit',
                'type'          => 'New',
                'description'   => 'Added Quick edit option for product in vendor dashboard.'
            ),
            array(
                'title'         => 'Searching Option',
                'type'          => 'New',
                'description'   => 'Add searching option in dokan vendor and refund page'
            ),
            array(
                'title'         => 'Admin Tools & Subscription Page Improvement',
                'type'          => 'Improvement',
                'description'   => 'Rewrite admin tools & subscription page in vue js'
            ),
            array(
                'title'         => 'Filter form & Map in Category Page',
                'type'          => 'Fix',
                'description'   => 'Show filter form and map in product category pages (geolocation module)'
            ),
            array(
                'title'         => 'Bookable Product Commission',
                'type'          => 'Fix',
                'description'   => 'Add per product commission option for bookable product'
            ),
            array(
                'title'         => 'Refund Calculation Issue',
                'type'          => 'Fix',
                'description'   => 'Refund calculation is wrong when shipping fee recipient is set to vendor'
            ),
            array(
                'title'         => 'Bulk Refund is Not Working',
                'type'          => 'Fix',
                'description'   => 'Approving batch refund is not working in admin backend'
            ),
            array(
                'title'         => 'Product Stock Issue on Refund',
                'type'          => 'Fix',
                'description'   => 'Increase stock ammount if the product is refunded'
            ),
            array(
                'title'         => 'Category Restriction Issue',
                'type'          => 'Fix',
                'description'   => 'Booking product category restriction for subscription pack is not working'
            )
        )
    ),
    array(
        'version'  => 'Version 2.9.2',
        'released' => '2018-11-09',
        'changes'  => array(
            array(
                'title'         => 'Geolocation Module',
                'type'          => 'New',
                'description'   => 'Added zoom level settings in geolocation module.'
            ),
            array(
                'title'         => 'Zone Wise Shipping',
                'type'          => 'New',
                'description'   => 'Added shipping policy and processing time settings in zone wise shipping.'
            ),
            array(
                'title'         => 'Rest API for Store Reviews',
                'type'          => 'New',
                'description'   => 'Added rest API support for store review post type.'
            ),
            array(
                'title'         => 'Show Tax on Bookable Product',
                'type'          => 'Fix',
                'description'   => 'Show tax on bookable product for vendor'
            ),

            array(
                'title'         => 'Product Importing Issue for Subscribed Vendor',
                'type'          => 'Fix',
                'description'   => 'Allow vendor to import only allowed number of products.'
            ),
            array(
                'title'         => 'Product and Order Discount Issue',
                'type'          => 'Fix',
                'description'   => 'Product and order discount for vendor is not working.'
            ),
            array(
                'title'         => 'Shipping Class Issue',
                'type'          => 'Fix',
                'description'   => 'Shipping class is not saving for bookable product.'
            )
        )
    ),
    array(
        'version'  => 'Version 2.9.0',
        'released' => '2018-10-03',
        'changes'  => array(
            array(
                'title'         => 'Geolocation Module',
                'type'          => 'New',
                'description'   => 'Enable this module to let the customers search for a specific product or vendor using any location they want.'
            ),
            array(
                'title'         => 'Moip Payment Gateway',
                'type'          => 'New',
                'description'   => 'Use one of the most popular payment system known for it\'s efficiency with Dokan.'
            ),
            array(
                'title'         => 'Allow Vendor to crate tags',
                'type'          => 'New',
                'description'   => 'Your vendors don\'t need to rely on prebuilt tags anymore. Now they can create their own in seconds'
            ),
            array(
                'title'         => 'Responsive Admin Pages',
                'type'          => 'New',
                'description'   => 'All the admin backend pages is now responsive for all devices'
            ),
            array(
                'title'         => 'Staff email for New Order',
                'type'          => 'New',
                'description'   => 'Staff will able to get all emails for new order from customer'
            )
        )
    ),
    array(
        'version'  => 'Version 2.8.3',
        'released' => '2018-07-19',
        'changes'  => array(
            array(
                'title'         => 'Live Chat Module',
                'type'          => 'Fix',
                'description'   => 'Right now the chat box is available in customer myaccount page and also make responsive chat box window'
            ),
            array(
                'title'         => 'Statement and Refund',
                'type'          => 'Fix',
                'description'   => 'Change core table structure for refund and statements. Now its easy to understand for vendor to check her statements. Also fixed statement exporting problem'
            ),
            array(
                'title'         => 'Zonewise Shipping',
                'type'          => 'Fix',
                'description'   => 'Shipping state rendering issue fixed. If any country have no states then states not showing undefine problem'
            ),
            array(
                'title'         => 'Stripe Module',
                'type'          => 'Fix',
                'description'   => 'Card is automatically saved if customer does not want to save his/her card info during checkout'
            )
        )
    ),
    array(
        'version'  => 'Version 2.8.2',
        'released' => '2018-06-29',
        'changes'  => array(
            array(
                'title'         => 'Live Chat Module',
                'type'          => 'New',
                'description'   => 'Vendors will now be able to provide live chat support to visitors and customers through this TalkJS integration. Talk from anywhere in your store, add attachments, get desktop notifications, enable email notifications, and store all your messages safely in Vendor Inbox.<br><iframe width="560" height="315" src="https://www.youtube.com/embed/BHuTLjY78cY" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>'
            ),
            array(
                'title'         => 'Added Refund and Announcement REST API',
                'type'          => 'New',
                'description'   => 'Admins can now modify refund and announcement section of Dokan easily through the Rest API'
            ),
            array(
                'title'         => 'Local pickup is visible when the cost is set to zero',
                'type'          => 'Fix',
                'description'   => 'When local pickup cost in Dokan Zone-wise shipping is set to zero it will show on the cart/checkout page'
            ),
            array(
                'title'         => 'Store Support ticket is visible in customer dashboard support menu',
                'type'          => 'Fix',
                'description'   => 'Now customers can view the support tickets they create in My Account> support ticket area'
            ),
            array(
                'title'         => 'Added tax and shipping functionalities in auction product',
                'type'          => 'Fix',
                'description'   => 'Now admins can add shipping and tax rates for auctionable product'
            ),
            array(
                'title'         => 'Appearance module for admins',
                'type'          => 'Fix',
                'description'   => 'Now Admins can view Color Customizer settings in backend without any problem'
            ),
            array(
                'title'         => 'Unable to delete vendor form admin panel',
                'type'          => 'Fix',
                'description'   => 'Admin was unable to delete a vendor from admin panel'
            ),
        )
    ),
    array(
        'version'  => 'Version 2.8.0',
        'released' => '2018-05-01',
        'changes'  => array(
            array(
                'title'         => 'Introduction of REST APIs',
                'type'          => 'New',
                'description'   => 'We have introduced REST APIs in dokan'
            ),
            array(
                'title'         => 'Zone wize shipping',
                'type'          => 'New',
                'description'   => 'We have introduced zone wize shipping functionality similar to WooCommerce in dokan. <img src="https://wedevs-com-wedevs.netdna-ssl.com/wp-content/uploads/2018/04/dokan-vendor-dashboard-settings-shipping-method-settings.gif">'
            ),
            array(
                'title'         => 'Earning suggestion for variable product',
                'type'          => 'New',
                'description'   => 'As like simple product, vendor will get to see the earning suggestion for variable product as well'
            ),
            array(
                'title'         => 'Confirmation on subscription cancellation',
                'type'          => 'New',
                'description'   => 'Cancellation of a subscription pack will ask for confirmation'
            ),
            array(
                'title'         => 'Unable to login with social media',
                'type'          => 'Fix',
                'description'   => 'Customer, Seller was unable to login with social media'
            ),
            array(
                'title'         => 'CSV earning report exporting',
                'type'          => 'Fix',
                'description'   => 'There were an issue with CSV report exporting from back end'
            ),
            array(
                'title'         => 'Unable to delete vendor form admin panel',
                'type'          => 'Fix',
                'description'   => 'Admin was unable to delete a vendor from admin panel'
            ),
            array(
                'title'         => 'Seller setup wizard is missing during email verification',
                'type'          => 'Fix',
                'description'   => 'Seller setup wizard after a seller is verified by email was missing'
            ),
            array(
                'title'         => 'Subscription Free pack visibility',
                'type'          => 'Fix',
                'description'   => 'Hide subscription product type from back end when a seller can access the back end'
            ),
            array(
                'title'         => 'Disable back end access for vendor staff',
                'type'          => 'Improvement',
                'description'   => 'Disable back end access for vendor staff for security perpose'
            ),
            array(
                'title'         => 'Updated deprecated functions',
                'type'          => 'Improvement',
                'description'   => 'Updated some deprecated functions'
            ),
            array(
                'title'         => 'Statement calculation',
                'type'          => 'Improvement',
                'description'   => 'Statement calculation'
            ),
            array(
                'title'         => 'Reduction of \'dokan\' text from staff permission',
                'type'          => 'Improvement',
                'description'   => 'Reduction of \'dokan\' text from staff permission',
            ),
            array(
                'title'         => 'Various UI, UX improvement',
                'type'          => 'Improvement',
                'description'   => 'Various UI, UX improvement',
            ),
        )
    ),
    array(
        'version'  => 'Version 2.7.5',
        'released' => '2018-03-06',
        'changes'  => array(
            array(
                'title'         => 'Re-organize dokan admin menu’s',
                'type'          => 'Improvement',
                'description'   => 'We re-organized doman menus in the amdin panel'
            ),
            array(
                'title'         => 'Shipping is not working if WPML is activated',
                'type'          => 'Fix',
                'description'   => 'Dokan shipping was\'t working when the WPML plugin was activated'
            ),
            array(
                'title'         => 'Subscription pack purchase during vendor registration [Subscription Module]',
                'type'          => 'Fix',
                'description'   => 'Vendor wasn\'t able to purchase subscription pack just after the registration process'
            ),
            array(
                'title'         => 'Cannot register using social media',
                'type'          => 'Fix',
                'description'   => 'User was unable to login or register using social media'
            ),
            array(
                'title'         => 'Subcsription pack shows wrong validaty date [Subscription Module]',
                'type'          => 'Fix',
                'description'   => 'Subscription pack [for unlimited number of days] expiration date shows invalid date from vendor\'s dashboard'
            ),
            array(
                'title'         => 'Store support widget is not working on single product page [Store Support Module]',
                'type'          => 'Fix',
                'description'   => 'Store support widget is not working on single product page'
            ),
            array(
                'title'         => 'Email verification wasn\'t working',
                'type'          => 'Fix',
                'description'   => 'Registration process through email verification doesn\'t verify vendors properly'
            )
        )
    ),

    array(
        'version'  => 'Version 2.7.4',
        'released' => '2018-02-12',
        'changes' => array(
            array(
                'title'       => 'Shipping rendering problem',
                'type'        => 'Fix',
                'description' => 'Shipping calculation is not showing on cart and checkout page if dokan shipping is disabled.'
            ),
            array(
                'title'       => 'Vendor verification and store support widgets are not showing on widget area',
                'type'        => 'Fix',
                'description' => 'Because of plugin loading sequesnce, the vendor verification and store support widget are not showing in Theme widget area.'
            ),
            array(
                'title'       => 'Vendor verifcation permission issue',
                'type'        => 'Fix',
                'description' => 'Due to wrong permission checking in vendor verification settings page and error is always showing in Vendor settings dashboard'
            ),
            array(
                'title'       => 'Compatibility Testing for WooCommerce 3.3+',
                'type'        => 'Improvement',
                'description' => 'Dokan pro are now compatible with WooCommerce 3.3+'
            )
        )
    ),

    array(
        'version'  => 'Version 2.7.3',
        'released' => '2018-02-04',
        'changes' => array(
            array(
                    'title'       => 'Email Verification on registration',
                    'type'        => 'New',
                    'description' => 'Now you can simply enable Email Verification from your Dokan Settings panel to restrict Registration to valid users only.'
                ),
            array(
                'title'       => 'Vendor Staff Management Module',
                'type'        => 'New',
                'description' => 'Dokan Vendor Staff Manager Module is a new introduction to Dokan Premium Modules Family. This module gives the sellers of your multivendor marketplace a unique opportunity to appoint an unlimited number of personnel or staffs to manage their individual stores. <a href="https://wedevs.com/docs/dokan/modules/dokan-vendor-staff-manager/">More Details and Documentation</a>
                                  Vendor Staff Manager is available in Dokan Business and Enterprise Packages. <br><iframe width="560" height="315" src="https://www.youtube.com/embed/z4yinwCxabI" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>'
            ),
            array(
                'title'       => 'Appearance   :  Color scheme not working on product edit page',
                'type'        => 'Fix',
                'description' => 'Fixed the color scheme customizer module not working properly on product edit page.'
            ),
            array(
                'title'       => 'Booking      :  Person type unlink or remove not working',
                'type'        => 'Fix',
                'description' => 'Fixed person type is not removable from booking type products'
            ),
            array(
                'title'       => 'Various other Bug fixes and Improvements',
                'type'        => 'Improvement',
                'description' => 'Many other improvements were made to improve the UX and Performance optimization. See the Change log for more details.'
            ),
        )
    ),
//    array(
//        'version'  => 'Version 2.7.2',
//        'released' => '2017-12-24',
//        'changes' => array(
//            array(
//                'title'       => 'Attributes not being saved properly in product edit section',
//                'type'        => 'Fix',
//                'description' => 'Vendor\'s product edit section under dashboard wasn\'t saving attributes properly , so variations were not created properly. This issue is resolved now with proper saving of attributes.'
//            ),
//            array(
//                'title'       => 'Dokan Import Export Module Importer showing blank page',
//                'type'        => 'Fix',
//                'description' => 'Import page for Dokan Import Export module was showing a blank page due to internal error. This issue is now resolved'
//            ),
//        )
//    ),
    //    array(
    //        'version'  => 'Version 2.7.1',
    //        'released' => '2017-12-13',
    //        'changes' => array(
    //            array(
    //                'title'       => 'Enable/Disable Withdraw menu for the payment modules',
    //                'type'        => 'New',
    //                'description' => 'Now admins using the modules Dokan Stripe and Dokan PayPal Adaptive have the option to display or hide the Withdraw menu on vendors dashboard.'
    //            ),
    //            array(
    //                'title'       => 'Help links added on Social API settings page',
    //                'type'        => 'New',
    //                'description' => 'For ease of configuration and creation, we have added links to our detailed documentation on social login & registration under Dokan Social API settings.'
    //            ),
    //            array(
    //                'title'       => 'Introducing a new module: “Single Product Multiple Vendor” [For Professional, Business & Enterprise Packages]',
    //                'type'        => 'New',
    //                'description' => 'With this module, vendors will be able to duplicate and sell another vendor\'s product without a hassle. Thus, one product can be sold by multiple vendors.
    //                <img src="https://wedevs.com/wp-content/uploads/2017/12/dokan-single-product-multivendor.gif" alt="Single Product Multiple Vendor Module">'
    //            ),
    //            array(
    //                'title'       => 'Verification widget for single product page. [For Professional, Business & Enterprise Packages]',
    //                'type'        => 'New',
    //                'description' => 'Previously, the verification widget was only available for the store page. Now it\'s available for both store and single product pages.'
    //            ),
    //            array(
    //                'title'       => 'New email template introduced for Auction-able products',
    //                'type'        => 'New',
    //                'description' => 'Admins can now customize a built-in email template and receive automatic email notifications every time a vendor adds a new auction type product'
    //            ),
    //
    //            array(
    //                'title'       => 'Product attribute saving issue',
    //                'type'        => 'Fix',
    //                'description' => 'When a pointing or comma terms need to add as a attribute value, the values are not saving properperly'
    //            ),
    //
    //        )
    //    ),
    // array(
    //     'version'  => 'Version 2.7.0',
    //     'released' => '2017-11-23',
    //     'changes' => array(
    //         array(
    //             'title'       => 'Introducing All New modules and packaging System',
    //             'type'        => 'New',
    //             'description' => 'Say bye bye to previous add-ons, which were very difficult to manage. From our new update, we are going to transform all our add-ons into modules. Guess what, you will be able to manage all of them from a single place. So, we have added a new menu called ‘Modules’ and removed the old ‘Add-ons’ menu. This is how the new page looks like.
    //             <img src="https://wedevs.com/wp-content/uploads/2017/11/Dokan-new-module-activation-deactivation.gif" alt="Dokan Module">'
    //         ),
    //         array(
    //             'title'       => 'Automatic Updates for Modules',
    //             'type'        => 'New',
    //             'description' => 'Previously, you didn’t get a live update for any of the Dokan add-ons. Now, you can manage them from a single place as well as get live updates directly with Dokan plugin. So, no more manual updates! You don’t have to download each add-ons and install them separately every time you get an update.'
    //         ),
    //         array(
    //             'title'       => 'Interactive Settings Page to Manage it All',
    //             'type'        => 'New',
    //             'description' => 'Dokan now has better and improved settings page where you can easily configure everything for your Dokan Multivendor.
    //             <img src="https://wedevs-com-wedevs.netdna-ssl.com/wp-content/uploads/2017/11/dokan-new-settings-page.png" alt="Dokan Settings">'
    //         ),
    //         array(
    //             'title'       => 'Shipping options showing for product edit while dokan shipping is disabled',
    //             'type'        => 'Fix',
    //             'description' => 'Shipping option all time showing in edit product page due to disable dokan shipping option. This problem fixed if dokan main shipping is disabled then shipping option are not showing in edit product page'
    //         ),

    //     )
    // )
);

function _dokan_changelog_content( $content ) {
    $content = wpautop( $content, true );

    return $content;
}
?>

<div class="wrap dokan-whats-new">
    <h1>What's New in Dokan?</h1>

    <div class="wedevs-changelog-wrapper">

        <?php foreach ( $changelog as $release ) { ?>
            <div class="wedevs-changelog">
                <div class="wedevs-changelog-version">
                    <h3><?php echo esc_html( $release['version'] ); ?></h3>
                    <p class="released">
                        (<?php echo human_time_diff( time(), strtotime( $release['released'] ) ); ?> ago)
                    </p>
                </div>
                <div class="wedevs-changelog-history">
                    <ul>
                        <?php foreach ( $release['changes'] as $change ) { ?>
                            <li>
                                <h4>
                                    <span class="title"><?php echo esc_html( $change['title'] ); ?></span>
                                    <span class="label <?php echo strtolower( $change['type'] ); ?>"><?php echo esc_html( $change['type'] ); ?></span>
                                </h4>

                                <div class="description">
                                    <?php echo _dokan_changelog_content( $change['description'] ); ?>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>

</div>
<?php
    $versions = get_option( 'dokan_whats_new_versions', array() );

    if ( ! in_array( DOKAN_PRO_PLUGIN_VERSION, $versions ) ) {
        $versions[] = DOKAN_PRO_PLUGIN_VERSION;
    }

    update_option( 'dokan_whats_new_versions', $versions );
?>
<style type="text/css">

.error, .udpated, .info, .notice {
    display: none;
}

.dokan-whats-new h1 {
    text-align: center;
    margin-top: 20px;
    font-size: 30px;
}

.wedevs-changelog {
    display: flex;
    max-width: 920px;
    border: 1px solid #e5e5e5;
    padding: 12px 20px 20px 20px;
    margin: 20px auto;
    background: #fff;
    box-shadow: 0 1px 1px rgba(0,0,0,0.04);
}

.wedevs-changelog-wrapper .wedevs-support-help {

}

.wedevs-changelog .wedevs-changelog-version {
    width: 360px;
}

.wedevs-changelog .wedevs-changelog-version .released {
    font-style: italic;
}

.wedevs-changelog .wedevs-changelog-history {
    width: 100%;
    font-size: 14px;
}

.wedevs-changelog .wedevs-changelog-history li {
    margin-bottom: 30px;
}

.wedevs-changelog .wedevs-changelog-history h4 {
    margin: 0 0 10px 0;
    font-size: 1.3em;
    line-height: 26px;
}

.wedevs-changelog .wedevs-changelog-history p {
    font-size: 14px;
    line-height: 1.5;
}

.wedevs-changelog .wedevs-changelog-history img,
.wedevs-changelog .wedevs-changelog-history iframe {
    margin-top: 30px;
    max-width: 100%;
}

.wedevs-changelog-history span.label {
    margin-left: 10px;
    position: relative;
    color: #fff;
    border-radius: 20px;
    padding: 0 8px;
    font-size: 12px;
    height: 20px;
    line-height: 19px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    font-weight: normal;
}

span.label.new {
    background: #3778ff;
    border: 1px solid #3778ff;
}

span.label.improvement {
    background: #3aaa55;
    border: 1px solid #3aaa
}

span.label.fix {
    background: #ff4772;
    border: 1px solid #ff4772;
}

</style>
