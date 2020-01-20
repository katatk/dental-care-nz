<?php

namespace Configuration;

use DatabaseObject\Generator;
use DatabaseObject\Property\Property;
use DatabaseObject\FormElement\Element;
use DatabaseObject\FormElement\Text;
use DatabaseObject\FormElement\Textarea;

// added classes for slide
use Database\QueryException;
use DatabaseObject\Entity;
use DatabaseObject\Property\LinkToProperty;
use DatabaseObject\FormElement\ImageElement;
use DatabaseObject\Property\ImageProperty;
use DatabaseObject\FormElement\Hidden;
use DatabaseObject\FormElement\BasicEditor;
use Files\Image;
/*use Pages\Page;*/
use Slide;

/**
 * Handles global configuration
 * @author    Callum Muir <callum@activatedesign.co.nz>
 */
class Configuration extends Generator implements AdminNavItemGenerator, Slide
{
    const TABLE = "configuration";
    const ID_FIELD = "configuration_id";
    const SINGULAR = "configuration";
    const PLURAL = "configuration";
    const LABEL_PROPERTY = "siteName";
    const USE_TABS = true;

    /**
     *    Default admin email address, used for dev sites
     * @var string DEV_EMAIL send site error alerts etc to this address, usually leave this
     *        as the address of the responsible Activate Design developer or programmer/support@ad
     */
    const DEV_EMAIL = 'programmer@activatedesign.co.nz';

    /** @var Configuration $singleton */
    private static $singleton = null;

    public $siteName = '';
    public $phone = '';
    public $email = '';
    public $address = '';
    public $openingHours = '';
    public $prefooterText = '';
    public $prefooterTextLink = '';
    public $footerHeadingOne = '';
    public $footerHeadingTwo = '';
    public $ctabanner = '';

    // cta banner
    public $ctaTmage = null;
    public $ctaResponsiveImage = null;
    public $ctaTitle = '';
    public $ctaSubtitle = '';
    public $ctaDescription = '';
    public $ctaLink = '';
    public $ctaButton = '';

   /* const HAS_ACTIVE = false;
    const HAS_POSITION = false;*/

    const DEFAULT_BUTTON_TEXT = "Find out more";

    const CONTAINS_MULTIPLE = PAGE_SLIDE_HAS_MULTIPLE_IMAGES;
    const IMAGES_LOCATION = DOC_ROOT . "/resources/images/page/";

    const DESKTOP_IMAGE_WIDTH = PAGE_SLIDESHOW_WIDTH;
    const DESKTOP_IMAGE_HEIGHT = PAGE_SLIDESHOW_HEIGHT;
    const DESKTOP_IMAGE_RESIZE_TYPE = ImageProperty::CROP;

    const RESPONSIVE_IMAGE_WIDTH = PAGE_SLIDESHOW_RESPONSIVE_WIDTH;
    const RESPONSIVE_IMAGE_HEIGHT = PAGE_SLIDESHOW_RESPONSIVE_HEIGHT;
    const RESPONSIVE_IMAGE_RESIZE_TYPE = ImageProperty::CROP;

    public $active = false;

    //google analytics and tag manager
    public $analyticsId = '';
    public $tagManagerId = '';
    public $googleSiteVerification = '';

    /**
     * Sets the properties for this class
     */
    protected static function properties()
    {
        parent::properties();

        static::addProperty(new Property('siteName', 'site_name', 'string'));
        static::addProperty(new Property('phone', 'phone', 'string'));
        static::addProperty(new Property('email', 'email', 'string'));
        static::addProperty(new Property('address', 'address', 'string'));
        static::addProperty(new Property('openingHours', 'opening_hours', 'string'));
        static::addProperty(new Property('prefooterText', 'prefooter_text', 'string'));
        static::addProperty(new Property('prefooterTextLink', 'prefooter_text_link', 'string'));
        static::addProperty(new Property('footerHeadingOne', 'footer_heading_one', 'string'));
        static::addProperty(new Property('footerHeadingTwo', 'footer_heading_two', 'string'));
        static::addProperty(new Property('analyticsId', 'analytics_id', 'string'));
        static::addProperty(new Property('tagManagerId', 'tag_manager_id', 'string'));
        static::addProperty(new Property('googleSiteVerification', 'google_site_verification', 'string'));

        // for the cta banner
       /* static::addProperty(new LinkToProperty("page", "page_id", Page::class));*/
        static::addProperty(new Property('ctaTitle', 'cta_title', 'html'));
        static::addProperty(new Property('ctaSubtitle', 'cta_subtitle', 'string'));
        static::addProperty(new Property('ctaDescription', 'cta_description', 'html'));
        static::addProperty(new Property("ctaLink", "cta_link", "string"));
        static::addProperty(new Property("ctaButton", "cta_button", "string"));
        static::addProperty(new ImageProperty('ctaImage', 'cta_image', static::IMAGES_LOCATION, static::DESKTOP_IMAGE_WIDTH, static::DESKTOP_IMAGE_HEIGHT, static::DESKTOP_IMAGE_RESIZE_TYPE));
        // don't need to make the property conditional, can just always be null.
        static::addProperty(new ImageProperty('ctaResponsiveImage', 'cta_responsive_image', static::IMAGES_LOCATION, static::RESPONSIVE_IMAGE_WIDTH, static::RESPONSIVE_IMAGE_HEIGHT, static::RESPONSIVE_IMAGE_RESIZE_TYPE));
    }

    /**
     * Gets the Configuration, or, if it doesn't exist, creates it
     * @return    Configuration    The configuration object
     */
    public static function acquire()
    {
        if (static::$singleton === null) {
            $query = "SELECT ~PROPERTIES "
                . "FROM ~TABLE "
                . "LIMIT 1";

            static::$singleton = static::makeOne($query, []);

            if (static::$singleton->isNull()) {
                static::$singleton = new static();
                static::$singleton->save();
            }
        }

        return static::$singleton;
    }

    /**
     * Any attempt to get this object should return the singleton
     * @param array $values Unused
     * @return    Configuration                The Configuration object
     */
    public static function loadForMultiple(array $values)
    {
        return static::acquire();
    }

    /**
     * Any attempt to get multiple of this object should return the singleton in an array
     * @param array $values Unused
     * @param bool[] $orderBy Unused
     * @return    Configuration[]                An array containing the single Configuration object
     */
    public static function loadAllForMultiple(array $values, array $orderBy = [])
    {
        return [static::acquire()];
    }

    /**
     * Hides the return link
     * @param Generator $generator Unused
     * @return    string                    Empty string
     */
    public static function returnLink(Generator $generator = null)
    {
        return "/admin";
    }

    /**
     * Hides the add link
     * @param Generator $generator Unused
     * @return    string                    Empty string
     */
    public static function addLink(Generator $generator = null)
    {
        return "";
    }

    /**
     * Gets the appropriate site email address depending on if the site is a dev site or not
     *
     * @return string    email address
     *        - send emails for site visitors (eg confirmations) from this address
     *        - send email for the site owner (eg from contact forms) to this address unless otherwise
     *            defined
     */
    public static function getAdminEmail()
    {
        return (IS_DEV_SITE) ? static::DEV_EMAIL : static::acquire()->email;
    }

    /**
     * Gets the site name so we can just call this menthod instead of acquire()ing everywhere we need it
     *
     * @return string the site name
     */
    public static function getSiteName()
    {
        return static::acquire()->siteName;
    }

    /**
     * Preserving USER_NOTIFICATIONS_ADDRESS until the new users stuff gets here
     *
     * @return string USER_NOTIFICATIONS_ADDRESS email address to send notifications of details changes
     *        (not addresses at present) when a user updates their own details. Set to '' to disable these
     *        notifications
     */
    public static function getUserNotificationEmail()
    {
        return static::getAdminEmail();
    }

    public static function getScalingMessage()
    {
        $scalingMessage = 'For a banner: simply add one image. This image will be ' . (static::DESKTOP_IMAGE_RESIZE_TYPE === ImageProperty::CROP ? 'cropped' : 'scaled') . ' down to a maximum width of '.static::DESKTOP_IMAGE_WIDTH.' pixels and a maximum height of '.static::DESKTOP_IMAGE_HEIGHT.' pixels. <br />';

        return $scalingMessage;
    }

    /**
     * Sets the Form Elements for this object
     */
    protected function formElements()
    {
        $this->addFormElement((new Text('siteName', 'Site Name <span>(appears in template locations and emails)</span>'))->addValidation(Element::REQUIRED), 'Contact Details');
        $this->addFormElement(new Text('phone', 'Phone'), 'Contact Details');
        $this->addFormElement((new Text('email', 'Email'))->addValidation(Element::REQUIRED), 'Contact Details');
        $this->addFormElement(new Textarea('address', 'Address'), 'Contact Details');
        $this->addFormElement(new Textarea('openingHours', 'Opening Hours'), 'Contact Details');
        $this->addFormElement(new Text('prefooterText', 'Prefooter Text'), 'Footer');
        $this->addFormElement(new Text('prefooterTextLink', 'Prefooter Text Link'), "Footer");
        $this->addFormElement(new Text('footerHeadingOne', 'Footer Heading One'), 'Footer');
        $this->addFormElement(new Text('footerHeadingTwo', 'Footer Heading Two'), 'Footer');
        $this->addFormElement(new Text('analyticsId', 'Google Analytics ID'), '3rd Party Integrations');
        $this->addFormElement(new Text('tagManagerId', 'Google Tag Manager ID'), '3rd Party Integrations');
        $this->addFormElement(new Text('googleSiteVerification', 'Google Site Verification'), '3rd Party Integrations');

        if(static::CONTAINS_MULTIPLE)
        {
            $this->addFormElement((new ImageElement('ctaImage', 'Image'))->setIsRepresentative(true)->setClasses('half first'), 'Call to Action');

            $this->addFormElement((new ImageElement('ctaResponsiveImage', 'Responsive Image (phones, tablets) <span>Optional</span>'))->setClasses('half'), 'Call to Action');
        }
        else
        {
            $imageElement = (new ImageElement('ctaImage', 'Image'))->setIsRepresentative(true)->setScalingMessage(static::getScalingMessage(), 'Call to Action');
            $this->addFormElement($imageElement);
        }

        if (PAGE_SLIDESHOW_CAPTION)
        {
            $this->addFormElement((new BasicEditor('ctaTitle', 'Title <span>(optional)</span>')), 'Call to Action');
            $this->addFormElement((new Text('ctaSubtitle', 'Sub Title <span>(optional)</span>')), 'Call to Action');
            $this->addFormElement((new Textarea('ctaDescription', 'Description <span>(optional)</span>')), 'Call to Action');
        }

        if(PAGE_SLIDESHOW_LINK)
        {
            $this->addFormElement((new Text("ctaLink", "Link <span>(optional, if no link set, button will not display)</span>")), 'Call to Action');

            if(PAGE_SLIDESHOW_BUTTON)
            {
                $this->getFormElements()['ctaLink']->setClasses('half first');
                $this->addFormElement(((new Text("ctaButton", "Button text <span>(optional, if left blank, text will say 'Find out more')</span>"))->setClasses('half')), 'Call to Action');
            }
        }
    }

    /**
     * Same as in Generator, but without a return link
     *
     * @return    string    The HTML for the html above the form
     */
    public function generateEditFormHeading()
    {
        $labelProperty = static::LABEL_PROPERTY;

        $html = "";
        $html .= "<h1>\n";
        $html .= "Edit " . ($labelProperty === "id" ? ucfirst(static::SINGULAR) : $this->$labelProperty) . "\n";
        $html .= "</h1>\n";

        return $html;
    }

    /**
     * Creates a nav at the top of the create and edit form pages
     *        by default includes links to
     *            - add another item without having to click back to the table page
     *            - breadcrumbs
     *
     * @param Generator $generator A Review that the return link returns from
     * @return    string    html to be included in the nav
     */
    public static function generateItemNav(Generator $generator = null)
    {
        $html = '';
        if ($generator !== null) {
            $html .= static::addLink();
        }

        return $html;
    }

    /**
     * Returns only digits of a phone number
     * @param string $phone The non processed phone number
     * @return    string            The digits of the phone number
     */
    public static function getDigits($phone)
    {
        return preg_replace("/[^0-9]/", "", $phone);
    }

    /**
     * Converts a phone number to use the international format (for New Zealand)
     * @param    $str    the phone number to format
     * @return    string    the phone number in international format
     */
    public static function formatAsInternationalPhone($str)
    {
        if (strpos($str, '+') === 0) {
            // already in international format
            $return = '+' . preg_replace('/[^\d]/', '', $str);
        } else {
            $return = '+64' . ltrim(preg_replace('/[^\d]/', '', $str), 0);
        }

        return $return;
    }

    /**
     * For update messages
     *
     * @return    string    The word "Configuration"
     * @deprecated with addition of siteName
     *
     */
    public function get_label()
    {
        return "Configuration";
    }

    /**
     * Gets the phone number digits
     * @return    string    The phone number with all non-digit characters stripped out
     */
    public function getPhoneDigits()
    {
        return static::getDigits($this->phone);
    }

    /**
     * Retrieves the link to this Generator's table
     * @return    string    The link to the table
     */
    public static function getAdminNavLink()
    {
        return static::acquire()->getEditLink();
    }

    /**
     * Get a google map for the configuration address
     *
     * @param Bool $useBusinessName If true the site name is prepended to the address,
     *         and is then used as the label for the map pin.
     *
     * @return String    html for an embeded map
     */
    public function getMap($useBusinessName = false)
    {
        return static::makeMap($this->address, $useBusinessName);
    }

    /**
     * Get a google map for an address
     * @param string $address Address to get a map for
     * @param bool $useBusinessName Whether to add the site name to the start of the address. This is unreliable and mostly requires them to have a business listing on Google.
     * @return    string                        HTML for the embedded map
     */
    public static function makeMap($address = '', $useBusinessName = false)
    {
        if ($address !== "") {
            $fullAddress = "";

            if ($useBusinessName) {
                $fullAddress .= static::getSiteName() . ", ";
            }

            // Remove carriage returns, and replace new lines with commas, which are typically preferred by Google
            $fullAddress .= str_replace(["\r", "\n"], ["", ", "], $address);

            $url = 'https://maps.google.com/maps?hl=en&amp;q=' . rawurlencode($fullAddress) . "&amp;output=embed";
            return '<iframe frameborder="0" style="border:0" src="' . $url . '" allowfullscreen="allowfullscreen"></iframe>';
        } else {
            return "";
        }
    }

    //region AdminNavItemGenerator

    /**
     * Gets the nav item for this class
     * @return    AdminNavItem        The admin nav item for this class
     */
    public static function getAdminNavItem()
    {
        return new AdminNavItem(static::getAdminNavLink(), "Configuration", [static::class], Registry::isEnabled("Configuration"));
    }

    //endregion

    //region Slide

    /**
     * Gets the image for this slide
     * @return    Image    The image for this slide
     */
    public function getSlideImage(): ?Image
    {
        return $this->image;
    }

    /**
     * Gets the responsive image for this slide
     *
     * Can always return null. Template uses this to just output non-responsive code without caring
     * why there is only one image (not enabled or not uploaded)
     *
     * @return    Image    The smaller image for this slide
     */
    public function getSmallScreenImage(): ?Image
    {
        return static::CONTAINS_MULTIPLE ? $this->responsiveImage : null;
    }

    /**
     * Gets the caption for this slide
     * @return    string    The caption for this slide
     */
    public function getSlideText(): string
    {
        $html = "";

        if($this->ctaTitle !== '')
        {
            $html .= "<h2>";
            $html .= nl2br($this->ctaTitle) . "\n";
            $html .= "</h2>";
        }

        if($this->ctaSubtitle !== '')
        {
            $html .= "<p class='subtitle'>";
            $html .= nl2br($this->ctaSubtitle) . "\n";
            $html .= "</p>";
        }

        if($this->ctaDescription !== '')
        {
            $html .= "<p class='description'>";
            $html .= nl2br($this->ctaDescription) . "\n";
            $html .= "</p>";
        }

        // if button field not empty, create a button
        if($this->ctaButton !== "")
        {
            $buttonText = static::DEFAULT_BUTTON_TEXT;

            if($this->ctaButton !== "")
            {
                $buttonText = $this->ctaButton;
            }

            // if no link set, default to contact page
            if($this->ctaLink !== "")
            {
                $buttonLink = $this->ctaLink;
            }
            else
            {
                $buttonLink = "Contact/";
            }

            $html .= "<div class='button-container'><a href='/" . $buttonLink . "' class='button light'>" . $buttonText . "</a></div>\n";
        }

        return $html;
    }

    //endregion
}
