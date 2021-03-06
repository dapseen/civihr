<?php

use CRM_HRCore_Test_Fabricator_Contact as ContactFabricator;
use CRM_HRCore_Test_Fabricator_RelationshipType as RelationshipTypeFabricator;

/**
 * Class CRM_HRSampleData_Importer_RelationshipsTest
 *
 * @group headless
 */
class CRM_HRSampleData_CSVProcessor_RelationshipsTest extends CRM_HRSampleData_BaseCSVProcessorTest {

  private $testContactA;

  private $testContactB;

  private $relationshipType;

  public function setUp() {
    $this->rows = [];
    $this->rows[] = $this->importHeadersFixture();

    $this->testContactA = ContactFabricator::fabricate();
    $this->testContactB = ContactFabricator::fabricate(['first_name' => 'chrollo2', 'last_name' => 'lucilfer2']);

    $this->relationshipType = RelationshipTypeFabricator::fabricate();
  }

  public function testProcess() {
    $this->rows[] = [
      $this->testContactA['id'],
      $this->testContactB['id'],
      $this->relationshipType['name_a_b'],
      '2016-01-01',
      1,
    ];

    $mapping = [
      ['contact_mapping', $this->testContactA['id']],
      ['contact_mapping', $this->testContactB['id']],
    ];

    $this->runProcessor('CRM_HRSampleData_Importer_Relationships', $this->rows, $mapping);

    $relationship = $this->apiGet('Relationship', ['contact_id_a' => $this->testContactA['id']]);

    foreach($this->rows[0] as $index => $fieldName) {
      if (!in_array($fieldName, ['relationship_type_id'])) {
        $this->assertEquals($this->rows[1][$index], $relationship[$fieldName]);
      }
    }
  }

  private function importHeadersFixture() {
    return [
      'contact_id_a',
      'contact_id_b',
      'relationship_type_id',
      'start_date',
      'is_active',
    ];
  }

}
