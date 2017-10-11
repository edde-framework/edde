<?php
	namespace App\Common\Index;

		use Edde\Common\Object\Object;

		/**
		 * Individual view usually shares the same, so it's good practice to prepare formal
		 * ancestor to simplify the code between them.
		 *
		 * However it should not be proprietary for view type (for example this class should not
		 * hold any http related stuff).
		 */
		abstract class AbstractIndexView extends Object {
		}
