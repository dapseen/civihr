define([
  'leave-absences/manager-leave/modules/components'
], function (components) {
  components.component('managerLeaveContainer', {
    bindings: {
      contactId: '<'
    },
    templateUrl: ['settings', function (settings) {
      return settings.pathTpl + 'components/manager-leave-container.html';
    }],
    controllerAs: 'managerLeave',
    controller: ['$log', function ($log) {
      console.log('CRM id of the currently logged in user: ', this.contactId);
      $log.debug('Component: manager-leave-container');

      var vm = Object.create(this);

      return vm;
    }]
  });
});