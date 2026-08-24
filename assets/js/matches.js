/* Matches — minimal functional (vibe removed) */
(function(){
  var CITIES=['Chennai','Bangalore','Coimbatore','Mumbai','Hyderabad','Delhi'];
  var RELIGIONS=['Hindu','Muslim','Christian','Sikh','Jain'];
  var TONGUES=['Tamil','Hindi','Telugu','Malayalam'];
  var EDUCATION=['Engineering','Medicine','Business','Arts',"Master's"];
  var PROFESSIONS=['Software Engineer','Doctor','Entrepreneur','Teacher','Finance'];
  var MARITAL=['Never Married','Divorced','Widowed','Awaiting Divorce'];
  var FIRST_F=['Ananya','Priya','Divya','Meera','Kavya','Shreya','Nithya','Aishwarya','Lakshmi','Sneha','Riya','Pooja'];
  var FIRST_M=['Arjun','Karthik','Rahul','Vikram','Aditya','Sanjay','Harish','Naveen','Ramesh','Vijay','Akash','Deepak'];
  function seed(){
    var list=[];
    for(var i=0;i<24;i++){
      var isF=i%2===0, name=(isF?FIRST_F:FIRST_M)[i%12];
      list.push({id:'p'+(i+1), name:name, gender:isF?'women':'men', age:22+((i*5)%20), heightIn:58+((i*3)%16), city:CITIES[i%CITIES.length], religion:RELIGIONS[i%RELIGIONS.length], tongue:TONGUES[i%TONGUES.length], education:EDUCATION[i%EDUCATION.length], profession:PROFESSIONS[i%PROFESSIONS.length], marital:MARITAL[i%MARITAL.length], score:72+((i*7)%27), created:Date.now()-i*86400000*3, active:Date.now()-(i%10)*3600000, distance:(i*13)%900});
    }
    return list;
  }
  var ALL=seed();
  var state={search:'', gender:'all', ageMin:21, ageMax:42, hMin:58, hMax:74, religions:[], tongues:[], locations:[], education:[], professions:[], maritalStatuses:[], sort:'recommended'};
  var AGE_MIN=18, AGE_MAX=70, H_MIN=56, H_MAX=76;
  function $(s,c){return (c||document).querySelector(s)}
  function $all(s,c){return Array.prototype.slice.call((c||document).querySelectorAll(s))}
  function fmt(n){return n.toLocaleString('en-IN')}
  function ftIn(v){return Math.floor(v/12)+"'"+(v%12)+'"'}
  function esc(s){return String(s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]})}
  var grid,chipsWrap,countEl,sidebarCountEl,emptyState;
  function passes(p){
    if(state.gender!=='all' && p.gender!==state.gender) return false;
    if(p.age<state.ageMin||p.age>state.ageMax) return false;
    if(p.heightIn<state.hMin||p.heightIn>state.hMax) return false;
    if(state.religions.length && state.religions.indexOf(p.religion)===-1) return false;
    if(state.tongues.length && state.tongues.indexOf(p.tongue)===-1) return false;
    if(state.locations.length && state.locations.indexOf(p.city)===-1) return false;
    if(state.education.length && state.education.indexOf(p.education)===-1) return false;
    if(state.professions.length && state.professions.indexOf(p.profession)===-1) return false;
    if(state.maritalStatuses.length && state.maritalStatuses.indexOf(p.marital)===-1) return false;
    if(state.search){ var hay=[p.name,p.city,p.religion,p.education,p.profession,p.tongue].join(' ').toLowerCase(); if(hay.indexOf(state.search.toLowerCase())===-1) return false; }
    return true;
  }
  function applySorting(list){
    var s=state.sort;
    return list.slice().sort(function(a,b){
      switch(s){case 'newest':return b.created-a.created; case 'recently_active':return b.active-a.active; case 'distance':return a.distance-b.distance; case 'age_asc':return a.age-b.age; case 'age_desc':return b.age-a.age; default:return b.score-a.score;}
    });
  }
  function cardHTML(p){
    return '<article class="m-card" data-id="'+p.id+'" tabindex="0">'
      +'<div class="m-card-head"><i class="bi bi-person-circle"></i></div>'
      +'<div class="m-card-body">'
      +'<h3 class="m-card-title">'+esc(p.name)+', '+p.age+'</h3>'
      +'<p class="m-card-sub">'+ftIn(p.heightIn)+' · '+esc(p.city)+' · '+esc(p.tongue)+'</p>'
      +'<p class="m-card-meta">'+esc(p.profession)+' · '+esc(p.education)+' · '+esc(p.religion)+'</p>'
      +'<div class="m-card-actions"><button type="button" class="m-btn m-btn-primary" style="flex:1">Connect</button></div>'
      +'</div></article>';
  }
  function renderProfiles(){
    var filtered=applySorting(ALL.filter(passes));
    if(countEl) countEl.innerHTML='<strong>'+fmt(filtered.length)+'</strong> profiles found';
    if(sidebarCountEl) sidebarCountEl.textContent=fmt(filtered.length);
    if(!filtered.length){ grid.hidden=true; emptyState.hidden=false; return; }
    emptyState.hidden=true; grid.hidden=false;
    grid.innerHTML=filtered.map(cardHTML).join('');
  }
  function chipData(){
    var chips=[];
    if(!(state.ageMin===21&&state.ageMax===42)) chips.push({key:'age', label:'Age '+state.ageMin+'–'+state.ageMax});
    if(!(state.hMin===58&&state.hMax===74)) chips.push({key:'height', label:'Height '+ftIn(state.hMin)+' — '+ftIn(state.hMax)});
    if(state.gender!=='all') chips.push({key:'gender', label:state.gender==='women'?'Women':'Men'});
    [['religions','religion'],['tongues','tongue'],['locations','location'],['education','education'],['professions','profession'],['maritalStatuses','marital']].forEach(function(pair){ state[pair[0]].forEach(function(v){chips.push({key:pair[0], value:v, label:v})}) });
    if(state.search) chips.push({key:'search', label:'"'+state.search+'"'});
    return chips;
  }
  function renderChips(){
    var chips=chipData();
    if(!chips.length){ chipsWrap.innerHTML=''; return; }
    var html=chips.map(function(c){ var dv=c.value?' data-value="'+esc(c.value)+'"':''; return '<span class="chip">'+esc(c.label)+'<button type="button" data-key="'+c.key+'"'+dv+'">&times;</button></span>'; }).join('');
    chipsWrap.innerHTML=html+'<button type="button" class="clear-all-btn">Clear all</button>';
  }
  function syncControls(scope){
    $all('.filter-checkbox[data-group]',scope).forEach(function(cb){ cb.checked=state[cb.getAttribute('data-group')].indexOf(cb.value)!==-1; });
    $all('input[name="m-gender"]',scope).forEach(function(r){ r.checked=r.value===state.gender; });
    $all('.js-age-min',scope).forEach(function(el){el.value=state.ageMin});
    $all('.js-age-max',scope).forEach(function(el){el.value=state.ageMax});
    $all('.js-h-min',scope).forEach(function(el){el.value=state.hMin});
    $all('.js-h-max',scope).forEach(function(el){el.value=state.hMax});
    updateRange(scope);
  }
  function updateRange(scope){
    $all('.js-age-wrap',scope).forEach(function(w){ var fill=$('.m-fill',w); if(!fill) return; fill.style.left=((state.ageMin-AGE_MIN)/(AGE_MAX-AGE_MIN))*100+'%'; fill.style.right=(100-((state.ageMax-AGE_MIN)/(AGE_MAX-AGE_MIN))*100)+'%'; var d=$('.js-age-display',w); if(d) d.textContent=state.ageMin+' – '+state.ageMax+' years'; });
    $all('.js-height-wrap',scope).forEach(function(w){ var fill=$('.m-fill',w); if(!fill) return; fill.style.left=((state.hMin-H_MIN)/(H_MAX-H_MIN))*100+'%'; fill.style.right=(100-((state.hMax-H_MIN)/(H_MAX-H_MIN))*100)+'%'; var d=$('.js-height-display',w); if(d) d.textContent=ftIn(state.hMin)+' — '+ftIn(state.hMax); });
  }
  function saveSort(){ try{localStorage.setItem('matrimony_sort',state.sort)}catch(e){} }
  function updateURL(){
    if(!history.replaceState) return;
    var p=new URLSearchParams();
    if(state.gender!=='all') p.set('g',state.gender);
    p.set('ageMin',state.ageMin); p.set('ageMax',state.ageMax); p.set('hMin',state.hMin); p.set('hMax',state.hMax);
    if(state.search) p.set('q',state.search);
    var MAP={rel:'religions', tongue:'tongues', loc:'locations', edu:'education', prof:'professions', ms:'maritalStatuses'};
    Object.keys(MAP).forEach(function(k){ if(state[MAP[k]].length) p.set(k,state[MAP[k]].join(','))});
    if(state.sort!=='recommended') p.set('sort',state.sort);
    history.replaceState({},'', location.pathname+(p.toString()?'?'+p.toString():''));
  }
  function loadURL(){
    var p=new URLSearchParams(location.search);
    if(p.get('g')) state.gender=p.get('g');
    if(p.get('ageMin')) state.ageMin=Math.max(AGE_MIN,parseInt(p.get('ageMin'),10));
    if(p.get('ageMax')) state.ageMax=Math.min(AGE_MAX,parseInt(p.get('ageMax'),10));
    if(p.get('hMin')) state.hMin=Math.max(H_MIN,parseInt(p.get('hMin'),10));
    if(p.get('hMax')) state.hMax=Math.min(H_MAX,parseInt(p.get('hMax'),10));
    if(p.get('q')) state.search=p.get('q');
    var MAP={rel:'religions', tongue:'tongues', loc:'locations', edu:'education', prof:'professions', ms:'maritalStatuses'};
    Object.keys(MAP).forEach(function(k){ if(p.get(k)) state[MAP[k]]=p.get(k).split(',')});
    var stored=null; try{stored=localStorage.getItem('matrimony_sort')}catch(e){}
    state.sort=p.get('sort')||stored||'recommended';
  }
  var urlTimer;
  function refresh(){ renderChips(); renderProfiles(); clearTimeout(urlTimer); urlTimer=setTimeout(updateURL,200); }
  function showSkeleton(n){
    var html=''; for(var i=0;i<n;i++) html+='<div class="skeleton-card"><div class="shimmer skeleton-image"></div><div class="skeleton-body"><div class="shimmer skeleton-row w60"></div><div class="shimmer skeleton-row w80"></div><div class="shimmer skeleton-row w40"></div></div></div>';
    grid.hidden=false; emptyState.hidden=true; grid.innerHTML=html;
  }
  function wire(){
    $all('input[name="m-gender"]').forEach(function(r){ r.addEventListener('change',function(){ state.gender=r.value; syncControls(document); refresh(); })});
    $all('.filter-checkbox[data-group]').forEach(function(cb){
      cb.addEventListener('change',function(){
        var g=cb.getAttribute('data-group'), arr=state[g], idx=arr.indexOf(cb.value);
        if(cb.checked&&idx===-1) arr.push(cb.value);
        if(!cb.checked&&idx>-1) arr.splice(idx,1);
        refresh();
      });
    });
    function bind(sel, loKey, hiKey){
      $all(sel+'-min').forEach(function(lo){ lo.addEventListener('input',function(){ var v=parseInt(lo.value,10); if(v>state[hiKey]-1){v=state[hiKey]-1; lo.value=v} state[loKey]=v; updateRange(document)}); lo.addEventListener('change',refresh); });
      $all(sel+'-max').forEach(function(hi){ hi.addEventListener('input',function(){ var v=parseInt(hi.value,10); if(v<state[loKey]+1){v=state[loKey]+1; hi.value=v} state[hiKey]=v; updateRange(document)}); hi.addEventListener('change',refresh); });
    }
    bind('.js-age','ageMin','ageMax'); bind('.js-h','hMin','hMax');
    var searchInput=$('#mSearch');
    var t;
    if(searchInput){ searchInput.addEventListener('input',function(){ clearTimeout(t); t=setTimeout(function(){ state.search=searchInput.value.trim(); refresh(); },250)}); }
    var sortSel=$('#sortSelect');
    if(sortSel){ sortSel.addEventListener('change',function(){ state.sort=sortSel.value; saveSort(); refresh(); }); }
    chipsWrap.addEventListener('click',function(e){
      var btn=e.target.closest('button'); if(!btn) return;
      if(btn.classList.contains('clear-all-btn')){ clearAll(); return; }
      removeChip(btn.getAttribute('data-key'), btn.getAttribute('data-value'));
    });
    function clearAll(){
      state.search=''; if(searchInput) searchInput.value='';
      state.gender='all'; state.ageMin=21; state.ageMax=42; state.hMin=58; state.hMax=74;
      ['religions','tongues','locations','education','professions','maritalStatuses'].forEach(function(k){state[k]=[]});
      syncControls(document); refresh();
    }
    function removeChip(key,val){
      switch(key){
        case 'age': state.ageMin=21; state.ageMax=42; break;
        case 'height': state.hMin=58; state.hMax=74; break;
        case 'gender': state.gender='all'; break;
        case 'search': state.search=''; if(searchInput) searchInput.value=''; break;
        default: var arr=state[key]; var idx=arr.indexOf(val); if(idx>-1) arr.splice(idx,1);
      }
      syncControls(document); refresh();
    }
    var ec=$('#emptyClear'); if(ec) ec.addEventListener('click', clearAll);
    grid.addEventListener('click',function(e){
      var card=e.target.closest('.m-card'); if(!card) return;
      window.location.href='./register.php';
    });
    // Drawer
    var drawer=$('#filterDrawer'), backdrop=$('#drawerBackdrop');
    function open(){ drawer.classList.add('open'); backdrop.classList.add('open'); document.body.style.overflow='hidden'; }
    function close(){ drawer.classList.remove('open'); backdrop.classList.remove('open'); document.body.style.overflow=''; drawer.setAttribute('aria-hidden','true'); }
    var btn=$('#mobileFilterBtn'); if(btn) btn.addEventListener('click', open);
    var closeBtn=$('.m-drawer-close',drawer); if(closeBtn) closeBtn.addEventListener('click', close);
    if(backdrop) backdrop.addEventListener('click', close);
    var ca=$('#drawerClear'); if(ca) ca.addEventListener('click', clearAll);
    var sc=$('#sidebarClear'); if(sc) sc.addEventListener('click', clearAll);
    var ap=$('#drawerApply'); if(ap) ap.addEventListener('click', close);
    document.addEventListener('keydown',function(e){ if(e.key==='Escape') close(); });
  }
  function init(){
    grid=$('#profileGrid'); chipsWrap=$('#filterChips'); countEl=$('#resultCount'); sidebarCountEl=$('#sidebarCount'); emptyState=$('#emptyState');
    loadURL();
    var si=$('#mSearch'); if(si&&state.search) si.value=state.search;
    var ss=$('#sortSelect'); if(ss) ss.value=state.sort;
    showSkeleton(8); syncControls(document); wire();
    setTimeout(function(){ renderChips(); renderProfiles(); updateURL(); }, 200);
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
