
(function(){
"use strict";
var KEY="coworklibrary.v1";
var SHIFT_KEYS=["full","morning","evening"];
var MON=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
var MONF=["January","February","March","April","May","June","July","August","September","October","November","December"];
var EXAMS=["UPSC","RAS","SSC","Banking","REET","Patwari","NEET","JEE","CA","CLAT","College","Other"];
var FACS=["AC","Wi-Fi","Locker","RO water","CCTV","Power backup","Parking","Girls-only section","Discussion room","Newspapers"];

/* ---------- utils ---------- */

function syncBackend(action, payload) {
  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  fetch('/api/library/sync', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrf,
      'Accept': 'application/json'
    },
    body: JSON.stringify({ action: action, payload: payload })
  }).then(function(res){ return res.json(); })
    .then(function(d){
      if(d && d.success) {
        console.log('[CoworkLibrary] Synced:', action);
        if(d.data){
          if(d.data.libs) S.libs = d.data.libs;
          if(d.data.students) S.students = d.data.students;
          if(d.data.requests) S.requests = d.data.requests;
          if(d.data.payments) S.payments = d.data.payments;
          if(d.data.myRequests) S.myRequests = d.data.myRequests;
          if(d.property_id) S.ownerLibId = String(d.property_id);
          save();
          render();
        }
      }
    }).catch(function(err){
      console.warn('[CoworkLibrary] Sync error:', err);
    });
}

function pad(n){return (n<10?"0":"")+n}
function iso(d){return d.getFullYear()+"-"+pad(d.getMonth()+1)+"-"+pad(d.getDate())}
function parse(s){var p=String(s).split("-");return new Date(+p[0],+p[1]-1,+p[2])}
function today(){return iso(new Date())}
function addDays(s,n){var d=parse(s);d.setDate(d.getDate()+n);return iso(d)}
function addMonths(s,n){var d=parse(s),day=d.getDate();d.setDate(1);d.setMonth(d.getMonth()+n);var last=new Date(d.getFullYear(),d.getMonth()+1,0).getDate();d.setDate(Math.min(day,last));return iso(d)}
function diff(a,b){return Math.round((parse(a)-parse(b))/864e5)}
function fmt(s){var d=parse(s);return d.getDate()+" "+MON[d.getMonth()]}
function rs(n){return "₹"+Math.round(n||0).toLocaleString("en-IN")}
function esc(s){return String(s==null?"":s).replace(/[&<>"']/g,function(c){return {"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]})}
function uid(){return Math.random().toString(36).slice(2,9)}
function initials(n){var p=String(n).trim().split(/\s+/);return ((p[0]||"")[0]||"").toUpperCase()+((p[1]||"")[0]||"").toUpperCase()}
function phoneIntl(p){var d=String(p||"").replace(/\D/g,"");if(d.length===10)d="91"+d;else if(d.length===11&&d[0]==="0")d="91"+d.slice(1);return d}
function phoneNice(p){var d=String(p||"").replace(/\D/g,"");if(d.length===10)return d.slice(0,5)+" "+d.slice(5);return p||""}
function validPhone(p){return /^[6-9]\d{9}$/.test(String(p||"").replace(/\D/g,"").replace(/^91(?=\d{10}$)/,""))}
function rng(seed){var s=seed>>>0;return function(){s=(s*1664525+1013904223)>>>0;return s/4294967296}}

/* ---------- icons ---------- */
function sv(p){return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'+p+'</svg>'}
var I={
  building:sv('<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M10 21v-4h4v4"/>'),
  home:sv('<path d="M3 11l9-7 9 7v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>'),
  seats:sv('<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>'),
  people:sv('<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.8-3.5 3.4-5.5 6.5-5.5s5.7 2 6.5 5.5"/><circle cx="17" cy="9" r="2.5"/><path d="M16.5 14.6c2.6.2 4.4 2 5 4.9"/>'),
  rupee:sv('<path d="M7 4h10M7 8.5h10M7 4h3.5a4.5 4.5 0 0 1 0 9H7l7.5 7"/>'),
  inbox:sv('<path d="M3 13l2.5-8h13L21 13v6a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M3 13h5l1.5 2.5h5L16 13h5"/>'),
  store:sv('<path d="M4 9l1.5-5h13L20 9"/><path d="M4 9v11h16V9"/><path d="M4 9c0 1.7 1.3 3 3 3s3-1.3 3-3c0 1.7 1.3 3 3 3s3-1.3 3-3c0 1.7 1.3 3 3 3"/><path d="M10 20v-5h4v5"/>'),
  search:sv('<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>'),
  pin:sv('<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>'),
  clock:sv('<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'),
  phone:sv('<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/>'),
  back:sv('<path d="M15 18l-6-6 6-6"/>'),
  x:sv('<path d="M6 6l12 12M18 6L6 18"/>'),
  plus:sv('<path d="M12 5v14M5 12h14"/>'),
  check:sv('<path d="M5 12.5l4.5 4.5L19 7.5"/>'),
  up:sv('<path d="M6 15l6-6 6 6"/>'),
  down:sv('<path d="M6 9l6 6 6-6"/>'),
  trash:sv('<path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/>'),
  edit:sv('<path d="M4 20h4L19 9l-4-4L4 16z"/><path d="M13.5 6.5l4 4"/>'),
  map:sv('<path d="M3 6l6-2 6 2 6-2v14l-6 2-6-2-6 2z"/><path d="M9 4v14M15 6v14"/>'),
  bell:sv('<path d="M6 16V11a6 6 0 0 1 12 0v5l2 2H4z"/><path d="M10 20a2 2 0 0 0 4 0"/>'),
  shield:sv('<path d="M12 3l8 3v6c0 4.5-3.4 8.3-8 9-4.6-.7-8-4.5-8-9V6z"/><path d="M8.5 12l2.5 2.5 4.5-5"/>'),
  tag:sv('<path d="M3 12V4h8l10 10-8 8z"/><circle cx="7.5" cy="8.5" r="1.5"/>'),
  wa:'<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3z"/></svg>'
};

/* ---------- state ---------- */
var S,storageOK=true;
function load(){
  var d;
  if(window.__INITIAL_DATA__ && Array.isArray(window.__INITIAL_DATA__.libs)){
    d = window.__INITIAL_DATA__;
    try{ localStorage.removeItem(KEY); }catch(e){}
  } else {
    try{
      var raw=localStorage.getItem(KEY);
      if(raw){
        var parsed=JSON.parse(raw);
        if(parsed&&parsed.v===1&&Array.isArray(parsed.libs)) d = parsed;
      }
    }catch(e){storageOK=false}
  }
  if(!d && !window.__INITIAL_DATA__) d = seed();
  if(!d) d = {v:1, ownerLibId:"", libs:[], students:[], payments:[], requests:[], myRequests:[]};
  if(!d.libs) d.libs = [];
  if(!d.students) d.students = [];
  if(!d.payments) d.payments = [];
  if(!d.requests) d.requests = [];
  if(!d.myRequests) d.myRequests = [];
  if(!d.ownerLibId && d.libs.length) d.ownerLibId = d.libs[0].id;
  return d;
}
function save(){try{localStorage.setItem(KEY,JSON.stringify(S));storageOK=true}catch(e){storageOK=false}}
var ui={mode:"student",q:"",city:"",acOnly:false,view:"list",libId:null,pubHall:null,pubShift:"morning",pubSeat:null,sent:null,
  page:"dash",seatTab:"full",hall:null,stuQ:"",stuF:"all",reqF:"pending",builder:null,sheet:null};

function shiftsDefault(f,m,e){return {full:{label:"Full day",time:"6 AM – 10 PM",price:f},morning:{label:"Morning",time:"6 AM – 2 PM",price:m},evening:{label:"Evening",time:"2 PM – 10 PM",price:e}}}
function mkHall(name,ac,rows,n,aisle){var h={id:uid(),name:name,ac:ac,rows:[]};for(var i=0;i<rows;i++)h.rows.push({id:uid(),n:(typeof n==="function"?n(i):n),aisle:aisle});return h}

var FIRST=["Aarav","Priya","Rohit","Neha","Vikas","Pooja","Ankit","Sneha","Manish","Kavita","Deepak","Ritu","Sandeep","Anjali","Rahul","Nisha","Amit","Komal","Suresh","Payal","Gaurav","Shivani","Mohit","Divya","Lokesh","Megha","Hemant","Sakshi","Yogesh","Bhavna","Naveen","Jyoti","Pankaj","Tanvi","Rakesh","Monika","Ajay","Swati","Kuldeep","Preeti","Dinesh","Aarti","Vivek","Sonam","Tarun","Mamta","Harsh","Rekha","Jitendra","Kiran","Sunil","Usha","Ravi","Asha","Nitin","Simran","Bhupendra","Laxmi","Arjun","Radhika","Kapil","Seema","Mahesh","Pinki"];
var LAST=["Sharma","Meena","Choudhary","Jat","Saini","Gupta","Yadav","Kumawat","Verma","Agarwal","Singh","Jangid","Soni","Bairwa","Khandelwal","Rathore","Prajapat","Mali","Joshi","Swami"];

function seed(){
  var r=rng(20261027),t=today();
  var own={id:"udaan",name:"Udaan Study Library",area:"Mansarovar",city:"Jaipur",address:"Plot 14, Shipra Path, near Technology Circle, Mansarovar, Jaipur 302020",
    phone:"9829012345",upi:"udaan.library@upi",open:"6:00 AM",close:"10:00 PM",facilities:["AC","Wi-Fi","Locker","RO water","CCTV","Power backup","Girls-only section"],
    desc:"Silent self-study hall for RAS, UPSC, SSC and REET aspirants. Separate AC and non-AC halls, personal lockers, 24-hour power backup and filtered water. Monthly passes by shift.",
    shifts:shiftsDefault(1200,700,700),
    halls:[mkHall("Ground floor",true,6,10,5),mkHall("First floor",false,4,12,6)],hue:250};
  var others=[
    {id:"shiksha",name:"Shiksha Self Study Point",area:"Vaishali Nagar",city:"Jaipur",address:"B-42, Amrapali Marg, Vaishali Nagar, Jaipur",phone:"9414055501",open:"6:00 AM",close:"11:00 PM",facilities:["AC","Wi-Fi","Locker","CCTV"],desc:"Quiet AC reading hall with cabin-style desks.",shifts:shiftsDefault(1400,800,800),halls:[mkHall("Main hall",true,5,12,6)],hue:205},
    {id:"lakshya",name:"Lakshya Library",area:"Malviya Nagar",city:"Jaipur",address:"Sector 3, near Gaurav Tower, Malviya Nagar, Jaipur",phone:"9660012233",open:"7:00 AM",close:"10:00 PM",facilities:["Wi-Fi","RO water","Newspapers","Power backup"],desc:"Affordable non-AC library close to coaching hubs.",shifts:shiftsDefault(900,550,550),halls:[mkHall("Hall A",false,4,10,5),mkHall("Hall B",false,3,8,4)],hue:160},
    {id:"ekagra",name:"Ekagra Reading Hall",area:"Piprali Road",city:"Sikar",address:"Opp. Kalyan Circle, Piprali Road, Sikar",phone:"9887766554",open:"5:30 AM",close:"10:30 PM",facilities:["AC","Wi-Fi","Locker","Girls-only section","Parking"],desc:"For NEET and JEE students. Separate girls' section and lockers.",shifts:shiftsDefault(1100,650,650),halls:[mkHall("AC hall",true,5,10,5)],hue:20},
    {id:"pratibha",name:"Pratibha Digital Library",area:"Ratanada",city:"Jodhpur",address:"Near Circuit House Road, Ratanada, Jodhpur",phone:"9928344556",open:"6:00 AM",close:"10:00 PM",facilities:["AC","Wi-Fi","CCTV","Discussion room"],desc:"Bright AC hall with a small discussion room for group study.",shifts:shiftsDefault(1000,600,600),halls:[mkHall("Main hall",true,4,11,5)],hue:330}
  ];
  others.forEach(function(L,li){L.upi="";L.occ=[];var dens=[.78,.6,.92,.5][li];
    L.halls.forEach(function(h){h.rows.forEach(function(row){for(var i=1;i<=row.n;i++){var x=r();if(x<dens*.45)L.occ.push({h:h.id,s:row.id+":"+i,shift:"full"});else if(x<dens*.75){L.occ.push({h:h.id,s:row.id+":"+i,shift:"morning"});if(r()<dens*.6)L.occ.push({h:h.id,s:row.id+":"+i,shift:"evening"})}else if(x<dens){L.occ.push({h:h.id,s:row.id+":"+i,shift:"evening"})}}})})});
  var students=[],payments=[],used={};
  function pickName(){var n;do{n=FIRST[Math.floor(r()*FIRST.length)]+" "+LAST[Math.floor(r()*LAST.length)]}while(used[n]);used[n]=1;return n}
  function phone(){return String(9)+String(Math.floor(r()*9)+1)+String(Math.floor(r()*1e8)).padStart(8,"0")}
  /* fee situations to show every state */
  var dueOffsets=[-12,-6,-3,-1,0,0,1,2,3,3];
  var allSeats=[];own.halls.forEach(function(h){h.rows.forEach(function(row){for(var i=1;i<=row.n;i++)allSeats.push({h:h.id,s:row.id+":"+i})})});
  var k=0;
  allSeats.forEach(function(seat,idx){var x=r();var list=[];
    if(x<.30)list=["full"];else if(x<.52)list=["morning","evening"];else if(x<.64)list=["morning"];else if(x<.72)list=["evening"];
    list.forEach(function(sh){
      var off=k<dueOffsets.length?dueOffsets[k]:Math.floor(r()*26)+4;k++;
      var paidUntil=addDays(t,off);var fee=own.shifts[sh].price;var joined=addMonths(paidUntil,-(1+Math.floor(r()*5)));
      var st={id:uid(),name:pickName(),phone:phone(),parent:r()<.7?phone():"",exam:EXAMS[Math.floor(r()*8)],shift:sh,hall:seat.h,seat:seat.s,fee:fee,joined:joined,paidUntil:paidUntil,status:"active",created:joined};
      students.push(st);
      var pd=addMonths(paidUntil,-1);if(diff(pd,t)<=0)payments.push({id:uid(),sid:st.id,amount:fee,months:1,mode:r()<.7?"UPI":"Cash",date:pd});
    })});
  /* shuffle so the fee-state students are spread across the hall */
  for(var i=students.length-1;i>0;i--){var j=Math.floor(r()*(i+1));var a=students[i],b=students[j];var tmp={hall:a.hall,seat:a.seat,shift:a.shift,fee:a.fee};
    if(a.shift===b.shift){a.hall=b.hall;a.seat=b.seat;b.hall=tmp.hall;b.seat=tmp.seat}}
  for(var q=0;q<3;q++){var nm=pickName();students.push({id:uid(),name:nm,phone:phone(),parent:"",exam:"SSC",shift:"morning",hall:own.halls[0].id,seat:"",fee:700,joined:addMonths(t,-4),paidUntil:addDays(t,-40),status:"left",created:addMonths(t,-4),leftOn:addDays(t,-35)})}
  var freeM=allSeats.filter(function(x){return !students.some(function(s){return s.status==="active"&&s.seat===x.s&&(s.shift==="morning"||s.shift==="full")})});
  var freeE=allSeats.filter(function(x){return !students.some(function(s){return s.status==="active"&&s.seat===x.s&&(s.shift==="evening"||s.shift==="full")})});
  var freeF=allSeats.filter(function(x){return !students.some(function(s){return s.status==="active"&&s.seat===x.s})});
  var reqs=[
    {id:uid(),libId:"udaan",name:"Ishita Bansal",phone:"9784512360",parent:"9414451236",exam:"RAS",shift:"morning",hall:freeM[0].h,seat:freeM[0].s,start:addDays(t,1),note:"Need a seat near the window if possible.",status:"pending",created:addDays(t,0)},
    {id:uid(),libId:"udaan",name:"Kunal Mehra",phone:"9660345178",parent:"",exam:"UPSC",shift:"full",hall:freeF[0].h,seat:freeF[0].s,start:t,note:"",status:"pending",created:addDays(t,-1)},
    {id:uid(),libId:"udaan",name:"Farhan Qureshi",phone:"9829456712",parent:"9829456700",exam:"SSC",shift:"evening",hall:freeE[1].h,seat:freeE[1].s,start:addDays(t,2),note:"Coming after coaching, around 3 PM.",status:"pending",created:addDays(t,-1)}
  ];
  return {v:1,ownerLibId:"udaan",libs:[own].concat(others),students:students,payments:payments,requests:reqs,myRequests:[]};
}

/* ---------- model helpers ---------- */
function libById(id){if(!S||!S.libs)return null;for(var i=0;i<S.libs.length;i++)if(String(S.libs[i].id)===String(id))return S.libs[i];return S.libs[0]||null}
function ownLibs(){
  if(!S||!S.libs)return [];
  var u=window.__CURRENT_USER__;
  if(!u)return S.libs;
  if(u.role==='administrator'||u.role==='super_admin'||u.role==='admin')return S.libs;
  var my=S.libs.filter(function(l){return String(l.userId)===String(u.id)});
  return my.length?my:S.libs;
}
function own(){
  var my=ownLibs();
  if(!my.length)return null;
  if(S&&S.ownerLibId){for(var i=0;i<my.length;i++)if(String(my[i].id)===String(S.ownerLibId))return my[i]}
  return my[0];
}
function hallOf(L,id){if(!L||!L.halls)return null;for(var i=0;i<L.halls.length;i++)if(String(L.halls[i].id)===String(id))return L.halls[i];return L.halls[0]||null}
function seatsOf(h){if(!h||!h.rows)return [];var out=[];h.rows.forEach(function(row,ri){for(var i=1;i<=row.n;i++)out.push({id:row.id+":"+i,label:"R"+(ri+1)+"-"+i,ri:ri,i:i})});return out}
function seatLabel(L,hid,sid){var h=hallOf(L,hid);if(!h||!sid)return "No seat";var ss=seatsOf(h);for(var i=0;i<ss.length;i++)if(ss[i].id===sid)return (L.halls.length>1?h.name+" · ":"")+ss[i].label;return "No seat"}
function shortSeat(L,hid,sid){var h=hallOf(L,hid);if(!h||!sid)return "—";var ss=seatsOf(h);for(var i=0;i<ss.length;i++)if(ss[i].id===sid)return ss[i].label;return "—"}
function totalSeats(L){return (L&&L.halls?L.halls:[]).reduce(function(a,h){return a+seatsOf(h).length},0)}
function active(targetLib){
  var L=targetLib||own();
  var targetId=L?String(L.id):null;
  return (S&&S.students?S.students:[]).filter(function(s){return s.status!=="left"&&(!targetId||!s.libId||String(s.libId)===targetId)});
}
function occ(L,hid,sid,exceptId){
  var stus = active(L).filter(function(s){
    var matchLib = s.libId ? (String(s.libId) === String(L.id)) : (String(L.id) === String(S.ownerLibId));
    return matchLib && String(s.hall) === String(hid) && String(s.seat) === String(sid) && s.id !== exceptId;
  });
  if(stus.length > 0){
    return stus.map(function(s){ return {shift: s.shift, st: s}; });
  }
  var approved = (S.requests || []).filter(function(r){
    return r.status === "approved" && String(r.libId) === String(L.id) && String(r.hall) === String(hid) && String(r.seat) === String(sid);
  });
  if(approved.length > 0){
    return approved.map(function(r){ return {shift: r.shift}; });
  }
  return (L.occ||[]).filter(function(o){
    return String(o.h) === String(hid) && String(o.s) === String(sid);
  }).map(function(o){
    return {shift: o.shift};
  });
}
function seatFree(L,hid,sid,shift,exceptId){var o=occ(L,hid,sid,exceptId);if(shift==="full")return o.length===0;return !o.some(function(x){return x.shift===shift||x.shift==="full"})}
function freeCount(L,shift){var n=0;L.halls.forEach(function(h){seatsOf(h).forEach(function(s){if(seatFree(L,h.id,s.id,shift))n++})});return n}
function daysLeft(st){return diff(st.paidUntil,today())}
function dueStage(st){var d=daysLeft(st);if(d<0)return "over";if(d===0)return "today";if(d===1)return "half";if(d<=3)return "soon";return "ok"}
function pending(st){if(st.status==="left")return 0;var d=daysLeft(st);if(d>0)return 0;var cycles=d<0?Math.floor((-d)/30)+1:1;return st.fee*cycles}
function statusOf(st){if(st.status==="left")return {t:"Left",c:"left"};var d=daysLeft(st);
  if(d<0)return {t:"Overdue "+(-d)+(d===-1?" day":" days"),c:"over"};
  if(d===0)return {t:"Due today",c:"over"};
  if(d<=7)return {t:"Due in "+d+(d===1?" day":" days"),c:"soon"};
  return {t:"Paid till "+fmt(st.paidUntil),c:"ok"}}
function minPrice(L){return Math.min(L.shifts.morning.price,L.shifts.evening.price,L.shifts.full.price)}
function pendingReqs(){var L=own();if(!L)return [];return S.requests.filter(function(r){return r.status==="pending"&&String(r.libId)===String(L.id)})}
function collectedThisMonth(){
  var L=own(),t=today().slice(0,7),targetId=L?String(L.id):null;
  return S.payments.filter(function(p){
    if(p.date.slice(0,7)!==t)return false;
    if(!targetId)return true;
    var st=(S.students||[]).find(function(x){return String(x.id)===String(p.sid)});
    return !st||!st.libId||String(st.libId)===targetId;
  }).reduce(function(a,p){return a+p.amount},0);
}
function waLink(phone,text){return "https://wa.me/"+phoneIntl(phone)+"?text="+encodeURIComponent(text)}
function reminderText(st,toParent){var L=own();var first=st.name.split(" ")[0];var d=daysLeft(st);var amt=pending(st)||st.fee;
  var when=d<0?"was due on "+fmt(st.paidUntil):d===0?"is due today":"is due on "+fmt(st.paidUntil);
  var who=toParent?"Hello, this is "+L.name+". "+first+"'s library fee of "+rs(amt)+" "+when+".":"Hi "+first+", your library fee of "+rs(amt)+" at "+L.name+" "+when+".";
  return who+(L.upi?"\nPay by UPI to: "+L.upi+" (amount "+rs(amt)+")":"")+"\nPlease share the payment screenshot after paying. Thank you!"}

/* ---------- render root ---------- */
var $app=document.getElementById("app");
function render(){
  var html=topbar()+(storageOK?"":'<div class="demo-note">This browser is not saving changes right now (private window or blocked storage). Your edits will be lost when you close it.</div>');
  html+=ui.mode==="student"?studentSite():ownerPortal();
  $app.innerHTML=html;
  fitMaps();
  if(ui.sheet)renderSheet();
}
function topbar(){
  var myN = (S && S.myRequests && S.myRequests.length) ? S.myRequests.length : 0;
  var user = window.__CURRENT_USER__;
  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  var isOwner = !!(user && (user.is_owner || user.role === 'vendor' || user.role === 'administrator'));

  var authBar = '';
  if(user){
    if(isOwner){
      authBar = '<div style="display:flex;align-items:center;gap:6px">' +
        '<span class="pill brand" style="font-size:11px">👤 ' + esc(user.name) + ' (Owner)</span>' +
        '<form action="/logout" method="POST" style="display:inline;margin:0"><input type="hidden" name="_token" value="' + csrf + '"><button type="submit" class="btn sm ghost" style="padding:4px 8px;font-size:11px;color:var(--red)">Logout</button></form>' +
      '</div>';
    } else {
      authBar = '<div style="display:flex;align-items:center;gap:6px">' +
        '<a href="/my-bookings" class="pill brand" style="font-size:11px;text-decoration:none;display:inline-flex;align-items:center;gap:4px">🎓 ' + esc(user.name) + ' · Dashboard</a>' +
        '<form action="/logout" method="POST" style="display:inline;margin:0"><input type="hidden" name="_token" value="' + csrf + '"><button type="submit" class="btn sm ghost" style="padding:4px 8px;font-size:11px;color:var(--red)">Logout</button></form>' +
      '</div>';
    }
  } else {
    authBar = '<div style="display:flex;align-items:center;gap:6px">' +
      '<a href="/login" class="btn sm ghost" style="font-size:12px;padding:6px 10px">Login</a>' +
      '<a href="/register?role=customer" class="btn sm" style="font-size:12px;padding:6px 10px">Register</a>' +
      '<a href="/register?role=vendor" class="btn sm soft" style="font-size:12px;padding:6px 10px;font-weight:700">+ List Library</a>' +
    '</div>';
  }

  var segSwitcher = isOwner
    ? '<div class="seg" role="group" aria-label="Switch view"><button data-act="mode" data-v="student" aria-pressed="'+(ui.mode==="student")+'">Student</button><button data-act="mode" data-v="owner" aria-pressed="'+(ui.mode==="owner")+'">Owner</button></div>'
    : '';

  return '<header class="topbar"><div class="topin">'+
  '<button class="logo" data-act="goHome" aria-label="CoworkLibrary home"><span class="mk">'+I.building+'</span><span><b>Cowork<span>Library</span></b><small>'+(isOwner && ui.mode==="owner"?"Owner portal":"Study seats near you")+'</small></span></button>'+
  '<span class="grow"></span>'+
  segSwitcher+
  (isOwner && ui.mode==="owner"?'<button class="iconbtn libbtn" data-act="page" data-v="library" aria-label="My library">'+I.store+'</button>':'')+
  (ui.mode==="student"?'<a class="iconbtn" href="/my-bookings" aria-label="My seat requests">'+I.inbox+(myN?'<span class="dot">'+myN+'</span>':'')+'</a>':'')+
  authBar+
  '</div></header>';
}
/* ============ STUDENT SITE ============ */
function studentSite(){return ui.view==="detail"&&libById(ui.libId)?libDetail(libById(ui.libId)):studentHome()}
function cities(){var c={};S.libs.forEach(function(L){c[L.city]=1});return Object.keys(c).sort()}
function filteredLibs(){var q=ui.q.trim().toLowerCase();return S.libs.filter(function(L){
  if(ui.city&&L.city!==ui.city)return false;if(ui.acOnly&&L.facilities.indexOf("AC")<0)return false;
  if(!q)return true;return (L.name+" "+L.area+" "+L.city+" "+L.address).toLowerCase().indexOf(q)>=0})}
function studentHome(){
  var libs=S.libs,seats=libs.reduce(function(a,L){return a+totalSeats(L)},0),freeM=libs.reduce(function(a,L){return a+freeCount(L,"morning")},0),freeE=libs.reduce(function(a,L){return a+freeCount(L,"evening")},0);
  var feats=[
    [I.seats,"#EEEDFD","#4F46E5","Live seat map","See which seats are free in each shift before you visit."],
    [I.tag,"#E6F6EC","#1B6B3A","Fees shown upfront","Monthly price for full day, morning and evening shifts."],
    [I.phone,"#FFF4DC","#8A5A00","Owner calls you back","Your request goes straight to the library owner."],
    [I.shield,"#FDECEC","#B42318","No booking fee","You pay the library directly. We add nothing on top."]];
  return '<div class="wrap">'+
  '<section class="hero"><span class="eyebrow"><i></i>Seats updated live by owners</span>'+
  '<h1>Find a quiet study seat near you</h1>'+
  '<p class="sub">Compare self-study libraries by shift, price and facilities. Pick your seat on the map and send a request to the owner.</p>'+
  '<div class="search"><div class="sbox">'+I.search+'<input id="q" type="search" placeholder="Library name, area or city" value="'+esc(ui.q)+'" aria-label="Search libraries"></div>'+
  '<select id="city" aria-label="City"><option value="">All cities</option>'+cities().map(function(c){return '<option'+(ui.city===c?" selected":"")+'>'+esc(c)+'</option>'}).join("")+'</select></div>'+
  '<div class="stats"><div><b class="num">'+libs.length+'</b><span>Libraries</span></div><div><b class="num p">'+seats+'</b><span>Study seats</span></div><div><b class="num g">'+freeM+'</b><span>Free in morning</span></div><div><b class="num g">'+freeE+'</b><span>Free in evening</span></div></div>'+
  '</section>'+
  '<div class="features">'+feats.map(function(f){return '<div class="card feat"><span class="ic" style="background:'+f[1]+';color:'+f[2]+'">'+f[0]+'</span><div><b>'+f[3]+'</b><p>'+f[4]+'</p></div></div>'}).join("")+'</div>'+
  '<div class="listhead"><div><h2>Study libraries</h2><p class="muted small" id="libcount"></p></div><div class="chips"><button class="chip" data-act="acOnly" aria-pressed="'+ui.acOnly+'">AC only</button></div></div>'+
  '<div class="libgrid" id="libgrid">'+libCards()+'</div></div>';
}
function libCards(){
  var L=filteredLibs();
  setTimeout(function(){
    var c=document.getElementById("libcount");
    if(c)c.textContent=L.length+(L.length===1?" library":" libraries")+(ui.city?" in "+ui.city:"");
  },0);
  if(!S.libs.length){
    return '<div class="card empty" style="grid-column:1/-1;text-align:center;padding:48px 20px">'+
      '<div style="font-size:40px;margin-bottom:10px">📚</div>'+
      '<h3>No Study Libraries Listed Yet</h3>'+
      '<p class="muted" style="max-width:440px;margin:8px auto 16px auto">Are you a study library owner? Register your library now to start receiving student bookings and manage desks.</p>'+
      '<a href="/register?role=vendor" class="btn" style="display:inline-flex;align-items:center;gap:6px">'+I.plus+'List Your Library</a>'+
    '</div>';
  }
  if(!L.length)return '<div class="card empty" style="grid-column:1/-1">No libraries match. Try another area or clear the search.</div>';
  return L.map(libCard).join("")}
function libCard(L){
  var h=L.hue,ac=L.facilities.indexOf("AC")>=0;
  var art="";for(var i=0;i<18;i++)art+='<i class="'+((i*7+h)%5<3?"f":"")+'"></i>';
  return '<article class="card lib"><div class="ph" style="background:linear-gradient(135deg,hsl('+h+' 62% 46%),hsl('+(h+28)+' 58% 30%))">'+
  '<div class="tag"><span>'+(ac?"AC":"Non-AC")+'</span><span>'+esc(L.city)+'</span></div><div class="seatsart">'+art+'</div></div>'+
  '<div class="bd"><div><h3>'+esc(L.name)+'</h3><div class="addr" style="margin-top:4px">'+I.pin+'<span>'+esc(L.area)+', '+esc(L.city)+' · '+esc(L.open)+' – '+esc(L.close)+'</span></div></div>'+
  '<div class="shiftrow">'+SHIFT_KEYS.map(function(k){var f=freeCount(L,k);return '<div><span class="t"><i class="sdot '+k+'"></i>'+esc(L.shifts[k].label)+'</span><div class="p num">'+rs(L.shifts[k].price)+'</div><div class="f num'+(f?"":" zero")+'">'+(f?f+" free":"Full")+'</div></div>'}).join("")+'</div>'+
  '<div class="facs">'+L.facilities.filter(function(f){return f!=="AC"}).slice(0,4).map(function(f){return '<span>'+esc(f)+'</span>'}).join("")+'</div>'+
  '<div class="foot"><span class="muted small">Per month</span><button class="btn" data-act="openLib" data-v="'+L.id+'">View seats</button></div></div></article>';
}
function libDetail(L){
  if(!hallOf(L,ui.pubHall))ui.pubHall=L.halls[0].id;
  var h=hallOf(L,ui.pubHall);
  var ac=L.facilities.indexOf("AC")>=0;
  var halls=L.halls.length>1?'<div class="hallpick">'+L.halls.map(function(x){return '<button class="chip" data-act="pubHall" data-v="'+x.id+'" aria-pressed="'+(x.id===h.id)+'">'+esc(x.name)+(x.ac?" · AC":" · Non-AC")+'</button>'}).join("")+'</div>':'';
  var free=seatsOf(h).filter(function(s){return seatFree(L,h.id,s.id,ui.pubShift)}).length;
  return '<div class="wrap detail"><button class="back" data-act="backList">'+I.back+'All libraries</button>'+
  '<div class="dhead"><div style="min-width:0"><h1>'+esc(L.name)+'</h1><div class="dmeta"><span>'+I.pin+esc(L.address)+'</span><span>'+I.clock+esc(L.open)+' – '+esc(L.close)+'</span><span>'+I.phone+'<span class="num" style="user-select:all">'+esc(phoneNice(L.phone))+'</span></span></div></div>'+
  '<span class="pill '+(ac?"brand":"neutral")+'">'+(ac?"AC library":"Non-AC")+'</span></div>'+
  '<div class="dlayout"><div style="display:flex;flex-direction:column;gap:14px;min-width:0">'+
  halls+
  '<div class="tabs3" role="tablist" aria-label="Shift">'+SHIFT_KEYS.map(function(k){return '<button role="tab" data-act="pubShift" data-v="'+k+'" aria-selected="'+(ui.pubShift===k)+'"><i class="sdot '+k+'"></i>'+esc(L.shifts[k].label)+'</button>'}).join("")+'</div>'+
  '<section class="card mapcard"><div class="maphead"><span><b>'+esc(h.name)+'</b> <span class="muted small">'+esc(L.shifts[ui.pubShift].time)+'</span></span><span class="muted small num">'+free+' of '+seatsOf(h).length+' free</span></div>'+
  '<div class="front">Entrance · Front</div>'+seatMap(L,h,"pub")+
  '<div class="legend" style="margin-top:6px"><span><i class="lg"></i>Free</span><span><i class="lg" style="background:var(--brand);border-color:var(--brand)"></i>Your pick</span><span><i class="lg" style="background:repeating-linear-gradient(135deg,var(--c-taken) 0 3px,transparent 3px 6px) border-box,var(--c-taken-base);border-color:transparent"></i>Taken</span></div></section>'+
  '<section class="card panel"><h3>About</h3><p class="muted" style="margin-top:6px">'+esc(L.desc)+'</p><div class="facs" style="margin-top:12px">'+L.facilities.map(function(f){return '<span>'+esc(f)+'</span>'}).join("")+'</div></section>'+
  '</div><aside class="side">'+
  '<section class="card panel"><h3>Monthly fees</h3><table class="ptable"><tbody>'+SHIFT_KEYS.map(function(k){return '<tr><td><span style="display:inline-flex;align-items:center;gap:7px"><i class="sdot '+k+'"></i><b>'+esc(L.shifts[k].label)+'</b></span><div class="faint small">'+esc(L.shifts[k].time)+' · '+freeCount(L,k)+' free</div></td><td class="num">'+rs(L.shifts[k].price)+'</td></tr>'}).join("")+'</tbody></table><p class="faint small" style="margin-top:8px">Pay at the library when you join. No booking fee.</p></section>'+
  requestPanel(L)+
  '</aside></div></div>';
}
function requestPanel(L){
  if(ui.sent&&ui.sent.libId===L.id){
    var r=ui.sent;
    return '<section class="card panel" style="border-color:#B7E4C7">'+
    '<span class="pill ok">'+I.check.replace('<svg','<svg width="13" height="13"')+' Request sent</span>'+
    '<h3 style="margin-top:10px">'+esc(L.name)+' will review your request</h3>'+
    '<p class="muted" style="margin-top:6px">Your seat booking request for <b>'+esc(seatLabel(L,r.hall,r.seat))+'</b> ('+esc(L.shifts[r.shift].label)+' shift) from '+esc(fmt(r.start))+' has been submitted successfully.</p>'+
    '<div style="margin-top:14px;display:flex;flex-direction:column;gap:8px">'+
    '<a href="/my-bookings" class="btn block">📋 View on Student Dashboard &rarr;</a>'+
    (L.phone?'<a href="https://wa.me/'+phoneIntl(L.phone)+'?text='+encodeURIComponent('Hi, I have requested seat '+seatLabel(L,r.hall,r.seat)+' for '+L.shifts[r.shift].label+' shift at '+L.name)+'" target="_blank" class="btn wa block">'+I.wa+'Chat with Library Owner on WhatsApp</a>':'')+
    '<button class="btn ghost block" data-act="newReq">Request another seat</button>'+
    '</div></section>';
  }

  var seat=ui.pubSeat&&ui.pubSeat.lib===L.id?ui.pubSeat:null;
  if(!seat)return '<section class="card panel"><h3>Book a seat</h3><p class="muted" style="margin-top:6px">Pick a shift, then tap a free seat on the map.</p></section>';

  var user = window.__CURRENT_USER__;
  if(!user){
    return '<section class="card panel" id="reqpanel">'+
    '<h3>Request seat '+esc(seatLabel(L,seat.h,seat.s))+'</h3>'+
    '<p class="muted small" style="margin-top:4px">'+esc(L.shifts[ui.pubShift].label)+' · '+esc(L.shifts[ui.pubShift].time)+' · <b>'+rs(L.shifts[ui.pubShift].price)+'/month</b></p>'+
    '<div class="box" style="background:var(--brand-soft);border:1.5px solid var(--brand);padding:20px;border-radius:14px;text-align:center;margin-top:14px">'+
      '<div style="font-size:26px;margin-bottom:6px">🔐</div>'+
      '<h4 style="font-size:15px;font-weight:800;color:var(--brand-ink);margin-bottom:6px">Account Required to Request Seat</h4>'+
      '<p style="font-size:12.5px;color:var(--ink2);margin-bottom:14px;line-height:1.5">To request this seat, please register with Email OTP or log in. Your request will be tracked live on your student dashboard.</p>'+
      '<div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap">'+
        '<a href="/register?role=customer" class="btn sm" style="font-weight:700">Register with Email OTP &rarr;</a>'+
        '<a href="/login" class="btn sm ghost" style="font-weight:700">Log In</a>'+
      '</div>'+
    '</div></section>';
  }

  var d=ui.draft||{};
  if(!d.name && user) d.name = user.name;
  if(!d.phone && user && user.phone) d.phone = user.phone;

  return '<section class="card panel" id="reqpanel"><h3>Request seat '+esc(seatLabel(L,seat.h,seat.s))+'</h3><p class="muted small" style="margin-top:4px">'+esc(L.shifts[ui.pubShift].label)+' · '+esc(L.shifts[ui.pubShift].time)+' · <b>'+rs(L.shifts[ui.pubShift].price)+'/month</b></p>'+
  '<div class="pill ok" style="margin-top:8px">Logged in as: '+esc(user.name)+'</div>'+
  '<form id="reqform" style="display:flex;flex-direction:column;gap:11px;margin-top:12px" novalidate>'+
  '<div class="field"><label for="r_name">Your name</label><input class="in" id="r_name" autocomplete="name" value="'+esc(d.name||"")+'"></div>'+
  '<div class="grid2"><div class="field"><label for="r_phone">Mobile number</label><input class="in num" id="r_phone" inputmode="numeric" maxlength="10" value="'+esc(d.phone||"")+'"></div>'+
  '<div class="field"><label for="r_parent">Parent\'s mobile <span class="faint">(optional)</span></label><input class="in num" id="r_parent" inputmode="numeric" maxlength="10" value="'+esc(d.parent||"")+'"></div></div>'+
  '<div class="grid2"><div class="field"><label for="r_exam">Preparing for</label><select class="in" id="r_exam">'+EXAMS.map(function(e){return '<option'+(d.exam===e?" selected":"")+">"+e+"</option>"}).join("")+'</select></div>'+
  '<div class="field"><label for="r_start">Start date</label><input class="in" type="date" id="r_start" min="'+today()+'" value="'+esc(d.start||addDays(today(),1))+'"></div></div>'+
  '<div class="field"><label for="r_note">Note for the owner <span class="faint">(optional)</span></label><textarea class="in" id="r_note" placeholder="Arrival time, seat preference…">'+esc(d.note||"")+'</textarea></div>'+
  '<p class="err" id="r_err" hidden></p>'+
  '<button class="btn block" type="submit">Send seat request</button><p class="faint small" style="text-align:center">The owner will review and confirm your desk.</p></form></section>';
}
/* seat map (shared by student & owner) */
function seatMap(L,h,kind){
  var maxUnits=1;h.rows.forEach(function(r){var u=r.n+(r.aisle&&r.aisle<r.n?.7:0);if(u>maxUnits)maxUnits=u});
  var rows=h.rows.map(function(row,ri){var cells="";for(var i=1;i<=row.n;i++){cells+=kind==="pub"?pubSeat(L,h,row.id+":"+i,i):ownSeat(L,h,row.id+":"+i,i,"R"+(ri+1)+"-"+i);if(row.aisle&&i===row.aisle&&i<row.n)cells+='<span class="aisle"></span>'}
    return '<div class="row"><span class="rl">R'+(ri+1)+'</span>'+cells+'</div>'}).join("");
  if(!h.rows.length)rows='<p class="muted small" style="padding:10px">No rows in this hall yet.</p>';
  return '<div class="map" data-units="'+maxUnits+'" data-rows="'+h.rows.length+'"><div class="rows">'+rows+'</div></div>';
}
function pubSeat(L,h,sid,n){var free=seatFree(L,h.id,sid,ui.pubShift);var sel=ui.pubSeat&&ui.pubSeat.lib===L.id&&ui.pubSeat.s===sid&&ui.pubSeat.h===h.id;
  return '<button class="seat pub'+(free?"":" taken")+(sel?" sel":"")+'" '+(free?'data-act="pickSeat" data-h="'+h.id+'" data-s="'+sid+'"':'disabled')+' aria-label="Seat '+n+(free?" free":" taken")+'">'+n+'</button>'}
function ownSeat(L,h,sid,n,label){
  var o=occ(L,h.id,sid),tab=ui.seatTab,mine=null,cls="seat";
  if(tab==="full"){var f=o.filter(function(x){return x.shift==="full"})[0];mine=f?f.st:null;if(!mine&&o.length)cls+=" taken"}
  else{var m=o.filter(function(x){return x.shift===tab})[0];mine=m?m.st:null;if(!mine&&o.some(function(x){return x.shift==="full"}))cls+=" taken"}
  var badge="";
  if(mine){cls+=" filled on-"+mine.shift;var st=dueStage(mine);
    if(st==="soon")cls+=" d-soon";else if(st==="half")cls+=" d-half";else if(st==="today")cls+=" d-full";else if(st==="over"){cls+=" d-full";badge='<span class="bdg">'+(-daysLeft(mine))+'</span>'}}
  return '<button class="'+cls+'" data-act="ownSeat" data-h="'+h.id+'" data-s="'+sid+'" aria-label="Seat '+label+(mine?", "+esc(mine.name):"")+'">'+n+badge+'</button>'}
function fitMaps(){document.querySelectorAll(".map").forEach(function(m){var units=+m.getAttribute("data-units")||1;var w=m.clientWidth-28-33;var gap=5;var s=Math.floor((w-gap*(units-1))/units);s=Math.max(23,Math.min(40,s));m.style.setProperty("--cs",s+"px");m.style.setProperty("--fs",(s<28?9:10.5)+"px")})}

/* ============ OWNER PORTAL ============ */
var PAGES=[["dash","Dashboard",I.home],["seats","Seat map",I.seats],["students","Students",I.people],["fees","Fees",I.rupee],["requests","Requests",I.inbox],["library","My library",I.store]];
function ownerPortal(){
  var my=ownLibs(), L=own();
  if(!L){
    return '<div class="onboard-container">'+
      '<div class="onboard-card">'+
        '<div class="onboard-hero-bg"></div>'+
        '<div class="onboard-content">'+
          '<div class="onboard-pill"><span class="onboard-dot"></span> Owner Management Portal</div>'+
          '<h1 class="onboard-title">Welcome to CoworkLibrary!</h1>'+
          '<p class="onboard-subtitle">Empower your self-study library with visual seat mapping, shift timing controls, and instant online student admissions.</p>'+
          '<div class="onboard-grid">'+
            '<div class="onboard-tile">'+
              '<div class="onboard-tile-icon" style="background:#EEEDFD;color:#4F46E5">'+I.seats+'</div>'+
              '<div class="onboard-tile-info">'+
                '<h4>Visual Seat Layouts</h4>'+
                '<p>Configure desks, aisle gaps, and AC/Non-AC study halls.</p>'+
              '</div>'+
            '</div>'+
            '<div class="onboard-tile">'+
              '<div class="onboard-tile-icon" style="background:#FFF4DC;color:#8A5A00">'+I.clock+'</div>'+
              '<div class="onboard-tile-info">'+
                '<h4>Flexible Shift Slots</h4>'+
                '<p>Full-day, Morning, and Evening shifts with independent pricing.</p>'+
              '</div>'+
            '</div>'+
            '<div class="onboard-tile">'+
              '<div class="onboard-tile-icon" style="background:#E6F6EC;color:#1B6B3A">'+I.rupee+'</div>'+
              '<div class="onboard-tile-info">'+
                '<h4>Fees &amp; WhatsApp Alerts</h4>'+
                '<p>1-click WhatsApp alerts, UPI QR support, and overdue fee alerts.</p>'+
              '</div>'+
            '</div>'+
          '</div>'+
          '<div class="onboard-action-bar">'+
            '<button class="btn-primary-lg" data-act="addNewLib">'+
              I.plus+' Setup &amp; Add Your Study Library'+
            '</button>'+
            '<p class="onboard-footnote">Takes less than 1 minute · Includes standard 40-seat Main Hall ready to customize</p>'+
          '</div>'+
        '</div>'+
      '</div>'+
    '</div>';
  }

  var pr=pendingReqs().length;
  var libSelectHtml='';
  if(my.length>1){
    libSelectHtml='<div style="margin-top:8px"><label class="faint small" style="display:block;margin-bottom:4px">Switch Library:</label>'+
      '<select class="in sm" data-act="switchOwnLib" style="width:100%;font-size:12px;font-weight:600;padding:4px 8px">'+
        my.map(function(item){
          return '<option value="'+esc(item.id)+'"'+(String(item.id)===String(L.id)?' selected':'')+'>'+esc(item.name)+' ('+esc(item.area||item.city)+')</option>';
        }).join('')+
      '</select></div>';
  }

  var side='<nav class="sidebar" aria-label="Owner menu">'+
    '<div class="libname">'+
      '<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:6px">'+
        '<div><span class="label">Your library</span><b>'+esc(L.name)+'</b><span class="faint small">'+esc(L.area)+', '+esc(L.city)+'</span></div>'+
        '<button class="btn ghost sm" data-act="addNewLib" title="Add another property" style="padding:4px 6px;font-size:11px">'+I.plus+'New</button>'+
      '</div>'+
      libSelectHtml+
    '</div>'+
    PAGES.map(function(p){
      return '<button class="nav" data-act="page" data-v="'+p[0]+'"'+(ui.page===p[0]||(ui.page==="builder"&&p[0]==="library")?' aria-current="page"':'')+'>'+
        p[2]+p[1]+(p[0]==="requests"&&pr?'<span class="cnt">'+pr+'</span>':'')+
      '</button>';
    }).join("")+
  '</nav>';

  var bn='<nav class="bottomnav" aria-label="Owner menu">'+
    PAGES.filter(function(p){return p[0]!=="library"}).map(function(p){
      return '<button data-act="page" data-v="'+p[0]+'"'+(ui.page===p[0]?' aria-current="page"':'')+'>'+
        p[2]+p[1].replace("Dashboard","Home").replace("Seat map","Seats")+(p[0]==="requests"&&pr?'<span class="cnt">'+pr+'</span>':'')+
      '</button>';
    }).join("")+
  '</nav>';

  var body=({dash:pDash,seats:pSeats,students:pStudents,fees:pFees,requests:pRequests,library:pLibrary,builder:pBuilder})[ui.page]();
  return '<div class="portal">'+side+'<main class="main">'+body+'</main></div>'+bn;
}
function phead(t,sub,right){return '<div class="phead"><div><h1>'+t+'</h1>'+(sub?'<p>'+sub+'</p>':'')+'</div>'+(right||'')+'</div>'}
function avatar(s){return '<span class="av '+(s.status==="left"?"left":s.shift)+'">'+esc(initials(s.name))+'</span>'}
function remindBtn(s,label){var ph=s.phone;return '<a class="btn wa sm" href="'+esc(waLink(ph,reminderText(s,false)))+'" target="_blank" rel="noopener" data-act="reminded" data-v="'+s.id+'">'+I.wa+'<span class="txt">'+(label||"Remind")+'</span></a>'}

function pDash(){
  var L=own();
  if(!L) return phead("Dashboard")+'<div class="card empty"><p>No library found.</p><button class="btn" data-act="addNewLib">'+I.plus+'Create Library</button></div>';
  var a=active(L),n=totalSeats(L);
  var due=a.filter(function(s){return daysLeft(s)<=0}).sort(function(x,y){return daysLeft(x)-daysLeft(y)});
  var soon=a.filter(function(s){var d=daysLeft(s);return d>0&&d<=3}).sort(function(x,y){return daysLeft(x)-daysLeft(y)});
  var pendAmt=due.reduce(function(t,s){return t+pending(s)},0);
  var reqs=pendingReqs();
  var d=new Date();
  return phead("Good "+(d.getHours()<12?"morning":d.getHours()<17?"afternoon":"evening"),["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"][d.getDay()]+", "+d.getDate()+" "+MONF[d.getMonth()]+" · "+a.length+" active students",'<button class="btn" data-act="addStudent">'+I.plus+'Add student</button>')+
  '<div class="tiles">'+
  '<button class="card tile" data-act="page" data-v="seats"><span class="label">Free seats · out of '+n+'</span><div class="freemini">'+SHIFT_KEYS.map(function(k){return '<div><b class="num">'+freeCount(L,k)+'</b><span><i class="sdot '+k+'"></i>'+esc((L.shifts[k]||{}).label||k)+'</span></div>'}).join("")+'</div></button>'+
  '<button class="card tile" data-act="page" data-v="fees"><span class="label">Fees pending</span><span class="v red num">'+rs(pendAmt)+'</span><span class="s">'+due.length+' students due today or overdue</span></button>'+
  '<button class="card tile" data-act="page" data-v="fees"><span class="label">Collected in '+MONF[d.getMonth()]+'</span><span class="v num">'+rs(collectedThisMonth())+'</span><span class="s">'+soon.length+' more due in the next 3 days</span></button>'+
  '<button class="card tile" data-act="page" data-v="requests"><span class="label">New seat requests</span><span class="v num">'+reqs.length+'</span><span class="s">'+(reqs.length?"Waiting for your call":"No new requests")+'</span></button>'+
  '</div>'+
  '<div class="cols2"><section class="card sec"><div class="sech"><h3>Fees to collect</h3><button class="btn ghost sm" data-act="page" data-v="fees">See all</button></div>'+
  (due.length?due.slice(0,7).map(feeLine).join(""):'<div class="empty">Everyone has paid. Nice.</div>')+'</section>'+
  '<div style="display:flex;flex-direction:column;gap:16px;min-width:0"><section class="card sec"><div class="sech"><h3>Due in the next 3 days</h3></div>'+(soon.length?soon.slice(0,6).map(feeLine).join(""):'<div class="empty">Nothing due in the next 3 days.</div>')+'</section>'+
  '<section class="card sec"><div class="sech"><h3>New seat requests</h3><button class="btn ghost sm" data-act="page" data-v="requests">Open</button></div>'+(reqs.length?reqs.slice(0,3).map(function(r){return '<div class="li"><span class="av '+r.shift+'">'+esc(initials(r.name))+'</span><div class="who"><b>'+esc(r.name)+'</b><span>'+esc((L.shifts[r.shift]||{}).label||r.shift)+' · '+esc(seatLabel(L,r.hall,r.seat))+' · from '+esc(fmt(r.start))+'</span></div></div>'}).join(""):'<div class="empty">No new requests.</div>')+'</section></div></div>';
}
function feeLine(s){var st=statusOf(s),L=own();var amt=pending(s)||s.fee;
  return '<div class="li">'+avatar(s)+'<div class="who"><b>'+esc(s.name)+'</b><span>'+esc(shortSeat(L,s.hall,s.seat))+' · '+esc((L.shifts[s.shift]||{}).label||s.shift)+' · <span class="pill '+st.c+'">'+esc(st.t)+'</span></span></div><span class="amt num">'+rs(amt)+'</span><div class="acts">'+remindBtn(s)+'<button class="btn soft sm" data-act="pay" data-v="'+s.id+'">'+I.check+'<span class="txt">Paid</span></button></div></div>'}

function pSeats(){
  var L=own();
  if(!L) return phead("Seat map")+'<div class="card empty"><p>No library found.</p><button class="btn" data-act="addNewLib">'+I.plus+'Create Library</button></div>';
  if(!L.halls.length)return phead("Seat map")+'<div class="card empty">No halls yet. <button class="btn" data-act="page" data-v="library">Add a hall</button></div>';
  if(!hallOf(L,ui.hall))ui.hall=L.halls[0].id;var h=hallOf(L,ui.hall),a=active(L);
  var badge=function(k){return a.filter(function(s){return s.shift===k&&daysLeft(s)<=3}).length};
  var colVar={full:"var(--c-full)",morning:"var(--c-morning)",evening:"var(--c-evening)"}[ui.seatTab];
  var free=seatsOf(h).filter(function(s){return seatFree(L,h.id,s.id,ui.seatTab)}).length;
  return phead("Seat map","Tap a seat to see who sits there or to give it to a new student.",'<button class="btn ghost" data-act="editHall" data-v="'+h.id+'">'+I.edit+'Edit layout</button>')+
  '<div style="display:flex;flex-direction:column;gap:12px;max-width:820px">'+
  (L.halls.length>1?'<div class="hallpick">'+L.halls.map(function(x){return '<button class="chip" data-act="hall" data-v="'+x.id+'" aria-pressed="'+(x.id===h.id)+'">'+esc(x.name)+(x.ac?" · AC":" · Non-AC")+'</button>'}).join("")+'</div>':'')+
  '<div class="tabs3" role="tablist" aria-label="Shift">'+SHIFT_KEYS.map(function(k){var b=badge(k);return '<button role="tab" data-act="seatTab" data-v="'+k+'" aria-selected="'+(ui.seatTab===k)+'"><i class="sdot '+k+'"></i>'+esc((L.shifts[k]||{}).label||k)+(b?'<span class="tbadge">'+b+'</span>':'')+'</button>'}).join("")+'</div>'+
  '<section class="card mapcard"><div class="maphead"><span><b>'+esc(h.name)+'</b> <span class="muted small">'+(h.ac?"AC":"Non-AC")+' · '+esc((L.shifts[ui.seatTab]||{}).time||"")+'</span></span><span class="muted small num">'+free+' of '+seatsOf(h).length+' free</span></div><div class="front">Entrance · Front</div>'+seatMap(L,h,"own")+'</section>'+
  '<div class="legend"><span><i class="lg" style="background:'+colVar+';border-color:transparent"></i>'+esc((L.shifts[ui.seatTab]||{}).label||ui.seatTab)+'</span><span><i class="lg" style="background:repeating-linear-gradient(135deg,var(--c-taken) 0 3px,transparent 3px 6px) border-box,var(--c-taken-base);border-color:transparent"></i>Taken in other shift</span><span><i class="lg"></i>Free</span></div>'+
  '<div class="legend"><span><i class="lg" style="box-shadow:inset 0 0 0 2px var(--red);border-color:transparent"></i>Due in 2–3 days</span><span><i class="lg" style="background:linear-gradient(to top,var(--red) 50%,'+colVar+' 50%) border-box no-repeat;border-color:transparent"></i>Due tomorrow</span><span><i class="lg" style="background:var(--red);border-color:transparent"></i>Due today / overdue (number = days late)</span></div></div>';
}

function pStudents(){
  var L=own();
  if(!L) return phead("Students")+'<div class="card empty"><p>No library found.</p><button class="btn" data-act="addNewLib">'+I.plus+'Create Library</button></div>';
  return phead("Students",active(L).length+" active · "+S.students.filter(function(s){return (!s.libId||String(s.libId)===String(L.id))&&s.status==="left"}).length+" left",'<button class="btn" data-act="addStudent">'+I.plus+'Add student</button>')+
  '<div class="toolbar"><input class="in" id="stuQ" type="search" placeholder="Search name, phone or seat" value="'+esc(ui.stuQ)+'" aria-label="Search students"><div class="chips">'+
  [["all","All"],["soon","Due soon"],["over","Overdue"],["left","Left"]].map(function(f){return '<button class="chip" data-act="stuF" data-v="'+f[0]+'" aria-pressed="'+(ui.stuF===f[0])+'">'+f[1]+'</button>'}).join("")+'</div></div>'+
  '<section class="card sec" id="stulist">'+stuList()+'</section>';
}
function stuList(){var L=own(),q=ui.stuQ.trim().toLowerCase();
  if(!L) return '<div class="empty">No library found.</div>';
  var list=S.students.filter(function(s){
    if(s.libId && String(s.libId) !== String(L.id)) return false;
    if(ui.stuF==="left"){if(s.status!=="left")return false}else if(s.status==="left")return false;
    var d=daysLeft(s);if(ui.stuF==="soon"&&!(d>=0&&d<=7))return false;if(ui.stuF==="over"&&!(d<0))return false;
    if(!q)return true;return (s.name+" "+s.phone+" "+shortSeat(L,s.hall,s.seat)+" "+s.exam).toLowerCase().indexOf(q)>=0}).sort(function(a,b){return a.name.localeCompare(b.name)});
  if(!list.length)return '<div class="empty">No students here.</div>';
  return list.map(function(s){var st=statusOf(s);return '<button class="li" data-act="student" data-v="'+s.id+'">'+avatar(s)+'<div class="who"><b>'+esc(s.name)+'</b><span>'+esc(shortSeat(L,s.hall,s.seat))+' · '+esc((L.shifts[s.shift]||{}).label||s.shift)+' · '+esc(s.exam)+' · <span class="num">'+esc(phoneNice(s.phone))+'</span></span></div><span class="pill '+st.c+'">'+esc(st.t)+'</span></button>'}).join("")}

function pFees(){
  var L=own();
  if(!L) return phead("Fees")+'<div class="card empty"><p>No library found.</p><button class="btn" data-act="addNewLib">'+I.plus+'Create Library</button></div>';
  var a=active(L),d=new Date();
  var due=a.filter(function(s){return daysLeft(s)<=0}).sort(function(x,y){return daysLeft(x)-daysLeft(y)});
  var week=a.filter(function(s){var x=daysLeft(s);return x>0&&x<=7}).sort(function(x,y){return daysLeft(x)-daysLeft(y)});
  var pays=S.payments.slice().filter(function(p){
    var st=(S.students||[]).find(function(x){return String(x.id)===String(p.sid)});
    return !st || !st.libId || String(st.libId)===String(L.id);
  }).sort(function(x,y){return y.date<x.date?-1:y.date>x.date?1:0}).slice(0,12);
  var expected=a.reduce(function(t,s){return t+s.fee},0);
  return phead("Fees","Remind on WhatsApp, then mark paid when the money comes in.")+
  '<div class="tiles"><div class="card tile"><span class="label">Pending now</span><span class="v red num">'+rs(due.reduce(function(t,s){return t+pending(s)},0))+'</span><span class="s">'+due.length+' students</span></div>'+
  '<div class="card tile"><span class="label">Due this week</span><span class="v num">'+rs(week.reduce(function(t,s){return t+s.fee},0))+'</span><span class="s">'+week.length+' students</span></div>'+
  '<div class="card tile"><span class="label">Collected in '+MONF[d.getMonth()]+'</span><span class="v num">'+rs(collectedThisMonth())+'</span><span class="s">'+pays.filter(function(p){return p.date.slice(0,7)===today().slice(0,7)}).length+' payments</span></div>'+
  '<div class="card tile"><span class="label">Monthly fee roll</span><span class="v num">'+rs(expected)+'</span><span class="s">If every active student pays</span></div></div>'+
  '<div class="cols2"><section class="card sec"><div class="sech"><h3>Due today or overdue</h3><span class="pill over">'+due.length+'</span></div>'+(due.length?due.map(feeLine).join(""):'<div class="empty">Nothing pending.</div>')+'</section>'+
  '<div style="display:flex;flex-direction:column;gap:16px;min-width:0"><section class="card sec"><div class="sech"><h3>Due in the next 7 days</h3></div>'+(week.length?week.map(feeLine).join(""):'<div class="empty">Nothing due this week.</div>')+'</section>'+
  '<section class="card sec"><div class="sech"><h3>Recent payments</h3></div>'+(pays.length?pays.map(function(p){var s=S.students.filter(function(x){return x.id===p.sid})[0];return '<div class="li"><div class="who"><b>'+esc(s?s.name:"Student")+'</b><span>'+esc(fmt(p.date))+' · '+esc(p.mode)+' · '+p.months+(p.months>1?" months":" month")+'</span></div><span class="amt num" style="color:var(--green-ink)">+'+rs(p.amount)+'</span></div>'}).join(""):'<div class="empty">No payments yet.</div>')+'</section></div></div>';
}

function pRequests(){
  var L=own();
  if(!L) return phead("Seat requests")+'<div class="card empty"><p>No library found.</p><button class="btn" data-act="addNewLib">'+I.plus+'Create Library</button></div>';
  var all=S.requests.filter(function(r){return String(r.libId)===String(L.id)});
  var list=all.filter(function(r){return r.status===ui.reqF}).sort(function(a,b){return a.created<b.created?1:-1});
  var cnt=function(s){return all.filter(function(r){return r.status===s}).length};
  return phead("Seat requests","Students who asked for a seat on your listing. Call them, then approve.")+
  '<div class="toolbar"><div class="chips">'+[["pending","New"],["approved","Approved"],["rejected","Declined"]].map(function(f){return '<button class="chip" data-act="reqF" data-v="'+f[0]+'" aria-pressed="'+(ui.reqF===f[0])+'">'+f[1]+' · '+cnt(f[0])+'</button>'}).join("")+'</div></div>'+
  (list.length?'<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px">'+list.map(function(r){var ok=seatFree(L,r.hall,r.seat,r.shift);
    return '<article class="card panel" style="display:flex;flex-direction:column;gap:10px"><div style="display:flex;gap:10px;align-items:center"><span class="av '+r.shift+'">'+esc(initials(r.name))+'</span><div style="flex:1;min-width:0"><b>'+esc(r.name)+'</b><div class="muted small num" style="user-select:all">'+esc(phoneNice(r.phone))+'</div></div>'+(r.status==="pending"?'<span class="pill brand">New</span>':r.status==="approved"?'<span class="pill ok">Approved</span>':'<span class="pill neutral">Declined</span>')+'</div>'+
    '<dl class="kv"><dt>Seat</dt><dd>'+esc(seatLabel(L,r.hall,r.seat))+'</dd><dt>Shift</dt><dd>'+esc((L.shifts[r.shift]||{}).label||r.shift)+' · '+rs((L.shifts[r.shift]||{}).price||0)+'</dd><dt>Starts</dt><dd>'+esc(fmt(r.start))+'</dd><dt>Preparing for</dt><dd>'+esc(r.exam)+'</dd>'+(r.parent?'<dt>Parent</dt><dd class="num">'+esc(phoneNice(r.parent))+'</dd>':'')+'</dl>'+
    (r.note?'<p class="msg">'+esc(r.note)+'</p>':'')+
    (r.status==="pending"?(ok?'':'<p class="warn">This seat is now taken for this shift. Pick another seat while approving.</p>')+'<div style="display:flex;gap:8px;flex-wrap:wrap"><button class="btn" data-act="approve" data-v="'+r.id+'">'+I.check+'Approve</button><a class="btn wa" href="'+esc(waLink(r.phone,"Hi "+r.name.split(" ")[0]+", this is "+L.name+". We got your seat request for "+(L.shifts[r.shift]||{}).label.toLowerCase()+" shift. When can you visit?"))+'" target="_blank" rel="noopener">'+I.wa+'Message</a><button class="btn danger" data-act="reject" data-v="'+r.id+'">Decline</button></div>':'')+
    '</article>'}).join("")+'</div>':'<div class="card empty">'+(ui.reqF==="pending"?"No new requests. Students who book from your listing show up here.":"Nothing here yet.")+'</div>');
}

function pLibrary(){
  var L=own(), my=ownLibs();
  if(!L) return phead("My library")+'<div class="card empty"><p>No library found.</p><button class="btn" data-act="addNewLib">'+I.plus+'Setup Your Library</button></div>';

  var libSelect = '<div class="box" style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px;background:#fff;padding:12px 16px;border-radius:14px;border:1px solid var(--line);flex-wrap:wrap">'+
    '<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">'+
      '<b>Current Property:</b>'+
      (my.length > 1 ? '<select class="in" style="width:auto;padding:5px 10px;font-weight:700" data-act="switchOwnLib">'+
        my.map(function(l){return '<option value="'+l.id+'"'+(String(l.id)===String(L.id)?' selected':'')+'>'+esc(l.name)+' ('+esc(l.city||l.area)+')</option>'}).join('')+
      '</select>' : '<span><b>'+esc(L.name)+'</b> ('+esc(L.area||L.city)+')</span>')+
    '</div>'+
    '<div style="display:flex;gap:8px;align-items:center">'+
      '<button class="btn sm soft" data-act="addNewLib" style="font-weight:700">'+I.plus+'Add Another Property</button>'+
      (my.length > 1 ? '<button class="btn danger sm" data-act="deleteLib" data-v="'+L.id+'">'+I.trash+'Delete</button>' : '')+
    '</div>'+
  '</div>';

  return phead("My library","What students see on your listing.",'<button class="btn" data-act="saveLib">'+I.check+'Save changes</button>')+
  libSelect+
  '<div class="cols2" style="margin-top:0"><section class="card panel" style="display:flex;flex-direction:column;gap:12px"><h3>Details</h3>'+
  '<div class="field"><label for="l_name">Library name</label><input class="in" id="l_name" value="'+esc(L.name)+'"></div>'+
  '<div class="grid2"><div class="field"><label for="l_area">Area</label><input class="in" id="l_area" value="'+esc(L.area)+'"></div><div class="field"><label for="l_city">City</label><input class="in" id="l_city" value="'+esc(L.city)+'"></div></div>'+
  '<div class="field"><label for="l_addr">Full address</label><input class="in" id="l_addr" value="'+esc(L.address)+'"></div>'+
  '<div class="grid2"><div class="field"><label for="l_open">Opens at</label><input class="in" id="l_open" value="'+esc(L.open)+'"></div><div class="field"><label for="l_close">Closes at</label><input class="in" id="l_close" value="'+esc(L.close)+'"></div></div>'+
  '<div class="grid2"><div class="field"><label for="l_phone">Phone for students</label><input class="in num" id="l_phone" inputmode="numeric" value="'+esc(L.phone)+'"></div><div class="field"><label for="l_upi">UPI ID for fees</label><input class="in" id="l_upi" value="'+esc(L.upi)+'"><span class="hint">Added to WhatsApp fee reminders.</span></div></div>'+
  '<div class="field"><span class="flabel">Facilities</span><div class="chips">'+FACS.map(function(f,i){return '<label class="check"><input type="checkbox" data-fac="'+esc(f)+'"'+(L.facilities.indexOf(f)>=0?" checked":"")+'>'+esc(f)+'</label>'}).join("")+'</div></div>'+
  '<div class="field"><label for="l_desc">About your library</label><textarea class="in" id="l_desc">'+esc(L.desc)+'</textarea></div>'+
  '<p class="faint small">Photos come next. In this prototype the listing shows a colour card.</p></section>'+
  '<div style="display:flex;flex-direction:column;gap:16px;min-width:0"><section class="card panel"><h3>Shifts and monthly fees</h3><div style="display:flex;flex-direction:column;gap:10px;margin-top:12px">'+SHIFT_KEYS.map(function(k){var s=L.shifts[k]||{label:k,time:"",price:700};return '<div class="box" style="display:grid;grid-template-columns:auto 1fr 110px;gap:10px;align-items:center"><i class="sdot '+k+'" style="width:12px;height:12px"></i><div class="field"><label for="s_t_'+k+'">'+esc(s.label)+' timing</label><input class="in" id="s_t_'+k+'" value="'+esc(s.time)+'"></div><div class="field"><label for="s_p_'+k+'">Fee ₹</label><input class="in num" id="s_p_'+k+'" inputmode="numeric" value="'+s.price+'"></div></div>'}).join("")+'</div><p class="faint small" style="margin-top:8px">A full-day student blocks the seat for both shifts. Morning and evening students can share one seat.</p></section>'+
  '<section class="card sec"><div class="sech"><h3>Halls and seats</h3><button class="btn ghost sm" data-act="newHall">'+I.plus+'Add hall</button></div>'+
  L.halls.map(function(h){var n=seatsOf(h).length;return '<div class="li"><span class="av" style="background:var(--brand-soft);color:var(--brand-ink)">'+I.seats.replace('<svg','<svg width="17" height="17"')+'</span><div class="who"><b>'+esc(h.name)+'</b><span>'+(h.ac?"AC":"Non-AC")+' · '+h.rows.length+' rows · '+n+' seats</span></div><button class="btn ghost sm" data-act="editHall" data-v="'+h.id+'">'+I.edit+'Edit layout</button></div>'}).join("")+'</section>'+
  '<section class="card panel"><h3>Prototype data</h3><p class="muted small" style="margin-top:6px">Bring back the sample library, students and requests.</p><button class="btn danger" style="margin-top:10px" data-act="resetAsk">Reset sample data</button></section></div></div>';
}

/* builder */
function startBuilder(hid){var L=own(),h=hid?hallOf(L,hid):null;
  ui.builder=h?{id:h.id,name:h.name,ac:h.ac,rows:h.rows.map(function(r){return {id:r.id,n:r.n,aisle:r.aisle}})}:{id:null,name:"Hall "+(L.halls.length+1),ac:false,rows:[{id:uid(),n:10,aisle:5},{id:uid(),n:10,aisle:5},{id:uid(),n:10,aisle:5}]};
  ui.page="builder";render();window.scrollTo(0,0)}
function lockedMax(rowId){var b=ui.builder;if(!b.id)return 0;var m=0;active().forEach(function(s){if(s.hall===b.id&&s.seat.indexOf(rowId+":")===0){var i=+s.seat.split(":")[1];if(i>m)m=i}});return m}
function pBuilder(){var b=ui.builder,L=own();if(!b){ui.page="library";return pLibrary()}
  var total=b.rows.reduce(function(a,r){return a+r.n},0);
  var preview={id:b.id||"preview",name:b.name,ac:b.ac,rows:b.rows};
  return '<button class="back" data-act="page" data-v="library">'+I.back+'My library</button>'+
  phead(b.id?"Edit layout":"New hall",total+" seats in "+b.rows.length+" rows",'<div style="display:flex;gap:8px">'+(b.id&&L.halls.length>1?'<button class="btn danger" data-act="delHall">'+I.trash+'Delete hall</button>':'')+'<button class="btn" data-act="saveHall">'+I.check+'Save layout</button></div>')+
  '<div class="cols2" style="margin-top:0;grid-template-columns:minmax(0,1fr) minmax(0,1fr)"><div style="display:flex;flex-direction:column;gap:14px;min-width:0">'+
  '<section class="card panel"><div class="grid2"><div class="field"><label for="b_name">Hall name</label><input class="in" id="b_name" value="'+esc(b.name)+'"></div><div class="field"><span class="flabel">Cooling</span><label class="check" style="align-self:flex-start"><input type="checkbox" id="b_ac"'+(b.ac?" checked":"")+'> AC hall</label></div></div></section>'+
  '<section class="card sec"><div class="sech"><h3>Rows</h3><button class="btn ghost sm" data-act="addRow">'+I.plus+'Add row</button></div>'+
  b.rows.map(function(r,i){var lm=lockedMax(r.id);var opts='<option value="0">No gap</option>';for(var k=1;k<r.n;k++)opts+='<option value="'+k+'"'+(r.aisle===k?" selected":"")+'>Gap after seat '+k+'</option>';
    return '<div class="brow"><span class="rl">R'+(i+1)+'</span><div class="ctl"><span class="step"><button data-act="rowN" data-i="'+i+'" data-d="-1" aria-label="Fewer seats in row '+(i+1)+'">−</button><span class="num">'+r.n+'</span><button data-act="rowN" data-i="'+i+'" data-d="1" aria-label="More seats in row '+(i+1)+'">+</button></span><select data-row-aisle="'+i+'" aria-label="Walking gap in row '+(i+1)+'">'+opts+'</select>'+(lm?'<span class="faint small">'+lm+' in use</span>':'')+'</div>'+
    '<div class="mv"><button data-act="rowMv" data-i="'+i+'" data-d="-1"'+(i===0?" disabled":"")+' aria-label="Move row up">'+I.up+'</button><button data-act="rowMv" data-i="'+i+'" data-d="1"'+(i===b.rows.length-1?" disabled":"")+' aria-label="Move row down">'+I.down+'</button><button data-act="rowDel" data-i="'+i+'" aria-label="Delete row">'+I.trash+'</button></div></div>'}).join("")+'</section></div>'+
  '<section class="card mapcard" style="position:sticky;top:76px"><div class="maphead"><b>Preview</b><span class="muted small">How the map will look</span></div><div class="front">Entrance · Front</div>'+builderPreview(preview)+'</section></div>';
}
function builderPreview(h){var maxUnits=1;h.rows.forEach(function(r){var u=r.n+(r.aisle&&r.aisle<r.n?.7:0);if(u>maxUnits)maxUnits=u});
  return '<div class="map" data-units="'+maxUnits+'"><div class="rows">'+h.rows.map(function(row,ri){var c="";for(var i=1;i<=row.n;i++){c+='<span class="seat" style="cursor:default">'+i+'</span>';if(row.aisle&&i===row.aisle&&i<row.n)c+='<span class="aisle"></span>'}return '<div class="row"><span class="rl">R'+(ri+1)+'</span>'+c+'</div>'}).join("")+'</div></div>'}
function readBuilder(){var b=ui.builder;if(!b)return;var n=document.getElementById("b_name");if(n)b.name=n.value;var a=document.getElementById("b_ac");if(a)b.ac=a.checked;
  document.querySelectorAll("[data-row-aisle]").forEach(function(s){var i=+s.getAttribute("data-row-aisle");if(b.rows[i])b.rows[i].aisle=+s.value})}

/* ---------- sheets ---------- */
var $layer=null;
function openSheet(s){ui.sheet=s;renderSheet()}
function closeSheet(){ui.sheet=null;if($layer){$layer.remove();$layer=null}}
function renderSheet(){if(!$layer){$layer=document.createElement("div");$layer.className="layer";document.body.appendChild($layer)}
  var s=ui.sheet,inner=(s.t==="custom"?{title:s.title,body:s.body}:({seat:shSeat,student:shStudent,pay:shPay,form:shForm,approve:shApprove,myReqs:shMyReqs,reset:shReset})[s.t](s));
  if(!inner){closeSheet();return}
  $layer.innerHTML='<div class="backdrop" data-act="closeSheet"></div><div class="sheet" role="dialog" aria-modal="true" aria-label="'+esc(inner.title)+'"><div class="sh"><h3>'+inner.title+'</h3><button class="iconbtn" data-act="closeSheet" aria-label="Close">'+I.x+'</button></div><div class="sb">'+inner.body+'</div></div>'}
function stuById(id){return S.students.filter(function(x){return x.id===id})[0]}
function shSeat(s){var L=own(),h=hallOf(L,s.h);if(!h)return null;var label=shortSeat(L,s.h,s.s);var o=occ(L,s.h,s.s);
  var rows=SHIFT_KEYS.map(function(k){var who=o.filter(function(x){return x.shift===k})[0];var blocked=!who&&k!=="full"&&o.some(function(x){return x.shift==="full"});var blockedFull=!who&&k==="full"&&o.length;
    var right=who?'<button class="btn ghost sm" data-act="student" data-v="'+who.st.id+'">Open</button>':(blocked||blockedFull)?'<span class="faint small">Not free</span>':'<button class="btn soft sm" data-act="assign" data-shift="'+k+'" data-h="'+s.h+'" data-s="'+s.s+'">'+I.plus+'Give seat</button>';
    var line=who?'<b>'+esc(who.st.name)+'</b><span><span class="pill '+statusOf(who.st).c+'">'+esc(statusOf(who.st).t)+'</span></span>':'<b>'+esc(L.shifts[k].label)+'</b><span>'+(blocked||blockedFull?"Taken by a "+(blockedFull?"shift":"full-day")+" student":"Free · "+rs(L.shifts[k].price)+"/month")+'</span>';
    return '<div class="li" style="padding-inline:0">'+(who?avatar(who.st):'<span class="av '+k+'" style="opacity:.55">'+esc(L.shifts[k].label[0])+'</span>')+'<div class="who">'+line+'</div>'+right+'</div>'}).join("");
  return {title:"Seat "+esc(label)+' <span class="muted small">'+esc(h.name)+'</span>',body:'<div>'+rows+'</div>'}}
function shStudent(s){var st=stuById(s.id);if(!st)return null;var L=own(),stt=statusOf(st),pend=pending(st);
  var body='<div style="display:flex;gap:12px;align-items:center">'+avatar(st).replace('class="av','style="width:48px;height:48px;font-size:15px" class="av')+'<div style="min-width:0"><b style="font-size:16px">'+esc(st.name)+'</b><div><span class="pill '+stt.c+'">'+esc(stt.t)+'</span></div></div></div>'+
  (st.status!=="left"&&pend?'<div class="box" style="border-color:#F5C7C8;background:var(--red-soft)"><div style="display:flex;justify-content:space-between;align-items:center"><span><span class="label" style="color:#B42318">Pending</span><div class="amt num" style="font-size:20px">'+rs(pend)+'</div></span><span class="muted small">Was due '+esc(fmt(st.paidUntil))+'</span></div></div>':'')+
  '<dl class="kv"><dt>Seat</dt><dd>'+esc(seatLabel(L,st.hall,st.seat))+'</dd><dt>Shift</dt><dd>'+esc(L.shifts[st.shift].label)+' · '+esc(L.shifts[st.shift].time)+'</dd><dt>Monthly fee</dt><dd class="num">'+rs(st.fee)+'</dd><dt>Paid till</dt><dd>'+esc(fmt(st.paidUntil))+'</dd><dt>Joined</dt><dd>'+esc(fmt(st.joined))+'</dd><dt>Preparing for</dt><dd>'+esc(st.exam)+'</dd><dt>Mobile</dt><dd class="num" style="user-select:all">'+esc(phoneNice(st.phone))+'</dd>'+(st.parent?'<dt>Parent</dt><dd class="num" style="user-select:all">'+esc(phoneNice(st.parent))+'</dd>':'')+'</dl>';
  if(st.status!=="left"){
    body+='<div style="display:flex;gap:8px;flex-wrap:wrap"><button class="btn" data-act="pay" data-v="'+st.id+'">'+I.check+'Mark fee paid</button>'+remindBtn(st,"Remind student")+(st.parent?'<a class="btn wa" href="'+esc(waLink(st.parent,reminderText(st,true)))+'" target="_blank" rel="noopener">'+I.wa+'Remind parent</a>':'')+'</div>'+
    '<details class="box"><summary class="small" style="cursor:pointer;font-weight:700">Preview reminder message</summary><p class="msg" style="margin-top:8px">'+esc(reminderText(st,false))+'</p><button class="btn ghost sm" style="margin-top:8px" data-act="copyMsg" data-v="'+st.id+'">Copy message</button></details>'+
    '<div style="display:flex;gap:8px;flex-wrap:wrap;border-top:1px solid var(--line2);padding-top:14px"><button class="btn ghost" data-act="editStudent" data-v="'+st.id+'">'+I.edit+'Edit or move seat</button><button class="btn danger" data-act="leftAsk" data-v="'+st.id+'">Mark as left</button></div>'+
    (s.confirmLeft?'<div class="box" style="border-color:#F5C7C8"><p class="small"><b>Mark '+esc(st.name.split(" ")[0])+' as left?</b> The seat becomes free for the '+esc(L.shifts[st.shift].label.toLowerCase())+' shift. Their record stays in Students → Left.</p><div style="display:flex;gap:8px;margin-top:10px"><button class="btn danger sm" data-act="leftYes" data-v="'+st.id+'">Yes, mark as left</button><button class="btn ghost sm" data-act="student" data-v="'+st.id+'">Cancel</button></div></div>':'');
  } else body+='<p class="muted small">Left on '+esc(fmt(st.leftOn||today()))+'.</p><button class="btn soft" data-act="rejoin" data-v="'+st.id+'">Add back as a student</button>';
  return {title:"Student",body:body}}
function shPay(s){var st=stuById(s.id);if(!st)return null;var m=s.months||1,mode=s.mode||"UPI";
  var from=daysLeft(st)<0?st.paidUntil:st.paidUntil;var until=addMonths(from,m);
  return {title:"Mark fee paid",body:'<p><b>'+esc(st.name)+'</b> · '+rs(st.fee)+'/month</p>'+
  '<div class="field"><span class="flabel">How many months?</span><div class="optrow">'+[1,2,3,6].map(function(n){return '<button class="chip" data-act="payM" data-v="'+n+'" aria-pressed="'+(m===n)+'">'+n+(n>1?" months":" month")+'</button>'}).join("")+'</div></div>'+
  '<div class="field"><span class="flabel">Paid by</span><div class="optrow">'+["UPI","Cash"].map(function(x){return '<button class="chip" data-act="payMode" data-v="'+x+'" aria-pressed="'+(mode===x)+'">'+x+'</button>'}).join("")+'</div></div>'+
  '<div class="box"><dl class="kv"><dt>Amount</dt><dd class="num" style="font-size:18px">'+rs(st.fee*m)+'</dd><dt>Paid till</dt><dd>'+esc(fmt(from))+' → '+esc(fmt(until))+'</dd></dl></div>'+
  '<button class="btn block" data-act="payYes" data-v="'+st.id+'">'+I.check+'Save payment of '+rs(st.fee*m)+'</button>'}}
function seatOptions(shift,cur,exceptId){var L=own(),o="";L.halls.forEach(function(h){seatsOf(h).forEach(function(seat){var v=h.id+"|"+seat.id;if(seatFree(L,h.id,seat.id,shift,exceptId)||v===cur)o+='<option value="'+v+'"'+(v===cur?" selected":"")+'>'+esc((L.halls.length>1?h.name+" · ":"")+seat.label)+'</option>'})});return o}
function shForm(s){var d=s.d,L=own(),edit=!!s.id;var cur=d.hall&&d.seat?d.hall+"|"+d.seat:"";var opts=seatOptions(d.shift,cur,s.id);
  return {title:edit?"Edit student":"Add student",body:'<form id="stuform" style="display:flex;flex-direction:column;gap:12px" novalidate>'+
  '<div class="field"><label for="f_name">Name</label><input class="in" id="f_name" value="'+esc(d.name)+'" autocomplete="off"></div>'+
  '<div class="grid2"><div class="field"><label for="f_phone">Mobile</label><input class="in num" id="f_phone" inputmode="numeric" maxlength="10" value="'+esc(d.phone)+'"></div><div class="field"><label for="f_parent">Parent\'s mobile <span class="faint">(optional)</span></label><input class="in num" id="f_parent" inputmode="numeric" maxlength="10" value="'+esc(d.parent)+'"></div></div>'+
  '<div class="field"><span class="flabel">Shift</span><div class="tabs3">'+SHIFT_KEYS.map(function(k){return '<button type="button" data-act="formShift" data-v="'+k+'" aria-selected="'+(d.shift===k)+'"><i class="sdot '+k+'"></i>'+esc(L.shifts[k].label)+'</button>'}).join("")+'</div></div>'+
  '<div class="grid2"><div class="field"><label for="f_seat">Seat</label><select class="in" id="f_seat">'+(opts||'<option value="">No free seat in this shift</option>')+'</select><span class="hint">Only seats free in this shift are listed.</span></div><div class="field"><label for="f_exam">Preparing for</label><select class="in" id="f_exam">'+EXAMS.map(function(e){return '<option'+(d.exam===e?" selected":"")+'>'+e+'</option>'}).join("")+'</select></div></div>'+
  '<div class="grid2"><div class="field"><label for="f_fee">Monthly fee ₹</label><input class="in num" id="f_fee" inputmode="numeric" value="'+esc(d.fee)+'"></div><div class="field"><label for="f_joined">Joining date</label><input class="in" type="date" id="f_joined" value="'+esc(d.joined)+'"></div></div>'+
  '<div class="field"><label for="f_paid">Fee paid till</label><input class="in" type="date" id="f_paid" value="'+esc(d.paidUntil)+'"><span class="hint">Set this to the joining date if they have not paid yet.</span></div>'+
  '<p class="err" id="f_err" hidden></p><button class="btn block" type="submit">'+(edit?"Save changes":"Add student")+'</button></form>'}}
function newForm(shift,hall,seat){var L=own(),t=today();shift=shift||"morning";return {name:"",phone:"",parent:"",exam:"RAS",shift:shift,hall:hall||"",seat:seat||"",fee:L.shifts[shift].price,joined:t,paidUntil:addMonths(t,1)}}
function shApprove(s){var r=S.requests.filter(function(x){return x.id===s.id})[0];if(!r)return null;var L=own();var cur=r.hall+"|"+r.seat;var free=seatFree(L,r.hall,r.seat,r.shift);var opts=seatOptions(r.shift,free?cur:"");var paid=s.paid===true;
  return {title:"Approve "+esc(r.name.split(" ")[0]),body:'<div class="box" style="background:#E6F6EC;border:1px solid #B7E4C7;border-radius:10px;padding:10px 12px;margin-bottom:12px;color:#1B6B3A;font-size:12px;display:flex;align-items:flex-start;gap:8px">'+
  '<span style="font-size:16px;line-height:1">⚡</span><div><b>Instant Seat Booking:</b> Approving will immediately book and lock this seat for <b>'+esc(r.name)+'</b> in the <b>'+esc(L.shifts[r.shift].label)+'</b> shift. Fee payment can be marked now or collected later at the library.</div></div>'+
  '<div class="field"><label for="a_seat">Seat for '+esc(L.shifts[r.shift].label.toLowerCase())+' shift</label><select class="in" id="a_seat">'+(opts||'<option value="">No free seat in this shift</option>')+'</select></div>'+
  '<div class="grid2"><div class="field"><label for="a_fee">Monthly fee ₹</label><input class="in num" id="a_fee" inputmode="numeric" value="'+L.shifts[r.shift].price+'"></div><div class="field"><label for="a_start">Starts</label><input class="in" type="date" id="a_start" value="'+esc(r.start)+'"></div></div>'+
  '<div class="field"><span class="flabel">First month\'s fee</span><div class="optrow"><button class="chip" data-act="apPaid" data-v="1" aria-pressed="'+paid+'">Already Collected</button><button class="chip" data-act="apPaid" data-v="0" aria-pressed="'+(!paid)+'">Not yet (Pay at library)</button></div><span class="hint">'+(paid?"Saved as a payment. Next due one month after start.":"Seat is still officially booked and blocked immediately.")+'</span></div>'+
  '<p class="err" id="a_err" hidden></p><button class="btn block" data-act="approveYes" data-v="'+r.id+'" style="background:#10B981;border-color:#059669;font-weight:700">'+I.check+'Approve & Book Seat Immediately</button>'}}
function shMyReqs(){var list=S.myRequests.map(function(id){return S.requests.filter(function(r){return r.id===id})[0]}).filter(Boolean).reverse();
  return {title:"My seat requests",body:list.length?list.map(function(r){var L=libById(r.libId);return '<div class="box"><div style="display:flex;justify-content:space-between;gap:8px;align-items:center"><b>'+esc(L?L.name:"Library")+'</b>'+(r.status==="pending"?'<span class="pill soon">Waiting for owner</span>':r.status==="approved"?'<span class="pill ok">Seat confirmed</span>':'<span class="pill neutral">Declined</span>')+'</div><p class="muted small" style="margin-top:4px">'+esc(L?seatLabel(L,r.hall,r.seat):"")+' · '+esc(L?L.shifts[r.shift].label:"")+' · from '+esc(fmt(r.start))+'</p></div>'}).join("")+(list.some(function(r){return r.libId===S.ownerLibId&&r.status==="pending"})?'<p class="faint small">Switch to Owner at the top to approve requests sent to Udaan Study Library.</p>':''):'<div class="empty">You have not requested a seat yet. Open a library and tap a free seat.</div>'}}
function shReset(){return {title:"Reset sample data?",body:'<p>This removes every change you made here and brings back the sample libraries, students and requests.</p><div style="display:flex;gap:8px"><button class="btn danger" data-act="resetYes">Reset</button><button class="btn ghost" data-act="closeSheet">Cancel</button></div>'}}

/* ---------- actions ---------- */
var tt;function toast(m){var el=document.querySelector(".toast");if(!el){el=document.createElement("div");el.className="toast";el.setAttribute("role","status");document.body.appendChild(el)}el.textContent=m;el.hidden=false;clearTimeout(tt);tt=setTimeout(function(){el.hidden=true},2400)}
function commit(msg){save();render();if(msg)toast(msg)}
function readReqDraft(){var g=function(id){var e=document.getElementById(id);return e?e.value:""};ui.draft={name:g("r_name"),phone:g("r_phone"),parent:g("r_parent"),exam:g("r_exam"),start:g("r_start"),note:g("r_note")}}

document.addEventListener("click",function(e){
  var el=e.target.closest("[data-act]");if(!el)return;
  var act=el.getAttribute("data-act"),v=el.getAttribute("data-v");
  if(el.tagName==="A"){ if(act==="reminded")toast("Opening WhatsApp…");return }
  e.preventDefault();
  switch(act){
  case "mode":(function(){
    var user = window.__CURRENT_USER__;
    var isOwner = !!(user && (user.is_owner || user.role === 'vendor' || user.role === 'administrator'));
    if(v === "owner" && !isOwner){
      window.location.href = "/login?role=owner";
      return;
    }
    ui.mode=v;closeSheet();render();window.scrollTo(0,0);
  })();break;
  case "goHome":if(ui.mode==="student"){ui.view="list";ui.sent=null}else ui.page="dash";closeSheet();render();window.scrollTo(0,0);break;
  case "acOnly":ui.acOnly=!ui.acOnly;render();break;
  case "openLib":ui.view="detail";ui.libId=v;ui.pubHall=null;ui.pubSeat=null;ui.sent=null;render();window.scrollTo(0,0);break;
  case "backList":ui.view="list";ui.sent=null;render();window.scrollTo(0,0);break;
  case "pubHall":readReqDraft();ui.pubHall=v;render();break;
  case "pubShift":readReqDraft();ui.pubShift=v;if(ui.pubSeat&&!seatFree(libById(ui.pubSeat.lib),ui.pubSeat.h,ui.pubSeat.s,v))ui.pubSeat=null;ui.sent=null;render();break;
  case "pickSeat":readReqDraft();ui.pubSeat={lib:ui.libId,h:el.getAttribute("data-h"),s:el.getAttribute("data-s")};ui.sent=null;render();
    var p=document.getElementById("reqpanel");if(p&&window.innerWidth<=980)p.scrollIntoView({behavior:"smooth",block:"start"});break;
  case "newReq":ui.sent=null;ui.pubSeat=null;render();break;
  case "myReqs":openSheet({t:"myReqs"});break;
  case "closeSheet":closeSheet();break;
  case "page":ui.page=v;closeSheet();render();window.scrollTo(0,0);break;
  case "hall":ui.hall=v;render();break;
  case "seatTab":ui.seatTab=v;render();break;
  case "ownSeat":openSheet({t:"seat",h:el.getAttribute("data-h"),s:el.getAttribute("data-s")});break;
  case "student":openSheet({t:"student",id:v});break;
  case "pay":openSheet({t:"pay",id:v,months:1,mode:"UPI"});break;
  case "payM":ui.sheet.months=+v;renderSheet();break;
  case "payMode":ui.sheet.mode=v;renderSheet();break;
  case "payYes":(function(){var st=stuById(v),m=ui.sheet.months||1;var from=st.paidUntil;st.paidUntil=addMonths(from,m);S.payments.push({id:uid(),sid:st.id,amount:st.fee*m,months:m,mode:ui.sheet.mode||"UPI",date:today()});
    syncBackend("save_payment", {sid:st.id, months:m, mode:ui.sheet.mode||"UPI", amount:st.fee*m});
    closeSheet();commit(rs(st.fee*m)+" saved for "+st.name.split(" ")[0]+". Paid till "+fmt(st.paidUntil)+".")})();break;
  case "copyMsg":(function(){var st=stuById(v),txt=reminderText(st,false);try{navigator.clipboard.writeText(txt).then(function(){toast("Message copied")},function(){toast("Couldn’t copy. Select the message and copy it.")})}catch(err){toast("Couldn’t copy. Select the message and copy it.")}})();break;
  case "addStudent":openSheet({t:"form",d:newForm()});break;
  case "assign":openSheet({t:"form",d:newForm(el.getAttribute("data-shift"),el.getAttribute("data-h"),el.getAttribute("data-s"))});break;
  case "editStudent":(function(){var st=stuById(v);openSheet({t:"form",id:st.id,d:{name:st.name,phone:st.phone,parent:st.parent,exam:st.exam,shift:st.shift,hall:st.hall,seat:st.seat,fee:st.fee,joined:st.joined,paidUntil:st.paidUntil}})})();break;
  case "formShift":readStuForm();var d=ui.sheet.d;if(d.shift!==v){var L=own();if(+d.fee===L.shifts[d.shift].price)d.fee=L.shifts[v].price;d.shift=v;if(d.hall&&d.seat&&!seatFree(L,d.hall,d.seat,v,ui.sheet.id)){d.hall="";d.seat=""}}renderSheet();break;
  case "leftAsk":ui.sheet.confirmLeft=true;renderSheet();break;
  case "leftYes":(function(){var st=stuById(v);st.status="left";st.leftOn=today();syncBackend("vacate_student", {id:st.id});closeSheet();commit(st.name.split(" ")[0]+" marked as left. Seat is free now.")})();break;
  case "rejoin":(function(){var st=stuById(v);openSheet({t:"form",d:Object.assign(newForm(st.shift),{name:st.name,phone:st.phone,parent:st.parent,exam:st.exam}),rejoin:st.id})})();break;
  case "stuF":ui.stuF=v;render();break;
  case "reqF":ui.reqF=v;render();break;
  case "approve":openSheet({t:"approve",id:v,paid:true});break;
  case "apPaid":ui.sheet.paid=v==="1";var keep={seat:(document.getElementById("a_seat")||{}).value,fee:(document.getElementById("a_fee")||{}).value,start:(document.getElementById("a_start")||{}).value};renderSheet();if(keep.seat)document.getElementById("a_seat").value=keep.seat;if(keep.fee)document.getElementById("a_fee").value=keep.fee;if(keep.start)document.getElementById("a_start").value=keep.start;break;
  case "approveYes":approveReq(v);break;
  case "reject":(function(){var r=S.requests.filter(function(x){return x.id===v})[0];if(r)r.status="rejected";syncBackend("reject_booking", {id:v});commit("Request declined")})();break;
  case "saveLib":saveLib();break;
  case "addNewLib":openNewLibModal();break;
  case "deleteLib":(function(){
    var my=ownLibs(), L=own();
    if(!L||my.length<=1){toast("You must have at least one library");return}
    if(confirm("Are you sure you want to delete '"+L.name+"'? All associated halls, seats and records will be deleted.")){
      syncBackend("delete_library",{id:L.id});
      commit("Deleting library...");
    }
  })();break;
  case "newHall":startBuilder(null);break;
  case "editHall":startBuilder(v);break;
  case "addRow":readBuilder();var b=ui.builder,last=b.rows[b.rows.length-1];b.rows.push({id:uid(),n:last?last.n:10,aisle:last?last.aisle:0});render();break;
  case "rowN":(function(){readBuilder();var i=+el.getAttribute("data-i"),dd=+el.getAttribute("data-d"),r=ui.builder.rows[i];var nn=r.n+dd;var lm=lockedMax(r.id);if(nn<1)return;if(nn>30){toast("A row can have up to 30 seats");return}if(nn<lm){toast("Seat "+lm+" in this row has a student. Move them first.");return}r.n=nn;if(r.aisle>=nn)r.aisle=0;render()})();break;
  case "rowMv":(function(){readBuilder();var i=+el.getAttribute("data-i"),dd=+el.getAttribute("data-d"),rows=ui.builder.rows,j=i+dd;if(j<0||j>=rows.length)return;var t=rows[i];rows[i]=rows[j];rows[j]=t;render()})();break;
  case "rowDel":(function(){readBuilder();var i=+el.getAttribute("data-i"),r=ui.builder.rows[i];if(lockedMax(r.id)){toast("Students sit in this row. Move them before deleting it.");return}if(ui.builder.rows.length===1){toast("A hall needs at least one row");return}ui.builder.rows.splice(i,1);render()})();break;
  case "saveHall":saveHall();break;
  case "delHall":(function(){var b=ui.builder;if(active().some(function(s){return s.hall===b.id})){toast("Students sit in this hall. Move them before deleting it.");return}var L=own();L.halls=L.halls.filter(function(h){return h.id!==b.id});ui.builder=null;ui.page="library";commit("Hall deleted")})();break;
  case "resetAsk":openSheet({t:"reset"});break;
  case "resetYes":S=seed();ui.page="dash";ui.view="list";ui.sent=null;ui.pubSeat=null;closeSheet();commit("Sample data restored");break;
  }
});
document.addEventListener("input",function(e){
  if(e.target.id==="q"){ui.q=e.target.value;var g=document.getElementById("libgrid");if(g)g.innerHTML=libCards()}
  if(e.target.id==="stuQ"){ui.stuQ=e.target.value;var l=document.getElementById("stulist");if(l)l.innerHTML=stuList()}
});
document.addEventListener("change",function(e){
  if(e.target.id==="city"){ui.city=e.target.value;var g=document.getElementById("libgrid");if(g)g.innerHTML=libCards()}
  if(e.target.getAttribute("data-act")==="switchOwnLib"){ S.ownerLibId = String(e.target.value); save(); render(); commit("Switched to " + (own()?esc(own().name):"library")); }
  if(e.target.hasAttribute&&e.target.hasAttribute("data-row-aisle")){readBuilder();render()}
  if(e.target.id==="b_ac"||e.target.id==="b_name")readBuilder();
});
document.addEventListener("submit",function(e){
  e.preventDefault();
  if(e.target.id==="reqform")sendRequest();
  if(e.target.id==="stuform")saveStudent();
  if(e.target.id==="newlibform")saveNewLibModal();
});
document.addEventListener("keydown",function(e){if(e.key==="Escape"&&ui.sheet)closeSheet()});
window.addEventListener("resize",function(){clearTimeout(window.__rz);window.__rz=setTimeout(fitMaps,120)});

function openNewLibModal(){
  var user = window.__CURRENT_USER__;
  var userPhone = user ? (user.phone || "") : "";
  var modalHtml = '<form id="newlibform" style="display:flex;flex-direction:column;gap:14px" novalidate>' +
    '<div style="background:var(--brand-soft);border:1px solid #D5D2FA;border-radius:10px;padding:11px 14px;color:var(--brand-ink);font-size:12.5px;display:flex;align-items:flex-start;gap:8px">' +
      '<span style="font-size:16px;line-height:1">✨</span>' +
      '<div><b>Quick Setup:</b> Standard AC Main Hall with 40 seats (Rows A–D) will be created automatically. You can customize halls and seats anytime!</div>' +
    '</div>' +
    '<div class="field">' +
      '<label for="nl_name">Library Name *</label>' +
      '<input class="in" id="nl_name" placeholder="e.g. Udaan Study Library - Main Branch" required autocomplete="off">' +
    '</div>' +
    '<div class="grid2">' +
      '<div class="field">' +
        '<label for="nl_area">Area / Colony *</label>' +
        '<input class="in" id="nl_area" placeholder="e.g. Gopalpura Bypass" required>' +
      '</div>' +
      '<div class="field">' +
        '<label for="nl_city">City</label>' +
        '<input class="in" id="nl_city" value="Jaipur">' +
      '</div>' +
    '</div>' +
    '<div class="field">' +
      '<label for="nl_addr">Full Address <span class="faint">(optional)</span></label>' +
      '<input class="in" id="nl_addr" placeholder="Plot No., Near Metro Pillar, Coaching Hub...">' +
    '</div>' +
    '<div class="grid2">' +
      '<div class="field">' +
        '<label for="nl_phone">Owner / Reception Mobile *</label>' +
        '<input class="in num" id="nl_phone" inputmode="numeric" maxlength="10" placeholder="10-digit mobile" value="' + esc(userPhone) + '" required>' +
      '</div>' +
      '<div class="field">' +
        '<label for="nl_upi">UPI ID for Fees <span class="faint">(optional)</span></label>' +
        '<input class="in" id="nl_upi" placeholder="e.g. library@okhdfcbank">' +
      '</div>' +
    '</div>' +
    '<div class="grid2">' +
      '<div class="field">' +
        '<label for="nl_open">Opening Time</label>' +
        '<input class="in" id="nl_open" value="6:00 AM">' +
      '</div>' +
      '<div class="field">' +
        '<label for="nl_close">Closing Time</label>' +
        '<input class="in" id="nl_close" value="10:00 PM">' +
      '</div>' +
    '</div>' +
    '<button class="btn block" type="submit" style="padding:12px;font-size:15px;margin-top:6px;font-weight:700">' +
      I.plus + 'Create &amp; Setup Library Branch' +
    '</button>' +
  '</form>';
  openSheet({t:"custom", title:"Add Study Library Branch", body:modalHtml});
}

function saveNewLibModal(){
  var name = (document.getElementById("nl_name") || {}).value || "";
  var area = (document.getElementById("nl_area") || {}).value || "";
  var city = (document.getElementById("nl_city") || {}).value || "Jaipur";
  var addr = (document.getElementById("nl_addr") || {}).value || "";
  var phone = (document.getElementById("nl_phone") || {}).value || "";
  var upi = (document.getElementById("nl_upi") || {}).value || "";
  var open = (document.getElementById("nl_open") || {}).value || "6:00 AM";
  var close = (document.getElementById("nl_close") || {}).value || "10:00 PM";

  if(!name.trim()){ toast("Please enter library name"); return; }
  if(!area.trim()){ toast("Please enter area / locality"); return; }
  if(!phone.trim()){ toast("Please enter owner phone"); return; }

  syncBackend("save_library", {
    id: "new",
    name: name.trim(),
    area: area.trim(),
    city: city.trim(),
    address: addr.trim(),
    phone: phone.trim(),
    upi: upi.trim(),
    open: open.trim(),
    close: close.trim(),
    facilities: ["AC","Wi-Fi","Locker","RO water","CCTV","Power backup"],
    shifts: shiftsDefault(1200, 700, 700),
    desc: "Silent self-study library with AC halls and dedicated desks."
  });
  closeSheet();
  toast("Creating library branch...");
}

function sendRequest(){readReqDraft();var d=ui.draft,err=document.getElementById("r_err"),seat=ui.pubSeat,L=libById(ui.libId);
  function bad(m){err.textContent=m;err.hidden=false}
  if(!d.name.trim())return bad("Enter your name.");
  if(!validPhone(d.phone))return bad("Enter a 10-digit mobile number.");
  if(d.parent&&!validPhone(d.parent))return bad("Parent's number should be 10 digits, or leave it empty.");
  if(!d.start||d.start<today())return bad("Pick a start date from today onwards.");
  if(!seatFree(L,seat.h,seat.s,ui.pubShift))return bad("This seat was just taken. Pick another one.");
  var r={id:uid(),libId:L.id,name:d.name.trim(),phone:d.phone.replace(/\D/g,"").slice(-10),parent:(d.parent||"").replace(/\D/g,"").slice(-10),exam:d.exam,shift:ui.pubShift,hall:seat.h,seat:seat.s,start:d.start,note:d.note.trim(),status:"pending",created:today()};
  S.requests.push(r);S.myRequests.push(r.id);ui.sent=r;ui.pubSeat=null;ui.draft={name:d.name,phone:d.phone,parent:d.parent,exam:d.exam};
  syncBackend("create_booking", r);
  if(L.id!==S.ownerLibId){/* other sample libraries have no owner in this prototype */}
  commit("Seat request sent");
}
function readStuForm(){var d=ui.sheet&&ui.sheet.d;if(!d)return;var g=function(id){var e=document.getElementById(id);return e?e.value:null};
  ["name","phone","parent","exam","fee","joined"].forEach(function(k){var x=g("f_"+k);if(x!==null)d[k]=x});var p=g("f_paid");if(p!==null)d.paidUntil=p;
  var seat=g("f_seat");if(seat){var sp=seat.split("|");d.hall=sp[0];d.seat=sp[1]}}
function saveStudent(){readStuForm();var s=ui.sheet,d=s.d,err=document.getElementById("f_err"),L=own();
  function bad(m){err.textContent=m;err.hidden=false}
  if(!d.name.trim())return bad("Enter the student's name.");
  if(!validPhone(d.phone))return bad("Enter a 10-digit mobile number.");
  if(d.parent&&!validPhone(d.parent))return bad("Parent's number should be 10 digits, or leave it empty.");
  if(!d.hall||!d.seat)return bad("No free seat in this shift. Pick another shift or free a seat.");
  if(!seatFree(L,d.hall,d.seat,d.shift,s.id))return bad("That seat is taken in this shift.");
  var fee=parseInt(String(d.fee).replace(/\D/g,""),10);if(!(fee>0))return bad("Enter the monthly fee.");
  if(!d.joined||!d.paidUntil)return bad("Fill both dates.");
  var rec={name:d.name.trim(),phone:d.phone.replace(/\D/g,"").slice(-10),parent:(d.parent||"").replace(/\D/g,"").slice(-10),exam:d.exam,shift:d.shift,hall:d.hall,seat:d.seat,fee:fee,joined:d.joined,paidUntil:d.paidUntil};
  if(s.id){Object.assign(stuById(s.id),rec);syncBackend("save_student", Object.assign({id:s.id, property_id:L.id}, rec));closeSheet();commit("Saved")}
  else{if(s.rejoin){var old=stuById(s.rejoin);if(old)S.students=S.students.filter(function(x){return x.id!==old.id})}
    var st=Object.assign({id:uid(),libId:L.id,status:"active",created:today()},rec);S.students.push(st);
    syncBackend("save_student", Object.assign({property_id:L.id}, rec));
    if(diff(d.paidUntil,d.joined)>0){var months=Math.max(1,Math.round(diff(d.paidUntil,d.joined)/30));S.payments.push({id:uid(),sid:st.id,amount:fee*months,months:months,mode:"Cash",date:d.joined<=today()?d.joined:today()})}
    closeSheet();commit(st.name.split(" ")[0]+" added to "+shortSeat(L,st.hall,st.seat))}}
function approveReq(id){var r=S.requests.filter(function(x){return x.id===id})[0],L=own(),err=document.getElementById("a_err");
  var seat=(document.getElementById("a_seat")||{}).value,fee=parseInt(((document.getElementById("a_fee")||{}).value||"").replace(/\D/g,""),10),start=(document.getElementById("a_start")||{}).value;
  function bad(m){err.textContent=m;err.hidden=false}
  if(!seat)return bad("No free seat in this shift. Free a seat first.");if(!(fee>0))return bad("Enter the monthly fee.");if(!start)return bad("Pick a start date.");
  var sp=seat.split("|");if(!seatFree(L,sp[0],sp[1],r.shift))return bad("That seat is taken in this shift.");
  var paid=ui.sheet.paid===true;
  var st={id:uid(),libId:L.id,name:r.name,phone:r.phone,parent:r.parent,exam:r.exam,shift:r.shift,hall:sp[0],seat:sp[1],fee:fee,joined:start,paidUntil:paid?addMonths(start,1):start,status:"active",created:today()};
  S.students.push(st);if(paid)S.payments.push({id:uid(),sid:st.id,amount:fee,months:1,mode:"Cash",date:today()});
  r.status="approved";r.hall=sp[0];r.seat=sp[1];
  syncBackend("approve_booking", {id:id, seat:sp[1], hall:sp[0], fee:fee, start:start, paid:paid});
  closeSheet();commit("✅ Seat "+shortSeat(L,st.hall,st.seat)+" is now BOOKED for "+r.name.split(" ")[0]+"!")}
function saveLib(){var L=own(),g=function(id){var e=document.getElementById(id);return e?e.value.trim():""};
  if(!g("l_name")){toast("Library name can't be empty");return}
  L.name=g("l_name");L.area=g("l_area");L.city=g("l_city")||L.city;L.address=g("l_addr");L.open=g("l_open");L.close=g("l_close");L.phone=g("l_phone");L.upi=g("l_upi");L.desc=g("l_desc");
  L.facilities=[];document.querySelectorAll("[data-fac]").forEach(function(c){if(c.checked)L.facilities.push(c.getAttribute("data-fac"))});
  SHIFT_KEYS.forEach(function(k){var t=g("s_t_"+k),p=parseInt(g("s_p_"+k).replace(/\D/g,""),10);if(t)L.shifts[k].time=t;if(p>0)L.shifts[k].price=p});
  syncBackend("save_library", L);
  commit("Listing saved")}
function saveHall(){readBuilder();var b=ui.builder,L=own();if(!b.name.trim()){toast("Give the hall a name");return}
  var rows=b.rows.map(function(r){return {id:r.id,n:r.n,aisle:r.aisle&&r.aisle<r.n?r.aisle:0}});
  if(b.id){var h=hallOf(L,b.id);h.name=b.name.trim();h.ac=b.ac;h.rows=rows;ui.hall=h.id}else{var nh={id:uid(),name:b.name.trim(),ac:b.ac,rows:rows};L.halls.push(nh);ui.hall=nh.id}
  syncBackend("save_hall", {property_id:L.id, id:b.id, name:b.name.trim(), ac:b.ac, rows:rows});
  ui.builder=null;ui.page="seats";commit("Layout saved")}

/* ---------- boot ---------- */
S=load();
var user = window.__CURRENT_USER__;
var isOwner = !!(user && (user.is_owner || user.role === 'vendor' || user.role === 'administrator'));
if(!isOwner){
  ui.mode = "student";
  if(location.hash && (location.hash === "#owner" || location.hash === "#dash" || location.hash === "#seats" || location.hash === "#students" || location.hash === "#fees" || location.hash === "#reqs" || location.hash === "#library")){
    try { history.replaceState(null, "", window.location.pathname); } catch(e){}
  }
} else {
  if(location.hash && location.hash.length > 1){
    var h = location.hash.slice(1);
    if(h === "owner" || h === "dash"){ ui.mode = "owner"; ui.page = "dash"; }
    else if(h === "seats" || h === "students" || h === "fees" || h === "reqs" || h === "library"){ ui.mode = "owner"; ui.page = h; }
  }
  if(window.__MODE__ === "owner"){ ui.mode = "owner"; }
}
render();
})();
