<?php

require_once EXTENSION_ROOT_DIR . 'CRM/HRSampleData/Importer/ContactPhone.php';

use CRM_HRCore_Test_Fabricator_Contact as ContactFabricator;

/**
 * Class CRM_HRSampleData_Importer_ContactPhoneTest
 *
 * @group headless
 */
class CRM_HRSampleData_Importer_ContactPhoneTest extends CRM_HRSampleData_BaseImporterTest {

  private $rows;

  private $testContact;

  public function setUp() {
    $this->rows = [];
    $this->rows[] = $this->importHeadersFixture();

    $this->testContact = ContactFabricator::fabricate();
  }

  public function testImport() {
    $this->rows[] = [
      $this->testContact['id'],
      1,
      7586311952,
      7586311952,
      'Mobile',
      'Main',
    ];

    $mapping = [
      ['contact_mapping', $this->testContact['id']]
    ];

    $this->runImporter('CRM_HRSampleData_Importer_ContactPhone', $this->rows, $mapping);

    $this->assertEquals(7586311952, $this->apiGet('Phone', 'phone', 7586311952));
  }

  private function importHeadersFixture() {
    return [
      'contact_id',
      'is_primary',
      'phone',
      'phone_numeric',
      'phone_type_id',
      'location_type_id',
    ];
  }

}
