<?php
namespace Lightwerk\L10nTranslator\Tests\Unit\Domain\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class TranslationFileWriterServiceTest extends UnitTestCase
{

    /**
     * @return void
     * @test
     * @expectedException \Lightwerk\L10nTranslator\Domain\Service\Exception
     */
    public function assureValidXmlThrowsExceptionForInvalideXml()
    {
        $translationFile = $this->getMock('Lightwerk\L10nTranslator\Domain\Model\TranslationFile');
        $fileWriter = $this->getAccessibleMock('Lightwerk\L10nTranslator\Domain\Service\TranslationFileWriterService', array('foo'));
        $fileWriter->_call('assureValidXml', 'foo', $translationFile);
    }


}
