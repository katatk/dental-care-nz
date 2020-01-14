<?php

use Database\QueryException;
use DatabaseObject\Entity;
use DatabaseObject\Generator;
use DatabaseObject\Property\LinkToProperty;
use DatabaseObject\Property\Property;
use DatabaseObject\Property\LinkFromMultipleProperty;
use DatabaseObject\Property\ImageProperty;
use DatabaseObject\Column;

use DatabaseObject\FormElement\GeneratorElement;
use DatabaseObject\FormElement\Editor;
use DatabaseObject\FormElement\Text;
use DatabaseObject\FormElement\Textarea;
use DatabaseObject\FormElement\ImageElement;


	class Treatments extends ContentPage
	{
		const TABLE = "treatments";

		const ID_FIELD = "treatments_id";

		const SINGULAR = 'treatments';
		const PLURAL = 'treatments';

		/** @var	bool	Whether Testimonials use positioning */
		const HAS_POSITION = true;
		const HAS_ACTIVE = true;
		const LABEL_PROPERTY = 'name';

		const IMAGE_LOCATION = DOC_ROOT . "/resources/images/treatments/";
		const IMAGE_WIDTH = TEAM_IMAGE_WIDTH;
		const IMAGE_HEIGHT = TEAM_IMAGE_HEIGHT;
		const IMAGE_SCALE_TYPE = ImageProperty::SCALE;

		public $name = "";
		public $image = null;
		public $description = "";

		/**
		 * Sets the properties for a Testimonial
		 */
		protected static function properties()
		{
			parent::properties();

			static::addProperty(new Property("name", "name", "string"));
			static::addProperty(new Property("bio", "bio", "string"));
			static::addProperty(new ImageProperty('image', 'image', static::IMAGE_LOCATION, static::IMAGE_WIDTH, static::IMAGE_HEIGHT, static::IMAGE_SCALE_TYPE));
		}

		/**
		 * Sets the array of Columns that are displayed to the user for this object type
		 */
		 protected static function columns()
		{
			static::addColumn(new Column('Treatments Profiles', 'name'));

			parent::columns();
		}

		/**
		 * Gets all the active testimonials
		 * @return	static[]	Said testimonials
		 */
		public static function loadAllActive()
		{
			return static::loadAllFor("active", true, ["position" => true]);
		}

		/**
		 * Gets a random Testimonial
		 * @return	Testimonial		A random Testimonial
		 */
		// public static function loadRandom()
		// {
		// 	$query = "SELECT ~PROPERTIES "
		// 		   . "FROM ~TABLE "
		// 		   . "ORDER BY RAND() "
		// 		   . "LIMIT 1";
		//
		// 	return static::makeOne($query);
		// }

		/**
		 * Sets the Form Elements for this object
		 */
		 protected function formElements()

		{
			parent::formElements();

			$this->addFormElement(new Text('name', 'Name'));
			$this->addFormElement(new Textarea('bio', 'Bio'));
			$this->addFormElement(new ImageElement("image", 'Image', $this->image));

		}

		//region AdminNavItemGenerator

		/**
		 * Gets the nav item for this class
		 * @return    AdminNavItem        The admin nav item for this class
		 */
		public static function getAdminNavItem()
		{
			return new AdminNavItem(static::getAdminNavLink(), "Treatments", [static::class], Registry::isEnabled("Treatments"));
		}

		//endregion
	}
