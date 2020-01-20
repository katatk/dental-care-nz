<?php

	use Configuration\AdminNavItem;
	use Configuration\AdminNavItemGenerator;
	use Configuration\Registry;
	use DatabaseObject\FormElement\ImageElement;
	use DatabaseObject\Generator;
	use DatabaseObject\Property\Property;
	use DatabaseObject\Property\ImageProperty;
	use DatabaseObject\Column;
    use DatabaseObject\FormElement\BasicEditor;

	use DatabaseObject\FormElement\Text;
	use DatabaseObject\FormElement\Textarea;


	class Treatments extends Generator implements AdminNavItemGenerator
	{
		const TABLE = "treatments";

		const ID_FIELD = "id";

		const SINGULAR = 'treatment';
		const PLURAL = 'treatments';

		/** @var	bool	Whether Testimonials use positioning */
		const HAS_POSITION = true;
		const HAS_ACTIVE = true;
		const LABEL_PROPERTY = 'name';

		const IMAGE_LOCATION = DOC_ROOT . "/resources/images/treatments/";
		const IMAGE_WIDTH = TREATMENTS_IMAGE_WIDTH;
		const IMAGE_HEIGHT = TREATMENTS_IMAGE_HEIGHT;
		const IMAGE_SCALE_TYPE = ImageProperty::SCALE;

		public $name = "";
		public $description = "";
		public $image = null;

		/**
		 * Sets the properties for a Testimonial
		 */
		protected static function properties()
		{
			parent::properties();

			static::addProperty(new Property("name", "name", "html"));
			static::addProperty(new Property("description", "description", "string"));
			static::addProperty(new ImageProperty('image', 'image', static::IMAGE_LOCATION, static::IMAGE_WIDTH, static::IMAGE_HEIGHT, static::IMAGE_SCALE_TYPE));
		}

		/**
		 * Sets the array of Columns that are displayed to the user for this object type
		 */
		 protected static function columns()
		{
			static::addColumn(new Column('Treatments', 'name'));

			parent::columns();
		}

		/**
		 * Gets all the active treatments members
		 * @return	static[]
		 */
		public static function loadAllActive()
		{
			return static::loadAllFor("active", true, ["position" => true]);
		}

		/**
		 * Sets the Form Elements for this object
		 */
		 protected function formElements()

		{
			parent::formElements();

			$this->addFormElement(new BasicEditor('name', 'Name'));
			$this->addFormElement(new Textarea('description', 'Description'));
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

	}
