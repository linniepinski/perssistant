<?php
/**
 * include file in core
*/

require_once dirname( __FILE__ ).'/fields/container.php';
require_once dirname( __FILE__ ).'/fields/container-users.php';
require_once dirname( __FILE__ ).'/fields/container-payments.php';
require_once dirname( __FILE__ ).'/fields/groups.php';
require_once dirname( __FILE__ ).'/fields/list.php';
require_once dirname( __FILE__ ).'/fields/cat.php';
require_once dirname( __FILE__ ).'/fields/sections.php';

require_once dirname( __FILE__ ).'/fields/field-text.php';
require_once dirname( __FILE__ ).'/fields/field-select.php';
require_once dirname( __FILE__ ).'/fields/field-multi-select.php';
require_once dirname( __FILE__ ).'/fields/field-image-option.php';
require_once dirname( __FILE__ ).'/fields/field-textarea.php';
require_once dirname( __FILE__ ).'/fields/field-editor.php';
require_once dirname( __FILE__ ).'/fields/field-switch.php';
require_once dirname( __FILE__ ).'/fields/field-image.php';
require_once dirname( __FILE__ ).'/fields/field-color.php';
require_once dirname( __FILE__ ).'/fields/field-language-list.php';
require_once dirname( __FILE__ ).'/fields/field-translate.php';
require_once dirname( __FILE__ ).'/fields/field-desc.php';
require_once dirname( __FILE__ ).'/fields/field-map.php';
require_once dirname( __FILE__ ).'/fields/field-button.php';


require_once dirname( __FILE__ ).'/class-ae-base.php';
require_once dirname( __FILE__ ).'/class-ae-page.php';
require_once dirname( __FILE__ ).'/class-ae-overview.php';
require_once dirname( __FILE__ ).'/import/parsers.php';
require_once dirname( __FILE__ ).'/import/class-ae-importer.php';
require_once dirname( __FILE__ ).'/class-ae-wizard.php';
require_once dirname( __FILE__ ).'/class-mobile-detect.php';
require_once dirname( __FILE__ ).'/class-options.php';
require_once dirname( __FILE__ ).'/class-ae-users.php';
require_once dirname( __FILE__ ).'/class-ae-post.php';
require_once dirname( __FILE__ ).'/class-ae-comments.php';
require_once dirname( __FILE__ ).'/class-ae-pack.php';
// schedule hook to archive expired post
require_once dirname( __FILE__ ).'/class-ae-schedule.php';
// abstract class for auto update
require_once dirname( __FILE__ ).'/class-plugin-updater.php';
// control mailing system
require_once dirname( __FILE__ ).'/class-ae-mailing.php';

// class control language
require_once dirname( __FILE__ ).'/class-ae-languages.php';
require_once dirname( __FILE__ ).'/category.php';

//payment system
require_once dirname( __FILE__ ).'/payment/payment-visitor.php';
require_once dirname( __FILE__ ).'/payment/visitor-factory.php';
require_once dirname( __FILE__ ).'/payment/visitor-free.php';
require_once dirname( __FILE__ ).'/payment/visitor-use-package.php';

//WC inegrate
require_once dirname(__FILE__) . '/wc_integration/class-wc-integrate.php';

// class control payment
require_once dirname( __FILE__ ).'/payments.php';
require_once dirname( __FILE__ ).'/class-ae-payments.php';
require_once dirname( __FILE__ ).'/class-ae-package.php';
require_once dirname( __FILE__ ).'/class-ae-orders.php';

require_once dirname( __FILE__ ).'/class-ae-mapstyle.php';

require_once dirname( __FILE__ ).'/framework.php';
require_once dirname( __FILE__ ).'/functions.php';
require_once dirname( __FILE__ ).'/template.php';
require_once dirname( __FILE__ ).'/social/index.php';
require_once dirname( __FILE__ ).'/update.php';
