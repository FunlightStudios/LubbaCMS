$(document).ready(function() {
	$('#login').submit(function(e) {
		$('.formMsg').html('');
		$('.formMsg').hide();
		$('.formLoader').show();
		$('#login').hide();

		$.ajax({
			type: 'POST',
			url: getSitePath() + '/login/post',
			data: $('#login').serialize(),
			success: function(response) {
				var obj = JSON.parse(response);
				if(obj.Success) {
					window.location = obj.Redirect;
				} else {
					$('.formLoader').hide();
					$('.formMsg').html(obj.Message);
					$('.formMsg').show();
					$('#login').show();
				}
			}
		});

		e.preventDefault();
	});

	$('#register').submit(function(e) {
		$('.formMsg').html('');
		$('.formMsg').hide();
		$('.formLoader').show();
		$('#register').hide();

		$.ajax({
			type: 'POST',
			url: getSitePath() + '/register/post',
			data: $('#register').serialize(),
			success: function(response) {
				var obj = JSON.parse(response);
				if(obj.Success) {
					window.location = obj.Redirect;
				} else {
					$('.formLoader').hide();
					$('.formMsg').html(obj.Message);
					$('.formMsg').show();
					$('#register').show();
				}
			}
		});

		e.preventDefault();
	});
});
