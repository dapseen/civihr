<?php

require_once EXTENSION_ROOT_DIR . 'CRM/HRSampleData/Importer/EmergencyContacts.php';

use CRM_HRCore_Test_Fabricator_Contact as ContactFabricator;

/**
 * Class CRM_HRSampleData_Importer_EmergencyContactsTest
 *
 * @group headless
 */
class CRM_HRSampleData_Importer_EmergencyContactsTest extends CRM_HRSampleData_BaseImporterTest {

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
      '070 8891 9127',
      'no',
      '35 Baron Grove',
      'Mitcham',
      'London',
      'CR4 4EH',
      '',
      1226,
      'Liam Carnegie',
      'Liam.Y.Carnegie@spambob.com',
      'Parent',
    ];

    $mapping = [
      ['contact_mapping', $this->testContact['id']],
    ];

    $this->runImporter('CRM_HRSampleData_Importer_EmergencyContacts', $this->rows, $mapping);

    $this->assertEquals($this->testContact['id'], $this->apiGet('CustomValue','entity_id', $this->testContact['id']));
  }

  private function importHeadersFixture() {
    return [
      'entity_id',
      'Mobile_number',
      'Dependant_s_',
      'Street_Address',
      'Street_Address_Line_2',
      'City',
      'Postal_Code',
      'Province',
      'Country',
      'Name',
      'Email',
      'Relationship_with_Employee',
    ];
  }

}
