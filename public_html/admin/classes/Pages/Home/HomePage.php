<?php
	namespace Pages\Home;

	use Pages\Page;
	use Pages\ContentPage;

  use DatabaseObject\FormElement\Editor;
  use DatabaseObject\FormElement\Text;
  use DatabaseObject\Property\Property;
	use DatabaseObject\Property\ImageProperty;
	use DatabaseObject\FormElement\ImageElement;
	use DatabaseObject\FormElement\Textarea;
	use DatabaseObject\FormElement\Checkbox;
	use DatabaseObject\FormElement\GridElement;

	use Files\Image;

	class HomePage extends ContentPage
	{

		const TABLE = "home_page";

		const SLIDE_CLASS = HomeSlide::class;


		// public $image_1 = null;


		/**
		 * Sets the array of Properties that determine how this Object interacts with the database
		 */
		protected static function properties()
		{
			parent::properties();



		}

		/**
		 * Sets the Form Elements for this Entity
		 */
		protected function formElements()
		{
			parent::formElements();

			if(PAGE_HAS_SLIDESHOW)
			{
				$this->addFormElement(new Checkbox("useSlideshow", 'Display slideshow'), "Slideshow");

				/** @var ImageProperty $imageProperty */
				$this->addFormElement(new GridElement('slides', 'Slides'), "Slideshow");
			}


		}
	}
