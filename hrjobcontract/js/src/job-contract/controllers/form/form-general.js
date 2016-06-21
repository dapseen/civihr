define([
    'common/moment',
    'job-contract/controllers/controllers',
    'common/filters/angular-date/format-date'
], function (moment, controllers){
    'use strict';

    controllers.controller('FormGeneralCtrl',['$scope','$log', 'HR_settings',
        function ($scope, $log, HR_settings) {
            $log.debug('Controller: FormGeneralCtrl');

            var entityDetails = $scope.entity.details;

            $scope.format = HR_settings.DATE_FORMAT;

            $scope.dpOpen = function($event, opened){
                $event.preventDefault();
                $event.stopPropagation();

                $scope[opened] = true;
            };

            function duration(dateStart, dateEnd){

                if (!dateStart || !dateEnd) {
                    return 'Unspecified'
                }

                var days, months, m, years;

                m = moment(dateEnd);
                years = m.diff(dateStart, 'years');

                m.add(-years, 'years');
                months = m.diff(dateStart, 'months');

                m.add(-months, 'months');
                days = m.diff(dateStart, 'days');

                years = years > 0  ? (years > 1 ? years + ' years ' : years + ' year ') :  '';
                months = months > 0 ? (months > 1 ? months + ' months ' : months + ' month ') :  '';
                days = days > 0 ? (days > 1 ? days + ' days' : days + ' day') : '';

                return (years + months + days) || '0 days';

            }

            $scope.datepickerOptions = {
              start: { maxDate: null },
              end: { minDate: null }
            };

            $scope.$watch('entity.details.period_start_date', function(){
              $scope.datepickerOptions.end.minDate = moment(entityDetails.period_start_date).add(1, 'day').toDate();
              $scope.duration = duration(entityDetails.period_start_date, entityDetails.period_end_date);
            });

            $scope.$watch('entity.details.period_end_date', function(){
              if (entityDetails.period_end_date) {
                $scope.datepickerOptions.start.maxDate = moment(entityDetails.period_end_date).subtract(1, 'day').toDate();
              } else {
                $scope.datepickerOptions.start.maxDate = null;
                entityDetails.end_reason = null;
              }

              $scope.duration = duration(entityDetails.period_start_date, entityDetails.period_end_date);
            });

            $scope.$watch('entity.details.position', function(newVal, oldVal){
                if (newVal != oldVal && entityDetails.title == oldVal) {
                    $scope.contractForm.detailsTitle.$setViewValue(newVal);
                    $scope.contractForm.detailsTitle.$render();
                }
            });

            $scope.$watch('entity.details.notice_amount', function(newVal, oldVal){
                if (+newVal && !entityDetails.notice_unit) {
                    $scope.contractForm.detailsNoticeUnit.$setValidity('required', false);
                    $scope.contractForm.detailsNoticeUnit.$dirty = true;
                }

                if (newVal != oldVal && entityDetails.notice_amount_employee == oldVal) {
                    entityDetails.notice_amount_employee = newVal;
                }
            });

            $scope.$watch('entity.details.notice_amount_employee', function(newVal){
                if (+newVal && !entityDetails.notice_unit_employee) {
                    $scope.contractForm.detailsNoticeUnitEmployee.$setValidity('required', false);
                    $scope.contractForm.detailsNoticeUnitEmployee.$dirty = true;
                }
            });

            $scope.$watch('entity.details.notice_unit', function(newVal, oldVal){
                if (newVal != oldVal && entityDetails.notice_unit_employee == oldVal) {
                    entityDetails.notice_unit_employee = newVal;
                }
            });

        }]);
});
