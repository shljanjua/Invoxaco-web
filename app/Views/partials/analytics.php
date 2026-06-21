<?php
$ga4 = setting('ga4_measurement_id', '');
$clarity = setting('clarity_project_id', '');
$pixel = setting('meta_pixel_id', '');
?>
<?php if ($ga4): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($ga4) ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?= e($ga4) ?>');
</script>
<?php endif; ?>
<?php if ($clarity): ?>
<script>
  (function(c,l,a,r,i,t,y){
    c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
    t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
    y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
  })(window, document, "clarity", "script", "<?= e($clarity) ?>");
</script>
<?php endif; ?>
<?php if ($pixel): ?>
<script>
  !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
  n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
  document,'script','https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '<?= e($pixel) ?>');
  fbq('track', 'PageView');
</script>
<?php endif; ?>
