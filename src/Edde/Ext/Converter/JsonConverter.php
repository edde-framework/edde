<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\File\IFile;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeUtils;

	/**
	 * Json converter from json encoded string to "something".
	 */
	class JsonConverter extends AbstractConverter {
		use LazyHttpResponseTrait;

		/**
		 * Objective: shoot yourself in the foot using a computer language.
		 *
		 * Perl: You shoot yourself in the foot.
		 *
		 * PHP: You make a website of yourself shooting yourself in the foot.
		 *
		 * Java: Your foot shoots itself while it waits for the team of programmers to finish the code.
		 *
		 * C++: You accidentally create a dozen instances of yourself and shoot them all in the foot. Providing emergency medical assistance is impossible since you can't tell which are bitwise copies and which are just pointing at others and saying, "That's me, over there."
		 *
		 * Visual Basic: You shoot Steve Jobs in the foot.
		 *
		 * FORTRAN: You shoot yourself in each toe, iteratively, until you run out of toes, then you read in the next foot and repeat. If you run out of bullets, you continue with the attempts to shoot yourself anyways because you have no exception-handling capability.
		 *
		 * Pascal: The compiler won't let you shoot yourself in the foot.
		 *
		 * LISP: You shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds...
		 *
		 * BASIC: Shoot yourself in the foot with a water pistol.
		 *
		 * Unix:
		 *
		 * % ls
		 * foot.c foot.h foot.o toe.c toe.o
		 * % rm * .o
		 * rm:.o no such file or directory
		 * % ls
		 * %Concurrent Euclid: You shoot yourself in somebody else's foot.
		 *
		 * Assembler: You try to shoot yourself in the foot, only to discover you must first invent the gun, the bullet, the trigger, and your foot.
		 */
		public function __construct() {
			$this->register('array', [
				'json',
				'http+json',
				'application/json',
				'http+application/json',
			]);
			$this->register([
				'application/json',
				'json',
			], [
				'array',
				'object',
				'node',
				INode::class,
			]);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 * @throws NodeException
		 */
		public function convert($convert, string $source, string $target, string $mime) {
			switch ($source) {
				case 'application/json':
				case 'json':
					$this->unsupported($convert, $target, $convert instanceof IFile || is_string($convert));
					$convert = $convert instanceof IFile ? $convert->get() : $convert;
					switch ($target) {
						case 'array':
							return json_decode($convert, true);
						case 'object':
							return json_decode($convert);
						case 'node':
						case INode::class:
							return NodeUtils::node(new Node(), json_decode($convert));
					}
					break;
				case 'array':
					switch ($target) {
						case 'http+json':
						case 'http+application/json':
							$this->httpResponse->send();
							echo $json = json_encode($convert);
							return $json;
						case 'json':
						case 'application/json':
							return json_encode($convert);
					}
					break;
			}
			$this->exception($source, $target);
		}
	}
