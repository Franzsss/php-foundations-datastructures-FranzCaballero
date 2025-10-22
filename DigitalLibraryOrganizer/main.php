<?php

require_once __DIR__ . '/recursion.php';
require_once __DIR__ . '/hashtable.php';
require_once __DIR__ . '/bst.php';


$bst = new BST();
if (isset($bookInfo) && is_array($bookInfo)) {
    foreach (array_keys($bookInfo) as $title) {
        $bst->insert($title);
    }
} else {

    die("Error: hashtable.php failed to load the book info array.");
}

if (!isset($library) || !isset($bookInfo)) {
    die("Error: Critical data (library or book info) not loaded.");
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Digital Library Organizer</title>
<style>
    :root{
        --bg: black;
        --panel: midnightblue;
        --muted: lightgray;
        --accent: turquoise;
        --accent-2: deepskyblue;
        --card-border: rgba(255,255,255,0.1);
        --text: white;
    }
    *{box-sizing:border-box}
    html,body{
        height:100%; margin:0;
        font-family: Arial, Helvetica, sans-serif;
        background: linear-gradient(180deg, black, navy);
        color: var(--text);
    }
    .info-box {
        background-color: black;
        color: white;
        padding: 10px 15px;
        border-radius: 10px;
        width: fit-content;
        margin: 20px auto;
        font-size: 0.9em;
        text-align: center;
        box-shadow: 0 0 10px gray;
    }
    .wrap{max-width:1100px; margin:20px auto; padding:18px; display:grid; gap:14px}
    header{display:flex; align-items:center; gap:12px}
    h1{margin:0; font-size:1.1rem}
    .sub{color:var(--muted); font-size:0.9rem}
    .layout{display:grid; grid-template-columns:1fr 420px; gap:16px}
    @media (max-width:980px){ .layout{grid-template-columns:1fr} }
    .leftcol{display:flex; flex-direction:column; gap:12px}
    .panel{
        background-color: var(--panel);
        border-radius:12px;
        padding:12px;
        border:1px solid var(--card-border);
    }
    .searchrow{display:flex; gap:8px; align-items:center}
    .search{
        flex:1;
        padding:10px 12px;
        border-radius:10px;
        background: transparent;
        border:1px solid gray;
        color: var(--text);
        outline:none;
    }
    .hint{
        background: linear-gradient(90deg, var(--accent-2), var(--accent));
        padding:8px 10px;
        border-radius:9px;
        color: black;
        font-weight:700;
        font-size:0.85rem;
    }
    .categories{max-height:56vh; overflow:auto; padding-right:6px}
    .maincat, .subtitle, .bookitem{
        border-radius:10px;
        cursor:pointer;
        user-select:none;
        padding: 8px 10px;
        margin-bottom: 4px;
    }
    .maincat{font-weight: bold;}
    .subtitle{padding-left: 15px;}
    .bookitem{padding-left: 30px;}
    .maincat:hover, .subtitle:hover, .bookitem:hover{
        background-color: darkslategray;
    }
    .sublist{padding-left:10px; margin-top:6px; display:none}
    .bookitem{padding:6px 10px; margin-left:8px}
    .centerpanel{
        min-height:120px; max-height:60vh;
        overflow:auto; display:none; margin-top:4px;
    }
    .titleitem{
        padding:10px; border-radius:10px;
        margin-bottom:8px; cursor:pointer;
        background-color: darkslateblue;
    }
    .titleitem:hover{
        background-color: steelblue;
    }
    .notfound{
        padding:18px; border-radius:10px;
        background-color: darkred;
        color: white; font-weight:700;
    }
    .modal-backdrop{
        position:fixed; inset:0;
        background-color: rgba(0,0,0,0.8);
        display:none; align-items:center;
        justify-content:center; z-index:80; padding:20px;
    }
    .modal{
        width:100%; max-width:680px;
        background-color: midnightblue;
        border-radius:14px; padding:18px;
        border:1px solid slategray;
    }
    .modal h2{margin:0 0 8px 0}
    .modal .meta{color:var(--muted); margin-bottom:12px}
    .chip{
        padding:8px 10px;
        border-radius:10px;
        background-color: darkslategray;
        cursor:pointer;
        margin-right: 5px;
        margin-bottom: 5px;
    }
    .chip:hover{background-color: teal;}
    .closebtn{
        background: transparent;
        color: var(--muted);
        border: 1px solid gray;
        padding:8px 10px;
        border-radius:8px;
        cursor:pointer;
    }
    footer{color:var(--muted); font-size:0.9rem; margin-top:6px}
</style>
</head>
<body>
<div class="wrap">
    <header>
        <div>
            <h1>Digital Library Organizer</h1>
            <div class="sub">Created by Franz</div>
        </div>
    </header>

    <div class="layout">
        <div class="leftcol">
            <div class="panel">
                <div class="searchrow">
                    <input id="searchInput" class="search" placeholder="Search titles..." />
                    <div class="hint">Search</div>
                </div>

                <div id="categoriesWrap" class="categories"></div>

                <div id="centerPanel" class="centerpanel panel">
                    <div id="centerHeading" style="margin:0 0 8px 0; font-weight:700">Alphabetical Titles (BST)</div>
                    <div id="centerList"></div>
                </div>
            </div>

            <footer class="info-box">Click a book in the category list or the search results to view details.</footer>

        </div>
        <div></div>
    </div>
</div>

<div id="modalBackdrop" class="modal-backdrop">
    <div class="modal">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 id="modalTitle">Title</h2>
                <div id="modalMeta" class="meta">Author • Year • Genre</div>
            </div>
            <button id="closeModal" class="closebtn">Close</button>
        </div>
        <div style="margin-top:8px">
            <div style="font-weight:600; margin-bottom:6px">Other books in same subcategory</div>
            <div id="modalOther" style="display:flex; flex-wrap:wrap; gap:8px;"></div>
        </div>
    </div>
</div>

<footer style="text-align: center; ">
    <p>©This project demonstrates PHP and JavaScript Data Structures Integration (Recursion, Hash Table, BST).</p>
</footer>

<script>

const LIBRARY = <?php echo json_encode($library, JSON_UNESCAPED_UNICODE); ?>;
const BOOK_INFO = <?php echo json_encode($bookInfo, JSON_UNESCAPED_UNICODE); ?>;
const ALL_TITLES = Object.keys(BOOK_INFO);

class BSTNode { constructor(data){ this.data=data; this.left=null; this.right=null; } }
class BST_JS {
    constructor(){ this.root=null; }
    insert(data){ this.root=this._insert(this.root,data); }
    _insert(node,data){
        if(!node) return new BSTNode(data);
        if(data.localeCompare(node.data)<0) node.left=this._insert(node.left,data);
        else if(data.localeCompare(node.data)>0) node.right=this._insert(node.right,data);
        return node;
    }
    inorder(){ const r=[]; this._inorder(this.root,r); return r; }
    _inorder(n,r){ if(!n)return; this._inorder(n.left,r); r.push(n.data); this._inorder(n.right,r); }
}
const bst=new BST_JS(); ALL_TITLES.forEach(t=>bst.insert(t));

const categoriesWrap=document.getElementById('categoriesWrap');
const centerPanel=document.getElementById('centerPanel');
const centerList=document.getElementById('centerList');
const searchInput=document.getElementById('searchInput');
const modalBackdrop=document.getElementById('modalBackdrop');
const modalTitle=document.getElementById('modalTitle');
const modalMeta=document.getElementById('modalMeta');
const modalOther=document.getElementById('modalOther');
const closeModal=document.getElementById('closeModal');

function renderCategories(){
    categoriesWrap.innerHTML='';
    for(const main in LIBRARY){
        const mainDiv=document.createElement('div');
        const mainHeader=document.createElement('div');
        mainHeader.className='maincat';
        mainHeader.textContent='📁 '+main;
        const subList=document.createElement('div');
        subList.className='sublist';
        for(const sub in LIBRARY[main]){
            const subDiv=document.createElement('div');
            const subHeader=document.createElement('div');
            subHeader.className='subtitle';
            subHeader.textContent='📂 '+sub;
            const bookList=document.createElement('div');
            bookList.style.display='none';
            LIBRARY[main][sub].forEach(book=>{
                const b=document.createElement('div');
                b.className='bookitem';
                b.textContent='📘 '+book;
                b.onclick=()=>openModal(book);
                bookList.appendChild(b);
            });
            subHeader.onclick=()=>bookList.style.display=bookList.style.display==='none'?'block':'none';
            subDiv.appendChild(subHeader);
            subDiv.appendChild(bookList);
            subList.appendChild(subDiv);
        }
        mainHeader.onclick=()=>subList.style.display=subList.style.display==='none'?'block':'none';
        subList.style.display='none';
        mainDiv.appendChild(mainHeader);
        mainDiv.appendChild(subList);
        categoriesWrap.appendChild(mainDiv);
    }
}

function openModal(title){
    
    const info=BOOK_INFO[title];
    modalTitle.textContent=title;
    modalMeta.textContent=info?`${info.author} • ${info.year} • ${info.genre}`:'Book not found';
    modalOther.innerHTML='';

    const loc=findBook(title);
    if(loc.main&&loc.sub){
        const others=LIBRARY[loc.main][loc.sub].filter(b=>b!==title);
        if(others.length) others.forEach(o=>{
            const c=document.createElement('div');
            c.className='chip';
            c.textContent=o;
            c.onclick=()=>openModal(o);
            modalOther.appendChild(c);
        });
        else modalOther.textContent='No other books in this subcategory.';
    } else {
        modalOther.textContent='Subcategory not found.';
    }
    modalBackdrop.style.display='flex';
}
closeModal.onclick=()=>modalBackdrop.style.display='none';
modalBackdrop.onclick=e=>{if(e.target===modalBackdrop)modalBackdrop.style.display='none';};

function findBook(title){
    for(const main in LIBRARY)
        for(const sub in LIBRARY[main])
            if(LIBRARY[main][sub].includes(title))
                return {main,sub};
    return {};
}

function renderList(list){
    centerList.innerHTML='';
    if(!list.length){
        const nf=document.createElement('div');
        nf.className='notfound';
        nf.textContent='No titles matched your search.';
        centerList.appendChild(nf);
        return;
    }
    
    list.forEach(t=>{
        const el=document.createElement('div');
        el.className='titleitem';
        el.textContent=t;
        el.onclick=()=>openModal(t);
        centerList.appendChild(el);
    });
}

searchInput.addEventListener('focus',()=>{
    categoriesWrap.style.display='none';
    centerPanel.style.display='block';
    renderList(bst.inorder()); 
});

searchInput.addEventListener('input',()=>{
    const q=searchInput.value.trim();
    if(!q){
        renderList(bst.inorder());
        return;
    }
  
    const matches=ALL_TITLES.filter(t=>t.toLowerCase().includes(q.toLowerCase()));
    renderList(matches);
});

searchInput.addEventListener('blur',()=>{
    
    setTimeout(()=>{
        if(!searchInput.value.trim()){
            categoriesWrap.style.display='block';
            centerPanel.style.display='none';
        }
    },150);
});


renderCategories();
centerPanel.style.display='none';
</script>
</body>
</html>
