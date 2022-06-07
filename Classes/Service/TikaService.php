<?php
namespace Networkteam\Tika\Service;

use Networkteam\Tika\Exception;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\PersistentResource;

/**
 * Tika Service. See http://tika.apache.org/1.3/gettingstarted.html#Using_Tika_as_a_command_line_utility
 *
 * @Flow\Scope("singleton")
 */
class TikaService {

	/**
	 * @Flow\Inject
	 * @var \Neos\Flow\Package\PackageManager
	 */
	protected $packageManager;

	/**
	 * @var string
	 */
	protected $javaCommand;

	/**
	 * @var string
	 */
	protected $tikaPathAndFilename;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * @return void
	 */
	public function initializeObject() {
		$this->javaCommand = isset($this->settings['javaCommand']) ? $this->settings['javaCommand'] : 'java';
		if (isset($this->settings['tikaPathAndFilename'])) {
			$this->tikaPathAndFilename = $this->settings['tikaPathAndFilename'];
		} else {
			$packageResourcesPath = $this->packageManager->getPackage('Networkteam.Tika')->getResourcesPath();
			$this->tikaPathAndFilename = \Neos\Utility\Files::concatenatePaths(array($packageResourcesPath, 'Private/Jar/tika-app.jar'));
		}
	}

	/**
	 * @param \Neos\Flow\ResourceManagement\PersistentResource $resource
	 * @param string $option
	 * @return string
	 * @throws \Networkteam\Tika\Exception
	 */
	protected function execute(PersistentResource $resource, $option) {
		// this only works for local stores
		$streamMetaData = stream_get_meta_data($resource->getStream());
		$pathAndFilename = $streamMetaData['uri'];
		$command = sprintf('%s -jar %s --%s %s', $this->javaCommand, $this->tikaPathAndFilename, $option, $pathAndFilename);
		$output = array();
		exec($command, $output, $result);
		if ($result !== 0) {
			$exceptionMessage = sprintf('Execution of Tika failed with exit code %d', $result);
			if (count($output) > 0) {
				$exceptionMessage .= ' and output:' .  PHP_EOL . PHP_EOL . implode(PHP_EOL, $output);
			} else {
				$exceptionMessage .= ' and no output.';
			}
			$exceptionMessage .= PHP_EOL . PHP_EOL . 'The erroneous command was:' . PHP_EOL . $command;
			throw new Exception($exceptionMessage, 1363701105);
		}

		return implode(PHP_EOL, $output);
	}

	/**
	 * @param \Neos\Flow\ResourceManagement\PersistentResource $resource
	 * @return string
	 */
	public function getText(PersistentResource $resource) {
		return $this->execute($resource, 'text');
	}

	/**
	 * @param \Neos\Flow\ResourceManagement\PersistentResource $resource
	 * @return string
	 */
	public function getTextMain(PersistentResource $resource) {
		return $this->execute($resource, 'text-main');
	}

	/**
	 * @param \Neos\Flow\ResourceManagement\PersistentResource $resource
	 * @return string
	 */
	public function getLanguage(PersistentResource $resource) {
		return $this->execute($resource, 'language');
	}

	/**
	 * @param \Neos\Flow\ResourceManagement\PersistentResource $resource
	 * @return string
	 */
	public function getContentType(PersistentResource $resource) {
		return $this->execute($resource, 'detect');
	}

	/**
	 * @param \Neos\Flow\ResourceManagement\PersistentResource $resource
	 * @return string
	 */
	public function getXhtml(PersistentResource $resource) {
		return $this->execute($resource, 'xml');
	}

	/**
	 * @param \Neos\Flow\ResourceManagement\PersistentResource $resource
	 * @return string
	 */
	public function getHtml(PersistentResource $resource) {
		return $this->execute($resource, 'html');
	}

	/**
	 * @param \Neos\Flow\ResourceManagement\PersistentResource $resource
	 * @return array
	 */
	public function getMetadata(PersistentResource $resource) {
		$metadataString = $this->execute($resource, 'json');
		return json_decode($metadataString, TRUE);
	}
}
