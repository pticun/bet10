$(document).ready(function(){
  $("#matches-history").on('change', describeMatch);
});


function describeMatch(){
  
  var matchId = $(this).val();

  $.ajax({
    url: 'match_form.php',
    method: "GET",
    data: { id: matchId },
    dataType: "JSON",

    success: function(data){
      console.log(data);

      matchInfo = $("#match-info");
      matchInfo.slideDown();

      matchInfo.find("#winner-match-id").val( data['id'] );
      matchInfo.find("#winner-match-first-team-id").val( data['1st_team_id'] );
      matchInfo.find("#winner-match-second-team-id").val( data['2nd_team_id'] );
    },

    error: function(err){
      console.log(err);
    },

  });

}
