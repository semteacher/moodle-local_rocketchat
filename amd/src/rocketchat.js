define(['core/ajax', 'local_rocketchat/rocketchat'], function(ajax) {
  var module = {};

  var set_rocketchat_course_sync = function(course_id, pendingsync) {
    var promises = ajax.call([
      { methodname: 'local_rocketchat_set_rocketchat_course_sync', args: { courseid: course_id, pendingsync: pendingsync } }
      ]);

    promises[0].done(function(response) {
      console.log('Save successful');
    }).fail(function(ex) {
      console.log(ex);
    });
  };

  var set_rocketchat_role_sync = function(role_id, requiresync) {
    var promises = ajax.call([
      { methodname: 'local_rocketchat_set_rocketchat_role_sync', args: { roleid: role_id, requiresync: requiresync } }
      ]);

    promises[0].done(function(response) {
      console.log('Save successful');
    }).fail(function(ex) {
      console.log(ex);
    });
  };

  var manually_trigger_sync = function(courseid, sync_button) {
    var promises = ajax.call([
      { methodname: 'local_rocketchat_manually_trigger_sync', args: { courseid: courseid } }
      ]);

    promises[0].done(function(response) {
      console.log('Save successful');
      location.reload();
    }).fail(function(ex) {
      location.reload();
      console.log('There was an error')
      console.log(ex);
    });
  };

  module.init = function() {
    $('#integrated-roles').on('click', 'input', function() {
      var checkbox = $(this);
      set_rocketchat_role_sync(checkbox.data('roleid'), checkbox.is(":checked"));
    });

    $('#integrated-courses').on('click', 'button', function(e) {
      e.preventDefault();
      $(this).text("Syncing ...");
      $(this).prop("disabled", "disabled");
      var courseid = $(this).data('courseid');
      console.log("The courseid is ", courseid);
      manually_trigger_sync(courseid, this);
    });    
  }

  $('#integrated-courses').on('click', 'input', function() {
    var checkbox = $(this);
    console.log(checkbox.data('courseid'));
    set_rocketchat_course_sync(checkbox.data('courseid'), checkbox.is(":checked"));
  });

  window.M.local_rocketchat = module;

  return module;
});
