<!-- Matomo -->
<script type="text/javascript">
  var _paq = _paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  /* ユーザーIDがあれば */
  @if( !empty($login_id) )
  _paq.push(['setUserId', '{{ $login_id }}']);
  @endif 
  /* 商品IDがあれば */
  @if( !empty($product_id) )
  _paq.push(['setCustomVariable', 1, 'product', '{{ $product_id }}', 'visit']);
  @endif
  /* メルマガIDがあれば */
  @if( !empty($melmaga_id) )
  _paq.push(['setCustomVariable', 1, 'melmaga', '{{ $melmaga_id }}', 'visit']);
  @endif
  /* 支払い方法があれば */
  @if( !empty($pay_method) )
  _paq.push(['setCustomVariable', 2, 'pay_method', '{{ $pay_method }}', 'visit']);
  @endif
  _paq.push(['trackPageView']);
  _paq.push(['trackAllContentImpressions']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//piwik.imgs.ws/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', '{{ $piwik_id }}']);
	_paq.push(['enableHeartBeatTimer']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Matomo Code -->