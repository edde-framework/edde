<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\ITreeTraversal;
	use Edde\Api\Resource\IResource;

	/**
	 * So Little Johnny's teacher is warned at the beginning of the school year not to ever make a bet with Johnny unless she is absolutely sure she will win it.
	 * One day in class, Johnny raises his hand and says "teacher, I'll bet you $50 I can guess what color your underwear is."
	 * She replies, "okay, meet me after class and we'll settle it." But beforeclass ends, she goes to the restroom and removes her panties.
	 * After class is over and the studentsclear out, Johnny makes his guess.
	 * "Blue."
	 * "Nope. You got it wrong," she says as she lifts her skirt to reveal she isn't wearing any underwear.
	 * "Well come with me out to my dads car, he's waiting for me, and I'll get you the money." She follows him out.
	 * When they get to the car she informs his dad that he got the bet wrong and that she showed Johnny that she wasn't wearing any underwear.
	 * His dad exclaims: "That mother fucker! He bet me $100 this morning that he'd see your pussy before the end of the day!"
	 */
	interface ICompiler extends IConfigurable, ITreeTraversal {
		/**
		 * @param string $name
		 * @param IMacro $macro
		 *
		 * @return ICompiler
		 */
		public function registerMacro(string $name, IMacro $macro): ICompiler;

		/**
		 * return macro or throw an exception
		 *
		 * @param string $name
		 * @param INode  $source
		 *
		 * @return IMacro
		 */
		public function getMacro(string $name, INode $source): IMacro;

		/**
		 * execute template compilation
		 *
		 * @param string    $name
		 * @param IResource $resource
		 *
		 * @return IFile
		 */
		public function compile(string $name, IResource $resource): IFile;
	}
