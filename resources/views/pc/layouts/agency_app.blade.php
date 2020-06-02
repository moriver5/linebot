<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta name="robots" content="noindex,nofollow">
    <meta charset="utf-8">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>代理店管理</title>

    <!-- Styles -->
	<link rel="preload" href="{{ asset('css/admin/app.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
	<noscript><link rel="stylesheet" href="{{ asset('css/admin/app.css') }}"></noscript>
	<link href="{{ asset('css/admin/allow.css') }}" rel="preload" />
	<link href="{{ asset('css/admin/admin.css') }}" rel="preload" />
	<link href="{{ asset('css/admin/colorbox.css') }}" rel="preload" />

	<!-- jQuery -->
	<script async src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


	<!-- JavaScript Library -->
	<script async src="{{ asset('js/admin/utility.js') }}"></script>

	<script>
		//高速化のCSSファイルの非同期読込みのためにloadCSSを読込む
		(function(a){if(!a.loadCSS){a.loadCSS=function(){}}var b=loadCSS.relpreload={};b.support=(function(){var d;try{d=a.document.createElement("link").relList.supports("preload")}catch(f){d=false}return function(){return d}})();b.bindMediaToggle=function(e){var f=e.media||"all";function d(){e.media=f}if(e.addEventListener){e.addEventListener("load",d)}else{if(e.attachEvent){e.attachEvent("onload",d)}}setTimeout(function(){e.rel="stylesheet";e.media="only x"});setTimeout(d,3000)};b.poly=function(){if(b.support()){return}var d=a.document.getElementsByTagName("link");for(var e=0;e<d.length;e++){var f=d[e];if(f.rel==="preload"&&f.getAttribute("as")==="style"&&!f.getAttribute("data-loadcss")){f.setAttribute("data-loadcss",true);b.bindMediaToggle(f)}}};if(!b.support()){b.poly();var c=a.setInterval(b.poly,500);if(a.addEventListener){a.addEventListener("load",function(){b.poly();a.clearInterval(c)})}else{if(a.attachEvent){a.attachEvent("onload",function(){b.poly();a.clearInterval(c)})}}}if(typeof exports!=="undefined"){exports.loadCSS=loadCSS}else{a.loadCSS=loadCSS}}(typeof global!=="undefined"?global:this));
	</script>
</head>
<body class="drawer drawer--left">
    <div id="app">
		@yield('content')
	</div>

    <!-- Scripts -->
    <script async src="{{ asset('js/app.js') }}"></script>
	
	<!-- Javascript library -->
	<script async src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<link href="{{ asset('css/admin/jquery.datetimepicker.css') }}" rel="stylesheet" />
<script async src="{{ asset('js/admin/jquery.datetimepicker.full.min.js') }}"></script>
<script async src="{{ asset('js/admin/jquery.colorbox-min.js') }}"></script>
</body>
</html>
