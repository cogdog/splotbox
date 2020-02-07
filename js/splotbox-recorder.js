
var recorderwidth = jQuery('#wMediaRecorder').parent().width();

var options = {
	controls: true,
	width: recorderwidth,
	height: recorderwidth / 2,
	plugins: {
		wavesurfer: {
			src: 'live',
			waveColor: '#fffa00',
			progressColor: '#FAFCD2',
			debug: true,
			cursorWidth: 1,
			msDisplayMax: 20,
			hideScrollbar: true
		},
		record: {
			audio: true,
			video: false,
			maxLength: recorderObject.recordMax,
			debug: true,
			audioEngine: 'lamejs',
			audioWorkerURL: recorderObject.stylesheetUrl + '/includes/lib/lamejs/worker-example/worker-realtime.js',
			audioSampleRate: 44100,
			audioBitRate: 128
		}
	}
};


// set up Video.js player
var player = videojs('wMediaRecorder', options, function() {
	// print version information at startup
	var msg = 'Using video.js ' + videojs.VERSION +
		' with videojs-record ' + videojs.getPluginVersion('record') +
		', videojs-wavesurfer ' + videojs.getPluginVersion('wavesurfer') +
		' and wavesurfer.js ' + WaveSurfer.VERSION;
	videojs.log(msg);
});

// handy flag for after the mic button clicked
var deviceready = false;

// insert divs for countdown trigger and display
jQuery('#wMediaRecorder').append('<div id="countdown"></div><div id="countrec"></div>');

// error handling
player.on('deviceError', function() {
	console.log('device error:', player.deviceErrorCode);
});

player.on('error', function(element, error) {
	console.error(error);
});

// mic button click
jQuery(".vjs-device-button").click(function(){
    jQuery("#recordstatus").html( 'Microphone activated. Speak into microphone to check levels, then click press <span style="color:red">&#9679;</span> to start recording.' );
    jQuery('#countingwrapper').css('z-index', '5');
    deviceready = true;
});

// user clicked the record button and started recording
player.on('startRecord', function() {
	console.log('started recording!');
	jQuery("#recordstatus").html( 'Microphone activated. Get ready to record...' );
});

// user completed recording and stream is available
player.on('finishRecord', function() {
  jQuery('#countrec').css('z-index', '5');
	jQuery("#recordstatus").html( 'Recording done. Click &#9656; to play back. Click "Use This Audio" to select this audio for use or click <span style="color:red">&#9679;</span> to record again.' );
	jQuery("#wUploadRecording").show(); // enable the upload button

	console.log('finished recording: ', player.recordedData);
});

jQuery("#wUploadRecording").click(function(){
	// upload recorded data
	jQuery("#recordstatus").text( 'Uploading audio...');
	splot_upload(player.recordedData);
});

// record intercept button, kicks off countdown
jQuery('#countrec').click(function(){
  // only if mic is ready
	if (deviceready) {
	  jQuery("#recordstatus").html( 'Get ready to record in...' );
	  jQuery("#wUploadRecording").hide();
		jQuery('#countdown').html('3');
		jQuery('#countdown').css('z-index', '10');
		start_countdown(3);
	}
});

function start_countdown(timer) {
  //Keeps the interval ID for later clear
  var intervalID;


  jQuery('#countdown').text(timer);

  // create the timed event
  intervalID = setInterval(function () {
    display(timer);
    timer--;

    if (timer < 0) {
        //Clears the timeout
        clearTimeout(intervalID);

        // reset things
        jQuery('#countdown').text('');
        jQuery('#countrec').css('z-index', '-5');
        jQuery('#countdown').css('z-index', '-10');

        // initiate the recording
        jQuery('.vjs-record-button').click();
        jQuery("#recordstatus").html( 'Recording started, click &#9632; to stop.' );
    }
  }, 1000);
}

//Modifies the countdown display
function display(timer) {
	jQuery('#countdown').html(timer);
}

// time to upload The Blob
function splot_upload( blob ) {
	var serverUrl = recorderObject.ajaxUrl;
	var formData = new FormData();
	formData.append('file', blob, 'splotbox_recording_' + blob.name);
	formData.append("action", "splotbox_upload_audio_action");
  jQuery("#recordstatus").text( 'Audio flle ' + 'splotbox_recording_' + blob.name + ' uploaded and ready to be saved with this form data');
	jQuery("#footlocker").text(jQuery("#wMediaURL").val());

	// start upload
	fetch(serverUrl, {
		method: 'POST',
		body: formData
	})

	.then((response) => response.json())
	.then((result) => {
 		 console.log('Upload Success:', result);
 		 jQuery("#recordstatus").text( 'Uploading splotbox_recording_' + blob.name + ' completed');
 		 jQuery("#wMediaURL").val(result.location);
	})
	.catch((error) => {
  		console.error('Upload Error:', error);
  		jQuery("#recordstatus").text( 'Upload Error:' + error);
  		jQuery("#wMediaURL").val(jQuery("#footlocker").text());

	});

	jQuery("#wUploadRecording").hide();
}
