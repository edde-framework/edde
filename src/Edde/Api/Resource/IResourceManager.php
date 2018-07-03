<?php
	declare(strict_types=1);

	namespace Edde\Api\Resource;

	use Edde\Api\Node\INode;

	interface IResourceManager extends IResourceProvider {
		/**
		 * register resource provider
		 *
		 * @param IResourceProvider $resourceProvider
		 *
		 * @return IResourceManager
		 */
		public function registerResourceProvider(IResourceProvider $resourceProvider): IResourceManager;

		/**
		 * IResource is created from the given url and then handler is selected based on a mime
		 *
		 * @param string $url
		 * @param string $mime override/specify mimetype
		 * @param INode  $root
		 *
		 * @return INode
		 */
		public function handle(string $url, string $mime = null, INode $root = null): INode;

		/**
		 * same as handle only formally for a file
		 *
		 * @param string      $file
		 * @param string|null $mime
		 * @param INode       $root
		 *
		 * @return INode
		 */
		public function file(string $file, string $mime = null, INode $root = null): INode;

		/**
		 * @param IResource   $resource
		 * @param string|null $mime
		 * @param INode       $root
		 *
		 * @return INode
		 */
		public function resource(IResource $resource, string $mime = null, INode $root = null): INode;
	}
