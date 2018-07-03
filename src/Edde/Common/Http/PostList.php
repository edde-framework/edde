<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IPostList;
	use Edde\Common\Collection\AbstractList;

	class PostList extends AbstractList implements IPostList {
		static public function create(array $postList) {
			$self = new self();
			foreach ($postList as $name => $value) {
				$self->set($name, $value);
			}
			return $self;
		}
	}
