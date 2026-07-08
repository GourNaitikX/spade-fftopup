const verifyBtn = document.getElementById('verifyBtn');
const uidInput  = document.getElementById('uidInput');
const loadBox   = document.getElementById('loadBox');
const resultBox = document.getElementById('resultBox');
const errBox    = document.getElementById('errBox');

verifyBtn?.addEventListener('click', doVerify);
uidInput?.addEventListener('keydown', e=>{ if(e.key==='Enter') doVerify(); });

async function doVerify(){
  const uid = uidInput.value.trim();
  errBox.classList.add('hide');
  resultBox.classList.add('hide');
  if(!/^\d{5,15}$/.test(uid)){
    errBox.textContent='Please enter a valid numeric UID.';
    errBox.classList.remove('hide'); return;
  }
  loadBox.classList.remove('hide');
  loadBox.classList.add('fade-loop');
  try{
    const res = await fetch(`https://ffuid2info.up.railway.app/api/check?uid=${uid}`);
    const json = await res.json();
    await new Promise(r=>setTimeout(r,1400)); // smooth loading feel
    loadBox.classList.add('hide');
    if(!json.success || !json.data){
      errBox.textContent='UID not found. Please check and try again.';
      errBox.classList.remove('hide'); return;
    }
    const d=json.data;
    document.getElementById('rName').textContent  = d.Name;
    document.getElementById('rUid').textContent   = d.UID;
    document.getElementById('rLevel').textContent = d.Level;
    document.getElementById('rLikes').textContent = d.Likes;
    document.getElementById('rRegion').textContent= d.Region;
    localStorage.setItem('ffAccount', JSON.stringify(d));
    resultBox.classList.remove('hide');
    resultBox.classList.add('pop-in');
    celebrate();
  }catch(err){
    loadBox.classList.add('hide');
    errBox.textContent='Server error. Please try again later.';
    errBox.classList.remove('hide');
  }
}

document.getElementById('continueBtn')?.addEventListener('click',()=>{
  document.body.classList.add('page-leave');
  setTimeout(()=>location.href='shop.php',350);
});

// confetti celebration
function celebrate(){
  const layer=document.getElementById('confetti');
  const colors=['#e01e2b','#ff5964','#ffd23f','#ffffff','#ff8fa3'];
  for(let i=0;i<80;i++){
    const p=document.createElement('span');
    p.className='conf-piece';
    p.style.left=Math.random()*100+'vw';
    p.style.background=colors[Math.floor(Math.random()*colors.length)];
    p.style.animationDelay=Math.random()*0.5+'s';
    p.style.transform=`rotate(${Math.random()*360}deg)`;
    layer.appendChild(p);
    setTimeout(()=>p.remove(),2600);
  }
}
