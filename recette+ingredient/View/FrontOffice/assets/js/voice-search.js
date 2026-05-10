// voice-search.js
(function(){
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

  function createStyle(){
    const css = `
    .voice-btn{display:inline-flex;align-items:center;justify-content:center;border:1px solid #e6e6e6;background:#fff;border-radius:6px;padding:6px 8px;cursor:pointer;margin-left:6px;height:36px;min-width:36px}
    .voice-btn svg{width:16px;height:16px}
    .voice-btn.active{background:#ef4444;color:#fff;border-color:#ef4444}
    .voice-btn.disabled{opacity:.45;cursor:not-allowed}
    .voice-field-wrap{display:flex;align-items:center;gap:8px;width:100%}
    .voice-field-wrap input,.voice-field-wrap textarea{flex:1 1 auto}
    .voice-field-wrap .voice-btn{flex:0 0 auto;margin-left:6px}
    `;
    const s = document.createElement('style'); s.appendChild(document.createTextNode(css)); document.head.appendChild(s);
  }

  function makeMicButton(){
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'voice-btn';
    btn.setAttribute('aria-label','Recherche vocale');
    btn.title = 'Recherche vocale';
    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><path d="M12 1v11a3 3 0 0 0 6 0V1"/><path d="M19 11a7 7 0 0 1-14 0"/><line x1="12" y1="19" x2="12" y2="23"/></svg>';
    return btn;
  }

  function init(){
    createStyle();
    // Attach to search inputs and any element opting-in via data-voice
    const selector = 'input[name="q"], textarea[data-voice], input[data-voice]';
    const els = document.querySelectorAll(selector);
    if (!els || els.length === 0) return;

    els.forEach(el => {
      // only attach to text inputs and textareas
      const tag = (el.tagName || '').toLowerCase();
      if (tag !== 'input' && tag !== 'textarea') return;

      if (el.dataset.voiceAttached) return;
      el.dataset.voiceAttached = '1';

      const btn = makeMicButton();

      // If the element is inside a .field (column layout), wrap the input and button
      const parent = el.parentElement;
      if (parent && parent.classList && parent.classList.contains('field')) {
        // avoid double-wrapping
        if (!el.dataset.voiceWrapped) {
          const wrapper = document.createElement('div');
          wrapper.className = 'voice-field-wrap';
          // insert wrapper before the element and move the element inside it
          parent.insertBefore(wrapper, el);
          wrapper.appendChild(el);
          el.dataset.voiceWrapped = '1';
          wrapper.appendChild(btn);
        } else {
          el.insertAdjacentElement('afterend', btn);
        }
      } else {
        el.insertAdjacentElement('afterend', btn);
      }

      if (!SpeechRecognition) {
        btn.classList.add('disabled');
        btn.title = 'Reconnaissance vocale non prise en charge dans ce navigateur';
        return;
      }

      const recognition = new SpeechRecognition();
      recognition.lang = el.dataset.voiceLang || 'fr-FR';
      recognition.interimResults = el.dataset.voiceInterim === 'true' ? true : false;
      recognition.maxAlternatives = parseInt(el.dataset.voiceMaxAlternatives || '1', 10) || 1;
      let listening = false;

      const shouldAutoSubmit = (el.name && el.name === 'q') || el.dataset.voiceAutosubmit === 'true';
      const shouldAppend = el.dataset.voiceAppend === 'true';

      btn.setAttribute('aria-pressed', 'false');

      btn.addEventListener('click', function(){
        try {
          if (listening) { recognition.stop(); return; }
          recognition.start();
        } catch(e){ console.error(e); }
      });

      recognition.addEventListener('start', function(){
        listening = true; btn.classList.add('active'); btn.setAttribute('aria-pressed', 'true');
      });

      recognition.addEventListener('end', function(){
        listening = false; btn.classList.remove('active'); btn.setAttribute('aria-pressed', 'false');
      });

      recognition.addEventListener('result', function(ev){
        try {
          // build transcript (handle interim results)
          let transcript = '';
          for (let i = 0; i < ev.results.length; i++) {
            transcript += ev.results[i][0].transcript;
            if (ev.results[i].isFinal === false) transcript += '';
            if (i < ev.results.length - 1) transcript += ' ';
          }

          if (shouldAppend && el.value) el.value = el.value + (el.value.endsWith(' ') ? '' : ' ') + transcript;
          else el.value = transcript;

          // submit only for search fields or when explicitly requested
          if (shouldAutoSubmit) {
            const form = el.form || el.closest('form');
            if (form) form.submit();
          }
        } catch(e){ console.error(e); }
      });

      recognition.addEventListener('error', function(e){
        console.error('SpeechRecognition error', e);
        listening = false; btn.classList.remove('active'); btn.setAttribute('aria-pressed', 'false');
        if (e && e.error === 'not-allowed') {
          btn.classList.add('disabled');
          btn.title = 'Accès au microphone refusé';
        }
      });
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();

})();
