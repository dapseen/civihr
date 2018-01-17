<?php

use CRM_HRContactActionsMenu_Component_UserRoleItem as UserRoleItem;
use CRM_HRCore_CMSData_UserRoleInterface as CMSUserRole;

/**
 * Class CRM_HRContactActionsMenu_ComponentUserRoleItemTest
 *
 * @group headless
 */
class CRM_HRContactActionsMenu_ComponentUserRoleItemTest extends BaseHeadlessTest {

  public function testRender() {
    $userRoles = [1 => 'Fake Role1', 2 => 'Fake Role2'];
    $cmsUserRole = $this->prophesize(CMSUserRole::class);
    $cmsUserRole->getRoles()->willReturn($userRoles);

    $userRoleItem = new UserRoleItem($cmsUserRole->reveal());
    $roles = implode(', ', $userRoles);

    $userRolesMarkup = '
      <p><span class="crm_contact_action_menu__bold_text">Roles: </span> 
        <span class="crm_contact_action_menu__grey_text">%s</span>
      </p>';

    $expectedResult = sprintf(
      $userRolesMarkup,
      $roles
    );

    $this->assertEquals($expectedResult, $userRoleItem->render());
  }
}
