<?php

use CRM_HRLeaveAndAbsences_BAO_AbsenceType as AbsenceType;
use CRM_HRLeaveAndAbsences_BAO_LeaveRequest as LeaveRequest;
use CRM_HRLeaveAndAbsences_BAO_LeaveRequestDate as LeaveRequestDate;
use CRM_HRLeaveAndAbsences_BAO_LeaveBalanceChange as LeaveBalanceChange;
use CRM_HRLeaveAndAbsences_BAO_PublicHoliday as PublicHoliday;

class CRM_HRLeaveAndAbsences_Service_PublicHolidayLeaveRequestCreation {

  /**
   * Creates a Public Holiday Leave Request for the contact with the
   * given $contactId
   *
   * @param int $contactID
   * @param \CRM_HRLeaveAndAbsences_BAO_PublicHoliday $publicHoliday
   */
  public function createForContact($contactID, PublicHoliday $publicHoliday) {
    $absenceTypes = $this->getAbsenceTypesWherePublicHolidaysMustBeTakenAsLeave();
    foreach($absenceTypes as $absenceType) {
      $leaveRequest = $this->createLeaveRequest($contactID, $absenceType, $publicHoliday);
      $this->createLeaveBalanceChangeRecord($leaveRequest);
    }
  }

  /**
   * Returns a list of all enabled Absence Types where the "Must Take Public
   * Holiday as Leave" option is set
   *
   * @return \CRM_HRLeaveAndAbsences_BAO_AbsenceType[]
   */
  private function getAbsenceTypesWherePublicHolidaysMustBeTakenAsLeave() {
    $allAbsenceTypes = AbsenceType::getEnabledAbsenceTypes();

    return array_filter($allAbsenceTypes, function(AbsenceType $absenceType) {
      return boolval($absenceType->must_take_public_holiday_as_leave);
    });
  }

  /**
   * Creates a Leave Request for the given $contactID and $absenceType with the
   * date of the given Public Holiday
   *
   * @param int $contactID
   * @param \CRM_HRLeaveAndAbsences_BAO_AbsenceType $absenceType
   * @param \CRM_HRLeaveAndAbsences_BAO_PublicHoliday $publicHoliday
   *
   * @return \CRM_HRLeaveAndAbsences_BAO_LeaveRequest|NULL
   */
  private function createLeaveRequest($contactID, AbsenceType $absenceType, PublicHoliday $publicHoliday) {
    $leaveRequestStatuses = array_flip(LeaveRequest::buildOptions('status_id'));
    $leaveRequestDayTypes = array_flip(LeaveRequest::buildOptions('from_date_type'));

    return LeaveRequest::create([
      'contact_id'     => $contactID,
      'type_id'        => $absenceType->id,
      'status_id'      => $leaveRequestStatuses['Admin Approved'],
      'from_date'      => CRM_Utils_Date::processDate($publicHoliday->date),
      'from_date_type' => $leaveRequestDayTypes['All Day']
    ]);
  }

  /**
   * Creates LeaveBalanceChange records for the dates of the given $leaveRequest.
   *
   * For PublicHolidays, the deducted amount will always be -1.
   *
   * If there is already a leave request to this on the same date, the deduction
   * amount for that specific date will be updated to be 0, in order to not
   * deduct the same day twice.
   *
   * @param \CRM_HRLeaveAndAbsences_BAO_LeaveRequest $leaveRequest
   */
  private function createLeaveBalanceChangeRecord(LeaveRequest $leaveRequest) {
    $leaveBalanceChangeTypes = array_flip(LeaveBalanceChange::buildOptions('type_id'));

    $dates = $leaveRequest->getDates();
    foreach($dates as $date) {
      $this->zeroDeductionForOverlappingLeaveRequestDate($leaveRequest, $date);

      LeaveBalanceChange::create([
        'source_id'   => $date->id,
        'source_type' => LeaveBalanceChange::SOURCE_LEAVE_REQUEST_DAY,
        'type_id'     => $leaveBalanceChangeTypes['Public Holiday'],
        'amount'      => -1
      ]);
    }
  }

  /**
   * First, searches for an existing balance change for the same contact and absence
   * type of the given $leaveRequest and linked to a LeaveRequestDate with the
   * same date as $leaveRequestDate. Next, if such balance change exists, update
   * it's amount to 0.
   *
   * @param \CRM_HRLeaveAndAbsences_BAO_LeaveRequest $leaveRequest
   * @param \CRM_HRLeaveAndAbsences_BAO_LeaveRequestDate $leaveRequestDate
   */
  private function zeroDeductionForOverlappingLeaveRequestDate(LeaveRequest $leaveRequest, LeaveRequestDate $leaveRequestDate) {
    $date = new DateTime($leaveRequestDate->date);

    $leaveBalanceChange = LeaveBalanceChange::getExistingBalanceChangeForALeaveRequestDate($leaveRequest, $date);

    if($leaveBalanceChange) {
      LeaveBalanceChange::create([
        'id' => $leaveBalanceChange->id,
        'amount' => 0
      ]);
    }
  }

}