<?php
	declare(strict_types=1);

	namespace Edde\Api\Asset;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\File\IFile;
	use Edde\Api\Resource\IResource;

	/**
	 * General storage for saving application data.
	 */
	interface IAssetStorage extends IConfigurable {
		/**
		 * save the given resource to the file storage and return a new resource (local resource file)
		 *
		 * @param IResource $resource
		 *
		 * @return IResource
		 */
		public function store(IResource $resource);

		/**
		 * create an empty file
		 *
		 * @param string $name
		 *
		 * @return IFile
		 */
		public function allocate(string $name): IFile;
	}
