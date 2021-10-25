<?php
/*
Plugin Name: Green Popups (formerly Layered Popups)
Plugin URI: https://greenpopups.com/
Description: The most advanced popup builder for WordPress.
Version: 7.24
Author: Halfdata, Inc.
Author URI: https://codecanyon.net/user/halfdata?ref=halfdata
*/
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
define('LEPOPUP_RECORDS_PER_PAGE', '50');
define('LEPOPUP_VERSION', 7.24);
define('LEPOPUP_WEBFONTS_VERSION', 4);
define('LEPOPUP_EXPORT_VERSION', '0002');
define('LEPOPUP_LIBRARY_URL', 'https://greenpopups.com/library/');
define('LEPOPUP_UPLOADS_DIR', 'green-popups');
define('LEPOPUP_RECORD_STATUS_NONE', 0);
define('LEPOPUP_RECORD_STATUS_UNCONFIRMED', 1);
define('LEPOPUP_RECORD_STATUS_CONFIRMED', 2);
define('LEPOPUP_RECORD_STATUS_UNPAID', 3);
define('LEPOPUP_RECORD_STATUS_PAID', 4);
define('LEPOPUP_UPLOAD_STATUS_OK', 0);
define('LEPOPUP_UPLOAD_STATUS_ERROR', 1);
define('LEPOPUP_UPLOAD_STATUS_DELETED', 2);
define('LEPOPUP_STYLE_TYPE_USER', 0);
define('LEPOPUP_STYLE_TYPE_NATIVE', 1);

include_once(dirname(__FILE__).'/update.php');

register_activation_hook(__FILE__, array("lepopup_class", "install"));
register_deactivation_hook(__FILE__, array("lepopup_class", "uninstall"));

class lepopup_class {
	var $installation_uid;
	var $version = LEPOPUP_VERSION;
	var $options;
	var $plugins_url;
	var $demo_mode = false;
	var $front_header = '';
	var $front_footer = '';
	var $options_checkboxes = array();
	var $advanced_options_checkboxes = array();
	var $google_fonts = array();
	var $element_properties_meta = array();
	var $toolbar_tools = array();
	var $validators_meta = array();
	var $autocomplete_meta = array();
	var $filters_meta = array();
	var $local_fonts = array('Arial','Bookman','Century Gothic','Comic Sans MS','Courier','Garamond','Georgia','Helvetica','Lucida Grande','Palatino','Tahoma','Times','Trebuchet MS','Verdana');
	var $fa_solid = array("ad","address-book","address-card","adjust","air-freshener","align-center","align-justify","align-left","align-right","allergies","ambulance","american-sign-language-interpreting","anchor","angle-double-down","angle-double-left","angle-double-right","angle-double-up","angle-down","angle-left","angle-right","angle-up","angry","ankh","apple-alt","archive","archway","arrow-alt-circle-down","arrow-alt-circle-left","arrow-alt-circle-right","arrow-alt-circle-up","arrow-circle-down","arrow-circle-left","arrow-circle-right","arrow-circle-up","arrow-down","arrow-left","arrow-right","arrow-up","arrows-alt","arrows-alt-h","arrows-alt-v","assistive-listening-systems","asterisk","at","atlas","atom","audio-description","award","baby","baby-carriage","backspace","backward","bacon","balance-scale","ban","band-aid","barcode","bars","baseball-ball","basketball-ball","bath","battery-empty","battery-full","battery-half","battery-quarter","battery-three-quarters","bed","beer","bell","bell-slash","bezier-curve","bible","bicycle","binoculars","biohazard","birthday-cake","blender","blender-phone","blind","blog","bold","bolt","bomb","bone","bong","book","book-dead","book-medical","book-open","book-reader","bookmark","bowling-ball","box","box-open","boxes","braille","brain","bread-slice","briefcase","briefcase-medical","broadcast-tower","broom","brush","bug","building","bullhorn","bullseye","burn","bus","bus-alt","business-time","calculator","calendar","calendar-alt","calendar-check","calendar-day","calendar-minus","calendar-plus","calendar-times","calendar-week","camera","camera-retro","campground","candy-cane","cannabis","capsules","car","car-alt","car-battery","car-crash","car-side","caret-down","caret-left","caret-right","caret-square-down","caret-square-left","caret-square-right","caret-square-up","caret-up","carrot","cart-arrow-down","cart-plus","cash-register","cat","certificate","chair","chalkboard","chalkboard-teacher","charging-station","chart-area","chart-bar","chart-line","chart-pie","check","check-circle","check-double","check-square","cheese","chess","chess-bishop","chess-board","chess-king","chess-knight","chess-pawn","chess-queen","chess-rook","chevron-circle-down","chevron-circle-left","chevron-circle-right","chevron-circle-up","chevron-down","chevron-left","chevron-right","chevron-up","child","church","circle","circle-notch","city","clinic-medical","clipboard","clipboard-check","clipboard-list","clock","clone","closed-captioning","cloud","cloud-download-alt","cloud-meatball","cloud-moon","cloud-moon-rain","cloud-rain","cloud-showers-heavy","cloud-sun","cloud-sun-rain","cloud-upload-alt","cocktail","code","code-branch","coffee","cog","cogs","coins","columns","comment","comment-alt","comment-dollar","comment-dots","comment-medical","comment-slash","comments","comments-dollar","compact-disc","compass","compress","compress-arrows-alt","concierge-bell","cookie","cookie-bite","copy","copyright","couch","credit-card","crop","crop-alt","cross","crosshairs","crow","crown","crutch","cube","cubes","cut","database","deaf","democrat","desktop","dharmachakra","diagnoses","dice","dice-d20","dice-d6","dice-five","dice-four","dice-one","dice-six","dice-three","dice-two","digital-tachograph","directions","divide","dizzy","dna","dog","dollar-sign","dolly","dolly-flatbed","donate","door-closed","door-open","dot-circle","dove","download","drafting-compass","dragon","draw-polygon","drum","drum-steelpan","drumstick-bite","dumbbell","dumpster","dumpster-fire","dungeon","edit","egg","eject","ellipsis-h","ellipsis-v","envelope","envelope-open","envelope-open-text","envelope-square","equals","eraser","ethernet","euro-sign","exchange-alt","exclamation","exclamation-circle","exclamation-triangle","expand","expand-arrows-alt","external-link-alt","external-link-square-alt","eye","eye-dropper","eye-slash","fast-backward","fast-forward","fax","feather","feather-alt","female","fighter-jet","file","file-alt","file-archive","file-audio","file-code","file-contract","file-csv","file-download","file-excel","file-export","file-image","file-import","file-invoice","file-invoice-dollar","file-medical","file-medical-alt","file-pdf","file-powerpoint","file-prescription","file-signature","file-upload","file-video","file-word","fill","fill-drip","film","filter","fingerprint","fire","fire-alt","fire-extinguisher","first-aid","fish","fist-raised","flag","flag-checkered","flag-usa","flask","flushed","folder","folder-minus","folder-open","folder-plus","font","football-ball","forward","frog","frown","frown-open","funnel-dollar","futbol","gamepad","gas-pump","gavel","gem","genderless","ghost","gift","gifts","glass-cheers","glass-martini","glass-martini-alt","glass-whiskey","glasses","globe","globe-africa","globe-americas","globe-asia","globe-europe","golf-ball","gopuram","graduation-cap","greater-than","greater-than-equal","grimace","grin","grin-alt","grin-beam","grin-beam-sweat","grin-hearts","grin-squint","grin-squint-tears","grin-stars","grin-tears","grin-tongue","grin-tongue-squint","grin-tongue-wink","grin-wink","grip-horizontal","grip-lines","grip-lines-vertical","grip-vertical","guitar","h-square","hamburger","hammer","hamsa","hand-holding","hand-holding-heart","hand-holding-usd","hand-lizard","hand-middle-finger","hand-paper","hand-peace","hand-point-down","hand-point-left","hand-point-right","hand-point-up","hand-pointer","hand-rock","hand-scissors","hand-spock","hands","hands-helping","handshake","hanukiah","hard-hat","hashtag","hat-wizard","haykal","hdd","heading","headphones","headphones-alt","headset","heart","heart-broken","heartbeat","helicopter","highlighter","hiking","hippo","history","hockey-puck","holly-berry","home","horse","horse-head","hospital","hospital-alt","hospital-symbol","hot-tub","hotdog","hotel","hourglass","hourglass-end","hourglass-half","hourglass-start","house-damage","hryvnia","i-cursor","ice-cream","icicles","id-badge","id-card","id-card-alt","igloo","image","images","inbox","indent","industry","infinity","info","info-circle","italic","jedi","joint","journal-whills","kaaba","key","keyboard","khanda","kiss","kiss-beam","kiss-wink-heart","kiwi-bird","landmark","language","laptop","laptop-code","laptop-medical","laugh","laugh-beam","laugh-squint","laugh-wink","layer-group","leaf","lemon","less-than","less-than-equal","level-down-alt","level-up-alt","life-ring","lightbulb","link","lira-sign","list","list-alt","list-ol","list-ul","location-arrow","lock","lock-open","long-arrow-alt-down","long-arrow-alt-left","long-arrow-alt-right","long-arrow-alt-up","low-vision","luggage-cart","magic","magnet","mail-bulk","male","map","map-marked","map-marked-alt","map-marker","map-marker-alt","map-pin","map-signs","marker","mars","mars-double","mars-stroke","mars-stroke-h","mars-stroke-v","mask","medal","medkit","meh","meh-blank","meh-rolling-eyes","memory","menorah","mercury","meteor","microchip","microphone","microphone-alt","microphone-alt-slash","microphone-slash","microscope","minus","minus-circle","minus-square","mitten","mobile","mobile-alt","money-bill","money-bill-alt","money-bill-wave","money-bill-wave-alt","money-check","money-check-alt","monument","moon","mortar-pestle","mosque","motorcycle","mountain","mouse-pointer","mug-hot","music","network-wired","neuter","newspaper","not-equal","notes-medical","object-group","object-ungroup","oil-can","om","otter","outdent","pager","paint-brush","paint-roller","palette","pallet","paper-plane","paperclip","parachute-box","paragraph","parking","passport","pastafarianism","paste","pause","pause-circle","paw","peace","pen","pen-alt","pen-fancy","pen-nib","pen-square","pencil-alt","pencil-ruler","people-carry","pepper-hot","percent","percentage","person-booth","phone","phone-slash","phone-square","phone-volume","piggy-bank","pills","pizza-slice","place-of-worship","plane","plane-arrival","plane-departure","play","play-circle","plug","plus","plus-circle","plus-square","podcast","poll","poll-h","poo","poo-storm","poop","portrait","pound-sign","power-off","pray","praying-hands","prescription","prescription-bottle","prescription-bottle-alt","print","procedures","project-diagram","puzzle-piece","qrcode","question","question-circle","quidditch","quote-left","quote-right","quran","radiation","radiation-alt","rainbow","random","receipt","recycle","redo","redo-alt","registered","reply","reply-all","republican","restroom","retweet","ribbon","ring","road","robot","rocket","route","rss","rss-square","ruble-sign","ruler","ruler-combined","ruler-horizontal","ruler-vertical","running","rupee-sign","sad-cry","sad-tear","satellite","satellite-dish","save","school","screwdriver","scroll","sd-card","search","search-dollar","search-location","search-minus","search-plus","seedling","server","shapes","share","share-alt","share-alt-square","share-square","shekel-sign","shield-alt","ship","shipping-fast","shoe-prints","shopping-bag","shopping-basket","shopping-cart","shower","shuttle-van","sign","sign-in-alt","sign-language","sign-out-alt","signal","signature","sim-card","sitemap","skating","skiing","skiing-nordic","skull","skull-crossbones","slash","sleigh","sliders-h","smile","smile-beam","smile-wink","smog","smoking","smoking-ban","sms","snowboarding","snowflake","snowman","snowplow","socks","solar-panel","sort","sort-alpha-down","sort-alpha-up","sort-amount-down","sort-amount-up","sort-down","sort-numeric-down","sort-numeric-up","sort-up","spa","space-shuttle","spider","spinner","splotch","spray-can","square","square-full","square-root-alt","stamp","star","star-and-crescent","star-half","star-half-alt","star-of-david","star-of-life","step-backward","step-forward","stethoscope","sticky-note","stop","stop-circle","stopwatch","store","store-alt","stream","street-view","strikethrough","stroopwafel","subscript","subway","suitcase","suitcase-rolling","sun","superscript","surprise","swatchbook","swimmer","swimming-pool","synagogue","sync","sync-alt","syringe","table","table-tennis","tablet","tablet-alt","tablets","tachometer-alt","tag","tags","tape","tasks","taxi","teeth","teeth-open","temperature-high","temperature-low","tenge","terminal","text-height","text-width","th","th-large","th-list","theater-masks","thermometer","thermometer-empty","thermometer-full","thermometer-half","thermometer-quarter","thermometer-three-quarters","thumbs-down","thumbs-up","thumbtack","ticket-alt","times","times-circle","tint","tint-slash","tired","toggle-off","toggle-on","toilet","toilet-paper","toolbox","tools","tooth","torah","torii-gate","tractor","trademark","traffic-light","train","tram","transgender","transgender-alt","trash","trash-alt","trash-restore","trash-restore-alt","tree","trophy","truck","truck-loading","truck-monster","truck-moving","truck-pickup","tshirt","tty","tv","umbrella","umbrella-beach","underline","undo","undo-alt","universal-access","university","unlink","unlock","unlock-alt","upload","user","user-alt","user-alt-slash","user-astronaut","user-check","user-circle","user-clock","user-cog","user-edit","user-friends","user-graduate","user-injured","user-lock","user-md","user-minus","user-ninja","user-nurse","user-plus","user-secret","user-shield","user-slash","user-tag","user-tie","user-times","users","users-cog","utensil-spoon","utensils","vector-square","venus","venus-double","venus-mars","vial","vials","video","video-slash","vihara","volleyball-ball","volume-down","volume-mute","volume-off","volume-up","vote-yea","vr-cardboard","walking","wallet","warehouse","water","weight","weight-hanging","wheelchair","wifi","wind","window-close","window-maximize","window-minimize","window-restore","wine-bottle","wine-glass","wine-glass-alt","won-sign","wrench","x-ray","yen-sign","yin-yang");
	var $fa_regular = array("address-book","address-card","angry","arrow-alt-circle-down","arrow-alt-circle-left","arrow-alt-circle-right","arrow-alt-circle-up","bell","bell-slash","bookmark","building","calendar","calendar-alt","calendar-check","calendar-minus","calendar-plus","calendar-times","caret-square-down","caret-square-left","caret-square-right","caret-square-up","chart-bar","check-circle","check-square","circle","clipboard","clock","clone","closed-captioning","comment","comment-alt","comment-dots","comments","compass","copy","copyright","credit-card","dizzy","dot-circle","edit","envelope","envelope-open","eye","eye-slash","file","file-alt","file-archive","file-audio","file-code","file-excel","file-image","file-pdf","file-powerpoint","file-video","file-word","flag","flushed","folder","folder-open","frown","frown-open","futbol","gem","grimace","grin","grin-alt","grin-beam","grin-beam-sweat","grin-hearts","grin-squint","grin-squint-tears","grin-stars","grin-tears","grin-tongue","grin-tongue-squint","grin-tongue-wink","grin-wink","hand-lizard","hand-paper","hand-peace","hand-point-down","hand-point-left","hand-point-right","hand-point-up","hand-pointer","hand-rock","hand-scissors","hand-spock","handshake","hdd","heart","hospital","hourglass","id-badge","id-card","image","images","keyboard","kiss","kiss-beam","kiss-wink-heart","laugh","laugh-beam","laugh-squint","laugh-wink","lemon","life-ring","lightbulb","list-alt","map","meh","meh-blank","meh-rolling-eyes","minus-square","money-bill-alt","moon","newspaper","object-group","object-ungroup","paper-plane","pause-circle","play-circle","plus-square","question-circle","registered","sad-cry","sad-tear","save","share-square","smile","smile-beam","smile-wink","snowflake","square","star","star-half","sticky-note","stop-circle","sun","surprise","thumbs-down","thumbs-up","times-circle","tired","trash-alt","user","user-circle","window-close","window-maximize","window-minimize","window-restore");
	var $fa_brands = array("500px","accessible-icon","accusoft","acquisitions-incorporated","adn","adobe","adversal","affiliatetheme","algolia","alipay","amazon","amazon-pay","amilia","android","angellist","angrycreative","angular","app-store","app-store-ios","apper","apple","apple-pay","artstation","asymmetrik","atlassian","audible","autoprefixer","avianex","aviato","aws","bandcamp","behance","behance-square","bimobject","bitbucket","bitcoin","bity","black-tie","blackberry","blogger","blogger-b","bluetooth","bluetooth-b","btc","buromobelexperte","canadian-maple-leaf","cc-amazon-pay","cc-amex","cc-apple-pay","cc-diners-club","cc-discover","cc-jcb","cc-mastercard","cc-paypal","cc-stripe","cc-visa","centercode","centos","chrome","cloudscale","cloudsmith","cloudversify","codepen","codiepie","confluence","connectdevelop","contao","cpanel","creative-commons","creative-commons-by","creative-commons-nc","creative-commons-nc-eu","creative-commons-nc-jp","creative-commons-nd","creative-commons-pd","creative-commons-pd-alt","creative-commons-remix","creative-commons-sa","creative-commons-sampling","creative-commons-sampling-plus","creative-commons-share","creative-commons-zero","critical-role","css3","css3-alt","cuttlefish","d-and-d","d-and-d-beyond","dashcube","delicious","deploydog","deskpro","dev","deviantart","dhl","diaspora","digg","digital-ocean","discord","discourse","dochub","docker","draft2digital","dribbble","dribbble-square","dropbox","drupal","dyalog","earlybirds","ebay","edge","elementor","ello","ember","empire","envira","erlang","ethereum","etsy","expeditedssl","facebook","facebook-f","facebook-messenger","facebook-square","fantasy-flight-games","fedex","fedora","figma","firefox","first-order","first-order-alt","firstdraft","flickr","flipboard","fly","font-awesome","font-awesome-alt","font-awesome-flag","fonticons","fonticons-fi","fort-awesome","fort-awesome-alt","forumbee","foursquare","free-code-camp","freebsd","fulcrum","galactic-republic","galactic-senate","get-pocket","gg","gg-circle","git","git-square","github","github-alt","github-square","gitkraken","gitlab","gitter","glide","glide-g","gofore","goodreads","goodreads-g","google","google-drive","google-play","google-plus","google-plus-g","google-plus-square","google-wallet","gratipay","grav","gripfire","grunt","gulp","hacker-news","hacker-news-square","hackerrank","hips","hire-a-helper","hooli","hornbill","hotjar","houzz","html5","hubspot","imdb","instagram","intercom","internet-explorer","invision","ioxhost","itunes","itunes-note","java","jedi-order","jenkins","jira","joget","joomla","js","js-square","jsfiddle","kaggle","keybase","keycdn","kickstarter","kickstarter-k","korvue","laravel","lastfm","lastfm-square","leanpub","less","line","linkedin","linkedin-in","linode","linux","lyft","magento","mailchimp","mandalorian","markdown","mastodon","maxcdn","medapps","medium","medium-m","medrt","meetup","megaport","mendeley","microsoft","mix","mixcloud","mizuni","modx","monero","napster","neos","nimblr","nintendo-switch","node","node-js","npm","ns8","nutritionix","odnoklassniki","odnoklassniki-square","old-republic","opencart","openid","opera","optin-monster","osi","page4","pagelines","palfed","patreon","paypal","penny-arcade","periscope","phabricator","phoenix-framework","phoenix-squadron","php","pied-piper","pied-piper-alt","pied-piper-hat","pied-piper-pp","pinterest","pinterest-p","pinterest-square","playstation","product-hunt","pushed","python","qq","quinscape","quora","r-project","raspberry-pi","ravelry","react","reacteurope","readme","rebel","red-river","reddit","reddit-alien","reddit-square","redhat","renren","replyd","researchgate","resolving","rev","rocketchat","rockrms","safari","sass","schlix","scribd","searchengin","sellcast","sellsy","servicestack","shirtsinbulk","shopware","simplybuilt","sistrix","sith","sketch","skyatlas","skype","slack","slack-hash","slideshare","snapchat","snapchat-ghost","snapchat-square","soundcloud","sourcetree","speakap","spotify","squarespace","stack-exchange","stack-overflow","staylinked","steam","steam-square","steam-symbol","sticker-mule","strava","stripe","stripe-s","studiovinari","stumbleupon","stumbleupon-circle","superpowers","supple","suse","teamspeak","telegram","telegram-plane","tencent-weibo","the-red-yeti","themeco","themeisle","think-peaks","trade-federation","trello","tripadvisor","tumblr","tumblr-square","twitch","twitter","twitter-square","typo3","uber","ubuntu","uikit","uniregistry","untappd","ups","usb","usps","ussunnah","vaadin","viacoin","viadeo","viadeo-square","viber","vimeo","vimeo-square","vimeo-v","vine","vk","vnv","vuejs","weebly","weibo","weixin","whatsapp","whatsapp-square","whmcs","wikipedia-w","windows","wix","wizards-of-the-coast","wolf-pack-battalion","wordpress","wordpress-simple","wpbeginner","wpexplorer","wpforms","wpressr","xbox","xing","xing-square","y-combinator","yahoo","yandex","yandex-international","yarn","yelp","yoast","youtube","youtube-square","zhihu");
	var $font_awesome_basic = array("star", "star-o", "check", "close", "lock", "picture-o", "upload", "download", "calendar", "clock-o", "chevron-left", "chevron-right", "phone", "envelope", "envelope-o", "pencil", "angle-double-left", "angle-double-right", "spinner", "smile-o", "frown-o", "meh-o", "send", "send-o", "user", "user-o", "building-o");
	var $sort_methods = array('date-za', 'date-az', 'name-za', 'name-az');
	var $advanced_options = array(
		'enable-custom-js' => 'off',
		'enable-htmlform' => 'off',
		'enable-post' => 'off',
		'enable-mysql' => 'off',
		'enable-wpuser' => 'off',
		'enable-acellemail' => 'off',
		'enable-activecampaign' => 'off',
		'enable-activetrail' => 'off',
		'enable-agilecrm' => 'off',
		'enable-automizy' => 'off',
		'enable-avangemail' => 'off',
		'enable-authorizenet' => 'off',
		'enable-aweber' => 'off',
		'enable-birdsend' => 'off',
		'enable-bitrix24' => 'off',
		'enable-campaignmonitor' => 'off',
		'enable-cleverreach' => 'off',
		'enable-constantcontact' => 'off',
		'enable-conversio' => 'off',
		'enable-convertkit' => 'off',
		'enable-drip' => 'off',
		'enable-egoi' => 'off',
		'enable-emailoctopus' => 'off',
		'enable-fluentcrm' => 'off',
		'enable-freshmail' => 'off',
		'enable-getresponse' => 'off',
		'enable-gist' => 'off',
		'enable-groundhogg' => 'off',
		'enable-hubspot' => 'off',
		'enable-inbox' => 'off',
		'enable-infomaniak' => 'off',
		'enable-intercom' => 'off',
		'enable-jetpack' => 'off',
		'enable-klaviyo' => 'off',
		'enable-madmimi' => 'off',
		'enable-mailautic' => 'off',
		'enable-mailchimp' => 'off',
		'enable-mailerlite' => 'off',
		'enable-mailfit' => 'off',
		'enable-mailgun' => 'off',
		'enable-mailjet' => 'off',
		'enable-mailpoet' => 'off',
		'enable-mailrelay' => 'off',
		'enable-mailster' => 'off',
		'enable-mailwizz' => 'off',
		'enable-mautic' => 'off',
		'enable-moosend' => 'off',
		'enable-mumara' => 'off',
		'enable-newsman' => 'off',
		'enable-omnisend' => 'off',
		'enable-ontraport' => 'off',
		'enable-pipedrive' => 'off',
		'enable-rapidmail' => 'off',
		'enable-salesflare' => 'off',
		'enable-salesautopilot' => 'off',
		'enable-sendfox' => 'off',
		'enable-sendgrid' => 'off',
		'enable-sendinblue' => 'off',
		'enable-sendpulse' => 'off',
		'enable-sendy' => 'off',
		'enable-sgautorepondeur' => 'off',
		'enable-socketlabs' => 'off',
		'enable-thenewsletterplugin' => 'off',
		'enable-tribulant' => 'off',
		'enable-verticalresponse' => 'off',
		'enable-ymlp' => 'off',
		'enable-zapier' => 'off',
		'enable-zohocrm' => 'off',
		'enable-blockchain' => 'off',
		'enable-instamojo' => 'off',
		'enable-interkassa' => 'off',
		'enable-mollie' => 'off',
		'enable-payfast' => 'off',
		'enable-paypal' => 'off',
		'enable-paystack' => 'off',
		'enable-payumoney' => 'off',
		'enable-perfectmoney' => 'off',
		'enable-razorpay' => 'off',
		'enable-skrill' => 'off',
		'enable-stripe' => 'off',
		'enable-wepay' => 'off',
		'enable-yandexmoney' => 'off',
		'enable-bulkgate' => 'off',
		'enable-gatewayapi' => 'off',
		'enable-nexmo' => 'off',
		'enable-twilio' => 'off',
		'enable-clearout' => 'off',
		'enable-emaillistvalidation' => 'off',
		'enable-emaillistverify' => 'off',
		'enable-kickbox' => 'off',
		'enable-thechecker' => 'off',
		'enable-truemail' => 'off',
		'enable-geoipdetect' => 'off',
		'enable-ipstack' => 'off',
		'minified-sources' => 'off',
		'admin-menu-stats' => 'on',
		'admin-menu-analytics' => 'on',
		'admin-menu-transactions' => 'on',
		'important-enable' => 'off',
		'async-init' => 'on',
		'enable-php-session' => 'on'
	);
	var $animation_effects_in = array(
		'bounceIn' => 'Bounce',
		'bounceInUp' => 'Bounce Up',
		'bounceInDown' => 'Bounce Down',
		'bounceInLeft' => 'Bounce Left',
		'bounceInRight' => 'Bounce Right',
		'fadeIn' => 'Fade',
		'fadeInUp' => 'Fade Up',
		'fadeInDown' => 'Fade Down',
		'fadeInLeft' => 'Fade Left',
		'fadeInRight' => 'Fade Right',
		'flipInX' => 'Flip X',
		'flipInY' => 'Flip Y',
		'lightSpeedIn' => 'Light Speed',
		'rotateIn' => 'Rotate',
		'rotateInDownLeft' => 'Rotate Down Left',
		'rotateInDownRight' => 'Rotate Down Right',
		'rotateInUpLeft' => 'Rotate Up Left',
		'rotateInUpRight' => 'Rotate Up Right',
		'rollIn' => 'Roll',
		'zoomIn' => 'Zoom',
		'zoomInUp' => 'Zoom Up',
		'zoomInDown' => 'Zoom Down',
		'zoomInLeft' => 'Zoom Left',
		'zoomInRight' => 'Zoom Right'
	);
	var $animation_effects_out = array(
		'bounceOut' => 'Bounce',
		'bounceOutUp' => 'Bounce Up',
		'bounceOutDown' => 'Bounce Down',
		'bounceOutLeft' => 'Bounce Left',
		'bounceOutRight' => 'Bounce Right',
		'fadeOut' => 'Fade',
		'fadeOutUp' => 'Fade Up',
		'fadeOutDown' => 'Fade Down',
		'fadeOutLeft' => 'Fade Left',
		'fadeOutRight' => 'Fade Right',
		'flipOutX' => 'Flip X',
		'flipOutY' => 'Flip Y',
		'lightSpeedOut' => 'Light Speed',
		'rotateOut' => 'Rotate',
		'rotateOutDownLeft' => 'Rotate Down Left',
		'rotateOutDownRight' => 'Rotate Down Right',
		'rotateOutUpLeft' => 'Rotate Up Left',
		'rotateOutUpRight' => 'Rotate Up Right',
		'rollOut' => 'Roll',
		'zoomOut' => 'Zoom',
		'zoomOutUp' => 'Zoom Up',
		'zoomOutDown' => 'Zoom Down',
		'zoomOutLeft' => 'Zoom Left',
		'zoomOutRight' => 'Zoom Right'
	);
	var $font_weights = array(
		'inherit' => 'Inherit',
		'100' => 'Thin',
		'200' => 'Extra-light',
		'300' => 'Light',
		'400' => 'Normal',
		'500' => 'Medium',
		'600' => 'Demi-bold',
		'700' => 'Bold',
		'800' => 'Heavy',
		'900' => 'Black'
	);

	var $geoip_services, $email_validators, $file_autodelete_options;
	var $gmt_offset = 0;
	function __construct() {
		global $lepopup_admin;
		if (function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain('lepopup', false, dirname(plugin_basename(__FILE__)).'/languages/');
		}
		$this->plugins_url = plugins_url('', __FILE__);
		
		$url = get_bloginfo('url');
		$domain = parse_url($url, PHP_URL_HOST);
		$this->gmt_offset = get_option('gmt_offset', 0);
		$this->options = array(
			"from-name" => get_bloginfo('name'),
			"from-email" => "noreply@".str_replace("www.", "", $domain),
			"fa-enable" => "off",
			"fa-solid-enable" => "on",
			"fa-regular-enable" => "off",
			"fa-brands-enable" => "off",
			"fa-css-disable" => "off",
			"ga-tracking" => "off",
			"mask-enable" => "off",
			"mask-js-disable" => "off",
			"airdatepicker-enable" => "on",
			"airdatepicker-js-disable" => "off",
			"jsep-enable" => "off",
			"jsep-js-disable" => "off",
			"signature-enable" => "off",
			"signature-js-disable" => "off",
			"range-slider-enable" => "off",
			"range-slider-js-disable" => "off",
			"adblock-detector-enable" => "off",
			"custom-fonts" => array(),
			"purchase-code" => "",
			"csv-separator" => ",",
			"email-validator" => "basic",
			"geoip-service" => "none",
			"file-autodelete" => "none",
			"cookie-value" => 'ilovefamily',
			"preload" => 'off',
			"preload-event-popups" => 'off',
			"sort-forms" => 'date-za',
			"sort-campaigns" => 'date-za',
			"sort-log" => 'date-za',
			"gettingstarted-enable" => 'on'
		);
		$this->advanced_options = array_merge($this->advanced_options, array(
			'label-form-values' => esc_html__('Form Values', 'lepopup'),
			'label-payment' => esc_html__('Payment', 'lepopup'),
			'label-general-info' => esc_html__('General Info', 'lepopup'),
			'label-raw-details' => esc_html__('Raw Details', 'lepopup'),
			'label-technical-info' => esc_html__('Technical Info', 'lepopup'))
		);
		
		foreach($this->options as $key => $value) {
			if ($value == 'on' || $value == 'off') $this->options_checkboxes[] = $key;
		}
		foreach($this->advanced_options as $key => $value) {
			if ($value == 'on' || $value == 'off') $this->advanced_options_checkboxes[] = $key;
		}
		$this->installation_uid = $this->random_string(9);
		$this->email_validators = array(
			'basic' => esc_html__('Basic (check syntax)', 'lepopup'),
			'advanced' => esc_html__('Advanced (check syntax and MX-record of domain)', 'lepopup')
		);
		$this->geoip_services = array(
			'none' => esc_html__('None', 'lepopup')
		);
		$this->file_autodelete_options = array(
			'0' => esc_html__('Do not delete files', 'lepopup'),
			'7' => esc_html__('Delete after one week (7 days)', 'lepopup'),
			'14' => esc_html__('Delete after two weeks (14 days)', 'lepopup'),
			'30' => esc_html__('Delete after one month (30 days)', 'lepopup'),
			'90' => esc_html__('Delete after three months (90 days)', 'lepopup'),
			'365' => esc_html__('Delete after one year (365 days)', 'lepopup')
		);

		$this->get_options();
		$this->get_advanced_options();

		$this->local_fonts = array_merge($this->local_fonts, (array)$this->options['custom-fonts']);

		$autocomplete_options = array(
			'off' => esc_html__('None', 'lepopup'),
			'name' => esc_html__('Full Name', 'lepopup').' (name)',
			'given-name' => esc_html__('First Name', 'lepopup').' (given-name)',
			'additional-name' => esc_html__('Middle Name', 'lepopup').' (additional-name)',
			'family-name' => esc_html__('Last Name', 'lepopup').' (family-name)',
			'email' => esc_html__('Email', 'lepopup').' (email)',
			'tel' => esc_html__('Phone', 'lepopup').' (tel)',
			'street-address' => esc_html__('Single Address Line', 'lepopup').' (street-address)',
			'address-line1' => esc_html__('Address Line 1', 'lepopup').' (address-line1)',
			'address-line2' => esc_html__('Address Line 2', 'lepopup').' (address-line2)',
			'address-level1' => esc_html__('State or Province', 'lepopup').' (address-level1)',
			'address-level2' => esc_html__('City', 'lepopup').' (address-level2)',
			'postal-code' => esc_html__('ZIP Code', 'lepopup').' (postal-code)',
			'country' => esc_html__('Country', 'lepopup').' (country)',
			'cc-name' => esc_html__('Name on Card', 'lepopup').' (cc-name)',
			'cc-number' => esc_html__('Card Number', 'lepopup').' (cc-number)',
			'cc-csc' => esc_html__('CVC', 'lepopup').' (cc-csc)',
			'cc-exp-month' => esc_html__('Expiry (month)', 'lepopup').' (cc-exp-month)',
			'cc-exp-year' => esc_html__('Expiry (year)', 'lepopup').' (cc-exp-year)',
			'cc-exp' => esc_html__('Expiry', 'lepopup').' (cc-exp)',
			'cc-type' => esc_html__('Card Type', 'lepopup').' (cc-type)'
		);
		$this->element_properties_meta = array(
			'settings' => array(
				'general-tab' => array('type' => 'tab', 'value' => 'general', 'label' => esc_html__('General', 'lepopup')),
					'name' => array('value' => esc_html__('Untitled', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name helps to identify the popup.', 'lepopup'), 'type' => 'text'),
					'active' => array('value' => 'on', 'label' => esc_html__('Active', 'lepopup'), 'tooltip' => esc_html__('Inactive forms will not appear on the site.', 'lepopup'), 'type' => 'checkbox'),
					'key-fields' => array('value' => array('primary' => '', 'secondary' => ''), 'caption' => array('primary' => esc_html__('Primary field', 'lepopup'), 'secondary' => esc_html__('Secondary field', 'lepopup')), 'placeholder' => array('primary' => esc_html__('Select primary field', 'lepopup'), 'secondary' => esc_html__('Select secondary field', 'lepopup')), 'label' => esc_html__('Key fields', 'lepopup'), 'tooltip' => esc_html__('The values of these fields are displayed on Log page in relevant columns.', 'lepopup'), 'type' => 'key-fields'),
					'datetime-args' => array('value' => array('date-format' => 'yyyy-mm-dd', 'time-format' => 'hh:ii', 'locale' => 'en'), 'label' => esc_html__('Date and time parameters', 'lepopup'), 'tooltip' => esc_html__('Choose the date and time formats and language for datetimepicker. It is used for "date" and "time" fields.', 'lepopup'), 'type' => 'datetime-args', 'date-format-options' => array('yyyy-mm-dd' => 'YYYY-MM-DD', 'mm/dd/yyyy' => 'MM/DD/YYYY', 'dd/mm/yyyy' => 'DD/MM/YYYY', 'dd.mm.yyyy' => 'DD.MM.YYYY'), 'date-format-label' => esc_html__('Date format', 'lepopup'), 'time-format-options' => array('hh:ii aa' => '12 hours', 'hh:ii' => '24 hours'), 'time-format-label' => esc_html__('Time format', 'lepopup'), 'locale-options' => array('en', 'cs', 'da', 'de', 'es', 'fi', 'fr', 'hu', 'nl', 'pl', 'pt', 'ro', 'ru', 'sk', 'tr', 'zh'), 'locale-label' => esc_html__('Language', 'lepopup')),
					'cross-domain' => array('value' => 'off', 'label' => esc_html__('Cross-domain calls', 'lepopup'), 'tooltip' => esc_html__('Enable this option if you want to use cross-domain embedding, i.e. plugin installed on domain1, and form is used on domain2. Due to security reasons this feature is automatically disabled if the popup has Signature field.', 'lepopup'), 'type' => 'checkbox'),
					'session-enable' => array('value' => 'off', 'label' => esc_html__('Enable sessions', 'lepopup'), 'tooltip' => esc_html__('Activate this option if you want to enable sessions for the popup. Session allows to keep non-completed form data, so user can continue form filling when come back.', 'lepopup'), 'type' => 'checkbox'),
					'session-length' => array('value' => '48', 'label' => esc_html__('Session length', 'lepopup'), 'tooltip' => esc_html__('Specify how many hours non-completed data are kept.', 'lepopup'), 'unit' => 'hrs', 'type' => 'units', 'visible' => array('session-enable' => array('on'))),
					'cookie-lifetime' => array('value' => '365', 'label' => esc_html__('Cookie lifetime', 'lepopup'), 'tooltip' => esc_html__('When form submission is successful, the cookie is set to avoid further appearance. This is cookie lifetime in days.', 'lepopup'), 'unit' => 'days', 'type' => 'units'),
					'esc-enable' => array('value' => 'on', 'label' => esc_html__('Close when clicking ESC', 'lepopup'), 'tooltip' => esc_html__('Activate this option if you want to close the popup by pressing ESC-button.', 'lepopup'), 'type' => 'checkbox'),
					'enter-enable' => array('value' => 'on', 'label' => esc_html__('Submit when clicking ENTER', 'lepopup'), 'tooltip' => esc_html__('Activate this option if you want to submit the popup by pressing ENTER/RETURN-button.', 'lepopup'), 'type' => 'checkbox'),
				'style-tab' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-sections' => array('type' => 'sections', 'sections' => array(
						'global' => array('label' => esc_html__('Global', 'lepopup'), 'icon' => 'fas fa-globe'),
						'inputs' => array('label' => esc_html__('Inputs', 'lepopup'), 'icon' => 'fas fa-pencil-alt'),
						'buttons' => array('label' => esc_html__('Buttons', 'lepopup'), 'icon' => 'far fa-paper-plane'),
						'errors' => array('label' => esc_html__('Errors', 'lepopup'), 'icon' => 'far fa-hand-paper'),
						'progress' => array('label' => esc_html__('Progress Bar', 'lepopup'), 'icon' => 'fas fa-sliders-h')
					)),
					'start-global' => array('type' => 'section-start', 'section' => 'global'),
						'position' => array('value' => 'middle-center', 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Select popup position on browser window.', 'lepopup'), 'type' => 'popup-position', 'group' => 'style'),
						'overlay-enable' => array('value' => 'on', 'label' => esc_html__('Enable overlay', 'lepopup'), 'tooltip' => esc_html__('Activate this option if you want to enable overlay.', 'lepopup'), 'type' => 'checkbox'),
						'overlay-animation' => array('value' => 'fadeIn', 'label' => esc_html__('Overlay animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the overlay animation effect.', 'lepopup'), 'type' => 'overlay-animation', 'visible' => array('overlay-enable' => array('on')), 'group' => 'style'),
						'overlay-color' => array('value' => 'rgba(0,0,0,0.7)', 'label' => esc_html__('Overlay color', 'lepopup'), 'tooltip' => esc_html__('Adjust the overlay color.', 'lepopup'), 'type' => 'color', 'visible' => array('overlay-enable' => array('on')), 'group' => 'style'),
						'overlay-click' => array('value' => 'on', 'label' => esc_html__('Active overlay', 'lepopup'), 'tooltip' => esc_html__('If enabled, the popup will be closed when user click overlay.', 'lepopup'), 'type' => 'checkbox', 'visible' => array('overlay-enable' => array('on'))),
						'spinner-color' => array('value' => array('color1' => '#FFFFFF', 'color2' => '#FFFFFF', 'color3' => '#FFFFFF'), 'label' => esc_html__('Spinner colors', 'lepopup'), 'tooltip' => esc_html__('Adjust the color of the spinner.', 'lepopup'), 'caption' => array('color1' => esc_html__('Small circle', 'lepopup'), 'color2' => esc_html__('Middle circle', 'lepopup'), 'color3' => esc_html__('Large circle', 'lepopup')), 'type' => 'three-colors', 'group' => 'style'),
						'hr-1' => array('type' => 'hr'),
						'text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#444', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'left'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Text style', 'lepopup'), 'tooltip' => esc_html__('Adjust the text style.', 'lepopup'), 'type' => 'text-style', 'group' => 'style'),
					'end-global' => array('type' => 'section-end'),
					'start-inputs' => array('type' => 'section-start', 'section' => 'inputs'),
						'input-icon' => array('value' => array('position' => 'inside', 'size' => '20', 'color' => '#444', 'background' => '', 'border' => ''), 'caption' => array('position' => esc_html__('Position', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'background' => esc_html__('Background', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Icon style', 'lepopup'), 'tooltip' => esc_html__('Adjust the style of input field icons.', 'lepopup'), 'type' => 'icon-style', 'group' => 'style'),
						'input-style-sections' => array('type' => 'sections', 'sections' => array(
							'inputs-default' => array('label' => esc_html__('Default', 'lepopup'), 'icon' => 'fas fa-globe', 'group' => 'style'),
							'inputs-hover' => array('label' => esc_html__('Hover', 'lepopup'), 'icon' => 'far fa-hand-pointer', 'group' => 'style'),
							'inputs-focus' => array('label' => esc_html__('Focus', 'lepopup'), 'icon' => 'fas fa-i-cursor', 'group' => 'style')
						)),
						'start-inputs-default' => array('type' => 'section-start', 'section' => 'inputs-default'),
							'input-text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#444', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'left'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Input text', 'lepopup'), 'tooltip' => esc_html__('Adjust the text style of input fields.', 'lepopup'), 'type' => 'text-style', 'group' => 'style'),
							'input-background-style' => array('value' => array('image' => '', 'size' => 'auto', 'horizontal-position' => 'left', 'vertical-position' => 'top', 'repeat' => 'repeat', 'color' => '#fff', 'color2' => '', 'gradient' => 'no'), 'caption' => array('image' => esc_html__('Image URL', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'horizontal-position' => esc_html__('Horizontal position', 'lepopup'), 'vertical-position' => esc_html__('Verical position', 'lepopup'), 'repeat' => esc_html__('Repeat', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'color2' => esc_html__('Second color', 'lepopup'), 'gradient' => esc_html__('Gradient', 'lepopup')), 'label' => esc_html__('Input background', 'lepopup'), 'tooltip' => esc_html__('Adjust the background of input fields.', 'lepopup'), 'type' => 'background-style', 'group' => 'style'),
							'input-border-style' => array('value' => array('width' => '1', 'style' => 'solid', 'radius' => '0', 'color' => '#ccc', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Input border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border style of input fields.', 'lepopup'), 'type' => 'border-style', 'group' => 'style'),
							'input-shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Input shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow of input fields.', 'lepopup'), 'type' => 'shadow', 'group' => 'style'),
						'end-inputs-default' => array('type' => 'section-end'),
						'start-inputs-hover' => array('type' => 'section-start', 'section' => 'inputs-hover'),
							'input-hover-inherit' => array('value' => 'on', 'label' => esc_html__('Inherit default style', 'lepopup'), 'tooltip' => esc_html__('Use the same style as for default state.', 'lepopup'), 'type' => 'checkbox', 'group' => 'style'),
							'input-hover-text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#444', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'left'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Input text', 'lepopup'), 'tooltip' => esc_html__('Adjust the text style of hovered input fields.', 'lepopup'), 'type' => 'text-style', 'visible' => array('input-hover-inherit' => array('off')), 'group' => 'style'),
							'input-hover-background-style' => array('value' => array('image' => '', 'size' => 'auto', 'horizontal-position' => 'left', 'vertical-position' => 'top', 'repeat' => 'repeat', 'color' => '#fff', 'color2' => '', 'gradient' => 'no'), 'caption' => array('image' => esc_html__('Image URL', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'horizontal-position' => esc_html__('Horizontal position', 'lepopup'), 'vertical-position' => esc_html__('Verical position', 'lepopup'), 'repeat' => esc_html__('Repeat', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'color2' => esc_html__('Second color', 'lepopup'), 'gradient' => esc_html__('Gradient', 'lepopup')), 'label' => esc_html__('Input background', 'lepopup'), 'tooltip' => esc_html__('Adjust the background of hovered input fields.', 'lepopup'), 'type' => 'background-style', 'visible' => array('input-hover-inherit' => array('off')), 'group' => 'style'),
							'input-hover-border-style' => array('value' => array('width' => '1', 'style' => 'solid', 'radius' => '0', 'color' => '#ccc', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Input border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border style of hovered input fields.', 'lepopup'), 'type' => 'border-style', 'visible' => array('input-hover-inherit' => array('off')), 'group' => 'style'),
							'input-hover-shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Input shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow of hovered input fields.', 'lepopup'), 'type' => 'shadow', 'visible' => array('input-hover-inherit' => array('off')), 'group' => 'style'),
						'end-inputs-hover' => array('type' => 'section-end'),
						'start-inputs-focus' => array('type' => 'section-start', 'section' => 'inputs-focus'),
							'input-focus-inherit' => array('value' => 'on', 'label' => esc_html__('Inherit default style', 'lepopup'), 'tooltip' => esc_html__('Use the same style as for default state.', 'lepopup'), 'type' => 'checkbox', 'group' => 'style'),
							'input-focus-text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#444', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'left'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Input text', 'lepopup'), 'tooltip' => esc_html__('Adjust the text style of focused input fields.', 'lepopup'), 'type' => 'text-style', 'visible' => array('input-focus-inherit' => array('off')), 'group' => 'style'),
							'input-focus-background-style' => array('value' => array('image' => '', 'size' => 'auto', 'horizontal-position' => 'left', 'vertical-position' => 'top', 'repeat' => 'repeat', 'color' => '#fff', 'color2' => '', 'gradient' => 'no'), 'caption' => array('image' => esc_html__('Image URL', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'horizontal-position' => esc_html__('Horizontal position', 'lepopup'), 'vertical-position' => esc_html__('Verical position', 'lepopup'), 'repeat' => esc_html__('Repeat', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'color2' => esc_html__('Second color', 'lepopup'), 'gradient' => esc_html__('Gradient', 'lepopup')), 'label' => esc_html__('Input background', 'lepopup'), 'tooltip' => esc_html__('Adjust the background of focused input fields.', 'lepopup'), 'type' => 'background-style', 'visible' => array('input-focus-inherit' => array('off')), 'group' => 'style'),
							'input-focus-border-style' => array('value' => array('width' => '1', 'style' => 'solid', 'radius' => '0', 'color' => '#ccc', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Input border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border style of focused input fields.', 'lepopup'), 'type' => 'border-style', 'visible' => array('input-focus-inherit' => array('off')), 'group' => 'style'),
							'input-focus-shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Input shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow of focused input fields.', 'lepopup'), 'type' => 'shadow', 'visible' => array('input-focus-inherit' => array('off')), 'group' => 'style'),
						'end-inputs-focus' => array('type' => 'section-end'),
						'hr-5' => array('type' => 'hr'),
						'checkbox-radio-style' => array('value' => array('position' => 'left', 'size' => 'medium', 'align' => 'left', 'layout' => '1'), 'caption' => array('position' => esc_html__('Position', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup'), 'layout' => esc_html__('Layout', 'lepopup')), 'label' => esc_html__('Checkbox and radio style', 'lepopup'), 'tooltip' => esc_html__('Choose how to display checkbox and radio button fields and their captions.', 'lepopup'), 'type' => 'checkbox-radio-style', 'group' => 'style'),
						'checkbox-view' => array('value' => 'classic', 'options' => array('classic', 'fa-check', 'square', 'tgl'), 'label' => esc_html__('Checkbox view', 'lepopup'), 'tooltip' => esc_html__('Choose the checkbox style.', 'lepopup'), 'type' => 'checkbox-view', 'group' => 'style'),
						'radio-view' => array('value' => 'classic', 'options' => array('classic', 'fa-check', 'dot'), 'label' => esc_html__('Radio button view', 'lepopup'), 'tooltip' => esc_html__('Choose the radio button style.', 'lepopup'), 'type' => 'radio-view', 'group' => 'style'),
						'checkbox-radio-sections' => array('type' => 'sections', 'sections' => array(
							'checkbox-radio-unchecked' => array('label' => esc_html__('Unchecked', 'lepopup'), 'icon' => 'far fa-square'),
							'checkbox-radio-checked' => array('label' => esc_html__('Checked', 'lepopup'), 'icon' => 'far fa-check-square')
						)),
						'start-checkbox-radio-unchecked' => array('type' => 'section-start', 'section' => 'checkbox-radio-unchecked'),
							'checkbox-radio-unchecked-color' => array('value' => array('color1' => '#ccc', 'color2' => '#fff', 'color3' => '#444'), 'label' => esc_html__('Checkbox and radio colors', 'lepopup'), 'tooltip' => esc_html__('Adjust colors of checkboxes and radio buttons.', 'lepopup'), 'caption' => array('color1' => 'Border', 'color2' => 'Background', 'color3' => 'Mark'), 'type' => 'three-colors', 'group' => 'style'),
						'end-checkbox-radio-unchecked' => array('type' => 'section-end'),
						'start-checkbox-radio-checked' => array('type' => 'section-start', 'section' => 'checkbox-radio-checked'),
							'checkbox-radio-checked-inherit' => array('value' => 'on', 'label' => esc_html__('Inherit colors', 'lepopup'), 'tooltip' => esc_html__('Use the same colors as for unchecked state.', 'lepopup'), 'type' => 'checkbox', 'group' => 'style'),
							'checkbox-radio-checked-color' => array('value' => array('color1' => '#ccc', 'color2' => '#fff', 'color3' => '#444'), 'label' => esc_html__('Checkbox and radio colors', 'lepopup'), 'tooltip' => esc_html__('Adjust colors of checkboxes and radio buttons.', 'lepopup'), 'caption' => array('color1' => 'Border', 'color2' => 'Background', 'color3' => 'Mark'), 'type' => 'three-colors', 'visible' => array('checkbox-radio-checked-inherit' => array('off')), 'group' => 'style'),
						'end-checkbox-radio-checked' => array('type' => 'section-end'),
						'hr-6' => array('type' => 'hr'),
						'imageselect-style' => array('value' => array('align' =>'left', 'effect' => 'none'), 'caption' => array('align' => esc_html__('Alignment', 'lepopup'), 'effect' => esc_html__('Effect', 'lepopup')), 'label' => esc_html__('Image Select style', 'lepopup'), 'tooltip' => esc_html__('Adjust image alignment and effect.', 'lepopup'), 'type' => 'imageselect-style', 'options' => array('none' => esc_html__('None', 'lepopup'), 'grayscale' => esc_html__('Grayscale', 'lepopup')), 'group' => 'style'),
						'imageselect-text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#444', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'left'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Image label text', 'lepopup'), 'tooltip' => esc_html__('Adjust the text style of image label.', 'lepopup'), 'type' => 'text-style', 'group' => 'style'),
						'imageselects-style-sections' => array('type' => 'sections', 'sections' => array(
							'imageselects-default' => array('label' => esc_html__('Default', 'lepopup'), 'icon' => 'fas fa-globe'),
							'imageselects-hover' => array('label' => esc_html__('Hover', 'lepopup'), 'icon' => 'far fa-hand-pointer'),
							'imageselects-selected' => array('label' => esc_html__('Selected', 'lepopup'), 'icon' => 'far fa-check-square')
						)),
						'start-imageselects-default' => array('type' => 'section-start', 'section' => 'imageselects-default'),
							'imageselect-border-style' => array('value' => array('width' => '1', 'style' => 'solid', 'radius' => '0', 'color' => '#ccc', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Image border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border style of images.', 'lepopup'), 'type' => 'border-style', 'group' => 'style'),
							'imageselect-shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Image shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow of images.', 'lepopup'), 'type' => 'shadow', 'group' => 'style'),
						'end-imageselects-default' => array('type' => 'section-end'),
						'start-imageselects-hover' => array('type' => 'section-start', 'section' => 'imageselects-hover'),
							'imageselect-hover-inherit' => array('value' => 'on', 'label' => esc_html__('Inherit default style', 'lepopup'), 'tooltip' => esc_html__('Use the same style as for default state.', 'lepopup'), 'type' => 'checkbox', 'group' => 'style'),
							'imageselect-hover-border-style' => array('value' => array('width' => '1', 'style' => 'solid', 'radius' => '0', 'color' => '#ccc', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Image border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border style of hovered images.', 'lepopup'), 'type' => 'border-style', 'visible' => array('imageselect-hover-inherit' => array('off')), 'group' => 'style'),
							'imageselect-hover-shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Image shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow of hovered images.', 'lepopup'), 'type' => 'shadow', 'visible' => array('imageselect-hover-inherit' => array('off')), 'group' => 'style'),
						'end-imageselects-hover' => array('type' => 'section-end'),
						'start-imageselects-selected' => array('type' => 'section-start', 'section' => 'imageselects-selected'),
							'imageselect-selected-inherit' => array('value' => 'on', 'label' => esc_html__('Inherit default style', 'lepopup'), 'tooltip' => esc_html__('Use the same style as for default state.', 'lepopup'), 'type' => 'checkbox', 'group' => 'style'),
							'imageselect-selected-border-style' => array('value' => array('width' => '1', 'style' => 'solid', 'radius' => '0', 'color' => '#ccc', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Image border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border style of selected images.', 'lepopup'), 'type' => 'border-style', 'visible' => array('imageselect-selected-inherit' => array('off')), 'group' => 'style'),
							'imageselect-selected-shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Image shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow of selected images.', 'lepopup'), 'type' => 'shadow', 'visible' => array('imageselect-selected-inherit' => array('off')), 'group' => 'style'),
							'imageselect-selected-scale' => array('value' => 'on', 'label' => esc_html__('Zoom selected image', 'lepopup'), 'tooltip' => esc_html__('Zoom selected image.', 'lepopup'), 'type' => 'checkbox', 'group' => 'style'),
						'end-imageselects-selected' => array('type' => 'section-end'),
						'hr-7' => array('type' => 'hr'),
						'multiselect-style' => array('value' => array('align' => 'left', 'height' => '120', 'hover-background' => '#bd4070', 'hover-color' => '#ffffff', 'selected-background' => '#a93a65', 'selected-color' => '#ffffff'), 'caption' => array('align' => esc_html__('Alignment', 'lepopup'), 'height' => esc_html__('Height', 'lepopup'), 'hover-color' => esc_html__('Hover colors', 'lepopup'), 'selected-color' => esc_html__('Selected colors', 'lepopup')), 'label' => esc_html__('Multiselect style', 'lepopup'), 'tooltip' => esc_html__('Choose how to display multiselect options.', 'lepopup'), 'type' => 'multiselect-style', 'group' => 'style'),
						'hr-8' => array('type' => 'hr'),
						'tile-style' => array('value' => array('size' => 'medium', 'width' => 'default', 'position' => 'left', 'layout' => 'inline'), 'caption' => array('size' => esc_html__('Size', 'lepopup'), 'width' => esc_html__('Width', 'lepopup'), 'position' => esc_html__('Position', 'lepopup'), 'layout' => esc_html__('Layout', 'lepopup')), 'label' => esc_html__('Tile style', 'lepopup'), 'tooltip' => esc_html__('Adjust the tile style.', 'lepopup'), 'type' => 'global-tile-style', 'group' => 'style'),
						'tile-style-sections' => array('type' => 'sections', 'sections' => array(
							'tiles-default' => array('label' => esc_html__('Default', 'lepopup'), 'icon' => 'fas fa-globe'),
							'tiles-hover' => array('label' => esc_html__('Hover', 'lepopup'), 'icon' => 'far fa-hand-pointer'),
							'tiles-active' => array('label' => esc_html__('Selected', 'lepopup'), 'icon' => 'far fa-check-square')
						)),
						'start-tiles-default' => array('type' => 'section-start', 'section' => 'tiles-default'),
							'tile-text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#444', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'center'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Tile text', 'lepopup'), 'tooltip' => esc_html__('Adjust the text style of tiles.', 'lepopup'), 'type' => 'text-style', 'group' => 'style'),
							'tile-background-style' => array('value' => array('image' => '', 'size' => 'auto', 'horizontal-position' => 'left', 'vertical-position' => 'top', 'repeat' => 'repeat', 'color' => '#ffffff', 'color2' => '', 'gradient' => 'no'), 'caption' => array('image' => esc_html__('Image URL', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'horizontal-position' => esc_html__('Horizontal position', 'lepopup'), 'vertical-position' => esc_html__('Vertical position', 'lepopup'), 'repeat' => esc_html__('Repeat', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'color2' => esc_html__('Second color', 'lepopup'), 'gradient' => esc_html__('Gradient', 'lepopup')), 'label' => esc_html__('Tile background', 'lepopup'), 'tooltip' => esc_html__('Adjust the background of tiles.', 'lepopup'), 'type' => 'background-style', 'group' => 'style'),
							'tile-border-style' => array('value' => array('width' => '1', 'style' => 'solid', 'radius' => '0', 'color' => '#ccc', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Tile border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border style of tiles.', 'lepopup'), 'type' => 'border-style', 'group' => 'style'),
							'tile-shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Tile shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow of tile.', 'lepopup'), 'type' => 'shadow', 'group' => 'style'),
						'end-tiles-default' => array('type' => 'section-end'),
						'start-tiles-hover' => array('type' => 'section-start', 'section' => 'tiles-hover'),
							'tile-hover-inherit' => array('value' => 'on', 'label' => esc_html__('Inherit default style', 'lepopup'), 'tooltip' => esc_html__('Use the same style as for default state.', 'lepopup'), 'type' => 'checkbox', 'group' => 'style'),
							'tile-hover-text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#444', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'center'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Tile text', 'lepopup'), 'tooltip' => esc_html__('Adjust the text style of hovered tiles.', 'lepopup'), 'type' => 'text-style', 'visible' => array('tile-hover-inherit' => array('off')), 'group' => 'style'),
							'tile-hover-background-style' => array('value' => array('image' => '', 'size' => 'auto', 'horizontal-position' => 'left', 'vertical-position' => 'top', 'repeat' => 'repeat', 'color' => '#ffffff', 'color2' => '', 'gradient' => 'no'), 'caption' => array('image' => esc_html__('Image URL', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'horizontal-position' => esc_html__('Horizontal position', 'lepopup'), 'vertical-position' => esc_html__('Verical position', 'lepopup'), 'repeat' => esc_html__('Repeat', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'color2' => esc_html__('Second color', 'lepopup'), 'gradient' => esc_html__('Gradient', 'lepopup')), 'label' => esc_html__('Tile background', 'lepopup'), 'tooltip' => esc_html__('Adjust the background of hovered tiles.', 'lepopup'), 'type' => 'background-style', 'visible' => array('tile-hover-inherit' => array('off')), 'group' => 'style'),
							'tile-hover-border-style' => array('value' => array('width' => '1', 'style' => 'solid', 'radius' => '0', 'color' => '#a93a65', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Tile border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border style of hovered tiles.', 'lepopup'), 'type' => 'border-style', 'visible' => array('tile-hover-inherit' => array('off')), 'group' => 'style'),
							'tile-hover-shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Tile shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow of hovered tiles.', 'lepopup'), 'type' => 'shadow', 'visible' => array('tile-hover-inherit' => array('off')), 'group' => 'style'),
						'end-tiles-hover' => array('type' => 'section-end'),
						'start-tiles-active' => array('type' => 'section-start', 'section' => 'tiles-active'),
							'tile-selected-inherit' => array('value' => 'on', 'label' => esc_html__('Inherit default style', 'lepopup'), 'tooltip' => esc_html__('Use the same style as for default state.', 'lepopup'), 'type' => 'checkbox', 'group' => 'style'),
							'tile-selected-text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#444', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'center'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Tile text', 'lepopup'), 'tooltip' => esc_html__('Adjust the text style of selected tiles.', 'lepopup'), 'type' => 'text-style', 'visible' => array('tile-selected-inherit' => array('off')), 'group' => 'style'),
							'tile-selected-background-style' => array('value' => array('image' => '', 'size' => 'auto', 'horizontal-position' => 'left', 'vertical-position' => 'top', 'repeat' => 'repeat', 'color' => '#ffffff', 'color2' => '', 'gradient' => 'no'), 'caption' => array('image' => esc_html__('Image URL', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'horizontal-position' => esc_html__('Horizontal position', 'lepopup'), 'vertical-position' => esc_html__('Verical position', 'lepopup'), 'repeat' => esc_html__('Repeat', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'color2' => esc_html__('Second color', 'lepopup'), 'gradient' => esc_html__('Gradient', 'lepopup')), 'label' => esc_html__('Tile background', 'lepopup'), 'tooltip' => esc_html__('Adjust the background of selected tiles.', 'lepopup'), 'type' => 'background-style', 'visible' => array('tile-selected-inherit' => array('off')), 'group' => 'style'),
							'tile-selected-border-style' => array('value' => array('width' => '1', 'style' => 'solid', 'radius' => '0', 'color' => '#a93a65', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Tile border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border style of selected tiles.', 'lepopup'), 'type' => 'border-style', 'visible' => array('tile-selected-inherit' => array('off')), 'group' => 'style'),
							'tile-selected-shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Tile shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow of selected tiles.', 'lepopup'), 'type' => 'shadow', 'visible' => array('tile-selected-inherit' => array('off')), 'group' => 'style'),
							'tile-selected-transform' => array('value' => 'zoom-in', 'label' => esc_html__('Transform', 'lepopup'), 'tooltip' => esc_html__('Adjust the transform of selected tiles.', 'lepopup'), 'type' => 'radio-bar', 'options' => array('none' => esc_html__('None', 'lepopup'), 'zoom-in' => esc_html__('Zoom In', 'lepopup'), 'zoom-out' => esc_html__('Zoom Out', 'lepopup'), 'shift-down' => esc_html__('Shift Down', 'lepopup')), 'group' => 'style'),
						'end-tiles-active' => array('type' => 'section-end'),
						'hr-10' => array('type' => 'hr'),
						'rangeslider-skin' => array('value' => 'flat', 'label' => esc_html__('Range slider skin', 'lepopup'), 'tooltip' => esc_html__('Select the skin of range slider.', 'lepopup'), 'type' => 'select', 'options' => array('flat' => esc_html__('Flat', 'lepopup'), 'sharp' => esc_html__('Sharp', 'lepopup'), 'round' => esc_html__('Round', 'lepopup')), 'group' => 'style'),
						'rangeslider-color' => array('value' => array('color1' => '#e8e8e8', 'color2' => '#888888', 'color3' => '#bd4070', 'color4' => '#a93a65', 'color5' => '#ffffff'), 'label' => esc_html__('Range slider colors', 'lepopup'), 'tooltip' => esc_html__('Adjust colors of range slider.', 'lepopup'), 'caption' => array('color1' => 'Main', 'color2' => 'Min/max text', 'color3' => 'Selected', 'color4' => 'Handle', 'color5' => 'Tooltip text'), 'type' => 'five-colors', 'group' => 'style'),
					'end-inputs' => array('type' => 'section-end'),
					'start-buttons' => array('type' => 'section-start', 'section' => 'buttons'),
						'button-style-sections' => array('type' => 'sections', 'sections' => array(
							'buttons-default' => array('label' => esc_html__('Default', 'lepopup'), 'icon' => 'fas fa-globe'),
							'buttons-hover' => array('label' => esc_html__('Hover', 'lepopup'), 'icon' => 'far fa-hand-pointer'),
							'buttons-active' => array('label' => esc_html__('Active', 'lepopup'), 'icon' => 'far fa-paper-plane')
						)),
						'start-buttons-default' => array('type' => 'section-start', 'section' => 'buttons-default'),
							'button-text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#fff', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'center'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Button text', 'lepopup'), 'tooltip' => esc_html__('Adjust the text style of buttons.', 'lepopup'), 'type' => 'text-style', 'group' => 'style'),
							'button-background-style' => array('value' => array('image' => '', 'size' => 'auto', 'horizontal-position' => 'left', 'vertical-position' => 'top', 'repeat' => 'repeat', 'color' => '#bd4070', 'color2' => '', 'gradient' => 'no'), 'caption' => array('image' => esc_html__('Image URL', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'horizontal-position' => esc_html__('Horizontal position', 'lepopup'), 'vertical-position' => esc_html__('Verical position', 'lepopup'), 'repeat' => esc_html__('Repeat', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'color2' => esc_html__('Second color', 'lepopup'), 'gradient' => esc_html__('Gradient', 'lepopup')), 'label' => esc_html__('Button background', 'lepopup'), 'tooltip' => esc_html__('Adjust the background of buttons.', 'lepopup'), 'type' => 'background-style', 'group' => 'style'),
							'button-border-style' => array('value' => array('width' => '1', 'style' => 'solid', 'radius' => '0', 'color' => '#a93a65', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Button border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border style of buttons.', 'lepopup'), 'type' => 'border-style', 'group' => 'style'),
							'button-shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Button shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow of button.', 'lepopup'), 'type' => 'shadow', 'group' => 'style'),
						'end-buttons-default' => array('type' => 'section-end'),
						'start-buttons-hover' => array('type' => 'section-start', 'section' => 'buttons-hover'),
							'button-hover-inherit' => array('value' => 'on', 'label' => esc_html__('Inherit default style', 'lepopup'), 'tooltip' => esc_html__('Use the same style as for default state.', 'lepopup'), 'type' => 'checkbox', 'group' => 'style'),
							'button-hover-text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#fff', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'center'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Button text', 'lepopup'), 'tooltip' => esc_html__('Adjust the text style of hovered buttons.', 'lepopup'), 'type' => 'text-style', 'visible' => array('button-hover-inherit' => array('off')), 'group' => 'style'),
							'button-hover-background-style' => array('value' => array('image' => '', 'size' => 'auto', 'horizontal-position' => 'left', 'vertical-position' => 'top', 'repeat' => 'repeat', 'color' => '#a93a65', 'color2' => '', 'gradient' => 'no'), 'caption' => array('image' => esc_html__('Image URL', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'horizontal-position' => esc_html__('Horizontal position', 'lepopup'), 'vertical-position' => esc_html__('Verical position', 'lepopup'), 'repeat' => esc_html__('Repeat', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'color2' => esc_html__('Second color', 'lepopup'), 'gradient' => esc_html__('Gradient', 'lepopup')), 'label' => esc_html__('Button background', 'lepopup'), 'tooltip' => esc_html__('Adjust the background of hovered buttons.', 'lepopup'), 'type' => 'background-style', 'visible' => array('button-hover-inherit' => array('off')), 'group' => 'style'),
							'button-hover-border-style' => array('value' => array('width' => '1', 'style' => 'solid', 'radius' => '0', 'color' => '#a93a65', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Button border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border style of hovered buttons.', 'lepopup'), 'type' => 'border-style', 'visible' => array('button-hover-inherit' => array('off')), 'group' => 'style'),
							'button-hover-shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Button shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow of hovered buttons.', 'lepopup'), 'type' => 'shadow', 'visible' => array('button-hover-inherit' => array('off')), 'group' => 'style'),
						'end-buttons-hover' => array('type' => 'section-end'),
						'start-buttons-active' => array('type' => 'section-start', 'section' => 'buttons-active'),
							'button-active-inherit' => array('value' => 'on', 'label' => esc_html__('Inherit default style', 'lepopup'), 'tooltip' => esc_html__('Use the same style as for default state.', 'lepopup'), 'type' => 'checkbox', 'group' => 'style'),
							'button-active-text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#fff', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'center'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Button text', 'lepopup'), 'tooltip' => esc_html__('Adjust the text style of clicked buttons.', 'lepopup'), 'type' => 'text-style', 'visible' => array('button-active-inherit' => array('off')), 'group' => 'style'),
							'button-active-background-style' => array('value' => array('image' => '', 'size' => 'auto', 'horizontal-position' => 'left', 'vertical-position' => 'top', 'repeat' => 'repeat', 'color' => '#a93a65', 'color2' => '', 'gradient' => 'no'), 'caption' => array('image' => esc_html__('Image URL', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'horizontal-position' => esc_html__('Horizontal position', 'lepopup'), 'vertical-position' => esc_html__('Verical position', 'lepopup'), 'repeat' => esc_html__('Repeat', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'color2' => esc_html__('Second color', 'lepopup'), 'gradient' => esc_html__('Gradient', 'lepopup')), 'label' => esc_html__('Button background', 'lepopup'), 'tooltip' => esc_html__('Adjust the background of clicked buttons.', 'lepopup'), 'type' => 'background-style', 'visible' => array('button-active-inherit' => array('off')), 'group' => 'style'),
							'button-active-border-style' => array('value' => array('width' => '1', 'style' => 'solid', 'radius' => '0', 'color' => '#a93a65', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Button border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border style of clicked buttons.', 'lepopup'), 'type' => 'border-style', 'visible' => array('button-active-inherit' => array('off')), 'group' => 'style'),
							'button-active-shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Button shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow of clicked buttons.', 'lepopup'), 'type' => 'shadow', 'visible' => array('button-active-inherit' => array('off')), 'group' => 'style'),
							'button-active-transform' => array('value' => 'zoom-out', 'label' => esc_html__('Transform', 'lepopup'), 'tooltip' => esc_html__('Adjust the transform of clicked buttons.', 'lepopup'), 'type' => 'radio-bar', 'options' => array('zoom-in' => esc_html__('Zoom In', 'lepopup'), 'zoom-out' => esc_html__('Zoom Out', 'lepopup'), 'shift-down' => esc_html__('Shift Down', 'lepopup')), 'group' => 'style'),
						'end-buttons-active' => array('type' => 'section-end'),
					'end-buttons' => array('type' => 'section-end'),
					'start-errors' => array('type' => 'section-start', 'section' => 'errors'),
						'error-background-style' => array('value' => array('image' => '', 'size' => 'auto', 'horizontal-position' => 'left', 'vertical-position' => 'top', 'repeat' => 'repeat', 'color' => '#d9534f', 'color2' => '', 'gradient' => 'no'), 'caption' => array('image' => esc_html__('Image URL', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'horizontal-position' => esc_html__('Horizontal position', 'lepopup'), 'vertical-position' => esc_html__('Verical position', 'lepopup'), 'repeat' => esc_html__('Repeat', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'color2' => esc_html__('Second color', 'lepopup'), 'gradient' => esc_html__('Gradient', 'lepopup')), 'label' => esc_html__('Bubble background', 'lepopup'), 'tooltip' => esc_html__('Adjust the background of error bubbles.', 'lepopup'), 'type' => 'background-style', 'group' => 'style'),
						'error-text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#fff', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'left'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Error text style', 'lepopup'), 'tooltip' => esc_html__('Adjust the text style of errors.', 'lepopup'), 'type' => 'text-style', 'group' => 'style'),
					'end-errors' => array('type' => 'section-end'),
					'start-progress' => array('type' => 'section-start', 'section' => 'progress'),
						'progress-type' => array('value' => 'progress-1', 'label' => esc_html__('Progress style', 'lepopup'), 'tooltip' => esc_html__('Select the general view of progress bar.', 'lepopup'), 'type' => 'select-image', 'options' => array('progress-1' => $this->plugins_url.'/images/progress-1.png', 'progress-2' => $this->plugins_url.'/images/progress-2.png'), 'width' => 350, 'height' => 90, 'group' => 'style'),
						'progress-color' => array('value' => array('color1' => '#e0e0e0', 'color2' => '#bd4070', 'color3' => '#FFFFFF', 'color4' => '#444'), 'label' => esc_html__('Colors', 'lepopup'), 'tooltip' => esc_html__('Adjust colors of progress bar.', 'lepopup'), 'caption' => array('color1' => 'Passive background', 'color2' => 'Active background', 'color3' => 'Page number (or %)', 'color4' => 'Page name'), 'type' => 'four-colors', 'group' => 'style'),
						'progress-striped' => array('value' => 'off', 'label' => esc_html__('Double-tone stripes', 'lepopup'), 'tooltip' => esc_html__('Add double-tone diagonal stripes to progress bar.', 'lepopup'), 'type' => 'checkbox', 'group' => 'style'),
						'progress-label-enable' => array('value' => 'off', 'label' => esc_html__('Show page name', 'lepopup'), 'tooltip' => esc_html__('Show page label.', 'lepopup'), 'type' => 'checkbox', 'group' => 'style'),
						'progress-confirmation-enable' => array('value' => 'on', 'label' => esc_html__('Include confirmation page', 'lepopup'), 'tooltip' => esc_html__('Consider Confirmation page as part of total pages and include it into progress bar.', 'lepopup'), 'type' => 'checkbox'),
					'end-progress' => array('type' => 'section-end'),
				'confirmation-tab' => array('type' => 'tab', 'value' => 'confirmation', 'label' => esc_html__('Confirmations', 'lepopup')),
					'confirmations' => array('type' => 'confirmations', 'values' => array(), 'label' => esc_html__('Confirmations', 'lepopup'), 'message' => esc_html__('By default after successfull form submission the Confirmation Page is displayed. You can customize confirmation and use conditional logic. If several confirmations match form conditions, the first one (higher priority) will be applied. Sort confirmations (drag and drop) to set priority.', 'lepopup')),
				'double-tab' => array('type' => 'tab', 'value' => 'double', 'label' => esc_html__('Double Opt-In', 'lepopup')),
					'double-enable' => array('value' => 'off', 'label' => esc_html__('Enable', 'lepopup'), 'tooltip' => esc_html__('Activate it if you want users to confirm submitted data. If enabled, the plugin sends email message with confirmation link to certain email address (submitted by user). When confirmation link clicked, relevant record is marked as "confirmed". Moreover, if enabled, all notifications and integrations are executed only when data confirmed by user. Important! Double opt-in is disabled if user is requested to pay via existing Payment Gateway.', 'lepopup'), 'type' => 'checkbox'),
					'double-email-recipient' => array('value' => '', 'label' => esc_html__('Recipient', 'lepopup'), 'tooltip' => esc_html__('Set email address to which confirmation link will be sent to.', 'lepopup'), 'type' => 'text-shortcodes'),
					'double-email-subject' => array('value' => esc_html__('Please confirm your email address', 'lepopup'), 'label' => esc_html__('Subject', 'lepopup'), 'tooltip' => esc_html__('The subject of the email message.', 'lepopup'), 'type' => 'text-shortcodes'),
					'double-email-message' => array('value' => esc_html__('Dear visitor!', 'lepopup').PHP_EOL.PHP_EOL.esc_html__('Please confirm your email address by clicking the following link:', 'lepopup').PHP_EOL.'<a href="{{confirmation-url}}">{{confirmation-url}}</a>'.PHP_EOL.PHP_EOL.esc_html__('Thanks.', 'lepopup'), 'label' => esc_html__('Message', 'lepopup'), 'tooltip' => sprintf(esc_html__('The content of the email message. It is mandatory to include %s{{confirmation-url}}%s shortcode.', 'lepopup'), '<code>', '</code>'), 'type' => 'textarea-shortcodes'),
					'double-from' => array('value' => array('email' => '{{global-from-email}}', 'name' => '{{global-from-name}}'), 'label' => esc_html__('From', 'lepopup'), 'tooltip' => esc_html__('Sets the "From" address and name. The email address and name set here will be shown as the sender of the email.', 'lepopup'), 'type' => 'from'),
					'double-message' => array('value' => '<h4 style="text-align: center;">Thank you!</h4><p style="text-align: center;">Your email address successfully confirmed.</p>', 'label' => esc_html__('Thanksgiving message', 'lepopup'), 'tooltip' => esc_html__('This message is displayed when users successfully confirmed their e-mail addresses.', 'lepopup'), 'type' => 'textarea-shortcodes'),
					'double-url' => array('value' => '', 'label' => esc_html__('Thanksgiving URL', 'lepopup'), 'tooltip' => esc_html__('This is alternate way of thanksgiving message. After confirmation users are redirected to this URL.', 'lepopup'), 'type' => 'text'),
				'notification-tab' => array('type' => 'tab', 'value' => 'notification', 'label' => esc_html__('Notifications', 'lepopup')),
					'notifications' => array('type' => 'notifications', 'values' => array(), 'label' => esc_html__('Notifications', 'lepopup'), 'message' => esc_html__('After successful form submission the notification, welcome, thanksgiving or whatever email can be sent. You can customize these emails and use conditional logic.', 'lepopup')),
				'integration-tab' => array('type' => 'tab', 'value' => 'integration', 'label' => esc_html__('Integrations', 'lepopup')),
					'integrations' => array('type' => 'integrations', 'values' => array(), 'label' => esc_html__('Integrations', 'lepopup'), 'message' => esc_html__('After successful form submission its data can be sent to 3rd party services (such as MailChimp, AWeber, GetResponse, etc.). You can configure integrations and use conditional logic. If you do not see your marketing/CRM provider, make sure that you enabled appropriate integration module on Advanced Settings page.', 'lepopup')),
				'advanced-tab' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'advanced-sections' => array('type' => 'sections', 'sections' => array(
						'math' => array('label' => esc_html__('Math Expressions', 'lepopup'), 'icon' => 'fas fa-plus'),
						'payment-gateways' => array('label' => esc_html__('Payment Gateways', 'lepopup'), 'icon' => 'fas fa-dollar-sign'),
						'misc' => array('label' => esc_html__('Miscellaneous', 'lepopup'), 'icon' => 'fas fa-project-diagram')
					)),
					'start-math' => array('type' => 'section-start', 'section' => 'math'),
						'math-expressions' => array('type' => 'math-expressions', 'values' => array(), 'label' => esc_html__('Math expressions', 'lepopup'), 'tooltip' => esc_html__('Create math expressions and use them along the popup.', 'lepopup')),
					'end-math' => array('type' => 'section-end'),
					'start-payment-gateways' => array('type' => 'section-start', 'section' => 'payment-gateways'),
						'payment-gateways' => array('type' => 'payment-gateways', 'values' => array(), 'label' => esc_html__('Payment gateways', 'lepopup'), 'message' => esc_html__('After successful form submission user can be requested to pay some amount via certain payment gateway. Customize payment gateways here. Then go to "Confirmations" tab and create confirmation of one of the following types: "Display Confirmation page and request payment", "Display Message and request payment" or "Request payment".', 'lepopup')),
					'end-payment-gateways' => array('type' => 'section-end'),
					'start-misc' => array('type' => 'section-start', 'section' => 'misc'),
						'misc-save-ip' => array('value' => 'on', 'label' => esc_html__('Save IP-address', 'lepopup'), 'tooltip' => esc_html__('Save user\'s IP-address in local database.', 'lepopup'), 'type' => 'checkbox'),
						'misc-save-user-agent' => array('value' => 'on', 'label' => esc_html__('Save User-Agent', 'lepopup'), 'tooltip' => esc_html__('Save user\'s User-Agent in local database.', 'lepopup'), 'type' => 'checkbox'),
						'misc-email-tech-info' => array('value' => 'on', 'label' => esc_html__('Send Technical Info by email', 'lepopup'), 'tooltip' => esc_html__('Include Technical Info into "{{form-data}}" shortcode sent by email.', 'lepopup'), 'type' => 'checkbox'),
						'misc-record-tech-info' => array('value' => 'on', 'label' => esc_html__('Show Technical Info on log record details', 'lepopup'), 'tooltip' => esc_html__('Show Technical Info on log record details.', 'lepopup'), 'type' => 'checkbox'),
						'personal-keys' => array('values' => array(), 'label' => esc_html__('Personal data key fields', 'lepopup'), 'tooltip' => esc_html__('Select fields which contains personal data keys. Usually it is an email field. WordPress uses this key to extract and handle personal data.', 'lepopup'), 'type' => 'personal-keys'),
					'end-misc' => array('type' => 'section-end'),
			),
			'page' => array(
				'general' => array('type' => 'tab', 'value' => 'general', 'label' => esc_html__('General', 'lepopup')),
					'name' => array('value' => esc_html__('Page', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name helps to identify the page.', 'lepopup'), 'type' => 'text'),
					'size' => array('value' => array('width' => '720', 'height' => '540'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Basic frame size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the basic frame.', 'lepopup'), 'type' => 'width-height'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this page', 'lepopup'), 'hide' => esc_html__('Hide this page', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
			),
			'page-confirmation' => array(
				'general' => array('type' => 'tab', 'value' => 'general', 'label' => esc_html__('General', 'lepopup')),
					'name' => array('value' => esc_html__('Confirmation', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name helps to identify the confirmation page.', 'lepopup'), 'type' => 'text'),
					'size' => array('value' => array('width' => '420', 'height' => '320'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Basic frame size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the basic frame.', 'lepopup'), 'type' => 'width-height')
			),
			'email' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Email Address', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '360', 'height' => '40'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'placeholder' => array('value' => esc_html__('Please enter email address...', 'lepopup'), 'label' => esc_html__('Placeholder', 'lepopup'), 'tooltip' => esc_html__('The placeholder text will appear inside the field until the user starts to type.', 'lepopup'), 'type' => 'text'),
					'autocomplete' => array('value' => 'email', 'label' => esc_html__('Autocomplete attribute', 'lepopup'), 'tooltip' => esc_html__('Choose the value of the autocomplete attribute. It helps browser to fill the field value, if required.', 'lepopup'), 'type' => 'select', 'options' => $autocomplete_options),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of input fields through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'align' => array('value' => 'left', 'label' => esc_html__('Text alignment', 'lepopup'), 'tooltip' => esc_html__('Adjust the alignment of the textarea text.', 'lepopup'), 'type' => 'align'),
					'icon' => array('value' => array('left-icon' => ($this->options['fa-enable'] == 'on' ? ($this->options['fa-regular-enable'] == 'on' ? 'far fa-envelope' : 'fas fa-envelope') : 'lepopup-fa lepopup-fa-envelope-o'), 'left-size' => '', 'left-color' => '', 'right-icon' => '', 'right-size' => '', 'right-color' => ''), 'caption' => array('left' => esc_html__('Left side', 'lepopup'), 'right' => esc_html__('Right side', 'lepopup')), 'label' => esc_html__('Input icons', 'lepopup'), 'tooltip' => esc_html__('These icons appear inside/near of the input field.', 'lepopup'), 'type' => 'input-icons'),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the input field.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							),
							'input' => array(
								'label' => esc_html__('Input field', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input'
							),
							'input-hover' => array(
								'label' => esc_html__('Input field (hover)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input:hover',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input:hover'
							),
							'input-focus' => array(
								'label' => esc_html__('Input field (focus)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input:focus',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input:focus'
							),
							'input-icon-left' => array(
								'label' => esc_html__('Input field icon (left)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left'
							),
							'input-icon-right' => array(
								'label' => esc_html__('Input field icon (right)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'default' => array('value' => '', 'label' => esc_html__('Default value', 'lepopup'), 'tooltip' => esc_html__('The default value is the value that the field has before the user has entered anything.', 'lepopup'), 'type' => 'text'),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'readonly' => array('value' => 'off', 'label' => esc_html__('Read only', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user can not edit the field value.', 'lepopup'), 'type' => 'checkbox'),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'filters' => array('values' => array(array("type" => "trim", "properties" => null)), 'allowed-values' => array('alpha', 'alphanumeric', 'digits', 'regex', 'strip-tags', 'trim'), 'label' => esc_html__('Filters', 'lepopup'), 'tooltip' => esc_html__('Filters allow you to strip various characters from the submitted value.', 'lepopup'), 'type' => 'filters'),
					'validators' => array('values' => array(array("type" => "email", "properties" => array('error' => ''))), 'allowed-values' => array('email', 'equal', 'equal-field', 'in-array', 'prevent-duplicates'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid.', 'lepopup'), 'type' => 'validators')
			),
			'text' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Text', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '360', 'height' => '40'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'placeholder' => array('value' => esc_html__('Please enter your value...', 'lepopup'), 'label' => esc_html__('Placeholder', 'lepopup'), 'tooltip' => esc_html__('The placeholder text will appear inside the field until the user starts to type.', 'lepopup'), 'type' => 'text'),
					'autocomplete' => array('value' => 'off', 'label' => esc_html__('Autocomplete attribute', 'lepopup'), 'tooltip' => esc_html__('Choose the value of the autocomplete attribute. It helps browser to fill the field value, if required.', 'lepopup'), 'type' => 'select', 'options' => $autocomplete_options),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of input fields through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'align' => array('value' => 'left', 'label' => esc_html__('Text alignment', 'lepopup'), 'tooltip' => esc_html__('Adjust the alignment of the textarea text.', 'lepopup'), 'type' => 'align'),
					'icon' => array('value' => array('left-icon' => '', 'left-size' => '', 'left-color' => '', 'right-icon' => '', 'right-size' => '', 'right-color' => ''), 'caption' => array('left' => esc_html__('Left side', 'lepopup'), 'right' => esc_html__('Right side', 'lepopup')), 'label' => esc_html__('Input icons', 'lepopup'), 'tooltip' => esc_html__('These icons appear inside/near of the input field.', 'lepopup'), 'type' => 'input-icons'),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the input field.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							),
							'input' => array(
								'label' => esc_html__('Input field', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input'
							),
							'input-hover' => array(
								'label' => esc_html__('Input field (hover)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input:hover',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input:hover'
							),
							'input-focus' => array(
								'label' => esc_html__('Input field (focus)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input:focus',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input:focus'
							),
							'input-icon-left' => array(
								'label' => esc_html__('Input field icon (left)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left'
							),
							'input-icon-right' => array(
								'label' => esc_html__('Input field icon (right)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'mask' => array('value' => array('preset' => '', 'mask' => ''), 'label' => esc_html__('Mask', 'lepopup'), 'tooltip' => esc_html__('Adjust the mask of the input field. Use the following special symbols:', 'lepopup').'<br /><br />'.esc_html__('0 - mandatory digit', 'lepopup').'<br />'.esc_html__('9 - optional digit', 'lepopup').'<br />'.esc_html__('A - alphanumeric character', 'lepopup').'<br />'.esc_html__('S - alpha character', 'lepopup'), 'preset-options' => array('(000)000-0000' => 'Phone number with area code: (000)000-0000', '(00)0000-0000' => 'Phone number with area code: (00)0000-0000', '+0(000)000-0000' => 'International phone number: +0(000)000-0000', '+00(000)000-0000' => 'International phone number: +00(000)000-0000', '099.099.099.099' => 'IP Address: 099.099.099.099', '000-00-0000' => 'SSN: 000-00-0000', '0000 0000 0000 0000' => 'Visa/Mastercard: 0000 0000 0000 0000', '0000 000000 00000' => 'AmEx: 0000 000000 00000', 'custom' => 'Custom Mask'), 'type' => 'mask'),
					'default' => array('value' => '', 'label' => esc_html__('Default value', 'lepopup'), 'tooltip' => esc_html__('The default value is the value that the field has before the user has entered anything.', 'lepopup'), 'type' => 'text'),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'readonly' => array('value' => 'off', 'label' => esc_html__('Read only', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user can not edit the field value.', 'lepopup'), 'type' => 'checkbox'),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'filters' => array('values' => array(array("type" => "trim", "properties" => null)), 'allowed-values' => array('alpha', 'alphanumeric', 'digits', 'regex', 'strip-tags', 'trim'), 'label' => esc_html__('Filters', 'lepopup'), 'tooltip' => esc_html__('Filters allow you to strip various characters from the submitted value.', 'lepopup'), 'type' => 'filters'),
					'validators' => array('values' => array(), 'allowed-values' => array('alpha', 'alphanumeric', 'digits', 'email', 'equal', 'equal-field', 'greater', 'iban', 'in-array', 'length', 'less', 'prevent-duplicates', 'regex', 'url'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid.', 'lepopup'), 'type' => 'validators')
			),
			'textarea' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Textarea', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '360', 'height' => '120'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'placeholder' => array('value' => '', 'label' => esc_html__('Placeholder', 'lepopup'), 'tooltip' => esc_html__('The placeholder text will appear inside the field until the user starts to type.', 'lepopup'), 'type' => 'text'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'caption' => esc_html__('The field is required', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of input fields through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'align' => array('value' => 'left', 'label' => esc_html__('Text alignment', 'lepopup'), 'tooltip' => esc_html__('Adjust the alignment of the textarea text.', 'lepopup'), 'type' => 'align'),
					'icon' => array('value' => array('left-icon' => '', 'left-size' => '', 'left-color' => '', 'right-icon' => '', 'right-size' => '', 'right-color' => ''), 'caption' => array('left' => esc_html__('Left side', 'lepopup'), 'right' => esc_html__('Right side', 'lepopup')), 'label' => esc_html__('Textarea icons', 'lepopup'), 'tooltip' => esc_html__('These icons appear inside/near of the textarea field.', 'lepopup'), 'type' => 'input-icons'),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the textarea field.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							),
							'textarea' => array(
								'label' => esc_html__('Textarea', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input textarea',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input textarea'
							),
							'textarea-hover' => array(
								'label' => esc_html__('Textarea (hover)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input textarea:hover',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input textarea:hover'
							),
							'textarea-focus' => array(
								'label' => esc_html__('Textarea (focus)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input textarea:focus',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input textarea:focus'
							),
							'textarea-icon-left' => array(
								'label' => esc_html__('Textarea icon (left)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left'
							),
							'textarea-icon-right' => array(
								'label' => esc_html__('Textarea icon (right)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'default' => array('value' => '', 'label' => esc_html__('Default value', 'lepopup'), 'tooltip' => esc_html__('The default value is the value that the field has before the user has entered anything.', 'lepopup'), 'type' => 'textarea'),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'maxlength' => array('value' => '', 'label' => esc_html__('Max length', 'lepopup'), 'tooltip' => esc_html__('Specifies the maximum number of characters allowed in the text area. Leave empty or set "0" for unlimited number of characters.', 'lepopup'), 'unit' => 'chars', 'type' => 'units'),
					'readonly' => array('value' => 'off', 'label' => esc_html__('Read only', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user can not edit the field value.', 'lepopup'), 'type' => 'checkbox'),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'filters' => array('values' => array(array("type" => "trim", "properties" => null)), 'allowed-values' => array('alpha', 'alphanumeric', 'digits', 'regex', 'strip-tags', 'trim'), 'label' => esc_html__('Filters', 'lepopup'), 'tooltip' => esc_html__('Filters allow you to strip various characters from the submitted value.', 'lepopup'), 'type' => 'filters'),
					'validators' => array('values' => array(), 'allowed-values' => array('alpha', 'alphanumeric', 'digits', 'email', 'equal', 'equal-field', 'greater', 'in-array', 'length', 'less', 'prevent-duplicates', 'regex', 'url'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid.', 'lepopup'), 'type' => 'validators')
			),
			'select' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Select', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '360', 'height' => '40'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'options' => array('multi-select' => 'off', 'values' => array(array('value' => 'Option 1', 'label' => 'Option 1'), array('value' => 'Option 2', 'label' => 'Option 2'), array('value' => 'Option 3', 'label' => 'Option 3')), 'label' => esc_html__('Options', 'lepopup'), 'tooltip' => esc_html__('These are the choices that the user will be able to choose from.', 'lepopup'), 'type' => 'options'),
					'please-select-option' => array('value' => 'off', 'label' => esc_html__('"Please select" option', 'lepopup'), 'tooltip' => esc_html__('Adds an option to the top of the list to let the user choose no value.', 'lepopup'), 'type' => 'checkbox'),
					'please-select-text' => array('value' => esc_html__('Please select', 'lepopup'), 'label' => esc_html__('"Please select" text', 'lepopup'), 'type' => 'text', 'visible' => array('please-select-option' => array('on'))),
					'autocomplete' => array('value' => 'off', 'label' => esc_html__('Autocomplete attribute', 'lepopup'), 'tooltip' => esc_html__('Choose the value of the autocomplete attribute. It helps browser to fill the field value, if required.', 'lepopup'), 'type' => 'select', 'options' => $autocomplete_options),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'caption' => esc_html__('The field is required', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of input fields through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the input field.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							),
							'select' => array(
								'label' => esc_html__('Select box', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input select',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input select'
							),
							'select-hover' => array(
								'label' => esc_html__('Select box (hover)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input select:hover',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input select:hover'
							),
							'select-focus' => array(
								'label' => esc_html__('Select box (focus)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input select:focus',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input select:focus'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'validators' => array('values' => array(), 'allowed-values' => array('equal', 'equal-field', 'greater', 'in-array', 'less', 'prevent-duplicates', 'regex'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid.', 'lepopup'), 'type' => 'validators')
			),
			'checkbox' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Checkbox', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size-width' => array('value' => '400', 'label' => esc_html__('Width', 'lepopup'), 'tooltip' => esc_html__('Specify the width of the element.', 'lepopup'), 'unit' => 'px', 'type' => 'units'),
					'options' => array('multi-select' => 'on', 'values' => array(array('value' => 'Option 1', 'label' => 'Option 1'), array('value' => 'Option 2', 'label' => 'Option 2'), array('value' => 'Option 3', 'label' => 'Option 3')), 'label' => esc_html__('Options', 'lepopup'), 'tooltip' => esc_html__('These are the choices that the user will be able to choose from.', 'lepopup'), 'type' => 'options'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'caption' => esc_html__('The field is required', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of checkboxes through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'checkbox-style' => array('value' => array('position' => '', 'align' => '', 'layout' => ''), 'caption' => array('position' => esc_html__('Position', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup'), 'layout' => esc_html__('Layout', 'lepopup')), 'label' => esc_html__('Checkbox style', 'lepopup'), 'tooltip' => esc_html__('Choose how to display checkbox fields and their captions.', 'lepopup'), 'type' => 'local-checkbox-style'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'validators' => array('values' => array(), 'allowed-values' => array('in-array', 'prevent-duplicates'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid.', 'lepopup'), 'type' => 'validators')
			),
			'imageselect' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Image select', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size-width' => array('value' => '400', 'label' => esc_html__('Width', 'lepopup'), 'tooltip' => esc_html__('Specify the width of the element.', 'lepopup'), 'unit' => 'px', 'type' => 'units'),
					'mode' => array('value' => 'radio', 'label' => esc_html__('Mode', 'lepopup'), 'tooltip' => esc_html__('Select the mode of the Image Select.', 'lepopup'), 'type' => 'imageselect-mode'),
					'options' => array('multi-select' => 'off', 'values' => array(array('value' => 'Option 1', 'label' => 'Option 1', 'image' => $this->plugins_url.'/images/placeholder-image.png'), array('value' => 'Option 2', 'label' => 'Option 2', 'image' => $this->plugins_url.'/images/placeholder-image.png'), array('value' => 'Option 3', 'label' => 'Option 3', 'image' => $this->plugins_url.'/images/placeholder-image.png')), 'label' => esc_html__('Options', 'lepopup'), 'tooltip' => esc_html__('These are the choices that the user will be able to choose from.', 'lepopup'), 'type' => 'image-options'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'caption' => esc_html__('The field is required', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of input element through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'image-style' => array('value' => array('width' => "120", 'height' => "160", 'size' => 'contain'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup'), 'size' => esc_html__('Size', 'lepopup')), 'label' => esc_html__('Image style', 'lepopup'), 'tooltip' => esc_html__('Choose how to display images.', 'lepopup'), 'type' => 'local-imageselect-style'),
					'label-enable' => array('value' => 'off', 'label' => esc_html__('Enable label', 'lepopup'), 'tooltip' => esc_html__('If enabled, the label will be displayed below the image.', 'lepopup'), 'caption' => esc_html__('Label enabled', 'lepopup'), 'type' => 'checkbox'),
					'label-height' => array('value' => '60', 'label' => esc_html__('Label height', 'lepopup'), 'tooltip' => esc_html__('Set the height of label area.', 'lepopup'), 'unit' => 'px', 'type' => 'units', 'visible' => array('label-enable' => array('on'))),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'validators' => array('values' => array(), 'allowed-values' => array('in-array', 'prevent-duplicates'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid.', 'lepopup'), 'type' => 'validators')
			),
			'tile' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Tile', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size-width' => array('value' => '400', 'label' => esc_html__('Width', 'lepopup'), 'tooltip' => esc_html__('Specify the width of the element.', 'lepopup'), 'unit' => 'px', 'type' => 'units'),
					'mode' => array('value' => 'radio', 'label' => esc_html__('Mode', 'lepopup'), 'tooltip' => esc_html__('Select the mode of the Tiles.', 'lepopup'), 'type' => 'tile-mode'),
					'options' => array('multi-select' => 'off', 'values' => array(array('value' => 'Option 1', 'label' => 'Option 1'), array('value' => 'Option 2', 'label' => 'Option 2'), array('value' => 'Option 3', 'label' => 'Option 3')), 'label' => esc_html__('Options', 'lepopup'), 'tooltip' => esc_html__('These are the choices that the user will be able to choose from.', 'lepopup'), 'type' => 'options'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must select at least one option.', 'lepopup'), 'caption' => esc_html__('The field is required', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of tiles through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'tile-style' => array('value' => array('size' => '', 'width' => '', 'position' => '', 'layout' => ''), 'caption' => array('size' => esc_html__('Size', 'lepopup'), 'width' => esc_html__('Width', 'lepopup'), 'position' => esc_html__('Position', 'lepopup'), 'layout' => esc_html__('Layout', 'lepopup')), 'label' => esc_html__('Tile style', 'lepopup'), 'tooltip' => esc_html__('Adjust the tile style.', 'lepopup'), 'type' => 'local-tile-style'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'validators' => array('values' => array(), 'allowed-values' => array('in-array', 'prevent-duplicates'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid.', 'lepopup'), 'type' => 'validators')
			),
			'multiselect' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Multiselect', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '360', 'height' => '120'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'options' => array('multi-select' => 'on', 'values' => array(array('value' => 'Option 1', 'label' => 'Option 1'), array('value' => 'Option 2', 'label' => 'Option 2'), array('value' => 'Option 3', 'label' => 'Option 3')), 'label' => esc_html__('Options', 'lepopup'), 'tooltip' => esc_html__('These are the choices that the user will be able to choose from.', 'lepopup'), 'type' => 'options'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'caption' => esc_html__('The field is required', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of input element through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'align' => array('value' => 'left', 'label' => esc_html__('Text alignment', 'lepopup'), 'tooltip' => esc_html__('Adjust the alignment of the textarea text.', 'lepopup'), 'type' => 'align'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'max-allowed' => array('value' => '0', 'label' => esc_html__('Maximum selected options', 'lepopup'), 'tooltip' => esc_html__('Enter how many options can be selected. Set 0 for unlimited number.', 'lepopup'), 'type' => 'integer'),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'validators' => array('values' => array(), 'allowed-values' => array('in-array', 'prevent-duplicates'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid.', 'lepopup'), 'type' => 'validators')
			),
			'radio' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Radio button', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size-width' => array('value' => '400', 'label' => esc_html__('Width', 'lepopup'), 'tooltip' => esc_html__('Specify the width of the element.', 'lepopup'), 'unit' => 'px', 'type' => 'units'),
					'options' => array('multi-select' => 'off', 'values' => array(array('value' => 'Option 1', 'label' => 'Option 1'), array('value' => 'Option 2', 'label' => 'Option 2'), array('value' => 'Option 3', 'label' => 'Option 3')), 'label' => esc_html__('Options', 'lepopup'), 'tooltip' => esc_html__('These are the choices that the user will be able to choose from.', 'lepopup'), 'type' => 'options'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'caption' => esc_html__('The field is required', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of radio buttons through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'radio-style' => array('value' => array('position' => '', 'align' => '', 'layout' => ''), 'caption' => array('position' => esc_html__('Position', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup'), 'layout' => esc_html__('Layout', 'lepopup')), 'label' => esc_html__('Radio button style', 'lepopup'), 'tooltip' => esc_html__('Choose how to display checkbox fields and their captions.', 'lepopup'), 'type' => 'local-checkbox-style'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'validators' => array('values' => array(), 'allowed-values' => array('in-array', 'prevent-duplicates'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid.', 'lepopup'), 'type' => 'validators')
			),
			'date' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Date', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '360', 'height' => '40'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'placeholder' => array('value' => '', 'label' => esc_html__('Placeholder', 'lepopup'), 'tooltip' => esc_html__('The placeholder text will appear inside the field until the user starts to type.', 'lepopup'), 'type' => 'text'),
					'autocomplete' => array('value' => 'off', 'label' => esc_html__('Autocomplete attribute', 'lepopup'), 'tooltip' => esc_html__('Choose the value of the autocomplete attribute. It helps browser to fill the field value, if required.', 'lepopup'), 'type' => 'select', 'options' => $autocomplete_options),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of input fields through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'align' => array('value' => 'left', 'label' => esc_html__('Text alignment', 'lepopup'), 'tooltip' => esc_html__('Adjust the alignment of the textarea text.', 'lepopup'), 'type' => 'align'),
					'icon' => array('value' => array('left-icon' => '', 'left-size' => '', 'left-color' => '', 'right-icon' => ($this->options['fa-enable'] == 'on' ? ($this->options['fa-regular-enable'] == 'on' ? 'far fa-calendar-alt' : 'fas fa-calendar-alt') : 'lepopup-fa lepopup-fa-calendar'), 'right-size' => '', 'right-color' => ''), 'caption' => array('left' => esc_html__('Left side', 'lepopup'), 'right' => esc_html__('Right side', 'lepopup')), 'label' => esc_html__('Input icons', 'lepopup'), 'tooltip' => esc_html__('These icons appear inside/near of the input field.', 'lepopup'), 'type' => 'input-icons'),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the input field.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							),
							'input' => array(
								'label' => esc_html__('Input field', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input'
							),
							'input-hover' => array(
								'label' => esc_html__('Input field (hover)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input:hover',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input:hover'
							),
							'input-focus' => array(
								'label' => esc_html__('Input field (focus)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input:focus',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input:focus'
							),
							'input-icon-left' => array(
								'label' => esc_html__('Input field icon (left)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left'
							),
							'input-icon-right' => array(
								'label' => esc_html__('Input field icon (right)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'default' => array('value' => array('type' => 'date', 'date' => '', 'offset' => '0'), 'caption' => array('type' => esc_html__('Type', 'lepopup'), 'date' => esc_html__('Date', 'lepopup'), 'offset' => esc_html__('Offset', 'lepopup')), 'type-values' => array('none' => esc_html__('None', 'lepopup'), 'yesterday' => esc_html__('Yesterday', 'lepopup'), 'today' => esc_html__('Today', 'lepopup'), 'offset' => esc_html__('Today + N days', 'lepopup'), 'tomorrow' => esc_html__('Tomorrow', 'lepopup'), 'date' => esc_html__('Fixed date', 'lepopup')), 'label' => esc_html__('Default', 'lepopup'), 'tooltip' => esc_html__('The default value is the value that the field has before the user has entered anything.', 'lepopup'), 'type' => 'date-default'),
//					'default' => array('value' => '', 'label' => esc_html__('Default value', 'lepopup'), 'tooltip' => esc_html__('The default value is the value that the field has before the user has entered anything.', 'lepopup'), 'type' => 'date'),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'min-date' => array('value' => array('type' => '', 'date' => '', 'field' => '', 'offset' => '0'), 'caption' => array('type' => esc_html__('Type', 'lepopup'), 'date' => esc_html__('Date', 'lepopup'), 'field' => esc_html__('Field', 'lepopup'), 'offset' => esc_html__('Offset', 'lepopup')), 'type-values' => array('none' => esc_html__('None', 'lepopup'), 'yesterday' => esc_html__('Yesterday', 'lepopup'), 'today' => esc_html__('Today', 'lepopup'), 'offset' => esc_html__('Today + N days', 'lepopup'), 'tomorrow' => esc_html__('Tomorrow', 'lepopup'), 'date' => esc_html__('Fixed date', 'lepopup'), 'field' => esc_html__('Other field', 'lepopup')), 'label' => esc_html__('Minimum date', 'lepopup'), 'tooltip' => esc_html__('Adjust the minimum date that can be selected.', 'lepopup'), 'type' => 'date-limit'),
					'min-date-error' => array('value' => esc_html__('The value is out of range.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('This error message appears if submitted date is less than minimum date.', 'lepopup'), 'type' => 'error', 'visible' => array('min-date-type' => array('yesterday', 'today', 'tomorrow', 'date', 'field', 'offset'))),
					'max-date' => array('value' => array('type' => '', 'date' => '', 'field' => '', 'offset' => '0'), 'caption' => array('type' => esc_html__('Type', 'lepopup'), 'date' => esc_html__('Date', 'lepopup'), 'field' => esc_html__('Field', 'lepopup'), 'offset' => esc_html__('Offset', 'lepopup')), 'type-values' => array('none' => esc_html__('None', 'lepopup'), 'yesterday' => esc_html__('Yesterday', 'lepopup'), 'today' => esc_html__('Today', 'lepopup'), 'offset' => esc_html__('Today + N days', 'lepopup'), 'tomorrow' => esc_html__('Tomorrow', 'lepopup'), 'date' => esc_html__('Fixed date', 'lepopup'), 'field' => esc_html__('Other field', 'lepopup')), 'label' => esc_html__('Maximum date', 'lepopup'), 'tooltip' => esc_html__('Adjust the maximum date that can be selected.', 'lepopup'), 'type' => 'date-limit'),
					'max-date-error' => array('value' => esc_html__('The value is out of range.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('This error message appears if submitted date is more than minimum date.', 'lepopup'), 'type' => 'error', 'visible' => array('max-date-type' => array('yesterday', 'today', 'tomorrow', 'date', 'field', 'offset'))),
					'readonly' => array('value' => 'off', 'label' => esc_html__('Read only', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user can not edit the field value.', 'lepopup'), 'type' => 'checkbox'),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'filters' => array('values' => array(array("type" => "trim", "properties" => null)), 'allowed-values' => array('alpha', 'alphanumeric', 'digits', 'regex', 'strip-tags', 'trim'), 'label' => esc_html__('Filters', 'lepopup'), 'tooltip' => esc_html__('Filters allow you to strip various characters from the submitted value.', 'lepopup'), 'type' => 'filters'),
					'validators' => array('values' => array(array("type" => "date", "properties" => array('error' => ''))), 'allowed-values' => array('date'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid.', 'lepopup'), 'type' => 'validators')
			),
			'time' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Time', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '360', 'height' => '40'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'placeholder' => array('value' => '', 'label' => esc_html__('Placeholder', 'lepopup'), 'tooltip' => esc_html__('The placeholder text will appear inside the field until the user starts to type.', 'lepopup'), 'type' => 'text'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of input fields through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'align' => array('value' => 'left', 'label' => esc_html__('Text alignment', 'lepopup'), 'tooltip' => esc_html__('Adjust the alignment of the textarea text.', 'lepopup'), 'type' => 'align'),
					'icon' => array('value' => array('left-icon' => '', 'left-size' => '', 'left-color' => '', 'right-icon' => ($this->options['fa-enable'] == 'on' ? ($this->options['fa-regular-enable'] == 'on' ? 'far fa-clock' : 'fas fa-clock') : 'lepopup-fa lepopup-fa-clock-o'), 'right-size' => '', 'right-color' => ''), 'caption' => array('left' => esc_html__('Left side', 'lepopup'), 'right' => esc_html__('Right side', 'lepopup')), 'label' => esc_html__('Input icons', 'lepopup'), 'tooltip' => esc_html__('These icons appear inside/near of the input field.', 'lepopup'), 'type' => 'input-icons'),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the input field.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							),
							'input' => array(
								'label' => esc_html__('Input field', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input'
							),
							'input-hover' => array(
								'label' => esc_html__('Input field (hover)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input:hover',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input:hover'
							),
							'input-focus' => array(
								'label' => esc_html__('Input field (focus)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input:focus',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input:focus'
							),
							'input-icon-left' => array(
								'label' => esc_html__('Input field icon (left)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left'
							),
							'input-icon-right' => array(
								'label' => esc_html__('Input field icon (right)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'default' => array('value' => '', 'label' => esc_html__('Default value', 'lepopup'), 'tooltip' => esc_html__('The default value is the value that the field has before the user has entered anything.', 'lepopup'), 'type' => 'time'),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'min-time' => array('value' => array('type' => '', 'time' => '', 'field' => ''), 'caption' => array('type' => esc_html__('Type', 'lepopup'), 'time' => esc_html__('Time', 'lepopup'), 'field' => esc_html__('Field', 'lepopup')), 'type-values' => array('none' => esc_html__('None', 'lepopup'), 'time' => esc_html__('Fixed time', 'lepopup'), 'field' => esc_html__('Other field', 'lepopup')), 'label' => esc_html__('Minimum time', 'lepopup'), 'tooltip' => esc_html__('Adjust the minimum time that can be selected.', 'lepopup'), 'type' => 'time-limit'),
					'min-time-error' => array('value' => esc_html__('The value is out of range.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('This error message appears if submitted time is less than minimum time.', 'lepopup'), 'type' => 'error', 'visible' => array('min-time-type' => array('time', 'field'))),
					'max-time' => array('value' => array('type' => '', 'time' => '', 'field' => ''), 'caption' => array('type' => esc_html__('Type', 'lepopup'), 'time' => esc_html__('Time', 'lepopup'), 'field' => esc_html__('Field', 'lepopup')), 'type-values' => array('none' => esc_html__('None', 'lepopup'), 'time' => esc_html__('Fixed time', 'lepopup'), 'field' => esc_html__('Other field', 'lepopup')), 'label' => esc_html__('Maximum time', 'lepopup'), 'tooltip' => esc_html__('Adjust the maximum time that can be selected.', 'lepopup'), 'type' => 'time-limit'),
					'max-time-error' => array('value' => esc_html__('The value is out of range.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('This error message appears if submitted time is more than minimum time.', 'lepopup'), 'type' => 'error', 'visible' => array('max-time-type' => array('time', 'field'))),
					'interval' => array('value' => '10', 'label' => esc_html__('Minute interval', 'lepopup'), 'tooltip' => esc_html__('Enter the minute interval.', 'lepopup'), 'type' => 'integer'),
					'readonly' => array('value' => 'off', 'label' => esc_html__('Read only', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user can not edit the field value manually, only via timepicker.', 'lepopup'), 'type' => 'checkbox'),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'filters' => array('values' => array(array("type" => "trim", "properties" => null)), 'allowed-values' => array('alpha', 'alphanumeric', 'digits', 'regex', 'strip-tags', 'trim'), 'label' => esc_html__('Filters', 'lepopup'), 'tooltip' => esc_html__('Filters allow you to strip various characters from the submitted value.', 'lepopup'), 'type' => 'filters'),
					'validators' => array('values' => array(array("type" => "time", "properties" => array('error' => ''))), 'allowed-values' => array('time'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid.', 'lepopup'), 'type' => 'validators')
			),
			'file' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Upload', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '160', 'height' => '40'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'button-label' => array('value' => esc_html__('Browse...', 'lepopup'), 'label' => esc_html__('Caption', 'lepopup'), 'tooltip' => esc_html__('This is the caption of upload button.', 'lepopup'), 'type' => 'text'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of buttons through %sPopup Settings >> Style Tab >> Buttons%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'button-style' => array('value' => array('size' => '', 'width' => '', 'position' => 'left'), 'caption' => array('size' => esc_html__('Size', 'lepopup'), 'width' => esc_html__('Width', 'lepopup'), 'position' => esc_html__('Position', 'lepopup')), 'label' => esc_html__('Button style', 'lepopup'), 'tooltip' => esc_html__('Adjust the button size and position.', 'lepopup'), 'type' => 'local-button-style'),
					'icon' => array('value' => array('left' => '', 'right' => ($this->options['fa-enable'] == 'on' ? 'fas fa-upload' : 'lepopup-fa lepopup-fa-upload')), 'caption' => array('left' => esc_html__('Left side', 'lepopup'), 'right' => esc_html__('Right side', 'lepopup')), 'label' => esc_html__('Icons', 'lepopup'), 'tooltip' => esc_html__('These icons appear near the button caption.', 'lepopup'), 'type' => 'button-icons'),
					'colors-sections' => array('type' => 'sections', 'sections' => array(
						'button-default' => array('label' => esc_html__('Default', 'lepopup'), 'icon' => 'fas fa-globe'),
						'button-hover' => array('label' => esc_html__('Hover', 'lepopup'), 'icon' => 'far fa-hand-pointer'),
						'button-active' => array('label' => esc_html__('Active', 'lepopup'), 'icon' => 'far fa-paper-plane')
					)),
					'start-button-default' => array('type' => 'section-start', 'section' => 'button-default'),
						'colors' => array('value' => array('background' => '', 'border' => '', 'text' => ''), 'caption' => array('background' => esc_html__('Background', 'lepopup'), 'border' => esc_html__('Border', 'lepopup'), 'text' => esc_html__('Text', 'lepopup')), 'label' => esc_html__('Colors', 'lepopup'), 'tooltip' => esc_html__('Adjust the colors of the button.', 'lepopup'), 'type' => 'colors'),
					'end-button-default' => array('type' => 'section-end'),
					'start-button-hover' => array('type' => 'section-start', 'section' => 'button-hover'),
						'colors-hover' => array('value' => array('background' => '', 'border' => '', 'text' => ''), 'caption' => array('background' => esc_html__('Background', 'lepopup'), 'border' => esc_html__('Border', 'lepopup'), 'text' => esc_html__('Text', 'lepopup')), 'label' => esc_html__('Colors', 'lepopup'), 'tooltip' => esc_html__('Adjust the colors of the hovered button.', 'lepopup'), 'type' => 'colors'),
					'end-button-hover' => array('type' => 'section-end'),
					'start-button-active' => array('type' => 'section-start', 'section' => 'button-active'),
						'colors-active' => array('value' => array('background' => '', 'border' => '', 'text' => ''), 'caption' => array('background' => esc_html__('Background', 'lepopup'), 'border' => esc_html__('Border', 'lepopup'), 'text' => esc_html__('Text', 'lepopup')), 'label' => esc_html__('Colors', 'lepopup'), 'tooltip' => esc_html__('Adjust the colors of the active button.', 'lepopup'), 'type' => 'colors'),
					'end-button-active' => array('type' => 'section-end'),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the button.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							),
							'button' => array(
								'label' => esc_html__('Button', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button'
							),
							'button-hover' => array(
								'label' => esc_html__('Button (hover)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button:hover',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button:hover'
							),
							'button-active' => array(
								'label' => esc_html__('Button (active)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button:active',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button:active'
							),
							'button-icon-left' => array(
								'label' => esc_html__('Button icon (left)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button i.lepopup-icon-left',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button i.lepopup-icon-left'
							),
							'button-icon-right' => array(
								'label' => esc_html__('Button icon (right)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button i.lepopup-icon-right',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button i.lepopup-icon-right'
							),
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'allowed-extensions' => array('value' => esc_html__('gif, jpg, jpeg, png', 'lepopup'), 'label' => esc_html__('Allowed extensions', 'lepopup'), 'tooltip' => esc_html__('Enter the comma-separated list of allowed file extensions.', 'lepopup'), 'type' => 'text'),
					'allowed-extensions-error' => array('value' => esc_html__('Selected file extension is not allowed.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('This message appears if user tries to upload any file with extension not from the list.', 'lepopup'), 'type' => 'error'),
					'max-size' => array('value' => '10', 'label' => esc_html__('Maximum allowed size', 'lepopup'), 'tooltip' => sprintf(esc_html__('Enter the maximum size of a file in MB. According to your PHP settings, the maximum file size allowed is %s. Do not exceed this value.', 'lepopup'), ini_get('upload_max_filesize')), 'unit' => 'mb', 'type' => 'units'),
					'max-size-error' => array('value' => esc_html__('Selected file is too big.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('This message appears if user tries to upload any file bigger then maximum allowed file size.', 'lepopup'), 'type' => 'error'),
					'max-files' => array('value' => '3', 'label' => esc_html__('Maximum number of files', 'lepopup'), 'tooltip' => esc_html__('Enter the maximum number of files that can be uploaded by user.', 'lepopup'), 'type' => 'integer'),
					'max-files-error' => array('value' => esc_html__('Too many files.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('This message appears if user tries to upload more files then maximum number of files.', 'lepopup'), 'type' => 'error'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id')
			),
			'star-rating' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Rating', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size-width' => array('value' => '240', 'label' => esc_html__('Width', 'lepopup'), 'tooltip' => esc_html__('Specify the width of the element.', 'lepopup'), 'unit' => 'px', 'type' => 'units'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'star-style' => array('value' => array('size' => 'medium', 'color-unrated' => '#aaa', 'color-rated' => '#FFD700'), 'caption' => array('size' => esc_html__('Size', 'lepopup'), 'color-unrated' => esc_html__('Unrated', 'lepopup'), 'color-rated' => esc_html__('Rated', 'lepopup')), 'label' => esc_html__('Star style', 'lepopup'), 'tooltip' => esc_html__('Adjust the style of stars.', 'lepopup'), 'type' => 'star-style'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'total-stars' => array('value' => '5', 'label' => esc_html__('Number of stars', 'lepopup'), 'tooltip' => esc_html__('Choose the total number of stars.', 'lepopup'), 'type' => 'select', 'options' => array('3' => esc_html__('3 Stars', 'lepopup'), '4' => esc_html__('4 Stars', 'lepopup'), '5' => esc_html__('5 Stars', 'lepopup'), '6' => esc_html__('6 Stars', 'lepopup'), '7' => esc_html__('7 Stars', 'lepopup'), '8' => esc_html__('8 Stars', 'lepopup'), '9' => esc_html__('9 Stars', 'lepopup'), '10' => esc_html__('10 Stars', 'lepopup'))),
					'default' => array('value' => '0', 'label' => esc_html__('Default rating', 'lepopup'), 'tooltip' => esc_html__('The default value is the value that the field has before the user has entered anything.', 'lepopup'), 'type' => 'select', 'options' => array('0' => esc_html__('No rating', 'lepopup'), '1' => esc_html__('1 Star', 'lepopup'), '2' => esc_html__('2 Stars', 'lepopup'), '3' => esc_html__('3 Stars', 'lepopup'), '4' => esc_html__('4 Stars', 'lepopup'), '5' => esc_html__('5 Stars', 'lepopup'), '6' => esc_html__('6 Stars', 'lepopup'), '7' => esc_html__('7 Stars', 'lepopup'), '8' => esc_html__('8 Stars', 'lepopup'), '9' => esc_html__('9 Stars', 'lepopup'), '10' => esc_html__('10 Stars', 'lepopup'))),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id')
			),
			'password' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Password', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '360', 'height' => '40'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'placeholder' => array('value' => '', 'label' => esc_html__('Placeholder', 'lepopup'), 'tooltip' => esc_html__('The placeholder text will appear inside the field until the user starts to type.', 'lepopup'), 'type' => 'text'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of input fields through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'align' => array('value' => 'left', 'label' => esc_html__('Text alignment', 'lepopup'), 'tooltip' => esc_html__('Adjust the alignment of the textarea text.', 'lepopup'), 'type' => 'align'),
					'icon' => array('value' => array('left-icon' => ($this->options['fa-enable'] == 'on' ? 'fas fa-lock' : 'lepopup-fa lepopup-fa-lock'), 'left-size' => '', 'left-color' => '', 'right-icon' => '', 'right-size' => '', 'right-color' => ''), 'caption' => array('left' => esc_html__('Left side', 'lepopup'), 'right' => esc_html__('Right side', 'lepopup')), 'label' => esc_html__('Input icons', 'lepopup'), 'tooltip' => esc_html__('These icons appear inside/near of the input field.', 'lepopup'), 'type' => 'input-icons'),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the input field.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							),
							'input' => array(
								'label' => esc_html__('Input field', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input'
							),
							'input-hover' => array(
								'label' => esc_html__('Input field (hover)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input:hover',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input:hover'
							),
							'input-focus' => array(
								'label' => esc_html__('Input field (focus)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input:focus',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input:focus'
							),
							'input-icon-left' => array(
								'label' => esc_html__('Input field icon (left)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left'
							),
							'input-icon-right' => array(
								'label' => esc_html__('Input field icon (right)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'min-length' => array('value' => '7', 'label' => esc_html__('Minimum length', 'lepopup'), 'tooltip' => esc_html__('Enter the minimum password length.', 'lepopup'), 'type' => 'integer'),
					'min-length-error' => array('value' => esc_html__('The password is too short.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('This message appears if submitted password is too short.', 'lepopup'), 'type' => 'error'),
					'capital-mandatory' => array('value' => 'off', 'label' => esc_html__('Capital letters is mandatory', 'lepopup'), 'tooltip' => esc_html__('If enabled, the password must contains at least one capital letter.', 'lepopup'), 'type' => 'checkbox'),
					'capital-mandatory-error' => array('value' => esc_html__('The password must contain capital letter.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('capital-mandatory' => array('on'))),
					'digit-mandatory' => array('value' => 'off', 'label' => esc_html__('Digit is mandatory', 'lepopup'), 'tooltip' => esc_html__('If enabled, the password must contains at least one digit.', 'lepopup'), 'type' => 'checkbox'),
					'digit-mandatory-error' => array('value' => esc_html__('The password must contain digit.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('digit-mandatory' => array('on'))),
					'special-mandatory' => array('value' => 'off', 'label' => esc_html__('Special character is mandatory', 'lepopup'), 'tooltip' => esc_html__('If enabled, the password must contains at least one special character: !$#%^&*~_-(){}[]\|/?.', 'lepopup'), 'type' => 'checkbox'),
					'special-mandatory-error' => array('value' => esc_html__('The password must contain special character.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('special-mandatory' => array('on'))),
					'save' => array('value' => 'off', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
			),
			'signature' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Signature', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '360', 'height' => '120'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must put signature.', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('Signature is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the input field.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id')
			),
			'rangeslider' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Range', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size-width' => array('value' => '360', 'label' => esc_html__('Width', 'lepopup'), 'tooltip' => esc_html__('Specify the width of the element.', 'lepopup'), 'unit' => 'px', 'type' => 'units'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of range sliders through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'grid-enable' => array('value' => 'off', 'label' => esc_html__('Show grid', 'lepopup'), 'tooltip' => esc_html__('Enables grid of values.', 'lepopup'), 'type' => 'checkbox'),
					'min-max-labels' => array('value' => 'off', 'label' => esc_html__('Show min/max labels', 'lepopup'), 'tooltip' => esc_html__('Enables labels for min and max values.', 'lepopup'), 'type' => 'checkbox'),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the input field.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'range' => array('value' => array('value1' => '0', 'value2' => '100', 'value3' => '1'), 'caption' => array('value1' => esc_html__('Min', 'lepopup'), 'value2' => esc_html__('Max', 'lepopup'), 'value3' => esc_html__('Step', 'lepopup')), 'label' => esc_html__('Range size', 'lepopup'), 'tooltip' => esc_html__('Set basic parameters of range slider. Min - slider minimum value. Max - slider maximum value. Step - slider step (always > 0).', 'lepopup'), 'type' => 'three-numbers'),
					'handle' => array('value' => '30', 'label' => esc_html__('Handle value', 'lepopup'), 'tooltip' => esc_html__('The default value is the value that the field has before the user has entered anything. If range slider has 2 handles, this is the default value of the left handle.', 'lepopup'), 'type' => 'integer'),
					'double' => array('value' => 'off', 'label' => esc_html__('Double handle', 'lepopup'), 'tooltip' => esc_html__('Enables second handle.', 'lepopup'), 'type' => 'checkbox'),
					'handle2' => array('value' => '70', 'label' => esc_html__('Second handle value', 'lepopup'), 'tooltip' => esc_html__('This is the default value of the right handle.', 'lepopup'), 'type' => 'integer', 'visible' => array('double' => array('on'))),
					'prefix' => array('value' => '', 'label' => esc_html__('Value prefix', 'lepopup'), 'tooltip' => esc_html__('Set prefix for values. Will be set up right before the number. For example - $100.', 'lepopup'), 'type' => 'text'),
					'postfix' => array('value' => '', 'label' => esc_html__('Value postfix', 'lepopup'), 'tooltip' => esc_html__('Set postfix for values. Will be set up right after the number. For example - 100k.', 'lepopup'), 'type' => 'text'),
					'readonly' => array('value' => 'off', 'label' => esc_html__('Read only', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user can not edit the field value.', 'lepopup'), 'type' => 'checkbox'),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'validators' => array('values' => array(), 'allowed-values' => array('equal', 'greater', 'in-array', 'less'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid. If range slider has 2 handles, both of them must match validator criteria.', 'lepopup'), 'type' => 'validators')
			),
			'number' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Number', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '100', 'height' => '40'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'placeholder' => array('value' => '', 'label' => esc_html__('Placeholder', 'lepopup'), 'tooltip' => esc_html__('The placeholder text will appear inside the field until the user starts to type.', 'lepopup'), 'type' => 'text'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
					'required' => array('value' => 'off', 'label' => esc_html__('Required', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user must fill out the field.', 'lepopup'), 'type' => 'checkbox'),
					'required-error' => array('value' => esc_html__('This field is required.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'type' => 'error', 'visible' => array('required' => array('on'))),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of input fields through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'align' => array('value' => 'left', 'label' => esc_html__('Text alignment', 'lepopup'), 'tooltip' => esc_html__('Adjust the alignment of the textarea text.', 'lepopup'), 'type' => 'align'),
					'icon' => array('value' => array('left-icon' => '', 'left-size' => '', 'left-color' => '', 'right-icon' => '', 'right-size' => '', 'right-color' => ''), 'caption' => array('left' => esc_html__('Left side', 'lepopup'), 'right' => esc_html__('Right side', 'lepopup')), 'label' => esc_html__('Input icons', 'lepopup'), 'tooltip' => esc_html__('These icons appear inside/near of the input field.', 'lepopup'), 'type' => 'input-icons'),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the input field.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							),
							'input' => array(
								'label' => esc_html__('Input field', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input'
							),
							'input-hover' => array(
								'label' => esc_html__('Input field (hover)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input:hover',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input:hover'
							),
							'input-focus' => array(
								'label' => esc_html__('Input field (focus)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input:focus',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input:focus'
							),
							'input-icon-left' => array(
								'label' => esc_html__('Input field icon (left)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-left'
							),
							'input-icon-right' => array(
								'label' => esc_html__('Input field icon (right)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input i.lepopup-icon-right'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'number' => array('value' => array('value1' => '', 'value2' => '', 'value3' => ''), 'caption' => array('value1' => esc_html__('Min', 'lepopup'), 'value2' => esc_html__('Max', 'lepopup'), 'value3' => esc_html__('Default', 'lepopup')), 'label' => esc_html__('Value', 'lepopup'), 'tooltip' => esc_html__('Set basic parameters of number input. Min - minimum value. Max - maximum value. Default - the value that the field has before the user has entered anything.', 'lepopup'), 'type' => 'three-numbers'),
					'decimal' => array('value' => '0', 'label' => esc_html__('Decimal digits', 'lepopup'), 'tooltip' => esc_html__('Select the allowed number of digits after the decimal separator.', 'lepopup'), 'options' => array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '6' => '6', '8' => '8'), 'type' => 'select'),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'readonly' => array('value' => 'off', 'label' => esc_html__('Read only', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user can not edit the field value.', 'lepopup'), 'type' => 'checkbox'),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'validators' => array('values' => array(), 'allowed-values' => array('equal', 'equal-field', 'greater', 'in-array', 'less'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid.', 'lepopup'), 'type' => 'validators')
			),
			'numspinner' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Number', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '120', 'height' => '40'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of input fields through %sPopup Settings >> Style Tab >> Inputs%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'align' => array('value' => 'left', 'label' => esc_html__('Text alignment', 'lepopup'), 'tooltip' => esc_html__('Adjust the alignment of the textarea text.', 'lepopup'), 'type' => 'align'),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the input field.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							),
							'input' => array(
								'label' => esc_html__('Input field', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input'
							),
							'input-hover' => array(
								'label' => esc_html__('Input field (hover)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} div.lepopup-input input:hover',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} div.lepopup-input input:hover'
							)
						)
					),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'simple-mode' => array('value' => 'on', 'label' => esc_html__('Simple mode', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can configure one range of values. If disabled - multiple ranges.', 'lepopup'), 'type' => 'checkbox'),
					'number' => array('value' => array('value1' => '0', 'value2' => '1', 'value3' => '10', 'value4' => '1'), 'caption' => array('value1' => esc_html__('Min', 'lepopup'), 'value2' => esc_html__('Default', 'lepopup'), 'value3' => esc_html__('Max', 'lepopup'), 'value4' => esc_html__('Step', 'lepopup')), 'label' => esc_html__('Value', 'lepopup'), 'tooltip' => esc_html__('Set basic parameters of number input. Min - minimum value. Max - maximum value. Default - the value that the field has before the user has entered anything. Step - increment value.', 'lepopup'), 'type' => 'four-numbers', 'visible' => array('simple-mode' => array('on'))),
					'number-advanced' => array('value' => array('value1' => '1', 'value2' => '0-10', 'value3' => '1'), 'caption' => array('value1' => esc_html__('Default', 'lepopup'), 'value2' => esc_html__('Ranges', 'lepopup'), 'value3' => esc_html__('Step', 'lepopup')), 'label' => esc_html__('Value', 'lepopup'), 'tooltip' => esc_html__('Set basic parameters of number input. Default - the value that the field has before the user has entered anything. Step - increment value. Ranges - list of comma-separated values. Example: 0, 1...5, 7...10, 12, 14, 20...25. Important! Use triple dots to specify range.', 'lepopup'), 'type' => 'number-string-number', 'visible' => array('simple-mode' => array('off'))),
					'decimal' => array('value' => '0', 'label' => esc_html__('Decimal digits', 'lepopup'), 'tooltip' => esc_html__('Select the allowed number of digits after the decimal separator.', 'lepopup'), 'options' => array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '6' => '6', '8' => '8'), 'type' => 'select'),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'readonly' => array('value' => 'off', 'label' => esc_html__('Read only', 'lepopup'), 'tooltip' => esc_html__('If enabled, the user can not edit the field value.', 'lepopup'), 'type' => 'checkbox'),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this field', 'lepopup'), 'hide' => esc_html__('Hide this field', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
					'validators' => array('values' => array(), 'allowed-values' => array('equal', 'equal-field', 'greater', 'in-array', 'less'), 'label' => esc_html__('Validators', 'lepopup'), 'tooltip' => esc_html__('Validators checks whether the data entered by the user is valid.', 'lepopup'), 'type' => 'validators')
			),
			'hidden' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Hidden field', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
				'data' => array('type' => 'tab', 'value' => 'data', 'label' => esc_html__('Data', 'lepopup')),
					'default' => array('value' => '', 'label' => esc_html__('Default value', 'lepopup'), 'tooltip' => esc_html__('The default value is the value that the field has before the user has entered anything.', 'lepopup'), 'type' => 'text'),
					'dynamic-default' => array('value' => 'off', 'label' => esc_html__('Dynamic default value', 'lepopup'), 'tooltip' => esc_html__('Allows the default value of the field to be set dynamically via a URL parameter.', 'lepopup'), 'type' => 'checkbox'),
					'dynamic-parameter' => array('value' => '', 'label' => esc_html__('Parameter name', 'lepopup'), 'tooltip' => esc_html__('This is the name of the parameter that you will use to set the default value.', 'lepopup'), 'type' => 'text', 'visible' => array('dynamic-default' => array('on'))),
					'save' => array('value' => 'on', 'label' => esc_html__('Save to database', 'lepopup'), 'tooltip' => esc_html__('If enabled, the submitted element data will be saved to the database and shown when viewing an entry.', 'lepopup'), 'type' => 'checkbox'),
				'advanced' => array('type' => 'tab', 'value' => 'advanced', 'label' => esc_html__('Advanced', 'lepopup')),
					'element-id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the input field.', 'lepopup'), 'type' => 'id'),
			),
			'button' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Button', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name is used for your reference.', 'lepopup'), 'type' => 'text'),
					'label' => array('value' => esc_html__('Submit', 'lepopup'), 'label' => esc_html__('Label', 'lepopup'), 'tooltip' => esc_html__('This is the label of the button.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '160', 'height' => '40'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'button-type' => array('value' => 'submit', 'label' => esc_html__('Type', 'lepopup'), 'tooltip' => esc_html__('Choose the type of the button.', 'lepopup'), 'type' => 'radio-bar', 'options' => array('submit' => esc_html__('Submit', 'lepopup'), 'prev' => esc_html__('Back', 'lepopup'), 'next' => esc_html__('Next', 'lepopup'))),
					'label-loading' => array('value' => esc_html__('Sending...', 'lepopup'), 'label' => esc_html__('Sending label', 'lepopup'), 'type' => 'text', 'tooltip' => esc_html__('This is the label of the button when data are sending to server.', 'lepopup'), 'visible' => array('button-type' => array('submit', 'next'))),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of buttons through %sPopup Settings >> Style Tab >> Buttons%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'icon' => array('value' => array('left' => '', 'right' => ''), 'caption' => array('left' => esc_html__('Left side', 'lepopup'), 'right' => esc_html__('Right side', 'lepopup')), 'label' => esc_html__('Icons', 'lepopup'), 'tooltip' => esc_html__('These icons appear near the button label.', 'lepopup'), 'type' => 'button-icons'),
					'colors-sections' => array('type' => 'sections', 'sections' => array(
						'button-default' => array('label' => esc_html__('Default', 'lepopup'), 'icon' => 'fas fa-globe'),
						'button-hover' => array('label' => esc_html__('Hover', 'lepopup'), 'icon' => 'far fa-hand-pointer'),
						'button-active' => array('label' => esc_html__('Active', 'lepopup'), 'icon' => 'far fa-paper-plane')
					)),
					'start-button-default' => array('type' => 'section-start', 'section' => 'button-default'),
						'colors' => array('value' => array('background' => '', 'border' => '', 'text' => ''), 'caption' => array('background' => esc_html__('Background', 'lepopup'), 'border' => esc_html__('Border', 'lepopup'), 'text' => esc_html__('Text', 'lepopup')), 'label' => esc_html__('Colors', 'lepopup'), 'tooltip' => esc_html__('Adjust the colors of the button.', 'lepopup'), 'type' => 'colors'),
					'end-button-default' => array('type' => 'section-end'),
					'start-button-hover' => array('type' => 'section-start', 'section' => 'button-hover'),
						'colors-hover' => array('value' => array('background' => '', 'border' => '', 'text' => ''), 'caption' => array('background' => esc_html__('Background', 'lepopup'), 'border' => esc_html__('Border', 'lepopup'), 'text' => esc_html__('Text', 'lepopup')), 'label' => esc_html__('Colors', 'lepopup'), 'tooltip' => esc_html__('Adjust the colors of the hovered button.', 'lepopup'), 'type' => 'colors'),
					'end-button-hover' => array('type' => 'section-end'),
					'start-button-active' => array('type' => 'section-start', 'section' => 'button-active'),
						'colors-active' => array('value' => array('background' => '', 'border' => '', 'text' => ''), 'caption' => array('background' => esc_html__('Background', 'lepopup'), 'border' => esc_html__('Border', 'lepopup'), 'text' => esc_html__('Text', 'lepopup')), 'label' => esc_html__('Colors', 'lepopup'), 'tooltip' => esc_html__('Adjust the colors of the active button.', 'lepopup'), 'type' => 'colors'),
					'end-button-active' => array('type' => 'section-end'),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the button.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							),
							'button' => array(
								'label' => esc_html__('Button', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button'
							),
							'button-hover' => array(
								'label' => esc_html__('Button (hover)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button:hover',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button:hover'
							),
							'button-active' => array(
								'label' => esc_html__('Button (active)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button:active',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button:active'
							),
							'button-icon-left' => array(
								'label' => esc_html__('Button icon (left)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button i.lepopup-icon-left',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button i.lepopup-icon-left'
							),
							'button-icon-right' => array(
								'label' => esc_html__('Button icon (right)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button i.lepopup-icon-right',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button i.lepopup-icon-right'
							),
						)
					),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this button', 'lepopup'), 'hide' => esc_html__('Hide this button', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
			),
			'link-button' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Button', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin, in the notification email and when viewing submitted form entries.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '160', 'height' => '40'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'label' => array('value' => esc_html__('Link', 'lepopup'), 'label' => esc_html__('Label', 'lepopup'), 'tooltip' => esc_html__('This is the label of the button.', 'lepopup'), 'type' => 'text'),
					'link' => array('value' => '', 'label' => esc_html__('URL', 'lepopup'), 'type' => 'text', 'tooltip' => esc_html__('Specify the URL where users redirected to.', 'lepopup')),
					'new-tab' => array('value' => 'off', 'label' => esc_html__('Open link in new tab', 'lepopup'), 'tooltip' => esc_html__('If enabled, the link will be opened in new tab.', 'lepopup'), 'type' => 'checkbox'),
					'close' => array('value' => 'none', 'label' => esc_html__('Close', 'lepopup'), 'tooltip' => esc_html__('Adjust the mode of the closing.', 'lepopup'), 'type' => 'radio-bar', 'options' => array('none' => esc_html__('None', 'lepopup'), 'single' => esc_html__('Just close', 'lepopup'), 'period' => esc_html__('Close for period', 'lepopup'), 'forever' => esc_html__('Close forever', 'lepopup'))),
					'cookie-lifetime' => array('value' => '2', 'label' => esc_html__('Cookie lifetime', 'lepopup'), 'tooltip' => esc_html__('This is a period in days.', 'lepopup'), 'unit' => 'days', 'type' => 'units', 'visible' => array('close' => array('period'))),
					'onclick' => array('value' => '', 'label' => esc_html__('OnClick', 'lepopup'), 'tooltip' => esc_html__('This is the onclick handler of the button. Use a valid javascript code here.', 'lepopup'), 'type' => 'text'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of buttons through %sPopup Settings >> Style Tab >> Buttons%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
					'icon' => array('value' => array('left' => '', 'right' => ''), 'caption' => array('left' => esc_html__('Left side', 'lepopup'), 'right' => esc_html__('Right side', 'lepopup')), 'label' => esc_html__('Icons', 'lepopup'), 'tooltip' => esc_html__('These icons appear near the button label.', 'lepopup'), 'type' => 'button-icons'),
					'colors-sections' => array('type' => 'sections', 'sections' => array(
						'button-default' => array('label' => esc_html__('Default', 'lepopup'), 'icon' => 'fas fa-globe'),
						'button-hover' => array('label' => esc_html__('Hover', 'lepopup'), 'icon' => 'far fa-hand-pointer'),
						'button-active' => array('label' => esc_html__('Active', 'lepopup'), 'icon' => 'far fa-paper-plane')
					)),
					'start-button-default' => array('type' => 'section-start', 'section' => 'button-default'),
						'colors' => array('value' => array('background' => '', 'border' => '', 'text' => ''), 'caption' => array('background' => esc_html__('Background', 'lepopup'), 'border' => esc_html__('Border', 'lepopup'), 'text' => esc_html__('Text', 'lepopup')), 'label' => esc_html__('Colors', 'lepopup'), 'tooltip' => esc_html__('Adjust the colors of the button.', 'lepopup'), 'type' => 'colors'),
					'end-button-default' => array('type' => 'section-end'),
					'start-button-hover' => array('type' => 'section-start', 'section' => 'button-hover'),
						'colors-hover' => array('value' => array('background' => '', 'border' => '', 'text' => ''), 'caption' => array('background' => esc_html__('Background', 'lepopup'), 'border' => esc_html__('Border', 'lepopup'), 'text' => esc_html__('Text', 'lepopup')), 'label' => esc_html__('Colors', 'lepopup'), 'tooltip' => esc_html__('Adjust the colors of the hovered button.', 'lepopup'), 'type' => 'colors'),
					'end-button-hover' => array('type' => 'section-end'),
					'start-button-active' => array('type' => 'section-start', 'section' => 'button-active'),
						'colors-active' => array('value' => array('background' => '', 'border' => '', 'text' => ''), 'caption' => array('background' => esc_html__('Background', 'lepopup'), 'border' => esc_html__('Border', 'lepopup'), 'text' => esc_html__('Text', 'lepopup')), 'label' => esc_html__('Colors', 'lepopup'), 'tooltip' => esc_html__('Adjust the colors of the active button.', 'lepopup'), 'type' => 'colors'),
					'end-button-active' => array('type' => 'section-end'),
					'css-class' => array('value' => '', 'label' => esc_html__('Custom CSS class', 'lepopup'), 'tooltip' => esc_html__('This class name will be added to the button.', 'lepopup'), 'type' => 'text'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							),
							'button' => array(
								'label' => esc_html__('Button', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button'
							),
							'button-hover' => array(
								'label' => esc_html__('Button (hover)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button:hover',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button:hover'
							),
							'button-active' => array(
								'label' => esc_html__('Button (active)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button:active',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button:active'
							),
							'button-icon-left' => array(
								'label' => esc_html__('Button icon (left)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button i.lepopup-icon-left',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button i.lepopup-icon-left'
							),
							'button-icon-right' => array(
								'label' => esc_html__('Button icon (right)', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a.lepopup-button i.lepopup-icon-right',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a.lepopup-button i.lepopup-icon-right'
							),
						)
					),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this button', 'lepopup'), 'hide' => esc_html__('Hide this button', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
			),
			'html' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('HTML Content', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '400', 'height' => '200'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'content' => array('value' => esc_html__('Default HTML Content.', 'lepopup'), 'label' => esc_html__('HTML', 'lepopup'), 'tooltip' => esc_html__('This is the content of HTML.', 'lepopup'), 'type' => 'textarea-shortcodes'),
					'scrollable' => array('value' => 'off', 'label' => esc_html__('Enable scrollbar', 'lepopup'), 'tooltip' => esc_html__('If enabled, the scrollbar is added to the layer.', 'lepopup'), 'type' => 'checkbox'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#444', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'left'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Text and font', 'lepopup'), 'tooltip' => esc_html__('Adjust the text and font.', 'lepopup'), 'type' => 'text-style'),
					'background-style' => array('value' => array('image' => '', 'size' => 'auto', 'horizontal-position' => 'left', 'vertical-position' => 'top', 'repeat' => 'repeat', 'color' => '', 'color2' => '', 'gradient' => 'no'), 'caption' => array('image' => esc_html__('Image URL', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'horizontal-position' => esc_html__('Horizontal position', 'lepopup'), 'vertical-position' => esc_html__('Verical position', 'lepopup'), 'repeat' => esc_html__('Repeat', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'color2' => esc_html__('Second color', 'lepopup'), 'gradient' => esc_html__('Gradient', 'lepopup')), 'label' => esc_html__('Background', 'lepopup'), 'tooltip' => esc_html__('Adjust the background.', 'lepopup'), 'type' => 'background-style'),
					'border-style' => array('value' => array('width' => '0', 'style' => 'solid', 'radius' => '0', 'color' => '#ccc', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border.', 'lepopup'), 'type' => 'border-style'),
					'shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow.', 'lepopup'), 'type' => 'shadow'),
					'padding' => array('value' => array('top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'right' => esc_html__('Right', 'lepopup'), 'bottom' => esc_html__('Bottom', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Padding', 'lepopup'), 'tooltip' => esc_html__('Adjust the padding.', 'lepopup'), 'type' => 'padding'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '#lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this element', 'lepopup'), 'hide' => esc_html__('Hide this element', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
			),
			'video' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Video', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '560', 'height' => '315'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'content' => array('value' => '<iframe src="https://www.youtube.com/embed/jOSsb2GgcoU"></iframe>', 'label' => esc_html__('Embed Code / Video URL', 'lepopup'), 'tooltip' => esc_html__('Paste embed code provided by video hosting such as YouTube or Vimeo. It must be iframe-code. Also you can use standard video-tag here.', 'lepopup'), 'type' => 'textarea'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '#lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this element', 'lepopup'), 'hide' => esc_html__('Hide this element', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
			),
			'rectangle' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Rectangle / Square', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '200', 'height' => '120'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'background-style' => array('value' => array('image' => '', 'size' => 'auto', 'horizontal-position' => 'left', 'vertical-position' => 'top', 'repeat' => 'repeat', 'color' => '#f0f0f0', 'color2' => '', 'gradient' => 'no'), 'caption' => array('image' => esc_html__('Image URL', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'horizontal-position' => esc_html__('Horizontal position', 'lepopup'), 'vertical-position' => esc_html__('Verical position', 'lepopup'), 'repeat' => esc_html__('Repeat', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'color2' => esc_html__('Second color', 'lepopup'), 'gradient' => esc_html__('Gradient', 'lepopup')), 'label' => esc_html__('Background', 'lepopup'), 'tooltip' => esc_html__('Adjust the background.', 'lepopup'), 'type' => 'background-style'),
					'border-style' => array('value' => array('width' => '0', 'style' => 'solid', 'radius' => '0', 'color' => '#ccc', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border.', 'lepopup'), 'type' => 'border-style'),
					'shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow.', 'lepopup'), 'type' => 'shadow'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '#lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this element', 'lepopup'), 'hide' => esc_html__('Hide this element', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
			),
			'image' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Image', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '400', 'height' => '240'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'image-style' => array('value' => array('image' => plugins_url('/images/image-placeholder.png', __FILE__), 'size' => 'cover', 'horizontal-position' => 'center', 'vertical-position' => 'center', 'repeat' => 'no-repeat'), 'caption' => array('image' => esc_html__('Image URL', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'horizontal-position' => esc_html__('Horizontal position', 'lepopup'), 'vertical-position' => esc_html__('Verical position', 'lepopup'), 'repeat' => esc_html__('Repeat', 'lepopup'), 'color' => esc_html__('Background color', 'lepopup'), 'color2' => esc_html__('Second color', 'lepopup'), 'gradient' => esc_html__('Gradient', 'lepopup')), 'label' => esc_html__('Image', 'lepopup'), 'tooltip' => esc_html__('Adjust the image URL, position and size.', 'lepopup'), 'type' => 'image-style'),
					'link' => array('value' => '', 'label' => esc_html__('URL', 'lepopup'), 'type' => 'text', 'tooltip' => esc_html__('Specify the URL where users redirected to.', 'lepopup')),
					'new-tab' => array('value' => 'off', 'label' => esc_html__('Open link in new tab', 'lepopup'), 'tooltip' => esc_html__('If enabled, the link will be opened in new tab.', 'lepopup'), 'type' => 'checkbox'),
					'close' => array('value' => 'none', 'label' => esc_html__('Close', 'lepopup'), 'tooltip' => esc_html__('Adjust the mode of the closing.', 'lepopup'), 'type' => 'radio-bar', 'options' => array('none' => esc_html__('None', 'lepopup'), 'single' => esc_html__('Just close', 'lepopup'), 'period' => esc_html__('Close for period', 'lepopup'), 'forever' => esc_html__('Close forever', 'lepopup'))),
					'cookie-lifetime' => array('value' => '2', 'label' => esc_html__('Cookie lifetime', 'lepopup'), 'tooltip' => esc_html__('This is a period in days.', 'lepopup'), 'unit' => 'days', 'type' => 'units', 'visible' => array('close' => array('period'))),
					'onclick' => array('value' => '', 'label' => esc_html__('OnClick', 'lepopup'), 'tooltip' => esc_html__('This is the onclick handler of the button. Use a valid javascript code here.', 'lepopup'), 'type' => 'text'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'border-style' => array('value' => array('width' => '0', 'style' => 'solid', 'radius' => '0', 'color' => '#ccc', 'top' => 'on', 'right' => 'on', 'bottom' => 'on', 'left' => 'on'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'radius' => esc_html__('Radius', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'border' => esc_html__('Border', 'lepopup')), 'label' => esc_html__('Border', 'lepopup'), 'tooltip' => esc_html__('Adjust the border.', 'lepopup'), 'type' => 'border-style'),
					'shadow' => array('value' => array('style' => 'regular', 'size' => '', 'color' => '#444'), 'caption' => array('style' => esc_html__('Style', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup')), 'label' => esc_html__('Shadow', 'lepopup'), 'tooltip' => esc_html__('Adjust the shadow.', 'lepopup'), 'type' => 'shadow'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '#lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this element', 'lepopup'), 'hide' => esc_html__('Hide this element', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
			),
			'label' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Label', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size' => array('value' => array('width' => '200', 'height' => '40'), 'caption' => array('width' => esc_html__('Width', 'lepopup'), 'height' => esc_html__('Height', 'lepopup')), 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Set the size of the element.', 'lepopup'), 'type' => 'width-height'),
					'content' => array('value' => esc_html__('Hello World', 'lepopup'), 'label' => esc_html__('Label', 'lepopup'), 'tooltip' => esc_html__('This is the label.', 'lepopup'), 'type' => 'textarea-shortcodes'),
					'link' => array('value' => '', 'label' => esc_html__('URL', 'lepopup'), 'type' => 'text', 'tooltip' => esc_html__('Specify the URL where users redirected to.', 'lepopup')),
					'new-tab' => array('value' => 'off', 'label' => esc_html__('Open link in new tab', 'lepopup'), 'tooltip' => esc_html__('If enabled, the link will be opened in new tab.', 'lepopup'), 'type' => 'checkbox'),
					'close' => array('value' => 'none', 'label' => esc_html__('Close', 'lepopup'), 'tooltip' => esc_html__('Adjust the mode of the closing.', 'lepopup'), 'type' => 'radio-bar', 'options' => array('none' => esc_html__('None', 'lepopup'), 'single' => esc_html__('Just close', 'lepopup'), 'period' => esc_html__('Close for period', 'lepopup'), 'forever' => esc_html__('Close forever', 'lepopup'))),
					'cookie-lifetime' => array('value' => '2', 'label' => esc_html__('Cookie lifetime', 'lepopup'), 'tooltip' => esc_html__('This is a period in days.', 'lepopup'), 'unit' => 'days', 'type' => 'units', 'visible' => array('close' => array('period'))),
					'onclick' => array('value' => '', 'label' => esc_html__('OnClick', 'lepopup'), 'tooltip' => esc_html__('This is the onclick handler of the button. Use a valid javascript code here.', 'lepopup'), 'type' => 'text'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'text-style' => array('value' => array('family' => '', 'size' => '15', 'color' => '#444', 'weight' => '', 'italic' => 'off', 'underline' => 'off', 'align' => 'left'), 'caption' => array('family' => esc_html__('Font family', 'lepopup'), 'size' => esc_html__('Size', 'lepopup'), 'color' => esc_html__('Color', 'lepopup'), 'style' => esc_html__('Style', 'lepopup'), 'weight' => esc_html__('Font weight', 'lepopup'), 'align' => esc_html__('Alignment', 'lepopup')), 'label' => esc_html__('Text and font', 'lepopup'), 'tooltip' => esc_html__('Adjust the text and font.', 'lepopup'), 'type' => 'text-style'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '#lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this element', 'lepopup'), 'hide' => esc_html__('Hide this element', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
			),
			'close' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Close icon', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size-width' => array('value' => '40', 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Specify the size of the element.', 'lepopup'), 'unit' => 'px', 'type' => 'units'),
					'mode' => array('value' => 'single', 'label' => esc_html__('Mode', 'lepopup'), 'tooltip' => esc_html__('Adjust the mode of the close icon.', 'lepopup'), 'type' => 'radio-bar', 'options' => array('single' => esc_html__('Just close', 'lepopup'), 'period' => esc_html__('Close for period', 'lepopup'), 'forever' => esc_html__('Close forever', 'lepopup'))),
					'cookie-lifetime' => array('value' => '2', 'label' => esc_html__('Cookie lifetime', 'lepopup'), 'tooltip' => esc_html__('This is a period in days.', 'lepopup'), 'unit' => 'days', 'type' => 'units', 'visible' => array('mode' => array('period'))),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'view' => array('value' => 'basic', 'label' => esc_html__('View', 'lepopup'), 'tooltip' => esc_html__('Adjust the view of the close icon.', 'lepopup'), 'type' => 'radio-bar', 'options' => array('basic' => '×', 'fa-1' => '<i class="lepopup-if lepopup-if-times"></i>', 'fa-2' => '<i class="lepopup-if lepopup-if-cancel-circled"></i>', 'fa-3' => '<i class="lepopup-if lepopup-if-cancel-circled2"></i>')),
					'colors' => array('value' => array('color1' => '#FF5722', 'color2' => '#FF9800', 'color3' => ''), 'label' => esc_html__('Colors', 'lepopup'), 'tooltip' => esc_html__('Adjust the color of the close icon.', 'lepopup'), 'caption' => array('color1' => esc_html__('Main', 'lepopup'), 'color2' => esc_html__('Hover', 'lepopup'), 'color3' => esc_html__('Shadow', 'lepopup')), 'type' => 'three-colors'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							)
						)
					),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this element', 'lepopup'), 'hide' => esc_html__('Hide this element', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
			),
			'progress' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Progress Bar', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size-width' => array('value' => '320', 'label' => esc_html__('Width', 'lepopup'), 'tooltip' => esc_html__('Specify the width of the progress bar.', 'lepopup'), 'unit' => 'px', 'type' => 'units'),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'style-message' => array('message' => sprintf(esc_html__('Configure style of progress bar through %sPopup Settings >> Style Tab >> Progress Bar%s.', 'lepopup'), '<code>', '</code>'), 'type' => 'message'),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this element', 'lepopup'), 'hide' => esc_html__('Hide this element', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
			),
			'fa-icon' => array(
				'basic' => array('type' => 'tab', 'value' => 'basic', 'label' => esc_html__('Basic', 'lepopup')),
					'name' => array('value' => esc_html__('Font Awesome', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name will be shown in place of the label throughout the plugin.', 'lepopup'), 'type' => 'text'),
					'position' => array('value' => array('top' => '0', 'left' => '0'), 'caption' => array('top' => esc_html__('Top', 'lepopup'), 'left' => esc_html__('Left', 'lepopup')), 'label' => esc_html__('Position', 'lepopup'), 'tooltip' => esc_html__('Set the position of the element relative to the upper left corner of the base frame.', 'lepopup'), 'type' => 'top-left'),
					'size-width' => array('value' => '30', 'label' => esc_html__('Size', 'lepopup'), 'tooltip' => esc_html__('Specify the size of the element.', 'lepopup'), 'unit' => 'px', 'type' => 'units'),
					'icon' => array('value' => '', 'label' => esc_html__('Icon', 'lepopup'), 'tooltip' => esc_html__('Select the icon.', 'lepopup'), 'type' => 'icon'),
					'link' => array('value' => '', 'label' => esc_html__('URL', 'lepopup'), 'type' => 'text', 'tooltip' => esc_html__('Specify the URL where users redirected to.', 'lepopup')),
					'new-tab' => array('value' => 'off', 'label' => esc_html__('Open link in new tab', 'lepopup'), 'tooltip' => esc_html__('If enabled, the link will be opened in new tab.', 'lepopup'), 'type' => 'checkbox'),
					'close' => array('value' => 'none', 'label' => esc_html__('Close', 'lepopup'), 'tooltip' => esc_html__('Adjust the mode of the closing.', 'lepopup'), 'type' => 'radio-bar', 'options' => array('none' => esc_html__('None', 'lepopup'), 'single' => esc_html__('Just close', 'lepopup'), 'period' => esc_html__('Close for period', 'lepopup'), 'forever' => esc_html__('Close forever', 'lepopup'))),
					'cookie-lifetime' => array('value' => '2', 'label' => esc_html__('Cookie lifetime', 'lepopup'), 'tooltip' => esc_html__('This is a period in days.', 'lepopup'), 'unit' => 'days', 'type' => 'units', 'visible' => array('close' => array('period'))),
					'animation' => array('value' => array('in' => 'fadeIn', 'duration' => '1000', 'delay' => '0', 'out' => 'fadeOut'), 'caption' => array('in' => esc_html__('Appearance', 'lepopup'), 'duration' => esc_html__('Duration', 'lepopup'), 'delay' => esc_html__('Start delay', 'lepopup'), 'out' => esc_html__('Disappearance', 'lepopup')), 'label' => esc_html__('Animation', 'lepopup'), 'tooltip' => esc_html__('Adjust the appearance and disappearance effect and duration.', 'lepopup'), 'type' => 'animation'),
				'style' => array('type' => 'tab', 'value' => 'style', 'label' => esc_html__('Style', 'lepopup')),
					'colors' => array('value' => array('color1' => '', 'color2' => '', 'color3' => ''), 'label' => esc_html__('Colors', 'lepopup'), 'tooltip' => esc_html__('Adjust the color of the icon.', 'lepopup'), 'caption' => array('color1' => esc_html__('Main', 'lepopup'), 'color2' => esc_html__('Hover', 'lepopup'), 'color3' => esc_html__('Shadow', 'lepopup')), 'type' => 'three-colors'),
					'css' => array('type' => 'css', 'values' => array(), 'label' => esc_html__('CSS styles', 'lepopup'), 'tooltip' => esc_html__('Once you have added a style, enter the CSS styles.', 'lepopup'), 'selectors' => array(
							'wrapper' => array(
								'label' => esc_html__('Wrapper', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id}',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id}'
							),
							'icon' => array(
								'label' => esc_html__('Icon', 'lepopup'),
								'admin-class' => '.lepopup-element-{element-id} a i',
								'front-class' => '.lepopup-form-{form-id} .lepopup-element-{element-id} a i'
							)
						)
					),
				'logic-tab' => array('type' => 'tab', 'value' => 'logic', 'label' => esc_html__('Logic', 'lepopup')),
					'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'checkbox'),
					'logic' => array('values' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Show this button', 'lepopup'), 'hide' => esc_html__('Hide this button', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show or hide this element depending on the values of other fields.', 'lepopup'), 'type' => 'logic-rules', 'visible' => array('logic-enable' => array('on'))),
			)
		);
		if ($this->options['mask-enable'] != 'on') {
			unset($this->element_properties_meta['text']['mask']);
		}
		if (defined('UAP_CORE')) {
			unset($this->element_properties_meta['settings']['personal-keys']);
		}
		$this->toolbar_tools = array(
			'rectangle' => array(
				'title' => esc_html__('Rectangle / Square', 'lepopup'),
				'icon' => 'far fa-square',
				'type' => 'other'
			),
			'image' => array(
				'title' => esc_html__('Image', 'lepopup'),
				'icon' => 'far fa-image',
				'type' => 'other'
			),
			'video' => array(
				'title' => esc_html__('Video', 'lepopup'),
				'icon' => 'fas fa-video',
				'type' => 'other'
			),
			'label' => array(
				'title' => esc_html__('Label', 'lepopup'),
				'icon' => 'fas fa-font',
				'type' => 'other'
			),
			'html' => array(
				'title' => esc_html__('HTML', 'lepopup'),
				'icon' => 'fas fa-code',
				'type' => 'other'
			),
			'close' => array(
				'title' => esc_html__('Close Icon', 'lepopup'),
				'icon' => 'fas fa-times',
				'type' => 'other'
			),
			'fa-icon' => array(
				'title' => esc_html__('Font Awesome Icon', 'lepopup'),
				'icon' => 'fab fa-font-awesome',
				'type' => 'other'
			),
			'progress' => array(
				'title' => esc_html__('Progress Bar', 'lepopup'),
				'icon' => 'fas fa-ruler-horizontal',
				'type' => 'other'
			),
			'link-button' => array(
				'title' => esc_html__('Link Button', 'lepopup'),
				'icon' => 'fas fa-link',
				'type' => 'other'
			),
			'text' => array(
				'title' => esc_html__('Text Field', 'lepopup'),
				'icon' => 'fas fa-pencil-alt',
				'type' => 'input'
			),
			'email' => array(
				'title' => esc_html__('Email Field', 'lepopup'),
				'icon' => 'far fa-envelope',
				'type' => 'input'
			),
			'number' => array(
				'title' => esc_html__('Number Field', 'lepopup'),
				'icon' => 'far lepopup-number-icon',
				'type' => 'input'
			),
			'numspinner' => array(
				'title' => esc_html__('Numeric Spinner', 'lepopup'),
				'icon' => 'fas fa-sort-numeric-down',
				'type' => 'input'
			),
			'textarea' => array(
				'title' => esc_html__('Textarea Field', 'lepopup'),
				'icon' => 'fas fa-align-left',
				'type' => 'input'
			),
			'select' => array(
				'title' => esc_html__('Select Box', 'lepopup'),
				'icon' => 'far fa-caret-square-down',
				'type' => 'input'
			),
			'checkbox' => array(
				'title' => esc_html__('Checkbox', 'lepopup'),
				'icon' => 'far fa-check-square',
				'type' => 'input'
			),
			'radio' => array(
				'title' => esc_html__('Radio Button', 'lepopup'),
				'icon' => 'far fa-dot-circle',
				'type' => 'input'
			),
			'multiselect' => array(
				'title' => esc_html__('Multiselect', 'lepopup'),
				'icon' => 'fas fa-list-ul',
				'type' => 'input'
			),
			'imageselect' => array(
				'title' => esc_html__('Image Select', 'lepopup'),
				'icon' => 'far fa-images',
				'type' => 'input'
			),
			'tile' => array(
				'title' => esc_html__('Tile', 'lepopup'),
				'icon' => 'far lepopup-tile-icon',
				'type' => 'input'
			),
			'date' => array(
				'title' => esc_html__('Date Field', 'lepopup'),
				'icon' => 'far fa-calendar-alt',
				'type' => 'input'
			),
			'time' => array(
				'title' => esc_html__('Time Field', 'lepopup'),
				'icon' => 'far fa-clock',
				'type' => 'input'
			),
			'file' => array(
				'title' => esc_html__('File Upload', 'lepopup'),
				'icon' => 'fas fa-upload',
				'type' => 'input'
			),
			'password' => array(
				'title' => esc_html__('Password Field', 'lepopup'),
				'icon' => 'fas fa-lock',
				'type' => 'input'
			),
			'signature' => array(
				'title' => esc_html__('Signature Pad', 'lepopup'),
				'icon' => 'fas fa-signature',
				'type' => 'input'
			),
			'rangeslider' => array(
				'title' => esc_html__('Range Slider', 'lepopup'),
				'icon' => 'fas fa-sliders-h',
				'type' => 'input'
			),
			'star-rating' => array(
				'title' => esc_html__('Star Rating', 'lepopup'),
				'icon' => 'far fa-star',
				'type' => 'input'
			),
			'hidden' => array(
				'title' => esc_html__('Hidden Field', 'lepopup'),
				'icon' => 'far fa-eye-slash',
				'type' => 'input'
			),
			'button' => array(
				'title' => esc_html__('Button', 'lepopup'),
				'icon' => 'far fa-paper-plane',
				'type' => 'submit'
			)
		);
		if ($this->options['signature-enable'] != 'on') {
			unset($this->toolbar_tools['signature']);
		}
		if ($this->options['range-slider-enable'] != 'on') {
			unset($this->toolbar_tools['rangeslider']);
			unset($this->element_properties_meta['settings']['rangeslider-skin']);
			unset($this->element_properties_meta['settings']['rangeslider-color']);
		}
		$this->filters_meta = array(
			'alpha' => array(
				'label' => esc_html__('Alpha', 'lepopup'),
				'tooltip' => esc_html__('Removes any non-alphabet characters.', 'lepopup'),
				'properties' => array(
					'whitespace-allowed' => array('value' => 'off', 'label' => esc_html__('Allow whitespace', 'lepopup'), 'tooltip' => esc_html__('If checked, any spaces or tabs will not be stripped.', 'lepopup'), 'type' => 'checkbox')
				)
			),
			'alphanumeric' => array(
				'label' => esc_html__('Alphanumeric', 'lepopup'),
				'tooltip' => esc_html__('Removes any non-alphabet characters and non-digits.', 'lepopup'),
				'properties' => array(
					'whitespace-allowed' => array('value' => 'off', 'label' => esc_html__('Allow whitespace', 'lepopup'), 'tooltip' => esc_html__('If checked, any spaces or tabs will not be stripped.', 'lepopup'), 'type' => 'checkbox')
				)
			),
			'digits' => array(
				'label' => esc_html__('Digits', 'lepopup'),
				'tooltip' => esc_html__('Removes any non-digits.', 'lepopup'),
				'properties' => array(
					'whitespace-allowed' => array('value' => 'off', 'label' => esc_html__('Allow whitespace', 'lepopup'), 'tooltip' => esc_html__('If checked, any spaces or tabs will not be stripped.', 'lepopup'), 'type' => 'checkbox')
				)
			),
			'regex' => array(
				'label' => esc_html__('Regex', 'lepopup'),
				'tooltip' => esc_html__('Removes characters matching the given regular expression.', 'lepopup'),
				'properties' => array(
					'pattern' => array('value' => '', 'label' => esc_html__('Pattern', 'lepopup'), 'tooltip' => esc_html__('Any text matching this regular expression pattern will be stripped. The pattern should include start and end delimiters, see below for an example.', 'lepopup').'<br /><br /><code>/[^a-zA-Z0-9]/</code>', 'type' => 'text')
				)
			),
			'strip-tags' => array(
				'label' => esc_html__('Strip Tags', 'lepopup'),
				'tooltip' => esc_html__('Removes any HTML tags.', 'lepopup'),
				'properties' => array(
					'tags-allowed' => array('value' => '', 'label' => esc_html__('Allowable tags', 'lepopup'), 'tooltip' => esc_html__('Enter allowable tags, one after the other, see below for an example.', 'lepopup').'<br /><br /><code>&amp;lt;p&amp;gt;&amp;lt;a&amp;gt;&amp;lt;span&amp;gt;</code>', 'type' => 'text')
				)
			),
			'trim' => array(
				'label' => esc_html__('Trim', 'lepopup'),
				'tooltip' => esc_html__('Removes white space from the start and end.', 'lepopup')
			)
		);
		$this->validators_meta = array(
			'alpha' => array(
				'label' => esc_html__('Alpha', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value contains only alphabet characters.', 'lepopup'),
				'properties' => array(
					'whitespace-allowed' => array('value' => 'off', 'label' => esc_html__('Allow whitespace', 'lepopup'), 'tooltip' => esc_html__('If checked, any spaces or tabs are allowed.', 'lepopup'), 'type' => 'checkbox'),
					'error' => array('value' => esc_html__('Only alphabet characters are allowed.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'alphanumeric' => array(
				'label' => esc_html__('Alphanumeric', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value contains only alphabet characters or digits.', 'lepopup'),
				'properties' => array(
					'whitespace-allowed' => array('value' => 'off', 'label' => esc_html__('Allow whitespace', 'lepopup'), 'tooltip' => esc_html__('If checked, any spaces or tabs are allowed.', 'lepopup'), 'type' => 'checkbox'),
					'error' => array('value' => esc_html__('Only alphabet characters and digits are allowed.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'date' => array(
				'label' => esc_html__('Date', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value is a valid date (according to pre-defined date format set on Popup Settings).', 'lepopup'),
				'properties' => array(
					'error' => array('value' => esc_html__('Invalid date.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'digits' => array(
				'label' => esc_html__('Digits', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value contains only digits.', 'lepopup'),
				'properties' => array(
					'whitespace-allowed' => array('value' => 'off', 'label' => esc_html__('Allow whitespace', 'lepopup'), 'tooltip' => esc_html__('If checked, any spaces or tabs are allowed.', 'lepopup'), 'type' => 'checkbox'),
					'error' => array('value' => esc_html__('Only digits are allowed.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'email' => array(
				'label' => esc_html__('Email', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value is a valid email address.', 'lepopup'),
				'properties' => array(
					'error' => array('value' => esc_html__('Invalid email address.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'equal' => array(
				'label' => esc_html__('Equal', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value is identical to the given token.', 'lepopup'),
				'properties' => array(
					'token' => array('value' => '', 'label' => esc_html__('Token', 'lepopup'), 'tooltip' => esc_html__('The token that the submitted value must be equal to.', 'lepopup'), 'type' => 'text'),
					'error' => array('value' => esc_html__('The value does not match {token}.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code><br /><code>{token} = '.esc_html__('the token', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'equal-field' => array(
				'label' => esc_html__('Equal To Field', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value is identical to the value of other field.', 'lepopup'),
				'properties' => array(
					'token' => array('value' => '', 'label' => esc_html__('Field', 'lepopup'), 'tooltip' => esc_html__('The field that the submitted value must be equal to.', 'lepopup'), 'type' => 'field'),
					'error' => array('value' => esc_html__('The value does not match.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'greater' => array(
				'label' => esc_html__('Greater Than', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value is numerically greater than the given minimum.', 'lepopup'),
				'properties' => array(
					'min' => array('value' => '0', 'label' => esc_html__('Minimum', 'lepopup'), 'tooltip' => esc_html__('The submitted value must be numerically greater than the minimum.', 'lepopup'), 'type' => 'integer'),
					'error' => array('value' => esc_html__('The value is not greater than {min}.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code><br /><code>{min} = '.esc_html__('the minimum allowed value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'iban' => array(
				'label' => esc_html__('IBAN', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value is a valid International Bank Account Number.', 'lepopup'),
				'properties' => array(
					'error' => array('value' => esc_html__('Invalid IBAN.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'in-array' => array(
				'label' => esc_html__('In Array', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value is in a list of allowed values.', 'lepopup'),
				'properties' => array(
					'values' => array('value' => '', 'label' => esc_html__('Allowed values', 'lepopup'), 'tooltip' => esc_html__('Enter one allowed value per line.', 'lepopup'), 'type' => 'textarea'),
					'invert' => array('value' => 'off', 'label' => esc_html__('Invert', 'lepopup'), 'tooltip' => esc_html__('Invert the check i.e. the submitted value must not be in the allowed values list.', 'lepopup'), 'type' => 'checkbox'),
					'error' => array('value' => esc_html__('This value is not valid.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'length' => array(
				'label' => esc_html__('Length', 'lepopup'),
				'tooltip' => esc_html__('Checks that the length of the value is between the given maximum and minimum.', 'lepopup'),
				'properties' => array(
					'min' => array('value' => '0', 'label' => esc_html__('Minimum length', 'lepopup'), 'tooltip' => esc_html__('The length of the submitted value must be greater than or equal to the minimum.', 'lepopup'), 'type' => 'integer'),
					'max' => array('value' => '0', 'label' => esc_html__('Maximum length', 'lepopup'), 'tooltip' => esc_html__('The length of the submitted value must be less than or equal to the maximum.', 'lepopup'), 'type' => 'integer'),
					'error' => array('value' => esc_html__('The number of characters must be in a range [{min}..{max}].', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code><br /><code>{length} = '.esc_html__('the length of the submitted value', 'lepopup').'</code><br /><code>{min} = '.esc_html__('the minimum allowed length', 'lepopup').'</code><br /><code>{max} = '.esc_html__('the maximum allowed length', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'less' => array(
				'label' => esc_html__('Less Than', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value is numerically less than the given maximum.', 'lepopup'),
				'properties' => array(
					'max' => array('value' => '0', 'label' => esc_html__('Maximum', 'lepopup'), 'tooltip' => esc_html__('The submitted value must be numerically less than the maximum.', 'lepopup'), 'type' => 'integer'),
					'error' => array('value' => esc_html__('The value is not less than {max}.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code><br /><code>{max} = '.esc_html__('the maximum allowed value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'prevent-duplicates' => array(
				'label' => esc_html__('Prevent Duplicates', 'lepopup'),
				'tooltip' => esc_html__('Checks that the same value has not already been submitted.', 'lepopup'),
				'properties' => array(
					'error' => array('value' => esc_html__('This value is a duplicate of a previously submitted form.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'regex' => array(
				'label' => esc_html__('Regex', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value matches the given regular expression.', 'lepopup'),
				'properties' => array(
					'pattern' => array('value' => '', 'label' => esc_html__('Pattern', 'lepopup'), 'tooltip' => esc_html__('The submitted value must match this regular expression. The pattern should include start and end delimiters, see below for an example.', 'lepopup').'<br /><br /><code>/[^a-zA-Z0-9]/</code>', 'type' => 'text'),
					'invert' => array('value' => 'off', 'label' => esc_html__('Invert', 'lepopup'), 'tooltip' => esc_html__('Invert the check i.e. the submitted value must not match the regular expression.', 'lepopup'), 'type' => 'checkbox'),
					'error' => array('value' => esc_html__('Invalid value.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'time' => array(
				'label' => esc_html__('Time', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value is a valid time (according to pre-defined time format set on Popup Settings).', 'lepopup'),
				'properties' => array(
					'error' => array('value' => esc_html__('Invalid time.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
			'url' => array(
				'label' => esc_html__('URL', 'lepopup'),
				'tooltip' => esc_html__('Checks that the value is a valid URL.', 'lepopup'),
				'properties' => array(
					'error' => array('value' => esc_html__('Invalid URL.', 'lepopup'), 'label' => esc_html__('Error message', 'lepopup'), 'tooltip' => esc_html__('Variables:', 'lepopup').'<br /><br /><code>{value} = '.esc_html__('the submitted value', 'lepopup').'</code>', 'type' => 'error')
				)
			),
		);
		$this->logic_rules = array(
			'is' => esc_html__('is', 'lepopup'),
			'is-not' => esc_html__('is not', 'lepopup'),
			'is-empty' => esc_html__('is empty', 'lepopup'),
			'is-not-empty' => esc_html__('is not empty', 'lepopup'),
			'is-greater' => esc_html__('is greater than', 'lepopup'),
			'is-less' => esc_html__('is less than', 'lepopup'),
			'contains' => esc_html__('contains', 'lepopup'),
			'starts-with' => esc_html__('starts with', 'lepopup'),
			'ends-with' => esc_html__('ends with', 'lepopup')
		);
		$this->confirmations_meta = array(
			'name' => array('value' => esc_html__('Confirmation', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name of the confirmation. It is used for your convenience.', 'lepopup'), 'type' => 'name'),
			'type' => array('value' => 'close', 'label' => esc_html__('Type', 'lepopup'), 'tooltip' => esc_html__('Choose the type of the confirmation.', 'lepopup'), 'type' => 'select', 'options' => array('close' => esc_html__('Close', 'lepopup'), 'page' => esc_html__('Display Confirmation page', 'lepopup'), 'page-redirect' => esc_html__('Display Confirmation page and redirect to certain URL', 'lepopup'), 'page-payment' => esc_html__('Display Confirmation page and request payment', 'lepopup'), 'redirect' => esc_html__('Redirect to certain URL', 'lepopup'), 'payment' => esc_html__('Request payment', 'lepopup'), 'form' => esc_html__('Display popup', 'lepopup'))),
			'form' => array('value' => '', 'label' => esc_html__('Popup', 'lepopup'), 'tooltip' => esc_html__('User will be redirected to this URL after successful form submission.', 'lepopup'), 'type' => 'text'),
			'payment-gateway' => array('value' => '', 'label' => esc_html__('Payment gateway', 'lepopup'), 'tooltip' => esc_html__('Select payment gateway. You can configure it on "Advanced" tab, "Payment Gateways" section.', 'lepopup'), 'type' => 'text'),
			'url' => array('value' => get_bloginfo('url'), 'label' => esc_html__('URL', 'lepopup'), 'tooltip' => esc_html__('User will be redirected to this URL after successful form submission.', 'lepopup'), 'type' => 'text'),
			'delay' => array('value' => "3", 'label' => esc_html__('Delay', 'lepopup'), 'tooltip' => esc_html__('Confirmation page (or thanksgiving popup) stays visible during this number of seconds.', 'lepopup'), 'type' => 'integer', 'unit' => esc_html__('seconds', 'lepopup')),
			'reset-form' => array('value' => 'on', 'label' => esc_html__('Reset form to default state', 'lepopup'), 'tooltip' => esc_html__('If enabled, the popup will be reset to default state.', 'lepopup'), 'type' => 'checkbox'),
			'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to enable this confirmation depending on the values of input fields.', 'lepopup'), 'type' => 'checkbox'),
			'logic' => array('value' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Enable this confirmation', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show this confirmation depending on the values of input fields.', 'lepopup'), 'type' => 'logic-rules')
		);
		$this->notifications_meta = array(
			'name' => array('value' => esc_html__('Notification', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name of the notification. It is used for your convenience.', 'lepopup'), 'type' => 'name'),
			'enabled' => array('value' => 'on', 'label' => esc_html__('Enabled', 'lepopup'), 'tooltip' => esc_html__('You can stop this notification being sent by turning this off.', 'lepopup'), 'type' => 'checkbox'),
			'action' => array('value' => 'submit', 'label' => esc_html__('Send', 'lepopup'), 'tooltip' => esc_html__('You can specify when notification will be sent.', 'lepopup'), 'type' => 'select', 'options' => array('submit' => esc_html__('After successful form submission', 'lepopup'), 'confirm' => esc_html__('When user confirmed submitted data using native double opt-in feature', 'lepopup'), 'payment-success' => esc_html__('After successfully completed payment', 'lepopup'), 'payment-fail' => esc_html__('After non-completed payment', 'lepopup'))),
			'recipient-email' => array('value' => '', 'label' => esc_html__('Recipient', 'lepopup'), 'tooltip' => esc_html__('Add email addresses (comma-separated) to which this email will be sent to.', 'lepopup'), 'type' => 'text'),
			'subject' => array('value' => esc_html__('New submission from {{form-name}}', 'lepopup'), 'label' => esc_html__('Subject', 'lepopup'), 'tooltip' => esc_html__('The subject of the email message.', 'lepopup'), 'type' => 'text'),
			'message' => array('value' => '{{form-data}}', 'label' => esc_html__('Message', 'lepopup'), 'tooltip' => esc_html__('The content of the email message.', 'lepopup'), 'type' => 'textarea-shortcodes'),
			'attachments' => array('value' => array(), 'label' => esc_html__('Attachments', 'lepopup'), 'tooltip' => esc_html__('Select files that you want to attach to the email message.', 'lepopup'), 'type' => 'attachments'),
			'from' => array('value' => array('email' => '{{global-from-email}}', 'name' => '{{global-from-name}}'), 'label' => esc_html__('From', 'lepopup'), 'tooltip' => esc_html__('Sets the "From" address and name. The email address and name set here will be shown as the sender of the email.', 'lepopup'), 'type' => 'from'),
			'reply-email' => array('value' => '', 'label' => esc_html__('Reply-To', 'lepopup'), 'tooltip' => esc_html__('Add a "Reply-To" email address. If not set, replying to the email will reply to the "From" address.', 'lepopup'), 'type' => 'text'),
			'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to enable this notification depending on the values of input fields.', 'lepopup'), 'type' => 'checkbox'),
			'logic' => array('value' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Enable this notification', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to show this notification depending on the values of input fields.', 'lepopup'), 'type' => 'logic-rules')
		);
		$this->math_meta = array(
			'id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the expression.', 'lepopup'), 'type' => 'id'),
			'name' => array('value' => esc_html__('Expression', 'lepopup'), 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name of the expression. It is used for your convenience.', 'lepopup'), 'type' => 'name'),
			'expression' => array('value' => '', 'label' => esc_html__('Expression', 'lepopup'), 'tooltip' => esc_html__('Type math expression here. Use basic arithmetic operators:', 'lepopup').' <code>-, +, *, /</code>.', 'type' => 'text'),
			'default' => array('value' => '0', 'label' => esc_html__('Default', 'lepopup'), 'tooltip' => esc_html__('This value is used if expression can not be calculated (for example, in case of division by zero, typos, missed variables, non-numeric values, etc.).', 'lepopup'), 'type' => 'text'),
			'decimal-digits' => array('value' => "2", 'label' => esc_html__('Decimal digits', 'lepopup'), 'tooltip' => esc_html__('Specify how many decimal digits the result must have.', 'lepopup'), 'type' => 'integer')
		);
		$this->integrations_meta = array(
			'name' => array('value' => '', 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name of the integration. It is used for your convenience.', 'lepopup'), 'type' => 'name'),
			'enabled' => array('value' => 'on', 'label' => esc_html__('Enabled', 'lepopup'), 'tooltip' => esc_html__('You can disable this integration by turning this off.', 'lepopup'), 'type' => 'checkbox'),
			'action' => array('value' => 'submit', 'label' => esc_html__('Execute', 'lepopup'), 'tooltip' => esc_html__('You can specify when integration will be executed.', 'lepopup'), 'type' => 'select', 'options' => array('submit' => esc_html__('After successful form submission', 'lepopup'), 'confirm' => esc_html__('When user confirmed submitted data using native double opt-in feature', 'lepopup'), 'payment-success' => esc_html__('After successfully completed payment', 'lepopup'), 'payment-fail' => esc_html__('After non-completed payment', 'lepopup'))),
			'logic-enable' => array('value' => 'off', 'label' => esc_html__('Enable conditional logic', 'lepopup'), 'tooltip' => esc_html__('If enabled, you can create rules to enable this integration depending on the values of input fields.', 'lepopup'), 'type' => 'checkbox'),
			'logic' => array('value' => array('action' => 'show', 'operator' => 'and', 'rules' => array()), 'actions' => array('show' => esc_html__('Enable this integration', 'lepopup')), 'operators' => array('and' => esc_html__('if all of these rules match', 'lepopup'), 'or' => esc_html__('if any of these rules match', 'lepopup')), 'label' => esc_html__('Logic rules', 'lepopup'), 'tooltip' => esc_html__('Create rules to enable this integration depending on the values of input fields.', 'lepopup'), 'type' => 'logic-rules')
		);
		$this->payment_gateways_meta = array(
			'id' => array('value' => '', 'label' => esc_html__('ID', 'lepopup'), 'tooltip' => esc_html__('The unique ID of the payment gateway.', 'lepopup'), 'type' => 'id'),
			'name' => array('value' => '', 'label' => esc_html__('Name', 'lepopup'), 'tooltip' => esc_html__('The name of the payment gateway. It is used for your convenience.', 'lepopup'), 'type' => 'name')
		);
		if (!in_array('curl', get_loaded_extensions())) {
			$this->advanced_options = array_merge($this->advanced_options, array(
				'enable-htmlform' => 'off',
				'enable-post' => 'off',
				'enable-acellemail' => 'off',
				'enable-activecampaign' => 'off',
				'enable-activetrail' => 'off',
				'enable-agilecrm' => 'off',
				'enable-automizy' => 'off',
				'enable-avangemail' => 'off',
				'enable-authorizenet' => 'off',
				'enable-aweber' => 'off',
				'enable-birdsend' => 'off',
				'enable-bitrix24' => 'off',
				'enable-campaignmonitor' => 'off',
				'enable-cleverreach' => 'off',
				'enable-constantcontact' => 'off',
				'enable-conversio' => 'off',
				'enable-convertkit' => 'off',
				'enable-drip' => 'off',
				'enable-egoi' => 'off',
				'enable-emailoctopus' => 'off',
				'enable-freshmail' => 'off',
				'enable-getresponse' => 'off',
				'enable-gist' => 'off',
				'enable-hubspot' => 'off',
				'enable-inbox' => 'off',
				'enable-infomaniak' => 'off',
				'enable-intercom' => 'off',
				'enable-klaviyo' => 'off',
				'enable-madmimi' => 'off',
				'enable-mailautic' => 'off',
				'enable-mailchimp' => 'off',
				'enable-mailerlite' => 'off',
				'enable-mailfit' => 'off',
				'enable-mailgun' => 'off',
				'enable-mailjet' => 'off',
				'enable-mailrelay' => 'off',
				'enable-mailwizz' => 'off',
				'enable-mautic' => 'off',
				'enable-moosend' => 'off',
				'enable-mumara' => 'off',
				'enable-newsman' => 'off',
				'enable-omnisend' => 'off',
				'enable-ontraport' => 'off',
				'enable-pipedrive' => 'off',
				'enable-rapidmail' => 'off',
				'enable-salesflare' => 'off',
				'enable-salesautopilot' => 'off',
				'enable-sendfox' => 'off',
				'enable-sendgrid' => 'off',
				'enable-sendinblue' => 'off',
				'enable-sendpulse' => 'off',
				'enable-sendy' => 'off',
				'enable-sgautorepondeur' => 'off',
				'enable-socketlabs' => 'off',
				'enable-verticalresponse' => 'off',
				'enable-ymlp' => 'off',
				'enable-zohocrm' => 'off',
				'enable-zapier' => 'off',
				'enable-blockchain' => 'off',
				'enable-instamojo' => 'off',
				'enable-mollie' => 'off',
				'enable-skrill' => 'off',
				'enable-payfast' => 'off',
				'enable-paypal' => 'off',
				'enable-paystack' => 'off',
				'enable-payumoney' => 'off',
				'enable-razorpay' => 'off',
				'enable-stripe' => 'off',
				'enable-wepay' => 'off',
				'enable-bulkgate' => 'off',
				'enable-gatewayapi' => 'off',
				'enable-nexmo' => 'off',
				'enable-twilio' => 'off',
				'enable-clearout' => 'off',
				'enable-emaillistvalidation' => 'off',
				'enable-emaillistverify' => 'off',
				'enable-kickbox' => 'off',
				'enable-thechecker' => 'off',
				'enable-truemail' => 'off',
				'enable-geoipdetect' => 'off',
				'enable-ipstack' => 'off'
			));
		}
		if (file_exists(dirname(__FILE__).'/modules/customjs.php') && $this->advanced_options['enable-custom-js'] == 'on') include_once(dirname(__FILE__).'/modules/customjs.php');
		
		if (file_exists(dirname(__FILE__).'/modules/acellemail.php') && $this->advanced_options['enable-acellemail'] == 'on') include_once(dirname(__FILE__).'/modules/acellemail.php');
		if (file_exists(dirname(__FILE__).'/modules/activecampaign.php') && $this->advanced_options['enable-activecampaign'] == 'on') include_once(dirname(__FILE__).'/modules/activecampaign.php');
		if (file_exists(dirname(__FILE__).'/modules/activetrail.php') && $this->advanced_options['enable-activetrail'] == 'on') include_once(dirname(__FILE__).'/modules/activetrail.php');
		if (file_exists(dirname(__FILE__).'/modules/agilecrm.php') && $this->advanced_options['enable-agilecrm'] == 'on') include_once(dirname(__FILE__).'/modules/agilecrm.php');
		if (file_exists(dirname(__FILE__).'/modules/automizy.php') && $this->advanced_options['enable-automizy'] == 'on') include_once(dirname(__FILE__).'/modules/automizy.php');
		if (file_exists(dirname(__FILE__).'/modules/avangemail.php') && $this->advanced_options['enable-avangemail'] == 'on') include_once(dirname(__FILE__).'/modules/avangemail.php');
		if (file_exists(dirname(__FILE__).'/modules/aweber.php') && $this->advanced_options['enable-aweber'] == 'on') include_once(dirname(__FILE__).'/modules/aweber.php');
		if (file_exists(dirname(__FILE__).'/modules/birdsend.php') && $this->advanced_options['enable-birdsend'] == 'on') include_once(dirname(__FILE__).'/modules/birdsend.php');
		if (file_exists(dirname(__FILE__).'/modules/bitrix24.php') && $this->advanced_options['enable-bitrix24'] == 'on') include_once(dirname(__FILE__).'/modules/bitrix24.php');
		if (file_exists(dirname(__FILE__).'/modules/bulkgate.php') && $this->advanced_options['enable-bulkgate'] == 'on') include_once(dirname(__FILE__).'/modules/bulkgate.php');
		if (file_exists(dirname(__FILE__).'/modules/campaignmonitor.php') && $this->advanced_options['enable-campaignmonitor'] == 'on') include_once(dirname(__FILE__).'/modules/campaignmonitor.php');
		if (file_exists(dirname(__FILE__).'/modules/cleverreach.php') && $this->advanced_options['enable-cleverreach'] == 'on') include_once(dirname(__FILE__).'/modules/cleverreach.php');
		if (file_exists(dirname(__FILE__).'/modules/constantcontact.php') && $this->advanced_options['enable-constantcontact'] == 'on') include_once(dirname(__FILE__).'/modules/constantcontact.php');
		if (file_exists(dirname(__FILE__).'/modules/conversio.php') && $this->advanced_options['enable-conversio'] == 'on') include_once(dirname(__FILE__).'/modules/conversio.php');
		if (file_exists(dirname(__FILE__).'/modules/convertkit.php') && $this->advanced_options['enable-convertkit'] == 'on') include_once(dirname(__FILE__).'/modules/convertkit.php');
		if (file_exists(dirname(__FILE__).'/modules/drip.php') && $this->advanced_options['enable-drip'] == 'on') include_once(dirname(__FILE__).'/modules/drip.php');
		if (file_exists(dirname(__FILE__).'/modules/egoi.php') && $this->advanced_options['enable-egoi'] == 'on') include_once(dirname(__FILE__).'/modules/egoi.php');
		if (file_exists(dirname(__FILE__).'/modules/emailoctopus.php') && $this->advanced_options['enable-emailoctopus'] == 'on') include_once(dirname(__FILE__).'/modules/emailoctopus.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/fluentcrm.php') && $this->advanced_options['enable-fluentcrm'] == 'on') include_once(dirname(__FILE__).'/modules/fluentcrm.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/freshmail.php') && $this->advanced_options['enable-freshmail'] == 'on') include_once(dirname(__FILE__).'/modules/freshmail.php');
		if (file_exists(dirname(__FILE__).'/modules/gatewayapi.php') && $this->advanced_options['enable-gatewayapi'] == 'on') include_once(dirname(__FILE__).'/modules/gatewayapi.php');
		if (file_exists(dirname(__FILE__).'/modules/getresponse.php') && $this->advanced_options['enable-getresponse'] == 'on') include_once(dirname(__FILE__).'/modules/getresponse.php');
		if (file_exists(dirname(__FILE__).'/modules/gist.php') && $this->advanced_options['enable-gist'] == 'on') include_once(dirname(__FILE__).'/modules/gist.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/groundhogg.php') && $this->advanced_options['enable-groundhogg'] == 'on') include_once(dirname(__FILE__).'/modules/groundhogg.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/hubspot.php') && $this->advanced_options['enable-hubspot'] == 'on') include_once(dirname(__FILE__).'/modules/hubspot.php');
		if (file_exists(dirname(__FILE__).'/modules/inbox.php') && $this->advanced_options['enable-inbox'] == 'on') include_once(dirname(__FILE__).'/modules/inbox.php');
		if (file_exists(dirname(__FILE__).'/modules/infomaniak.php') && $this->advanced_options['enable-infomaniak'] == 'on') include_once(dirname(__FILE__).'/modules/infomaniak.php');
		if (file_exists(dirname(__FILE__).'/modules/intercom.php') && $this->advanced_options['enable-intercom'] == 'on') include_once(dirname(__FILE__).'/modules/intercom.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/jetpack.php') && $this->advanced_options['enable-jetpack'] == 'on') include_once(dirname(__FILE__).'/modules/jetpack.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/klaviyo.php') && $this->advanced_options['enable-klaviyo'] == 'on') include_once(dirname(__FILE__).'/modules/klaviyo.php');
		if (file_exists(dirname(__FILE__).'/modules/madmimi.php') && $this->advanced_options['enable-madmimi'] == 'on') include_once(dirname(__FILE__).'/modules/madmimi.php');
		if (file_exists(dirname(__FILE__).'/modules/mailautic.php') && $this->advanced_options['enable-mailautic'] == 'on') include_once(dirname(__FILE__).'/modules/mailautic.php');
		if (file_exists(dirname(__FILE__).'/modules/mailchimp.php') && $this->advanced_options['enable-mailchimp'] == 'on') include_once(dirname(__FILE__).'/modules/mailchimp.php');
		if (file_exists(dirname(__FILE__).'/modules/mailerlite.php') && $this->advanced_options['enable-mailerlite'] == 'on') include_once(dirname(__FILE__).'/modules/mailerlite.php');
		if (file_exists(dirname(__FILE__).'/modules/mailfit.php') && $this->advanced_options['enable-mailfit'] == 'on') include_once(dirname(__FILE__).'/modules/mailfit.php');
		if (file_exists(dirname(__FILE__).'/modules/mailgun.php') && $this->advanced_options['enable-mailgun'] == 'on') include_once(dirname(__FILE__).'/modules/mailgun.php');
		if (file_exists(dirname(__FILE__).'/modules/mailjet.php') && $this->advanced_options['enable-mailjet'] == 'on') include_once(dirname(__FILE__).'/modules/mailjet.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/mailpoet.php') && $this->advanced_options['enable-mailpoet'] == 'on') include_once(dirname(__FILE__).'/modules/mailpoet.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/mailrelay.php') && $this->advanced_options['enable-mailrelay'] == 'on') include_once(dirname(__FILE__).'/modules/mailrelay.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/mailster.php') && $this->advanced_options['enable-mailster'] == 'on') include_once(dirname(__FILE__).'/modules/mailster.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/mailwizz.php') && $this->advanced_options['enable-mailwizz'] == 'on') include_once(dirname(__FILE__).'/modules/mailwizz.php');
		if (file_exists(dirname(__FILE__).'/modules/mautic.php') && $this->advanced_options['enable-mautic'] == 'on') include_once(dirname(__FILE__).'/modules/mautic.php');
		if (file_exists(dirname(__FILE__).'/modules/moosend.php') && $this->advanced_options['enable-moosend'] == 'on') include_once(dirname(__FILE__).'/modules/moosend.php');
		if (file_exists(dirname(__FILE__).'/modules/mumara.php') && $this->advanced_options['enable-mumara'] == 'on') include_once(dirname(__FILE__).'/modules/mumara.php');
		if (file_exists(dirname(__FILE__).'/modules/newsman.php') && $this->advanced_options['enable-newsman'] == 'on') include_once(dirname(__FILE__).'/modules/newsman.php');
		if (file_exists(dirname(__FILE__).'/modules/nexmo.php') && $this->advanced_options['enable-nexmo'] == 'on') include_once(dirname(__FILE__).'/modules/nexmo.php');
		if (file_exists(dirname(__FILE__).'/modules/omnisend.php') && $this->advanced_options['enable-omnisend'] == 'on') include_once(dirname(__FILE__).'/modules/omnisend.php');
		if (file_exists(dirname(__FILE__).'/modules/ontraport.php') && $this->advanced_options['enable-ontraport'] == 'on') include_once(dirname(__FILE__).'/modules/ontraport.php');
		if (file_exists(dirname(__FILE__).'/modules/pipedrive.php') && $this->advanced_options['enable-pipedrive'] == 'on') include_once(dirname(__FILE__).'/modules/pipedrive.php');
		if (file_exists(dirname(__FILE__).'/modules/rapidmail.php') && $this->advanced_options['enable-rapidmail'] == 'on') include_once(dirname(__FILE__).'/modules/rapidmail.php');
		if (file_exists(dirname(__FILE__).'/modules/salesflare.php') && $this->advanced_options['enable-salesflare'] == 'on') include_once(dirname(__FILE__).'/modules/salesflare.php');
		if (file_exists(dirname(__FILE__).'/modules/salesautopilot.php') && $this->advanced_options['enable-salesautopilot'] == 'on') include_once(dirname(__FILE__).'/modules/salesautopilot.php');
		if (file_exists(dirname(__FILE__).'/modules/sendfox.php') && $this->advanced_options['enable-sendfox'] == 'on') include_once(dirname(__FILE__).'/modules/sendfox.php');
		if (file_exists(dirname(__FILE__).'/modules/sendgrid.php') && $this->advanced_options['enable-sendgrid'] == 'on') include_once(dirname(__FILE__).'/modules/sendgrid.php');
		if (file_exists(dirname(__FILE__).'/modules/sendinblue.php') && $this->advanced_options['enable-sendinblue'] == 'on') include_once(dirname(__FILE__).'/modules/sendinblue.php');
		if (file_exists(dirname(__FILE__).'/modules/sendpulse.php') && $this->advanced_options['enable-sendpulse'] == 'on') include_once(dirname(__FILE__).'/modules/sendpulse.php');
		if (file_exists(dirname(__FILE__).'/modules/sendy.php') && $this->advanced_options['enable-sendy'] == 'on') include_once(dirname(__FILE__).'/modules/sendy.php');
		if (file_exists(dirname(__FILE__).'/modules/sgautorepondeur.php') && $this->advanced_options['enable-sgautorepondeur'] == 'on') include_once(dirname(__FILE__).'/modules/sgautorepondeur.php');
		if (file_exists(dirname(__FILE__).'/modules/socketlabs.php') && $this->advanced_options['enable-socketlabs'] == 'on') include_once(dirname(__FILE__).'/modules/socketlabs.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/thenewsletterplugin.php') && $this->advanced_options['enable-thenewsletterplugin'] == 'on') include_once(dirname(__FILE__).'/modules/thenewsletterplugin.php');
			if (file_exists(dirname(__FILE__).'/modules/tribulant.php') && $this->advanced_options['enable-tribulant'] == 'on') include_once(dirname(__FILE__).'/modules/tribulant.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/twilio.php') && $this->advanced_options['enable-twilio'] == 'on') include_once(dirname(__FILE__).'/modules/twilio.php');
		if (file_exists(dirname(__FILE__).'/modules/verticalresponse.php') && $this->advanced_options['enable-verticalresponse'] == 'on') include_once(dirname(__FILE__).'/modules/verticalresponse.php');
		if (file_exists(dirname(__FILE__).'/modules/ymlp.php') && $this->advanced_options['enable-ymlp'] == 'on') include_once(dirname(__FILE__).'/modules/ymlp.php');
		if (file_exists(dirname(__FILE__).'/modules/zapier.php') && $this->advanced_options['enable-zapier'] == 'on') include_once(dirname(__FILE__).'/modules/zapier.php');
		if (file_exists(dirname(__FILE__).'/modules/zohocrm.php') && $this->advanced_options['enable-zohocrm'] == 'on') include_once(dirname(__FILE__).'/modules/zohocrm.php');
		if (file_exists(dirname(__FILE__).'/modules/htmlform.php') && $this->advanced_options['enable-htmlform'] == 'on') include_once(dirname(__FILE__).'/modules/htmlform.php');
		if (file_exists(dirname(__FILE__).'/modules/post.php') && $this->advanced_options['enable-post'] == 'on') include_once(dirname(__FILE__).'/modules/post.php');
		if (file_exists(dirname(__FILE__).'/modules/mysql.php') && $this->advanced_options['enable-mysql'] == 'on') include_once(dirname(__FILE__).'/modules/mysql.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/wpuser.php') && $this->advanced_options['enable-wpuser'] == 'on') include_once(dirname(__FILE__).'/modules/wpuser.php');
		}

		if (file_exists(dirname(__FILE__).'/modules/authorizenet.php') && $this->advanced_options['enable-authorizenet'] == 'on') include_once(dirname(__FILE__).'/modules/authorizenet.php');
		if (file_exists(dirname(__FILE__).'/modules/blockchain.php') && $this->advanced_options['enable-blockchain'] == 'on') include_once(dirname(__FILE__).'/modules/blockchain.php');
		if (file_exists(dirname(__FILE__).'/modules/instamojo.php') && $this->advanced_options['enable-instamojo'] == 'on') include_once(dirname(__FILE__).'/modules/instamojo.php');
		if (file_exists(dirname(__FILE__).'/modules/interkassa.php') && $this->advanced_options['enable-interkassa'] == 'on') include_once(dirname(__FILE__).'/modules/interkassa.php');
		if (file_exists(dirname(__FILE__).'/modules/mollie.php') && $this->advanced_options['enable-mollie'] == 'on') include_once(dirname(__FILE__).'/modules/mollie.php');
		if (file_exists(dirname(__FILE__).'/modules/payfast.php') && $this->advanced_options['enable-payfast'] == 'on') include_once(dirname(__FILE__).'/modules/payfast.php');
		if (file_exists(dirname(__FILE__).'/modules/paypal.php') && $this->advanced_options['enable-paypal'] == 'on') include_once(dirname(__FILE__).'/modules/paypal.php');
		if (file_exists(dirname(__FILE__).'/modules/paystack.php') && $this->advanced_options['enable-paystack'] == 'on') include_once(dirname(__FILE__).'/modules/paystack.php');
		if (file_exists(dirname(__FILE__).'/modules/payumoney.php') && $this->advanced_options['enable-payumoney'] == 'on') include_once(dirname(__FILE__).'/modules/payumoney.php');
		if (file_exists(dirname(__FILE__).'/modules/perfectmoney.php') && $this->advanced_options['enable-perfectmoney'] == 'on') include_once(dirname(__FILE__).'/modules/perfectmoney.php');
		if (file_exists(dirname(__FILE__).'/modules/razorpay.php') && $this->advanced_options['enable-razorpay'] == 'on') include_once(dirname(__FILE__).'/modules/razorpay.php');
		if (file_exists(dirname(__FILE__).'/modules/skrill.php') && $this->advanced_options['enable-skrill'] == 'on') include_once(dirname(__FILE__).'/modules/skrill.php');
		if (file_exists(dirname(__FILE__).'/modules/stripe.php') && $this->advanced_options['enable-stripe'] == 'on') include_once(dirname(__FILE__).'/modules/stripe.php');
		if (file_exists(dirname(__FILE__).'/modules/wepay.php') && $this->advanced_options['enable-wepay'] == 'on') include_once(dirname(__FILE__).'/modules/wepay.php');
		if (file_exists(dirname(__FILE__).'/modules/yandexmoney.php') && $this->advanced_options['enable-yandexmoney'] == 'on') include_once(dirname(__FILE__).'/modules/yandexmoney.php');

		if (file_exists(dirname(__FILE__).'/modules/clearout.php') && $this->advanced_options['enable-clearout'] == 'on') include_once(dirname(__FILE__).'/modules/clearout.php');
		if (file_exists(dirname(__FILE__).'/modules/emaillistvalidation.php') && $this->advanced_options['enable-emaillistvalidation'] == 'on') include_once(dirname(__FILE__).'/modules/emaillistvalidation.php');
		if (file_exists(dirname(__FILE__).'/modules/emaillistverify.php') && $this->advanced_options['enable-emaillistverify'] == 'on') include_once(dirname(__FILE__).'/modules/emaillistverify.php');
		if (file_exists(dirname(__FILE__).'/modules/kickbox.php') && $this->advanced_options['enable-kickbox'] == 'on') include_once(dirname(__FILE__).'/modules/kickbox.php');
		if (file_exists(dirname(__FILE__).'/modules/thechecker.php') && $this->advanced_options['enable-thechecker'] == 'on') include_once(dirname(__FILE__).'/modules/thechecker.php');
		if (file_exists(dirname(__FILE__).'/modules/truemail.php') && $this->advanced_options['enable-truemail'] == 'on') include_once(dirname(__FILE__).'/modules/truemail.php');

		if (file_exists(dirname(__FILE__).'/modules/geoipdetect.php') && $this->advanced_options['enable-geoipdetect'] == 'on') include_once(dirname(__FILE__).'/modules/geoipdetect.php');
		if (file_exists(dirname(__FILE__).'/modules/ipstack.php') && $this->advanced_options['enable-ipstack'] == 'on') include_once(dirname(__FILE__).'/modules/ipstack.php');

		$this->element_properties_meta = apply_filters("lepopup_element_properties_meta", $this->element_properties_meta);
		$this->email_validators = apply_filters("lepopup_email_validators", $this->email_validators);
		$this->geoip_services = apply_filters("lepopup_geoip_services", $this->geoip_services);
		
		add_action('init', array(&$this, 'handle_demo_mode'));
		if ($this->advanced_options['enable-php-session'] == 'on') {
			add_action('init', array(&$this, 'register_session'));
		}
		if (function_exists('register_block_type')) {
			add_action('init', array(&$this, 'register_block'));
		}
		add_action('widgets_init', array(&$this, 'widgets_init'));
		if (defined('DOING_AJAX') && DOING_AJAX) {
			include_once(dirname(__FILE__).'/modules/core-ajax.php');
			$lepopup_ajax = new lepopup_ajax_class();
		} else if (is_admin()) {
			add_action('wpmu_new_blog', array(&$this, 'install_new_blog'), 10, 6);
			add_action('delete_blog', array(&$this, 'uninstall_blog'), 10, 2);
			include_once(dirname(__FILE__).'/modules/core-admin.php');
			$lepopup_admin = new lepopup_admin_class();
		} else {
			include_once(dirname(__FILE__).'/modules/core-front.php');
			$lepopup_front = new lepopup_front_class();
		}
		$update = new halfdata_update_v1(__FILE__, 'green-popups', $this->options['purchase-code']);
	}

	static function install($_networkwide = null) {
		global $wpdb;
		if (function_exists('is_multisite') && is_multisite()) {
			if ($_networkwide) {
				$old_blog = $wpdb->blogid;
				$blog_ids = $wpdb->get_col('SELECT blog_id FROM '.esc_sql($wpdb->blogs));
				foreach ($blog_ids as $blog_id) {
					switch_to_blog($blog_id);
					self::activate();
				}
				switch_to_blog($old_blog);
				return;
			}
		}
		self::activate();
	}

	function install_new_blog($_blog_id, $_user_id, $_domain, $_path, $_site_id, $_meta) {
		if (is_plugin_active_for_network(basename(dirname(__FILE__)).'/' ).basename(__FILE__)) {
			switch_to_blog($_blog_id);
			self::activate();
			restore_current_blog();
		}
	}
	
	static function activate() {
		global $wpdb;
		$webfont_version = get_option('lepopup-webfonts-version', 0);
		if (!defined('UAP_CORE')) {
			include_once(dirname(__FILE__).'/modules/core-targeting.php');
			lepopup_class_targeting::activate();
		}
		$create_default = false;
		$table_name = $wpdb->prefix."lepopup_campaigns";
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql = "CREATE TABLE ".$table_name." (
				id int(11) NOT NULL auto_increment,
				name varchar(255) collate utf8_unicode_ci NULL,
				slug varchar(255) collate latin1_general_cs NULL,
				options longtext collate utf8_unicode_ci NULL,
				active int(11) NULL default '1',
				created int(11) NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix."lepopup_campaign_items";
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql = "CREATE TABLE ".$table_name." (
				id int(11) NOT NULL auto_increment,
				campaign_id int(11) NULL,
				form_id int(11) NULL,
				impressions int(11) NULL default '0',
				submits int(11) NULL default '0',
				created int(11) NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix."lepopup_forms";
		if($wpdb->get_var("SHOW TABLES LIKE '".esc_sql($table_name)."'") != $table_name) {
			$sql = "CREATE TABLE ".esc_sql($table_name)." (
				id int(11) NOT NULL auto_increment,
				name varchar(255) collate utf8_unicode_ci NULL,
				slug varchar(255) collate latin1_general_cs NULL,
				options longtext collate utf8_unicode_ci NULL,
				pages longtext collate utf8_unicode_ci NULL,
				elements longtext collate utf8_unicode_ci NULL,
				cache_style longtext collate utf8_unicode_ci NULL,
				cache_html longtext collate utf8_unicode_ci NULL,
				cache_uids longtext collate utf8_unicode_ci NULL,
				cache_time int(11) NULL default '0',
				active int(11) NULL default '1',
				created int(11) NULL,
				modified int(11) NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
			$create_default = true;
		}
		$table_name = $wpdb->prefix."lepopup_records";
		if($wpdb->get_var("SHOW TABLES LIKE '".esc_sql($table_name)."'") != $table_name) {
			$sql = "CREATE TABLE ".esc_sql($table_name)." (
				id int(11) NOT NULL auto_increment,
				form_id int(11) NULL,
				personal_data_keys longtext collate utf8_unicode_ci NULL,
				unique_keys longtext collate utf8_unicode_ci NULL,
				fields longtext collate utf8_unicode_ci NULL,
				info longtext collate utf8_unicode_ci NULL,
				extra longtext collate utf8_unicode_ci NULL,
				status int(11) NULL default '0',
				str_id varchar(255) collate latin1_general_cs NULL,
				gateway_id int(11) NULL,
				amount float NULL,
				currency varchar(7) COLLATE utf8_unicode_ci NULL,
				created int(11) NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix."lepopup_fieldvalues";
		if($wpdb->get_var("SHOW TABLES LIKE '".esc_sql($table_name)."'") != $table_name) {
			$sql = "CREATE TABLE ".esc_sql($table_name)." (
				id int(11) NOT NULL auto_increment,
				form_id int(11) NULL,
				record_id int(11) NULL,
				field_id int(11) NULL,
				value longtext collate utf8_unicode_ci NULL,
				datestamp int(11) NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix."lepopup_transactions";
		if($wpdb->get_var("SHOW TABLES LIKE '".esc_sql($table_name)."'") != $table_name) {
			$sql = "CREATE TABLE ".esc_sql($table_name)." (
				id int(11) NULL AUTO_INCREMENT,
				record_id int(11) NULL,
				provider varchar(63) COLLATE utf8_unicode_ci NULL,
				payer_name varchar(255) COLLATE utf8_unicode_ci NULL,
				payer_email varchar(255) COLLATE utf8_unicode_ci NULL,
				gross float NULL,
				currency varchar(15) COLLATE utf8_unicode_ci NULL,
				payment_status varchar(63) COLLATE utf8_unicode_ci NULL,
				transaction_type varchar(63) COLLATE utf8_unicode_ci NULL,
				txn_id varchar(255) COLLATE utf8_unicode_ci NULL,
				details text COLLATE utf8_unicode_ci NULL,
				created int(11) NULL,
				deleted int(11) NULL DEFAULT '0',
				UNIQUE KEY id (id)
			);";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix."lepopup_uploads";
		if($wpdb->get_var("SHOW TABLES LIKE '".esc_sql($table_name)."'") != $table_name) {
			$sql = "CREATE TABLE ".esc_sql($table_name)." (
				id int(11) NOT NULL auto_increment,
				record_id int(11) NULL,
				form_id int(11) NULL,
				element_id int(11) NULL,
				upload_id varchar(63) collate latin1_general_cs NULL,
				str_id varchar(63) collate latin1_general_cs NULL,
				status int(11) NULL,
				message longtext collate utf8_unicode_ci NULL,
				filename varchar(255) collate utf8_unicode_ci NULL,
				filename_original varchar(255) collate utf8_unicode_ci NULL,
				created int(11) NULL,
				deleted int(11) NULL default '0',
				file_deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix."lepopup_stats";
		if($wpdb->get_var("SHOW TABLES LIKE '".esc_sql($table_name)."'") != $table_name) {
			$sql = "CREATE TABLE ".esc_sql($table_name)." (
				id int(11) NOT NULL auto_increment,
				form_id int(11) NULL,
				impressions int(11) NULL default '0',
				submits int(11) NULL default '0',
				confirmed int(11) NULL default '0',
				payments int(11) NULL default '0',
				datestamp int(11) NULL,
				timestamp int(11) NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix."lepopup_validations";
		if($wpdb->get_var("SHOW TABLES LIKE '".esc_sql($table_name)."'") != $table_name) {
			$sql = "CREATE TABLE ".esc_sql($table_name)." (
				id int(11) NOT NULL auto_increment,
				type varchar(15) collate latin1_general_cs NULL,
				hash varchar(63) collate latin1_general_cs NULL,
				valid int(11) NULL default '0',
				created int(11) NULL,
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix."lepopup_geoip";
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql = "CREATE TABLE ".$table_name." (
				id int(11) NOT NULL auto_increment,
				ip varchar(63) collate utf8_unicode_ci NULL,
				country varchar(15) collate latin1_general_ci NULL,
				region varchar(255) collate utf8_unicode_ci NULL,
				city varchar(255) collate utf8_unicode_ci NULL,
				zip varchar(255) collate utf8_unicode_ci NULL,
				service varchar(31) collate latin1_general_cs NULL,
				created int(11) NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix."lepopup_webfonts";
		if($wpdb->get_var("SHOW TABLES LIKE '".esc_sql($table_name)."'") != $table_name) {
			$sql = "CREATE TABLE ".esc_sql($table_name)." (
				id int(11) NOT NULL auto_increment,
				family varchar(255) collate utf8_unicode_ci NULL,
				variants varchar(255) collate utf8_unicode_ci NULL,
				subsets varchar(255) collate utf8_unicode_ci NULL,
				source varchar(31) collate latin1_general_cs NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
			$webfont_version = 0;
		}
		if ($webfont_version < LEPOPUP_WEBFONTS_VERSION) {
			include_once(dirname(__FILE__).'/webfonts.php');
			$webfonts_array = json_decode($fonts, true);
			if (is_array($webfonts_array['items'])) {
				$sql = "DELETE FROM ".$wpdb->prefix."lepopup_webfonts";
				$wpdb->query($sql);
				$values = array();
				foreach($webfonts_array['items'] as $fontvars) {
					if (!empty($fontvars['family'])) {
						$variants = '';
						if (!empty($fontvars['variants']) && is_array($fontvars['variants'])) {
							foreach ($fontvars['variants'] as $key => $var) {
									if ($var == 'regular') $fontvars['variants'][$key] = '400';
									if ($var == 'italic') $fontvars['variants'][$key] = '400italic';
							}
							$variants = implode(",", $fontvars['variants']);
						}
						$subsets = '';
						if (!empty($fontvars['subsets']) && is_array($fontvars['subsets'])) {
							$subsets = implode(",", $fontvars['subsets']);
						}
						$values[] = "('".esc_sql($fontvars['family'])."', '".esc_sql($variants)."', '".esc_sql($subsets)."', 'google', '0')";
						if (sizeof($values) > 9) {
							$sql = "INSERT INTO ".$wpdb->prefix."lepopup_webfonts (family, variants, subsets, source, deleted) 
									VALUES ".implode(', ', $values);
							$wpdb->query($sql);
							$values = array();
						}
					}
				}
				if (sizeof($values) > 0) {
					$sql = "INSERT INTO ".$wpdb->prefix."lepopup_webfonts (family, variants, subsets, source, deleted) 
							VALUES ".implode(', ', $values);
					$wpdb->query($sql);
				}
			}
			update_option('lepopup-webfonts-version', LEPOPUP_WEBFONTS_VERSION);
		}
		update_option('lepopup-version', LEPOPUP_VERSION);
		update_option('lepopup-update-time', time());
		$installation_uid = get_option('lepopup-installation-uid', null);
		if (empty($installation_uid)) update_option('lepopup-installation-uid', self::random_string(9));
		$upload_dir = wp_upload_dir();
		wp_mkdir_p($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR);
		wp_mkdir_p($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads');
		wp_mkdir_p($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/temp');
		if (file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR) && !file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/index.html')) {
			file_put_contents($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/index.html', 'Silence is the gold!');
		}
		if (file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads') && !file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/index.html')) {
			file_put_contents($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/index.html', 'Silence is the gold!');
		}
		if (file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads') && !file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/.htaccess')) {
			file_put_contents($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/.htaccess', 'deny from all');
		}
		if ($create_default) {
			if (file_exists(dirname(__FILE__).'/default') && is_dir(dirname(__FILE__).'/default')) {
				$dircontent = scandir(dirname(__FILE__).'/default');
				include_once(dirname(__FILE__).'/modules/core-admin.php');
				$lepopup_admin = new lepopup_admin_class();
				for ($i=0; $i<sizeof($dircontent); $i++) {
					if ($dircontent[$i] != "." && $dircontent[$i] != ".." && $dircontent[$i] != "index.html" && $dircontent[$i] != ".htaccess") {
						if (is_file(dirname(__FILE__).'/default/'.$dircontent[$i])) {
							$lepopup_admin->_import_form(dirname(__FILE__).'/default/'.$dircontent[$i], str_replace('http://', '//', plugins_url('/images/default', __FILE__)));
						}
					}
				}
			}
		}
	}

	static function uninstall() {
		global $wpdb;
		if (function_exists('is_multisite') && is_multisite()) {
			$old_blog = $wpdb->blogid;
			$blog_ids = $wpdb->get_col('SELECT blog_id FROM '.esc_sql($wpdb->blogs));
			foreach ($blog_ids as $blog_id) {
				switch_to_blog($blog_id);
				self::deactivate(false);
			}
			switch_to_blog($old_blog);
		} else {
			self::deactivate(false);
		}
	}

	function uninstall_blog($_blog_id, $_drop) {
		if (is_plugin_active_for_network(basename(dirname(__FILE__)).'/'.basename(__FILE__)) && $_drop) {
			switch_to_blog($_blog_id);
			self::deactivate(true);
			restore_current_blog();
		}
	}
	
	static function deactivate($_force_delete = false) {
		global $wpdb;
		$clean_database = get_option('lepopup-advanced-clean-database', 'off');
		if ($clean_database == 'on' || $_force_delete) {
			if (!defined('UAP_CORE')) {
				include_once(dirname(__FILE__).'/modules/core-targeting.php');
				lepopup_class_targeting::deactivate();
			}
		}
	}

	function get_advanced_options() {
		$options = json_decode(get_option('lepopup-advanced-options', '[]'), true);
		if (is_array($options) && !empty($options)) {
			$this->advanced_options = array_merge($this->advanced_options, $options);
		} else {
			foreach ($this->advanced_options as $key => $value) {
				$this->advanced_options[$key] = get_option('lepopup-advanced-'.$key, $this->advanced_options[$key]);
			}
		}
	}

	function update_advanced_options() {
		if (current_user_can('manage_options')) {
			update_option('lepopup-advanced-options', json_encode($this->advanced_options));
			update_option('lepopup-update-time', time());
		}
	}

	function populate_advanced_options() {
		foreach ($this->advanced_options as $key => $value) {
			if (array_key_exists('lepopup-advanced-'.$key, $_REQUEST)) {
				$this->advanced_options[$key] = trim(stripslashes($_REQUEST['lepopup-advanced-'.$key]));
			} else if (in_array($key, $this->advanced_options_checkboxes)) $this->advanced_options[$key] = "off";
		}
	}
	
	function get_options() {
		$options = json_decode(get_option('lepopup-options', '[]'), true);
		$this->installation_uid = get_option('lepopup-installation-uid', 'NONE');
		$this->version = get_option('lepopup-version', LEPOPUP_VERSION);
		if (is_array($options) && !empty($options)) {
			$this->options = array_merge($this->options, $options);
		} else {
			foreach ($this->options as $key => $value) {
				$this->options[$key] = get_option('lepopup-'.$key, $this->options[$key]);
			}
		}
	}

	function update_options() {
		if (current_user_can('manage_options')) {
			update_option('lepopup-options', json_encode($this->options));
			update_option('lepopup-update-time', time());
		}
	}

	function populate_options() {
		foreach ($this->options as $key => $value) {
			if (array_key_exists('lepopup-'.$key, $_REQUEST)) {
				$this->options[$key] = stripslashes($_REQUEST['lepopup-'.$key]);
			} else if (in_array($key, $this->options_checkboxes)) $this->options[$key] = "off";
		}
		$this->options['custom-fonts'] = array();
		if (array_key_exists('lepopup-custom-font-options', $_REQUEST)) {
			$font_options = explode("\n", $_REQUEST['lepopup-custom-font-options']);
			foreach ($font_options as $option) {
				$option = trim(stripslashes($option));
				if (!empty($option)) {
					$this->options['custom-fonts'][] = $option;
				}
			}
			sort($this->options['custom-fonts']);
		}
		
	}

	function register_block() {
		wp_register_script('lepopup-form', plugins_url('/js/block.js', __FILE__), array('wp-blocks', 'wp-element', 'wp-i18n'));
		register_block_type('lepopup/form', array('editor_script' => 'lepopup-form'));
	}
	
	function shortcode_handler($_atts) {
		include_once(dirname(__FILE__).'/modules/core-front.php');
		$lepopup_front = new lepopup_front_class();
		$html = $lepopup_front->shortcode_handler($_atts);
		return $html;
	}
	
	function default_form_options($_type = 'settings') {
		$form_options = array();
		if (!array_key_exists($_type, $this->element_properties_meta)) return array();
		foreach ($this->element_properties_meta[$_type] as $key => $value) {
			if (array_key_exists('value', $value)) {
				if (is_array($value['value'])) {
					foreach ($value['value'] as $option_key => $option_value) {
						$form_options[$key.'-'.$option_key] = $option_value;
					}
				} else $form_options[$key] = $value['value'];
			} else if (array_key_exists('values', $value)) $form_options[$key] = $value['values'];
		}
		return $form_options;
	}
	
	function get_info_label($_key) {
		$label = '-';
		if ($_key == 'ip') $label = esc_html__('IP Address', 'lepopup');
		else if ($_key == 'url') $label = esc_html__('Popup URL', 'lepopup');
		else if ($_key == 'page-title') $label = esc_html__('Page Title', 'lepopup');
		else if ($_key == 'user-agent') $label = esc_html__('User Agent', 'lepopup');
		else if ($_key == 'record-id') $label = esc_html__('Record ID', 'lepopup');
		else if ($_key == 'wp-user-login') $label = esc_html__('WP User Login', 'lepopup');
		else if ($_key == 'wp-user-email') $label = esc_html__('WP User Email', 'lepopup');
		return $label;
	}
	
	function stats_array($_form_id, $_start_date, $_end_date) {
		global $wpdb;
		$current_date = $_start_date;
		$sql_start_date = $_start_date->format("Ymd");
		$output = array();
		do {
			$key_date = $current_date->format("Ymd");
			$output[$key_date] = array(
				'impressions' => 0,
				'submits' => 0,
				'confirmed' => 0,
				'payments' => 0,
				'label' => $current_date->format("Y-m-d")
			);
			$current_date->modify('+1 day');
		} while ($current_date <= $_end_date);
		$stats = $wpdb->get_results("SELECT SUM(t1.impressions) AS impressions, SUM(t1.submits) AS submits, SUM(t1.confirmed) AS confirmed, SUM(t1.payments) AS payments, t1.datestamp FROM ".$wpdb->prefix."lepopup_stats t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.id = t1.form_id WHERE t1.deleted = '0' AND t2.deleted = '0' AND t1.datestamp >= '".esc_sql($sql_start_date)."' AND t1.datestamp <= '".esc_sql($_end_date->format("Ymd"))."'".(!empty($_form_id) ? " AND t1.form_id = '".esc_sql(intval($_form_id))."'" : "")." GROUP BY t1.datestamp", ARRAY_A);
		foreach($stats as $stat_record) {
			if (array_key_exists($stat_record['datestamp'], $output)) {
				$output[$stat_record['datestamp']]['impressions'] = $stat_record['impressions'];
				$output[$stat_record['datestamp']]['submits'] = $stat_record['submits'];
				$output[$stat_record['datestamp']]['confirmed'] = $stat_record['confirmed'];
				$output[$stat_record['datestamp']]['payments'] = $stat_record['payments'];
			}
		}
		return $output;
	}

	function transaction_details_html($_id, $_pdf = false) {
		global $wpdb;
		$record_id = null;
		$html = '';
		if (!empty($_id)) {
			$record_id = intval($_id);
			$record_details = $wpdb->get_row("SELECT t1.*, t2.form_id AS form_id FROM ".$wpdb->prefix."lepopup_transactions t1 LEFT JOIN ".$wpdb->prefix."lepopup_records t2 ON t2.id = t1.record_id WHERE t1.deleted = '0' AND t1.id = '".esc_sql($record_id)."'", ARRAY_A);
			if (empty($record_details)) $record_id = null;
		}
		if (empty($record_id)) {
			$return_data = array(
				'status' => 'ERROR',
				'message' => esc_html__('Requested transaction not found.', 'lepopup')
			);
			return $return_data;
		}
		$raw_data_html = apply_filters('lepopup_payment_gateways_transaction_html_'.$record_details['provider'], "", $record_details, $_pdf);
		if (empty($raw_data_html)) {
			$return_data = array(
				'status' => 'ERROR',
				'message' => esc_html__('Transaction details not found.', 'lepopup')
			);
			return $return_data;
		}
		$html = '<div class="lepopup-record-details">';
		$html .= '
			<h3>'.(!empty($this->advanced_options['label-general-info']) ? esc_html($this->advanced_options['label-general-info']) : esc_html__('General Info', 'lepopup')).'</h3>
			<table class="lepopup-record-details-table">
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Transaction ID', 'lepopup').'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html($record_details['txn_id']).'</td></tr>
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Transaction type', 'lepopup').'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html($record_details['transaction_type']).'</td></tr>
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Payment provider', 'lepopup').'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html($record_details['provider']).'</td></tr>
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Payer', 'lepopup').'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html($record_details['payer_name']).' ('.esc_html($record_details['payer_email']).')</td></tr>
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Amount', 'lepopup').'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.($record_details['currency'] != 'BTC' ? number_format($record_details['gross'], 2, '.', '') : number_format($record_details['gross'], 8, '.', '')).' '.esc_html($record_details['currency']).'</td></tr>
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Status', 'lepopup').'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html($record_details['payment_status']).'</td></tr>
			</table>';
		$html .= '<h3>'.(!empty($this->advanced_options['label-raw-details']) ? esc_html($this->advanced_options['label-raw-details']) : esc_html__('Raw Details', 'lepopup')).'</h3>'.$raw_data_html;
		
		$html .= '
			<h3>'.(!empty($this->advanced_options['label-technical-info']) ? esc_html($this->advanced_options['label-technical-info']) : esc_html__('Technical Info', 'lepopup')).'</h3>
			<table class="lepopup-record-details-table">
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Record ID', 'lepopup').'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html($record_details['id']).'</td></tr>
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Reference ID', 'lepopup').'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html($record_details['record_id']).'</td></tr>
			</table>';
		$html .= '</div>';
		$return_data = array(
			'status' => 'OK',
			'txn_id' => $record_details['txn_id'],
			'html' => $html
		);
		return $return_data;
	}
	
	function log_record_details_html($_id, $_pdf = false) {
		global $wpdb;
		$record_id = null;
		if (!empty($_id)) {
			$record_id = intval($_id);
			$record_details = $wpdb->get_row("SELECT t1.*, t2.name AS form_name, t2.options AS form_options, t2.elements AS form_elements FROM ".$wpdb->prefix."lepopup_records t1 LEFT JOIN ".$wpdb->prefix."lepopup_forms t2 ON t2.id = t1.form_id WHERE t1.deleted = '0' AND t1.id = '".esc_sql($record_id)."'", ARRAY_A);
			if (empty($record_details)) $record_id = null;
		}
		if (empty($record_id)) {
			$return_data = array(
				'status' => 'ERROR',
				'message' => esc_html__('Requested record not found.', 'lepopup')
			);
			return $return_data;
		}

		if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/modules/core-form.php');
		$form_object = new lepopup_form($record_details['form_id'], true);

		if (empty($form_object->id)) {
			$return_data = array(
				'status' => 'ERROR',
				'message' => esc_html__('Requested form doesn not exists.', 'lepopup')
			);
			return $return_data;
		}

		$fields = json_decode($record_details['fields'], true);
		if (!is_array($fields)) {
			$return_data = array(
				'status' => 'ERROR',
				'message' => esc_html__('Requested record corrupted.', 'lepopup')
			);
			return $return_data;
		}

		$fields_meta = array();
		$form_elements = $form_object->input_fields_sort();
		foreach($form_elements as $form_element) {
			if (is_array($form_element) && array_key_exists('name', $form_element)) {
				$fields_meta[$form_element['id']] = $form_element;
			}
		}
		
		$html = '
		<div class="lepopup-record-details" data-id="'.esc_html($record_details['id']).'">';
		if (sizeof($fields) > 0) {
			$html .= '
			<h3>'.(!empty($this->advanced_options['label-form-values']) ? esc_html($this->advanced_options['label-form-values']) : esc_html__('Form Values', 'lepopup')).'</h3>
			<table class="lepopup-record-details-table">';
			$upload_dir = wp_upload_dir();
			$current_page_id = 0;
			foreach ($fields_meta as $id => $field_meta) {
				if (array_key_exists($id, $fields)) {
					if (sizeof($form_object->form_pages) > 2 && $current_page_id != $field_meta['page-id']) {
						$html .= '
			</table>
			<h4>'.esc_html($field_meta['page-name']).'</h4>
			<table class="lepopup-record-details-table">';
						$current_page_id = $field_meta['page-id'];
					}
					$values = $fields[$id];
					if ($field_meta['type'] == 'file') {
						if (!empty($values)) {
							foreach ($values as $values_key => $values_value) {
								$values[$values_key] = esc_sql($values_value);
							}
							$uploads = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE id IN ('".implode("', '", $values)."')", ARRAY_A);
							$values = array();
							foreach($uploads as $upload_details) {
								if (file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$record_details['form_id'].'/'.$upload_details['filename']) && is_file($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$record_details['form_id'].'/'.$upload_details['filename'])) {
									$values[] = '<a href="'.admin_url('admin.php').'?page=lepopup&lepopup-action=download&id='.esc_html($upload_details['id']).'" target="_blank">'.esc_html($upload_details['filename_original']).'</a>';
								} else {
									$values[] = esc_html($upload_details['filename_original']).' ('.esc_html__('file deleted', 'lepopup').')';
								}
							}
							if (!empty($values)) $value = implode("<br />", $values);
							else $value = '-';
						} else $value = '-';
					} else if ($field_meta['type'] == 'signature') {
						if (substr($values, 0, strlen('data:image/png;base64,')) != 'data:image/png;base64,') $value = '-';
						else {
							if ($_pdf) $value = '<img src="@'.esc_html(preg_replace('#^data:image/[^;]+;base64,#', '', $values)).'" />';
							else $value = '<img class="lepopup-signature-view" src="'.esc_html($values).'" alt="" />';
						}
					} else if ($field_meta['type'] == 'rangeslider') {
						$value = esc_html(str_replace(':', ' ... ', $values));
					} else if (in_array($field_meta['type'], array('select', 'radio', 'checkbox', 'multiselect', 'imageselect', 'tile'))) {
						$esc_array = array();
						foreach ((array)$values as $key => $values_value) {
							$added = false;
							foreach($field_meta['options'] as $option) {
								if ($option['value'] == $values_value && $option['value'] != $option['label']) {
									$added = true;
									$esc_array[] = $option['label'].' ('.$option['value'].')';
								}
							}
							if (!$added) $esc_array[] = $values_value;
						}
						$value = implode('<br />', $esc_array);
					} else if (is_array($values)) {
						foreach ($values as $key => $values_value) {
							$values_value = trim($values_value);
							if ($values_value == "") $values[$key] = "-";
							else $values[$key] = esc_html($values_value);
						}
						$value = implode("<br />", $values);
					} else if ($values != "") {
						if ($field_meta['type'] == 'textarea') {
							$value_strings = explode("\n", $values);
							foreach ($value_strings as $key => $values_value) {
								$value_strings[$key] = esc_html(trim($values_value));
							}
							$value = implode("<br />", $value_strings);
						} else $value = esc_html($values);
					} else $value = "-";
					$toolbar = '';
					if (!$_pdf) {
						$allow_edit = true;
						if (in_array($field_meta['type'], array('signature', 'file'))) $allow_edit = false;
						$toolbar = '<div class="lepopup-record-details-toolbar">'.($allow_edit ? '<span onclick="return lepopup_record_field_load_editor(this);"><i class="fas fa-pencil-alt"></i></span>' : '').'<span onclick="return lepopup_record_field_empty(this);"><i class="fas fa-eraser"></i></span><span onclick="return lepopup_record_field_remove(this);"><i class="far fa-trash-alt"></i></span></div><div class="lepopup-record-field-editor"></div>';
					}
					$html .= '
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html($field_meta['name']).'</td><td class="lepopup-record-details-table-value" data-id="'.esc_html($id).'" data-type="'.esc_html($field_meta['type']).'"'.($_pdf ? ' style="width:67%;"' : '').'>'.$toolbar.'<div class="lepopup-record-field-value">'.$value.'</div></td></tr>';
				}
				unset($fields[$id]);
			}
			foreach($fields as $id => $values) {
				if (!empty($values)) {
					if (is_array($values)) {
						foreach ($values as $key => $values_value) {
							$values_value = trim($values_value);
							if ($values_value == "") $values[$key] = "-";
							else $values[$key] = esc_html($values_value);
						}
						$value = implode("<br />", $values);
					} else {
						if (substr($values, 0, strlen('data:image/png;base64,')) == 'data:image/png;base64,') {
							if ($_pdf) $value = '<img src="@'.esc_html(preg_replace('#^data:image/[^;]+;base64,#', '', $values)).'" />';
							else $value = '<img class="lepopup-signature-view" src="'.esc_html($values).'" alt="" />';
						} else $value = str_replace("\n", "<br />", esc_html($values));
					}
					$toolbar = '';
					if (!$_pdf) {
						$toolbar = '<div class="lepopup-record-details-toolbar"><span onclick="return lepopup_record_field_empty(this);"><i class="fas fa-eraser"></i></span><span onclick="return lepopup_record_field_remove(this);"><i class="far fa-trash-alt"></i></span></div><div class="lepopup-record-field-editor"></div>';
					}
					$html .= '
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Deleted field', 'lepopup').' (ID: '.$id.')</td><td class="lepopup-record-details-table-value" data-id="'.esc_html($id).'" data-type=""'.($_pdf ? ' style="width:67%;"' : '').'>'.$toolbar.'<div class="lepopup-record-field-value">'.$value.'</div></td></tr>';
				}
			}
			$html .= '
			</table>';
		}
		if ($record_details['amount'] > 0) {
				$html .= '
			<h3>'.(!empty($this->advanced_options['label-payment']) ? esc_html($this->advanced_options['label-payment']) : esc_html__('Payment', 'lepopup')).'</h3>
			<table class="lepopup-record-details-table">
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Amount', 'lepopup').'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.($record_details['currency'] != 'BTC' ? number_format($record_details['amount'], 2, '.', '') : number_format($record_details['amount'], 8, '.', '')).' '.esc_html($record_details['currency']).'</td></tr>
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Status', 'lepopup').'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.($record_details['status'] == LEPOPUP_RECORD_STATUS_PAID ? '<span class="lepopup-badge lepopup-badge-success">'.esc_html__('Paid', 'lepopup').'</span>' : '<span class="lepopup-badge lepopup-badge-danger">'.esc_html__('Unpaid', 'lepopup').'</span>').'</td></tr>
			</table>';
		}
		$info = json_decode($record_details['info'], true);
		if (is_array($info) && $form_object->form_options['misc-record-tech-info'] == 'on') {
			$html .= '
			<h3>'.(!empty($this->advanced_options['label-technical-info']) ? esc_html($this->advanced_options['label-technical-info']) : esc_html__('Technical Info', 'lepopup')).'</h3>
			<table class="lepopup-record-details-table">';
			$html .= '
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Record ID', 'lepopup').'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html($record_details['id']).'</td></tr>
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Popup', 'lepopup').'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html($record_details['form_name'].' (ID: '.$record_details['form_id'].')').'</td></tr>
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html__('Created', 'lepopup').'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html($this->unixtime_string($record_details['created'])).'</td></tr>';
			foreach($info as $info_key => $info_value) {
				if (!empty($info_value)) {
					$label = $this->get_info_label($info_key);
					$html .= '
				<tr><td class="lepopup-record-details-table-name"'.($_pdf ? ' style="width:33%;"' : '').'>'.esc_html($label).'</td><td class="lepopup-record-details-table-value"'.($_pdf ? ' style="width:67%;"' : '').'>'.esc_html($info_value).'</td></tr>';
				}
			}
			$html .= '
			</table>';
		}
		$html .= '</div>';
		$return_data = array(
			'status' => 'OK',
			'html' => $html,
			'form_name' => esc_html($record_details['form_name']),
			'record-id' => esc_html($record_details['id'])
		);
		return $return_data;
	}

	function uploads_delete($_record_id, $_element_id = null) {
		global $wpdb;
		$uploads = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."lepopup_uploads WHERE deleted = '0' AND record_id IN ('".implode("','", (array)$_record_id)."')".(!empty($_element_id) ? " AND element_id = '".esc_sql($_element_id)."'" : "")." AND upload_id = '' AND str_id = ''", ARRAY_A);
		$upload_dir = wp_upload_dir();
		foreach ($uploads as $upload) {
			if (file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$upload['form_id'].'/'.$upload['filename'])) {
				unlink($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/uploads/'.$upload['form_id'].'/'.$upload['filename']);
			}
		}
		$wpdb->query("UPDATE ".$wpdb->prefix."lepopup_uploads SET deleted = '1' WHERE deleted = '0' AND record_id IN ('".implode("','", (array)$_record_id)."')".(!empty($_element_id) ? " AND element_id = '".esc_sql($_element_id)."'" : "")." AND upload_id = '' AND str_id = ''");
	}

	function export($_form_id) {
		global $wpdb;
		if (!class_exists("lepopup_form")) include_once(dirname(__FILE__).'/modules/core-form.php');
		$form_object = new lepopup_form($_form_id);
		$form_full = array();
		if (!empty($form_object->id)) {
			// Remove Integrations and Payment Gateways for safety reasons - begin
			$form_object->form_options['integrations'] = null;
			$form_object->form_options['payment-gateways'] = null;
			// Remove Integrations and Payment Gateways for safety reasons - end
			$upload_dir = wp_upload_dir();
			if (!class_exists('ZipArchive') || !class_exists('DOMDocument') || !file_exists($upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/temp')) {
				$form_full['name'] = $form_object->name;
				$form_full['slug'] = $form_object->slug;
				$form_full['options'] = $form_object->form_options;
				$form_full['pages'] = $form_object->form_pages;
				$form_full['elements'] = $form_object->form_elements;
				$form_data = json_encode($form_full);
				$output = LEPOPUP_EXPORT_VERSION.PHP_EOL.$form_object->slug.PHP_EOL.md5($form_data).PHP_EOL.base64_encode($form_data);
				if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE")) {
					header("Pragma: public");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Content-type: application-download");
					header("Content-Length: ".strlen($output));
					header('Content-Disposition: attachment; filename="popup-'.$form_object->slug.'.txt"');
					header("Content-Transfer-Encoding: binary");
				} else {
					header("Content-type: application-download");
					header("Content-Length: ".strlen($output));
					header('Content-Disposition: attachment; filename="popup-'.$form_object->slug.'.txt"');
				}
				echo $output;
				flush();
				ob_flush();
				exit;
			} else {
				if (!defined('UAP_CORE')) {
					require_once(ABSPATH.'wp-admin/includes/file.php');
				}
				$zip = new ZipArchive();
				$zip_filename = $upload_dir["basedir"].'/'.LEPOPUP_UPLOADS_DIR.'/temp/'.$this->random_string(16).'.zip';
				if ($zip->open($zip_filename, ZipArchive::CREATE) !== true) {
					$this->error_message = esc_html__('Can not create ZIP-file.', 'lepopup');
					return;
				}
				$images_processed = array();
				
				foreach ($this->element_properties_meta['settings'] as $key => $element) {
					if (array_key_exists('type', $element) && $element['type'] == 'background-style') {
						if (array_key_exists($key.'-image', $form_object->form_options)) {
							if (!empty($form_object->form_options[$key.'-image']) && preg_match('~^((http(s)?://)|(//))[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$~i', $form_object->form_options[$key.'-image'])) {
								$filename = $this->_add_to_archive($zip, $form_object->form_options[$key.'-image'], $images_processed);
								if ($filename !== false) {
									$form_object->form_options[$key.'-image'] = 'LEPOPUP-FORM-DIR/'.$filename;
								}
							}
						}
					}
				}
				if (array_key_exists('confirmations', $form_object->form_options) && !empty($form_object->form_options['confirmations'])) {
					foreach ($form_object->form_options['confirmations'] as $key => $confirmation) {
						$form_object->form_options['confirmations'][$key]['message'] = $this->_process_images_in_html($confirmation['message'], $zip, $images_processed);
					}
				}
				if (array_key_exists('double-email-message', $form_object->form_options) && !empty($form_object->form_options['double-email-message'])) {
					$form_object->form_options['double-email-message'] = $this->_process_images_in_html($form_object->form_options['double-email-message'], $zip, $images_processed);
				}
				if (array_key_exists('double-message', $form_object->form_options) && !empty($form_object->form_options['double-message'])) {
					$form_object->form_options['double-message'] = $this->_process_images_in_html($form_object->form_options['double-message'], $zip, $images_processed);
				}
				if (array_key_exists('notifications', $form_object->form_options) && !empty($form_object->form_options['notifications'])) {
					foreach ($form_object->form_options['notifications'] as $key => $notification) {
						$form_object->form_options['notifications'][$key]['message'] = $this->_process_images_in_html($notification['message'], $zip, $images_processed);
					}
				}
				foreach ($form_object->form_elements as $key => $element) {
					if ($element['type'] == 'html') {
						$form_object->form_elements[$key]['content'] = $this->_process_images_in_html($form_object->form_elements[$key]['content'], $zip, $images_processed);
						if (!empty($form_object->form_elements[$key]['background-style-image']) && preg_match('~^((http(s)?://)|(//))[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$~i', $form_object->form_elements[$key]['background-style-image'])) {
							$filename = $this->_add_to_archive($zip, $form_object->form_elements[$key]['background-style-image'], $images_processed);
							if ($filename !== false) {
								$form_object->form_elements[$key]['background-style-image'] = 'LEPOPUP-FORM-DIR/'.$filename;
							}
						}
					} else if ($element['type'] == 'rectangle') {
						if (!empty($form_object->form_elements[$key]['background-style-image']) && preg_match('~^((http(s)?://)|(//))[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$~i', $form_object->form_elements[$key]['background-style-image'])) {
							$filename = $this->_add_to_archive($zip, $form_object->form_elements[$key]['background-style-image'], $images_processed);
							if ($filename !== false) {
								$form_object->form_elements[$key]['background-style-image'] = 'LEPOPUP-FORM-DIR/'.$filename;
							}
						}
					} else if ($element['type'] == 'imageselect') {
						foreach($element['options'] as $option_key => $option) {
							if (!empty($option['image'])) {
								$filename = $this->_add_to_archive($zip, $option['image'], $images_processed);
								if ($filename !== false) {
									$form_object->form_elements[$key]['options'][$option_key]['image'] = 'LEPOPUP-FORM-DIR/'.$filename;
								}
							}
						}
					}
				}

				$form_full['name'] = $form_object->name;
				$form_full['slug'] = $form_object->slug;
				$form_full['options'] = $form_object->form_options;
				$form_full['pages'] = $form_object->form_pages;
				$form_full['elements'] = $form_object->form_elements;
				
				$form_data = json_encode($form_full);
				$zip->addFromString('popup.txt', LEPOPUP_EXPORT_VERSION.PHP_EOL.$form_object->slug.PHP_EOL.md5($form_data).PHP_EOL.base64_encode($form_data));
				$zip->addFromString('index.html', 'Get your copy of <a href="https://greenforms.pro/">Green Popups</a>.');
				$zip->close();
				error_reporting(0);
				$length = filesize($zip_filename);
				if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) {
					header("Pragma: public");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Content-Type: application-download");
					header("Content-Length: ".$length);
					header('Content-Disposition: attachment; filename="popup-'.$form_object->slug.'.zip"');
					header("Content-Transfer-Encoding: binary");
				} else {
					header("Content-Type: application-download");
					header("Content-Length: ".$length);
					header('Content-Disposition: attachment; filename="popup-'.$form_object->slug.'.zip"');
				}
				$handle_read = fopen($zip_filename, "rb");
				while (!feof($handle_read) && $length > 0) {
					$content = fread($handle_read, 1024);
					echo substr($content, 0, min($length, 1024));
					flush();
					$length = $length - strlen($content);
					if ($length < 0) $length = 0;
				}
				fclose($handle_read);
				unlink($zip_filename);
				foreach ($images_processed as $value) {
					if (!empty($value['temp']) && file_exists($value['temp']) && is_file($value['temp'])) unlink($value['temp']);
				}
				exit;
			}
		}
		return false;
	}

	function _process_images_in_html($_html, &$_zip, &$_images_processed) {
		global $wpdb;
		if (function_exists('libxml_use_internal_errors')) libxml_use_internal_errors(true);
		if (!empty($_html)) {
			$dom = new DOMDocument();
			$dom->loadHTML($_html);
			if (!$dom) return $_html;
			
			$imgs = $dom->getElementsByTagName('img');
			foreach ($imgs as $img) {
				$img_string = $img->getAttribute('src');
				if (!empty($img_string) && preg_match('~^((http(s)?://)|(//))[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$~i', $img_string)) {
					$filename = $this->_add_to_archive($_zip, $img_string, $_images_processed);
					if ($filename !== false) {
						$_html = str_replace($img_string, 'LEPOPUP-FORM-DIR/'.$filename, $_html);
					}
				}
			}								
		}
		return $_html;
	}
	
	function _add_to_archive(&$_zip, $_image_url, &$_images_processed) {
		global $wpdb;
		if (substr($_image_url, 0, 2) == '//') $_image_url = 'http:'.$_image_url;
		if (strtolower(substr($_image_url, 0, 8)) == 'https://') $processed_key = substr($_image_url, 8);
		else $processed_key = substr($_image_url, 7);
		if (strtolower(substr($processed_key, 0, 4)) == 'www.') $processed_key = substr($processed_key, 4);
		if (array_key_exists($processed_key, $_images_processed)) {
			return $_images_processed[$processed_key]['image'];
		}
		$filename = 'img-'.sizeof($_images_processed);
		$mime_types = array(
			'image/png' => 'png',
			'image/jpeg' => 'jpg',
			'image/gif' => 'gif',
			'image/bmp' => 'bmp',
			'image/vnd.microsoft.icon' => 'ico',
			'image/tiff' => 'tiff',
			'image/svg+xml' => 'svg',
			'image/svg+xml' => 'svgz'
		);
		$download_file = download_url($_image_url);
		if (is_wp_error($download_file)) {
			return false;
		}
		$path = parse_url($_image_url, PHP_URL_PATH);
		$check_image = true;
		if ($path !== false && strlen($path) > 4) {
			$ext = strtolower(substr($path, strlen($path)-4));
			if ($ext == '.svg') {
				$filename .= '.svg';
				$check_image = false;
			}
		}
		if ($check_image) {
			$img_data = getimagesize($download_file);
			if (is_array($img_data) && array_key_exists('mime', $img_data)) {
				if (array_key_exists($img_data['mime'], $mime_types)) {
					$filename .= '.'.$mime_types[$img_data['mime']];
				}
			}
		}
		if ($_zip->addFile($download_file, $filename)) {
			$_images_processed[$processed_key] = array(
				'image' => $filename,
				'temp' => $download_file
			);
			return $filename;
		}
		unlink($download_file);
		return false;
	}
	
	function page_switcher ($_urlbase, $_currentpage, $_totalpages) {
		$pageswitcher = "";
		if ($_totalpages > 1) {
			$pageswitcher = '<div class="lepopup-table-list-pages"><span>';
			if (strpos($_urlbase, "?") !== false) $_urlbase .= "&";
			else $_urlbase .= "?";
			if ($_currentpage == 1) $pageswitcher .= "<a href='#' class='lepopup-table-list-page-active' onclick='return false'>1</a> ";
			else $pageswitcher .= " <a href='".$_urlbase."p=1'>1</a> ";

			$start = max($_currentpage-3, 2);
			$end = min(max($_currentpage+3,$start+6), $_totalpages-1);
			$start = max(min($start,$end-6), 2);
			if ($start > 2) $pageswitcher .= " <strong>...</strong> ";
			for ($i=$start; $i<=$end; $i++) {
				if ($_currentpage == $i) $pageswitcher .= " <a href='#' class='lepopup-table-list-page-active' onclick='return false'>".$i."</a> ";
				else $pageswitcher .= " <a href='".$_urlbase."p=".$i."'>".$i."</a> ";
			}
			if ($end < $_totalpages-1) $pageswitcher .= " <strong>...</strong> ";

			if ($_currentpage == $_totalpages) $pageswitcher .= " <a href='#' class='lepopup-table-list-page-active' onclick='return false'>".$_totalpages."</a> ";
			else $pageswitcher .= " <a href='".$_urlbase."p=".$_totalpages."'>".$_totalpages."</a> ";
			$pageswitcher .= "</span></div>";
		}
		return $pageswitcher;
	}

	function datetime_string($_datetime) {
		$dt = (string)$_datetime;
		if (strlen($dt) != 12) return '';
		return substr($dt, 0, 4).'-'.substr($dt, 4, 2).'-'.substr($dt, 6, 2).' '.substr($dt, 8, 2).':'.substr($dt, 10, 2);
	}

	function unixtime_string($_time, $_format = "Y-m-d H:i") {
		return date($_format, $_time+3600*$this->gmt_offset);
	}

	function validate_date($_date, $_format = 'Y-m-d') {
		$replacements = array(
			'yyyy-mm-dd' => 'Y-m-d',
			'dd/mm/yyyy' => 'd/m/Y',
			'mm/dd/yyyy' => 'm/d/Y',
			'dd.mm.yyyy' => 'd.m.Y'
		);
		if (array_key_exists($_format, $replacements)) $_format = $replacements[$_format];
		$date = DateTime::createFromFormat($_format, $_date);
		if ($date && $date->format($_format) === $_date) return $date;
		return false;
	}

	function validate_time($_time, $_format = 'H:i') {
		$replacements = array(
			'hh:ii' => 'H:i',
			'hh:ii aa' => 'h:i a'
		);
		if (array_key_exists($_format, $replacements)) $_format = $replacements[$_format];
		$time = DateTime::createFromFormat('Y-m-d '.$_format, '2020-01-01 '.$_time);
		if ($time && $time->format($_format) === $_time) return $time;
		return false;
	}

	function validate_email($_email, $_advanced = false) {
		global $wpdb;
		if (!preg_match("/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,19})$/i", $_email)) return false;
		if (!$_advanced) return true;
		if ($this->options['email-validator'] == 'basic') return true;

		$validation = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."lepopup_validations WHERE created > '".(time()-1800)."' AND type = 'email' AND hash = '".esc_sql(md5(strtolower($_email)))."'", ARRAY_A);
		if (!empty($validation)) return $validation['valid'] == 1;

		$result = true;
		if ($this->options['email-validator'] == 'advanced') {
			if(filter_var($_email, FILTER_VALIDATE_EMAIL) === false) return false;
			$domain = explode("@", $_email, 2);
			$result = (checkdnsrr($domain[1]) ? true : false);
		} else {
			$result = apply_filters('lepopup_validate_email_do_'.$this->options['email-validator'], true, $_email);
		}
		$wpdb->query("INSERT INTO ".$wpdb->prefix."lepopup_validations (type,hash,valid,created) VALUES ('email','".esc_sql(md5(strtolower($_email)))."','".esc_sql($result ? '1' : '0')."','".time()."')");
		return $result;
	}

	function validate_iban($_iban) {
		$_iban = strtolower(str_replace(' ', '', $_iban));
		$countries = array('al'=>28,'ad'=>24,'at'=>20,'az'=>28,'bh'=>22,'be'=>16,'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,'cy'=>28,'cz'=>24,'dk'=>18,'do'=>28,'ee'=>20,'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,'is'=>26,'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,'lt'=>20,'lu'=>20,'mk'=>19,'mt'=>31,'mr'=>27,'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,'pt'=>25,'qa'=>29,'ro'=>24,'sm'=>27,'sa'=>24,'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24);
		$chars = array('a'=>10,'b'=>11,'c'=>12,'d'=>13,'e'=>14,'f'=>15,'g'=>16,'h'=>17,'i'=>18,'j'=>19,'k'=>20,'l'=>21,'m'=>22,'n'=>23,'o'=>24,'p'=>25,'q'=>26,'r'=>27,'s'=>28,'t'=>29,'u'=>30,'v'=>31,'w'=>32,'x'=>33,'y'=>34,'z'=>35);

		if (array_key_exists(substr($_iban, 0, 2), $countries) && strlen($_iban) == $countries[substr($_iban,0,2)]) {
			$moved_char = substr($_iban, 4).substr($_iban,0,4);
			$moved_char_array = str_split($moved_char);
			$new_string = "";
			foreach ($moved_char_array as $key => $value){
				if (!is_numeric($moved_char_array[$key])){
					$moved_char_array[$key] = $chars[$moved_char_array[$key]];
				}
				$new_string .= $moved_char_array[$key];
			}
			if(bcmod($new_string, '97') == 1) {
				return true;
			}
		}
		return false;
	}	

	function get_rgb($_color) {
		if (strlen($_color) != 7 && strlen($_color) != 4) return false;
		$color = preg_replace('/[^#a-fA-F0-9]/', '', $_color);
		if (strlen($color) != strlen($_color)) return false;
		if (strlen($color) == 7) list($r, $g, $b) = array($color[1].$color[2], $color[3].$color[4], $color[5].$color[6]);
		else list($r, $g, $b) = array($color[1].$color[1], $color[2].$color[2], $color[3].$color[3]);
		return array("r" => hexdec($r), "g" => hexdec($g), "b" => hexdec($b));
	}

	static function random_string($_length = 16) {
		$symbols = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$string = "";
		for ($i=0; $i<$_length; $i++) {
			$string .= $symbols[rand(0, strlen($symbols)-1)];
		}
		return $string;
	}

	function close_html_tags($_html) {
		preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $_html, $result);
		$openedtags = $result[1];
		preg_match_all('#</([a-z]+)>#iU', $_html, $result);
		$closedtags = $result[1];
		$len_opened = count($openedtags);
		if (count($closedtags) == $len_opened) {
			return $_html;
		}
		$openedtags = array_reverse($openedtags);
		for ($i=0; $i < $len_opened; $i++) {
			if (!in_array($openedtags[$i], $closedtags)) {
				$_html .= '</'.$openedtags[$i].'>';
			} else {
				unset($closedtags[array_search($openedtags[$i], $closedtags)]);
			}
		}
		return $_html;
	}

	function extract_number($_value) {
		preg_match_all('#\((.*?)\)#', $_value, $match);
		if (is_array($match) && sizeof($match[1]) > 0) {
			$var_value = str_replace(',', '.', $match[1][sizeof($match[1])-1]);
			if (is_numeric($var_value)) return $var_value;
			else return $_value;
		} else {
			$var_value = str_replace(',', '.', $_value);
			if (is_numeric($var_value)) return $var_value;
			else return $_value;
		}
	}
	
	function wpml_parse_form_id($_form_id, $_default_all_value = '', $_current_language = '') {
		$form_id = $_form_id;
		$forms = array('all' => $_default_all_value);
		$pairs = explode(',', $_form_id);
		foreach($pairs as $pair) {
			$data = explode(':', $pair);
			if (sizeof($data) != 2) $forms['all'] = $data[0];
			else $forms[$data[0]] = $data[1];
		}
		if (!defined('ICL_LANGUAGE_CODE')) $form_id = $forms['all'];
		else {
			if (!empty($_current_language) && array_key_exists($_current_language, $forms)) $form_id = $forms[$_current_language];
			else if (array_key_exists(ICL_LANGUAGE_CODE, $forms)) $form_id = $forms[ICL_LANGUAGE_CODE];
			else $form_id = $forms['all'];
		}
		return $form_id;
	}
	
	function wpml_compile_form_id($_form_id, $_old) {
		$new = $_form_id;
		if (defined('ICL_LANGUAGE_CODE')) {
			if (ICL_LANGUAGE_CODE == 'all') {
				$new = $_form_id;
			} else {
				$forms = array();
				$pairs = explode(',', $_old);
				foreach($pairs as $pair) {
					$data = explode(':', $pair);
					if (sizeof($data) != 2) $forms['all'] = $data[0];
					else $forms[$data[0]] = $data[1];
				}
				$forms[ICL_LANGUAGE_CODE] = $_form_id;
				$data = array();
				foreach ($forms as $key => $value) {
					$data[] = $key.':'.$value;
				}
				$new = implode(',', $data);
			}
		}
		return $new;
	}

	function register_session() {
		if (headers_sent()) return;
		if (!session_id()) {
			$secure = false;
			if (array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
				$schema = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']);
				if (strpos($schema, 'https') !== false) $secure = true;
				else $secure = false;
			} else if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != 'off') $secure = true;
			if ($secure) {
				if (PHP_VERSION_ID < 70300) session_set_cookie_params(0, '/; samesite=none', '', true);
				else session_set_cookie_params(array('samesite' => 'None', 'secure' => true));
			}
			session_start(array('read_and_close' => true));
		}
	}

	function handle_demo_mode() {
		if (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true && !defined('UAP_CORE') && is_user_logged_in() && !current_user_can('edit_posts') && is_admin()) {
			$this->demo_mode = true;
		} else if (defined('HALFDATA_DEMO') && HALFDATA_DEMO === true && defined('UAP_CORE')) {
			$this->demo_mode = true;
		}
	}
	
	function widgets_init() {
		include_once(dirname(__FILE__).'/widget.php');
		register_widget('lepopup_widget');
	}
}
$lepopup = new lepopup_class();
?>