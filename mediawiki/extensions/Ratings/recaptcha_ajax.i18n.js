var RecaptchaTemplates_en = {
	VertHtml : '<div id="recaptcha_scripts" style="display:none"></div>\n<input type="password" autocomplete="off" style="display:none" name="recaptcha_challenge_field" id="recaptcha_challenge_field" />\n\n<a id=\'recaptcha_whatsthis_btn\' style="display:none" tabindex=\'-1\'></a>\n\t<img id="recaptcha_image" width="230" height="57" alt=""><a id=\'recaptcha_reload_btn\' tabindex=\'-1\'><img src="/extensions/Ratings/reload.gif"></a><span id=\'recaptcha_play_audio\'></span> \n\t <div class="recaptcha_text">Enter <span style="color:black;"><b>both</b></span> words below, separated by a <span style="color:black"><b>space</b></span>.<div class="audiocaptcha"><a id=\'recaptcha_switch_img_btn\' tabindex=\'-1\' style="display:none">Back to text.<a><br /><a id=\'recaptcha_switch_audio_btn\' tabindex=\'-1\'>Try an audio captcha</a></div>',
	VertCss : '.recaptchatable td img {\n  /* see http://developer.mozilla.org/en/docs/Images%2C_Tables%2C_and_Mysterious_Gaps */\n  display: block;\n}\n.recaptchatable .recaptcha_r1_c1 { width:318px; height:9px; }\n.recaptchatable .recaptcha_r2_c1 { width:9px; height:57px; }\n.recaptchatable .recaptcha_r2_c2 { width:9px; height:57px; } \n.recaptchatable .recaptcha_r3_c1 { width:9px; height:63px; }\n.recaptchatable .recaptcha_r3_c2 { width:300px; height:6px; }\n.recaptchatable .recaptcha_r3_c3 { width:9px; height:63px; }\n.recaptchatable .recaptcha_r4_c1 { width:171px; height:49px; }\n.recaptchatable .recaptcha_r4_c2 { width:7px; height:57px; } \n.recaptchatable .recaptcha_r4_c4 { width:97px; height:57px; }\n.recaptchatable .recaptcha_r7_c1 { width:171px; height:8px; }\n.recaptchatable .recaptcha_r8_c1 { width:25px; height:7px; }\n.recaptchatable .recaptcha_image_cell center img { height:57px;}\n.recaptchatable .recaptcha_image_cell center { height:57px;}\n.recaptchatable .recaptcha_image_cell {\n  background-color:white; height:57px;\n}\n\n/* some people break their style sheet, we need to clean up after them */\n#recaptcha_area {\n  width: 300px !important; vertical-align: top; \n}\n\n.recaptchatable, #recaptcha_area tr, #recaptcha_area td, #recaptcha_area th {\n  margin:0px !important;\n  border:0px !important;\n  padding:0px !important;\n  border-collapse: collapse !important;\n}\n\n.recaptchatable * {\n\tmargin:0px;\n\tpadding:0px;\n\tborder:0px;\n\tfont-family:helvetica,sans-serif;\n\tfont-size:8pt;\n\tcolor:black;\n\tposition:static;\n\ttop:auto;\n\tleft:auto;\n\tright:auto;\n\tbottom:auto;\n\ttext-align:left !important;\n}\n\n.recaptchatable #recaptcha_image {\n  margin:auto;\n}\n\n.recaptchatable a img {\n  border:0px;\n}\n\n.recaptchatable a, .recaptchatable a:hover {\n  -moz-outline:none;\n  border:0px !important;\n  padding:0px !important;\n  text-decoration:none;\n  color:blue;\n  background:none !important;\n  font-weight: normal;\n}\n\n.recaptcha_input_area {\n  position:relative !important;\n  width:146px !important;\n  height:45px !important;\n  margin-left:20px !important;\n  margin-right:5px !important;\n  margin-top:4px !important;\n  background:none !important;\n}\n\n.recaptchatable label.recaptcha_input_area_text {\n  margin:0px !important;  \n  padding:0px !important;\n  position:static !important;\n  top:auto !important;\n  left:auto !important;\n  right:auto !important;\n  bottom:auto !important;\n}\n\n.recaptcha_theme_red label.recaptcha_input_area_text,\n.recaptcha_theme_white label.recaptcha_input_area_text {\n  color:black !important;\n}\n\n.recaptcha_theme_blackglass label.recaptcha_input_area_text {\n  color:white !important;\n}\n\n.recaptchatable #recaptcha_response_field  {\n  width:145px !important;\n  position:absolute !important;\n  bottom:7px !important;\n\n  padding:0px !important;\n  margin:0px !important;\n  font-size:10pt;\n}\n\n.recaptcha_theme_blackglass #recaptcha_response_field,\n.recaptcha_theme_white #recaptcha_response_field {\n  border: 1px solid gray;\n}\n\n.recaptcha_theme_red #recaptcha_response_field {\n  border:1px solid #cca940;\n}\n\n.recaptcha_audio_cant_hear_link {\n  font-size:7pt;\n  color:black;\n}\n\n.recaptchatable {\n  line-height:1em;\n}\n\n.recaptcha_error_text {\n  color:red;\n}\n'
};

var RecaptchaTemplates_zhhk = {
	VertHtml : '<div id="recaptcha_scripts" style="display:none"></div>\n<input type="password" autocomplete="off" style="display:none" name="recaptcha_challenge_field" id="recaptcha_challenge_field" />\n\n<a id=\'recaptcha_whatsthis_btn\' style="display:none" tabindex=\'-1\'></a>\n\t<img id="recaptcha_image" width="230" height="57" alt=""><a id=\'recaptcha_reload_btn\' tabindex=\'-1\'><img src="/extensions/Ratings/reload.gif"></a><span id=\'recaptcha_play_audio\'></span> \n\t <div class="recaptcha_text">&#35531;&#36664;&#20837;&#19978;&#22294;&#20841;&#20491;&#33521;&#25991;&#23383;&#65292;&#20006;&#20197;&#31354;&#26684;&#38548;&#38283;&#12290;</span><div class="audiocaptcha"><a id=\'recaptcha_switch_img_btn\' tabindex=\'-1\' style="display:none">Back to text.<a><br /><a id=\'recaptcha_switch_audio_btn\' tabindex=\'-1\'>Try an audio captcha</a></div>',
	VertCss : '.recaptchatable td img {\n  /* see http://developer.mozilla.org/en/docs/Images%2C_Tables%2C_and_Mysterious_Gaps */\n  display: block;\n}\n.recaptchatable .recaptcha_r1_c1 { width:318px; height:9px; }\n.recaptchatable .recaptcha_r2_c1 { width:9px; height:57px; }\n.recaptchatable .recaptcha_r2_c2 { width:9px; height:57px; } \n.recaptchatable .recaptcha_r3_c1 { width:9px; height:63px; }\n.recaptchatable .recaptcha_r3_c2 { width:300px; height:6px; }\n.recaptchatable .recaptcha_r3_c3 { width:9px; height:63px; }\n.recaptchatable .recaptcha_r4_c1 { width:171px; height:49px; }\n.recaptchatable .recaptcha_r4_c2 { width:7px; height:57px; } \n.recaptchatable .recaptcha_r4_c4 { width:97px; height:57px; }\n.recaptchatable .recaptcha_r7_c1 { width:171px; height:8px; }\n.recaptchatable .recaptcha_r8_c1 { width:25px; height:7px; }\n.recaptchatable .recaptcha_image_cell center img { height:57px;}\n.recaptchatable .recaptcha_image_cell center { height:57px;}\n.recaptchatable .recaptcha_image_cell {\n  background-color:white; height:57px;\n}\n\n/* some people break their style sheet, we need to clean up after them */\n#recaptcha_area {\n  width: 250px !important;\n}\n\n.recaptchatable, #recaptcha_area tr, #recaptcha_area td, #recaptcha_area th {\n  margin:0px !important;\n  border:0px !important;\n  padding:0px !important;\n  border-collapse: collapse !important;\n}\n\n.recaptchatable * {\n\tmargin:0px;\n\tpadding:0px;\n\tborder:0px;\n\tfont-family:helvetica,sans-serif;\n\tfont-size:8pt;\n\tcolor:black;\n\tposition:static;\n\ttop:auto;\n\tleft:auto;\n\tright:auto;\n\tbottom:auto;\n\ttext-align:left !important;\n}\n\n.recaptchatable #recaptcha_image {\n  margin:auto;\n}\n\n.recaptchatable a img {\n  border:0px;\n}\n\n.recaptchatable a, .recaptchatable a:hover {\n  -moz-outline:none;\n  border:0px !important;\n  padding:0px !important;\n  text-decoration:none;\n  color:blue;\n  background:none !important;\n  font-weight: normal;\n}\n\n.recaptcha_input_area {\n  position:relative !important;\n  width:146px !important;\n  height:45px !important;\n  margin-left:20px !important;\n  margin-right:5px !important;\n  margin-top:4px !important;\n  background:none !important;\n}\n\n.recaptchatable label.recaptcha_input_area_text {\n  margin:0px !important;  \n  padding:0px !important;\n  position:static !important;\n  top:auto !important;\n  left:auto !important;\n  right:auto !important;\n  bottom:auto !important;\n}\n\n.recaptcha_theme_red label.recaptcha_input_area_text,\n.recaptcha_theme_white label.recaptcha_input_area_text {\n  color:black !important;\n}\n\n.recaptcha_theme_blackglass label.recaptcha_input_area_text {\n  color:white !important;\n}\n\n.recaptchatable #recaptcha_response_field  {\n  width:145px !important;\n  position:absolute !important;\n  bottom:7px !important;\n\n  padding:0px !important;\n  margin:0px !important;\n  font-size:10pt;\n}\n\n.recaptcha_theme_blackglass #recaptcha_response_field,\n.recaptcha_theme_white #recaptcha_response_field {\n  border: 1px solid gray;\n}\n\n.recaptcha_theme_red #recaptcha_response_field {\n  border:1px solid #cca940;\n}\n\n.recaptcha_audio_cant_hear_link {\n  font-size:7pt;\n  color:black;\n}\n\n.recaptchatable {\n  line-height:1em;\n}\n\n.recaptcha_error_text {\n  color:red;\n}\n'
};

var RecaptchaLangMap = { 
  "en": RecaptchaTemplates_en,
  "zh-hk": RecaptchaTemplates_zhhk
};

var RecaptchaTemplates = RecaptchaTemplates_en;

var RecaptchaStr = {
	visual_challenge : "Get a visual challenge",
	audio_challenge : "Get an audio challenge",
	refresh_btn : "Get a new challenge",

	instructions_visual : "Type the two words:",
	instructions_audio : "Type the eight numbers:",

	help_btn : "Help",
	learn_more : "This helps fight spam and read old books.",
	cant_hear_this : "Can't hear the sound?"
};

var RecaptchaOptions;

var RecaptchaDefaultOptions = {
	tabindex: 0,
	theme: 'red',
	callback:null
};

var Recaptcha = {

	widget: null,

	timer_id: -1,

	style_set: false,

	theme: null,

	callback: null,

	type: 'image',

	helplink: 'http://recaptcha.net/popuphelp/',

	$: function(id) {
		if (typeof(id) == "string") {
			return document.getElementById(id);
		}
		else {
			return id;
		}
	},

	create: function(public_key, element, options) {
		if (Recaptcha.widget) {
			Recaptcha.destroy();
		}
		Recaptcha.widget = Recaptcha.$(element);
		RecaptchaOptions = options;
		Recaptcha.call_challenge(public_key);
	},

	destroy: function() {
		if (!Recaptcha.widget) {
			return;
		}
		if (Recaptcha.timer_id != -1) {
			clearInterval(Recaptcha.timer_id);
		}
		Recaptcha.timer_id = -1;
		Recaptcha.widget.innerHTML = "";
		Recaptcha.widget = null;
	},

	focus_response_field: function() {
		var $ = Recaptcha.$;
		var field = $('recaptcha_response_field');
		if (field) {
			field.focus();
		}
	},

	get_challenge: function() {
		if (typeof(RecaptchaState) == "undefined") {
			return null;
		}
		return RecaptchaState.challenge;
	},

	get_response: function() {
		var $ = Recaptcha.$;
		var field = $('recaptcha_response_field');
		if (!field) {
			return null;
		}
		return field.value;
	},
	
	call_challenge: function(public_key) {
		var protocol = window.location.protocol;
		if (protocol == 'https:') {
			var server = "api-secure.recaptcha.net";
		}
		else {
			var server = "api.recaptcha.net";
		}
		scriptURL = protocol + "//" + server + "/challenge?k=" + public_key + "&ajax=1";
		Recaptcha.add_script(scriptURL);
	},

	add_script: function(scriptURL) {
		var scriptTag = document.createElement("script");
		scriptTag.type = "text/javascript";
		scriptTag.src = scriptURL;
		Recaptcha.get_script_area().appendChild(scriptTag);
	},

	get_script_area: function() {
		var parentElement = document.getElementsByTagName("head");
		if (!parentElement || parentElement.length < 1) {
			parentElement = document.body;
		}
		else {
			parentElement = parentElement[0];
		}
		return parentElement;
	},

	challenge_callback: function() {
		var element = Recaptcha.widget;
		Recaptcha.reset_timer ();

		var comb_opt = RecaptchaDefaultOptions;
		RecaptchaOptions = RecaptchaOptions || {};

		for (var p in RecaptchaOptions) {
			comb_opt[p] = RecaptchaOptions[p];
		}
		RecaptchaOptions = comb_opt;

		var lang = RecaptchaLangMap[RecaptchaOptions.lang];
		if (typeof(lang) != "undefined") {
			RecaptchaTemplates = lang;
		}

		if (window.addEventListener) {
			window.addEventListener('unload', function(e){ Recaptcha.destroy(); },false );
		}

		if (navigator.userAgent.indexOf("MSIE") > 0 && window.attachEvent) {
			window.attachEvent('onbeforeunload', function () {
			});
		}

		if (navigator.userAgent.indexOf("KHTML") > 0) {
			var iframe = document.createElement('iframe');
			iframe.src = "about:blank";
			iframe.style.height = "0px";
			iframe.style.width = "0px";
			iframe.style.visibility = "hidden";
			iframe.style.border = "none";
			var textNode = document.createTextNode("This frame prevents back/forward cache problems in Safari.");
			iframe.appendChild(textNode);
			document.body.appendChild(iframe);
		}

		if(Recaptcha.is_gecko17_or_less()){
			window.setTimeout(Recaptcha.finish_widget, 0);
		}
		else {
			Recaptcha.finish_widget();
		}
	},

	set_style: function(css) {
		if (Recaptcha.style_set) {
			return;
		}
		Recaptcha.style_set = true;
		var styleTag = document.createElement("style");
		styleTag.type = "text/css";
		if (styleTag.styleSheet) {
			styleTag.styleSheet.cssText=css;
		}
		else {
			var textNode = document.createTextNode(css);
			styleTag.appendChild(textNode);
		}
		Recaptcha.get_script_area().appendChild(styleTag);
	},

	finish_widget: function() {
		var $ = Recaptcha.$;
		var $_ = RecaptchaStr;
		var $ST = RecaptchaState;
		var $OPT = RecaptchaOptions;

		var theme = $OPT.theme;
		switch (theme) {
		case 'red': case 'white': case 'blackglass': 
			break;
		default:
			theme = 'red';
			break;
		}
		if (!Recaptcha.theme) {
			Recaptcha.theme = theme;
		}


		var server_no_slash = $ST.server;
		if (server_no_slash[server_no_slash.length - 1] == "/")
		server_no_slash = server_no_slash.substring (0, server_no_slash.length - 1);
		var IMGROOT = server_no_slash + "/img/" + Recaptcha.theme

		var css = RecaptchaTemplates.VertCss;
		css = css.replace(/IMGROOT/g, IMGROOT);

		var html = RecaptchaTemplates.VertHtml;

		Recaptcha.set_style(css);
		Recaptcha.widget.innerHTML = "<div id='recaptcha_area'>"+html+"</div>";
		$('recaptcha_image').src = $ST.server + 'image?c=' + $ST.challenge;

		$('recaptcha_challenge_field').value = $ST.challenge;

		$('recaptcha_reload_btn').href = "javascript:Recaptcha.reload ('r');";
		$('recaptcha_reload_btn').title = $_.refresh_btn;
		$('recaptcha_reload_btn').alt = $_.refresh_btn;

		$('recaptcha_switch_audio_btn').href = "javascript:Recaptcha.switch_type('audio');";
		$('recaptcha_switch_audio_btn').title = $_.audio_challenge;
		$('recaptcha_switch_audio_btn').alt = $_.audio_challenge;

		$('recaptcha_switch_img_btn').href = "javascript:Recaptcha.switch_type('image');";
		$('recaptcha_switch_img_btn').title = $_.visual_challenge;
		$('recaptcha_switch_img_btn').alt = $_.visual_challenge;

		$('recaptcha_whatsthis_btn').href = Recaptcha.helplink;
		$('recaptcha_whatsthis_btn').target = "_blank";
		$('recaptcha_whatsthis_btn').title = $_.help_btn;
		$('recaptcha_whatsthis_btn').alt = $_.help_btn;

		$('recaptcha_whatsthis_btn').onclick = function() {
			Recaptcha.showhelp();
			return false;
		};

		Recaptcha.set_instructions();

		if ($ST.error_message) {
			Recaptcha.$("recaptcha_instructions").innerHTML=$ST.error_message;
		}

		Recaptcha.widget.style.display = '';
		if ($OPT.callback) {
			$OPT.callback();
		}
	},

	switch_type : function (new_type) {
		var $C = Recaptcha;
		$C.type = new_type;
		$C.reload ($C.type == 'audio' ? 'a' : 'v');
	},

	reload: function (reason) {
		var $C = Recaptcha;
		var $ = $C.$;
		var $ST = RecaptchaState;

		$('recaptcha_area').style.cursor = 'progress';
		$('recaptcha_reload_btn').style.cursor = 'progress';

		$('recaptcha_reload_btn').onclick = function () { return false; };


		var scriptURL = $ST.server + "reload?c=" + $ST.challenge + "&k=" + $ST.site + "&reason=" + reason + "&type=" + $C.type;
		$C.add_script(scriptURL);
		$C.doing_timeout = reason == 't';
	},

	finish_reload: function (new_challenge, type) 
	{
		var $C = Recaptcha;
		var $ST = RecaptchaState;
		var $ = $C.$;
		$ST.challenge = new_challenge;


		$C.type = type;

		$ ('recaptcha_challenge_field').value = $ST.challenge;

		function unwait () {
			$('recaptcha_area').style.cursor = '';
			$('recaptcha_reload_btn').style.cursor = '';
			$('recaptcha_reload_btn').onclick = null;
		}

		if (type == 'audio') {
			$('recaptcha_image').style.display='none'
			unwait();

			var wavURL = $ST.server + "image?c=" + $ST.challenge;
			var embedCode = '<EMBED SRC="' + wavURL + '" height="40" bgcolor="white" AUTOSTART="true"/><br/><a class="recaptcha_audio_cant_hear_link" target="_blank" href="' + wavURL + '">' + RecaptchaStr.cant_hear_this + '</a>';
			$("recaptcha_play_audio").innerHTML = embedCode;

			$("recaptcha_switch_audio_btn").style.display = 'none';
			$("recaptcha_switch_img_btn").style.display = '';

		} else if (type == 'image') {
			$('recaptcha_image').onload = function () {

				$('recaptcha_image').style.display = ''
				unwait ();
			}

			$("recaptcha_play_audio").innerHTML = "";

			$('recaptcha_image').src = $ST.server + 'image?c=' + $ST.challenge;

			$("recaptcha_switch_img_btn").style.display = 'none';
			$("recaptcha_switch_audio_btn").style.display = '';
		}

		$C.set_instructions();

		$C.clear_input();
		if (!$C.doing_timeout) {
			$ ('captcha_response').focus();
		}

		$C.reset_timer ();
		$C.doing_timeout = false;
	},
	reset_timer : function () {
		var $ST = RecaptchaState;
		clearInterval(Recaptcha.timer_id);
		Recaptcha.timer_id = setInterval ("Recaptcha.reload('t');", ($ST.timeout - 60*5) * 1000);
	},
	showhelp : function () {
		window.open(Recaptcha.helplink,"recaptcha_popup","width=460,height=570,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes,resizable=yes");
	},
	clear_input : function ()
	{
		var resp=Recaptcha.$('captcha_response');
		resp.value = ""
	},
	set_instructions : function () {},
	reloaderror : function (msg) {
		var $=Recaptcha.$;
		$('recaptcha_area').style.cursor = '';
		$('recaptcha_reload_btn').style.cursor = '';
		$('recaptcha_reload_btn').onclick = null;

		$('recaptcha_image').style.display = 'none'
		$("recaptcha_play_audio").innerHTML = msg;
	},
	is_gecko17_or_less : function () {
		var s = navigator.userAgent.toLowerCase();
		if(!/gecko\//.test(s))
			return false;

		var geckoVersion = s.match( /gecko\/(\d+)/ )[1] ;

		return ( ( geckoVersion < 20051111 ) || ( /rv:1\.7/.test(s) ) );
	}
};


