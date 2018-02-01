<?php
namespace Networkteam\Tika\Tests\Functional;

/*                                                                                 *
 * This script belongs to the TYPO3 Flow package "Networkteam.Tika".               *
 *                                                                                 *
 *                                                                                 */

use TYPO3\Flow\Tests\FunctionalTestCase;

class TikaServiceTest extends FunctionalTestCase {

	static protected $testablePersistenceEnabled = TRUE;

	/**
	 * @test
	 */
	public function getContentTypeForResourceExtractsContentType() {
		/** @var \Networkteam\Tika\Service\TikaService $service */
		$service = $this->objectManager->get('Networkteam\Tika\Service\TikaService');
		/** @var \TYPO3\Flow\ResourceManagement\ResourceManager $resourceManager */
		$resourceManager = $this->objectManager->get('TYPO3\Flow\ResourceManagement\ResourceManager');

		$resource = $resourceManager->importResource(__DIR__ . '/Fixtures/document.pdf');
		$this->assertNotEquals(FALSE, $resource, 'Imported resource should not be false');

		$contentType = $service->getContentType($resource);

		$this->assertEquals('application/pdf', $contentType, 'Tika should detect correct content type');
	}

}
?>