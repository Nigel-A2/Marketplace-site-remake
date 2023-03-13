(function($){

	FLBuilder.registerModuleHelper('audio', {

		init: function()
		{
			var form  		= $('.fl-builder-settings'),
				type  		= form.find('select[name=audio_type]'),
				audioField  = form.find('input[name=audios]');

			type.on('change', $.proxy(this._audioFieldChanged, this));
			audioField.on('change', $.proxy(this._audioFieldChanged, this));
			this._audioFieldChanged();

		},

		_audioFieldChanged: function()
		{
			var form   			  = $('.fl-builder-settings'),
				audio  			  = form.find('input[name=audios]'),
				count  			  = audio.val() == '' ? 0 : parseInt(JSON.parse(audio.val()).length),
				type      		  = form.find('select[name=audio_type]').val(),
				toggle 			  = form.find('.fl-multiple-audios-field').attr('data-toggle'),
				playlistFields 	  = [],
				singleAudioFields = [],
				i       		  = 0;

			if (typeof toggle !== 'undefined') {

				toggle = JSON.parse(toggle);
				playlistFields 		= toggle['playlist'].fields;
				singleAudioFields	= toggle['single_audio'].fields;

				// Only show playlist options if user selected more than one files
				if (count > 1 && type == 'media_library') {
					this._showhide(singleAudioFields, 'hide');
					this._showhide(playlistFields, 'show');
				}
				else if ((count == 1 && type == 'media_library') || (type == 'link')) {
					this._showhide(playlistFields, 'hide');
					this._showhide(singleAudioFields, 'show');
				}
				else {
					// Hide by default
					this._showhide(playlistFields, 'hide');
					this._showhide(singleAudioFields, 'hide');
				}
			}

		},

		_showhide: function(fieldArray, method) {
			var i 		= 0;
			if(typeof fieldArray !== 'undefined') {
				for( ; i < fieldArray.length; i++) {
					$('#fl-field-' + fieldArray[i])[method]();
				}
			}
		}
	});

})(jQuery);
