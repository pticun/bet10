var bettedTeam;
$(document).ready(function(){
  $('.team-bet').on('click', confirmPopUp);
  $('body').on('click', '#close-pop-up', hidePopUp);
  $('body').on('click', '#continue', sendBet);

  $('#value').on('mouseenter', showBetHint);
  $('#value').on('mouseleave', hideBetHint);

  setTimeout(hideBanners, 3000);
});

function popAlert(message, success='info'){

  if (success == 'true') {
    color = "#2AFF63";
  }
  else if (success == 'info') {
    color = '#4187FF';
  }
  else {
    color = "#FF5445";
  }

  return $('<div id="pop-up"> <div id="handle"> <div id="border" style="background-color: '+color+'"> <div id="close-pop-up">X</div> </div> <div id="message"> '+message+' </div> </div> </div>');
}

function confirmPopUp(){
  bettedTeam = $(this).find('.hidden').html()*1;

  $('body').append(
    popAlert('<p> Are you sure you want to continue? This action cannot be undone.</p> <div id="continue">Continue</div>')
  );

}

function sendBet(){

  matchId = $('#match-id').html()*1;
  firstTeam = $('#team-first-id').html()*1;
  secondTeam = $('#team-second-id').html()*1;
  amount = $('#credits').val()*1;

  try {
    if ( isNaN(matchId) || isNaN(firstTeam) || isNaN(secondTeam) || isNaN(amount) || isNaN(bettedTeam)) {
      throw "Problem with data, refresh the page";
    }
    else {
      if ( amount == '' ) {
        throw "Empty amount";
      }
      else {
        $.ajax({
          url: 'bet.php',
          method: "POST",
          dataType: "JSON",
          data: {
            match: matchId,
            team1: firstTeam,
            team2: secondTeam,
            teambet: bettedTeam,
            amount: amount
          },

          success: function(data){
            console.log(data);
            $('#pop-up').remove();
            if (data.success == true) {
              s = 'true';
            }
            else {
              s = 'false';
            }
            $('body').append( popAlert( '<p> '+data.message+' </p>', s ) );
            if (data.success) {
              $('#user-coins').html( $('#user-coins').html()*1 - amount );
            }
          },

          error: function(err){ console.log(err); alert(); },
        });
      }

    }
  } catch (e) {
    $('#pop-up').remove();
    $('body').append(popAlert( '<p>'+e+'</p>' ));
  }

}

function hidePopUp(){
  $('#pop-up').remove();
  //$('#pop-up').remove();
}

function showBetHint(){
  $(this).find('.hint').fadeIn();
}

function hideBetHint(){
  $(this).find('.hint').fadeOut();
}

function hideBanners(){
  $('.banner').slideUp(400);
  setTimeout(function(){$('.banner').remove();}, 400);
}
