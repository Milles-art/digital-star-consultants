const nav=document.getElementById('site-nav');
if(nav) addEventListener('scroll',()=>nav.classList.toggle('scrolled',scrollY>30),{passive:true});
const io=new IntersectionObserver(entries=>entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target)}}),{threshold:.12});
document.querySelectorAll('.reveal').forEach(el=>io.observe(el));
document.querySelectorAll('[data-faq]').forEach(item=>{const b=item.querySelector('button');b?.addEventListener('click',()=>item.classList.toggle('open'))});
const canvas=document.getElementById('stars');
if(canvas && !matchMedia('(prefers-reduced-motion: reduce)').matches){const ctx=canvas.getContext('2d');let stars=[];function resize(){canvas.width=innerWidth;canvas.height=innerHeight;stars=Array.from({length:Math.min(180,Math.floor(innerWidth*innerHeight/9000))},()=>({x:Math.random()*canvas.width,y:Math.random()*canvas.height,r:Math.random()*1+.2,a:Math.random()*.7+.15,p:Math.random()*Math.PI*2}))}resize();addEventListener('resize',resize,{passive:true});function tick(t){ctx.clearRect(0,0,canvas.width,canvas.height);for(const s of stars){ctx.globalAlpha=s.a*(.55+.45*Math.sin(t*.001+s.p));ctx.fillStyle='#fff';ctx.beginPath();ctx.arc(s.x,s.y,s.r,0,Math.PI*2);ctx.fill()}requestAnimationFrame(tick)}requestAnimationFrame(tick)}
