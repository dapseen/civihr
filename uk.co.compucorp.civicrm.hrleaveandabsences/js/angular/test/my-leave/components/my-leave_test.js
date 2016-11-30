(function (CRM) {
  define([
    'common/angular',
    'common/angularMocks',
    'leave-absences/shared/config',
    'leave-absences/my-leave/app'
  ], function (angular) {
    'use strict';

    describe('myLeave', function () {
      var $compile, $log, $rootScope, component;

      beforeEach(module('leave-absences.templates', 'my-leave'));
      beforeEach(inject(function (_$compile_, _$log_, _$rootScope_) {
        $compile = _$compile_;
        $log = _$log_;
        $rootScope = _$rootScope_;

        spyOn($log, 'debug');

        compileComponent();
      }));

      it('is initialized', function () {
        expect($log.debug).toHaveBeenCalled();
      });

      it('is contains the expected markup', function () {
        expect(component.find('div.my-leave-page').length).toBe(1);
      });

      function compileComponent() {
        var $scope = $rootScope.$new();
        var contactId = CRM.vars.leaveAndAbsences.contactId;

        component = angular.element('<my-leave contact-id="' + contactId + '"></my-leave>');
        $compile(component)($scope);
        $scope.$digest();
      }
    });
  })
})(CRM);