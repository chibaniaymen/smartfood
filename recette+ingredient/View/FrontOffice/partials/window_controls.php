<?php
// Window control buttons partial — shows minimize / reduce / close controls
?>
<style>
  .fo-window-controls{position:fixed;top:12px;right:12px;z-index:3000;display:flex;gap:8px;align-items:center}
  .fo-nav-arrows{position:fixed;top:12px;left:12px;z-index:3000;display:flex;gap:8px;align-items:center}
  .fo-nav-btn{width:40px;height:36px;border-radius:8px;border:0;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:16px;font-weight:700;box-shadow:0 6px 18px rgba(2,6,23,0.06);background:rgba(6,182,212,0.95)}
  .fo-nav-btn[disabled]{opacity:.45;cursor:not-allowed}
  .fo-nav-btn:hover{transform:translateY(-2px)}
  .fo-wc-btn{width:44px;height:36px;border-radius:8px;border:0;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;box-shadow:0 6px 18px rgba(2,6,23,0.08)}
  .fo-wc-reduce{background:#06b6d4}
  .fo-wc-minimize{background:#06b6d4}
  .fo-wc-close{background:#ef4444}
  .fo-wc-btn:hover{transform:translateY(-2px)}
  body.fo-minimized .wrap, body.fo-minimized .panel, body.fo-minimized .card{display:none !important}
  body.fo-reduced .wrap{max-height:120px;overflow:hidden}
  .fo-window-closed-message{display:none;position:fixed;inset:0;align-items:center;justify-content:center;z-index:4000}
  body.fo-closed .fo-window-closed-message{display:flex}
  .fo-window-closed-message .box{background:rgba(2,6,23,0.9);color:#fff;padding:18px 22px;border-radius:10px;box-shadow:0 10px 40px rgba(2,6,23,0.6);font-size:16px}
  .fo-window-closed-message .box button{margin-left:12px;padding:8px 12px;border-radius:8px;border:0;cursor:pointer}
</style>

<div class="fo-nav-arrows" aria-hidden="false">
  <button class="fo-nav-btn fo-nav-btn-back" title="Retour" aria-label="Retour">◀</button>
  <button class="fo-nav-btn fo-nav-btn-forward" title="Page suivante" aria-label="Page suivante">▶</button>
</div>

<div class="fo-window-controls" aria-hidden="false">
  <button class="fo-wc-btn fo-wc-reduce" title="Réduire" aria-label="Réduire">−</button>
  <button class="fo-wc-btn fo-wc-minimize" title="Minimiser" aria-label="Minimiser">▢</button>
  <button class="fo-wc-btn fo-wc-close" title="Fermer" aria-label="Fermer">✕</button>
</div>

<div class="fo-window-closed-message" role="dialog" aria-modal="true">
  <div class="box">Fenêtre fermée.<button class="fo-window-restore">Réouvrir</button></div>
</div>

<script>
(function(){
  function $(sel){return document.querySelector(sel)}
  document.addEventListener('DOMContentLoaded', function(){
    var reduce = $('.fo-wc-reduce');
    var mini = $('.fo-wc-minimize');
    var close = $('.fo-wc-close');
    var restore = document.querySelector('.fo-window-restore');
    var back = $('.fo-nav-btn-back');
    var forward = $('.fo-nav-btn-forward');
    if(reduce){reduce.addEventListener('click', function(){document.body.classList.toggle('fo-reduced')})}
    if(mini){mini.addEventListener('click', function(){document.body.classList.toggle('fo-minimized')})}
    if(close){close.addEventListener('click', function(){document.body.classList.add('fo-closed')})}
    if(restore){restore.addEventListener('click', function(e){e.preventDefault();document.body.classList.remove('fo-closed')})}
    if(back){back.addEventListener('click', function(e){e.preventDefault(); try { history.back(); } catch(err){} })}
    if(forward){forward.addEventListener('click', function(e){e.preventDefault(); try { history.forward(); } catch(err){} })}
  });
})();
</script>
