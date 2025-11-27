var carroatual = 0;

function backcar(){
    carroatual ++;
    if(carroatual>3){
        carroatual=0
    }
    passbar()
    setcar()
    document.getElementById("car").innerHTML = carroatual;
}
function passcar(){
    carroatual--;
    if(carroatual<0){
        carroatual=3
    }
    
    backbar()
    setcar()
    document.getElementById("car").innerHTML = carroatual;
}

function sleep(milliseconds) {
  const date = Date.now();
  let currentDate = null;
  do {
    currentDate = Date.now();
  } while (currentDate - date < milliseconds);
}


var id = null;
function backbar() {
    
var e1 = document.getElementById("img1");
  var e2 = document.getElementById("img2");
  
  var e3 = document.getElementById("img3");
  
  var pos = -700;
  var scl1 = 0.5;
  var scl2 = 1.55;
  clearInterval(id);
  id = setInterval(frame, 2);
  function frame() {
    if (pos == 0){
    clearInterval(id);
    } else {
      pos=pos+4;
      scl1 += 0.0032;
      scl2 -= 0.0032;
    e1.style.left = pos + 'px';
    e2.style.left = pos + 'px';
    e2.style.scale = scl1;
    e3.style.left = pos + 'px';
    e3.style.scale = scl2;
    }

  }
}
  
  
function passbar() {
    
  var e1 = document.getElementById("img1");
  var e2 = document.getElementById("img2");
  
  var e3 = document.getElementById("img3");
  
  var pos = 700;
  var scl1 = 1.55;
  var scl2 = 0.5;
  clearInterval(id);
  id = setInterval(frame, 2);
  function frame() {
    if (pos == 0){
    clearInterval(id);
    } else {
      pos=pos-4;
      scl1 -= 0.0032;
      scl2 += 0.0032;
    e1.style.left = pos + 'px';
    e2.style.left = pos + 'px';
    e1.style.scale = scl1;
    e3.style.left = pos + 'px';
    e2.style.scale = scl2;
    }
  
 
}}



function setcar(){
    var car1;
    var car2;
    var car3;
    if(carroatual == 0){
        car1 = "url(carro1.png)"
        car2 = "url(carro2.png)"
        car3 = "url(carro3.png)"
    }
     if(carroatual == 1){
        car1 = "url(carro2.png)"
        car2 = "url(carro3.png)"
        car3 = "url(carro4.webp)"
    }
     if(carroatual == 2){
        car1 = "url(carro3.png)"
        car2 = "url(carro4.webp)"
        car3 = "url(carro1.png)"
    }
    if(carroatual == 3){
        car1 = "url(carro4.webp)"
        car2 = "url(carro1.png)"
        car3 = "url(carro2.png)"
    }

    
    document.getElementById("img1").style.backgroundImage = car1;
    document.getElementById("img2").style.backgroundImage = car2;
    document.getElementById("img3").style.backgroundImage = car3;

    
    document.getElementById("barpass").style.backgroundImage = car1
    document.getElementById("barback").style.backgroundImage = car3
}

function changecar(carro){
  var carroimg = document.getElementById("carroshow")
  carroimg.style.backgroundImage =  carro
}

function pesquisa(){
  var nameshow = document.getElementById("name")
  var carrostring =  document.getElementById("carpesquisa").value
  var carropesquisado = carrostring.toLowerCase()

  nameshow.innerHTML = carropesquisado
  if (carropesquisado == "lamborgini"){
    changecar("url(carro1.png)")
  }
}
function found(){
  var lupa = document.getElementById("lupa")
  var carrostring =  document.getElementById("carpesquisa").value
  var carropesquisado = carrostring.toLowerCase()
  if (carropesquisado == "lamborgini"){
    lupa.classList.toggle("giralupa")
  }
}
var linguagem = 0
function changelg(){
  const lg = document.getElementById("linguagemslct")
  const l1 = document.querySelector(".lnk1")
  const l2 = document.querySelectorAll(".lnk2")
  const l3 = document.querySelector(".lnk3")
  const l4 = document.querySelector(".lnk4")
  const lgText = document.getElementById("lgn")
  
  if(linguagem == 0){
    lg.style.backgroundImage = "url(Flag_of_the_United_States.svg.png)"
    linguagem = 1;
  }else if (linguagem == 1){
    lg.style.backgroundImage = "url(Flag_of_Brazil.svg.webp)"
    linguagem = 0;
  }

  if(linguagem==0){
    l2[0].innerHTML = "estoque"
    l1.innerHTML = "registro"
    l2[1].innerHTML = "catálogo"
    l3.innerHTML = "reserva"
    l4.innerHTML = "financiamento"
    lgText.innerHTML = "linguagem"
    document.getElementById("destaque").innerHTML = "Destaque"
    
    document.getElementById("spec-nome").innerHTML = "Nome"
    document.getElementById("spec-marca").innerHTML = "Marca"
    document.getElementById("spec-cor").innerHTML = "Cor"
    document.getElementById("spec-preco").innerHTML = "Preço"
    document.getElementById("spec-fabricante").innerHTML = "Fabricante"
    document.getElementById("spec-tipo").innerHTML = "Tipo"
    document.getElementById("spec-combustivel").innerHTML = "Combustivel"

    
    // Tradução do popup
    document.querySelectorAll('.tab-btn')[0].innerHTML = "Registro"
    document.querySelectorAll('.tab-btn')[1].innerHTML = "Login"
    document.querySelector('#registro h1').innerHTML = "Registro"
    document.querySelector('#login h1').innerHTML = "Login"
    document.getElementById('botao-registro').value = "Cadastrar"
    document.getElementById('botao-login').value = "Entrar"
    
    // Tradução dos labels do formulário
    const registroLabels = document.querySelectorAll('#registro .input-container')
    if(registroLabels[0]) {
      registroLabels[0].childNodes[0].textContent = "Nome completo"
      registroLabels[0].childNodes[4].textContent = "Data de nascimento"
      registroLabels[0].childNodes[8].textContent = "E-mail"
      registroLabels[0].childNodes[12].textContent = "Telefone"
      registroLabels[0].childNodes[16].textContent = "Senha"
      registroLabels[0].childNodes[20].textContent = "Repetir senha"
    }
    const loginLabels = document.querySelectorAll('#login .input-container')
    if(loginLabels[0]) {
      loginLabels[0].childNodes[0].textContent = "E-mail"
      loginLabels[0].childNodes[4].textContent = "Senha"
    }
  }
  else{
    l2[0].innerHTML = "stock"
    l1.innerHTML = "register"
    l2[1].innerHTML = "catalog"
    l3.innerHTML = "reservation"
    l4.innerHTML = "financing"
    lgText.innerHTML = "language"
    document.getElementById("destaque").innerHTML = "Featured"
    document.getElementById("spec-nome").innerHTML = "Name"
    document.getElementById("spec-marca").innerHTML = "Brand"
    document.getElementById("spec-cor").innerHTML = "Color"
    document.getElementById("spec-preco").innerHTML = "Price"
    document.getElementById("spec-fabricante").innerHTML = "Manufacturer"
    document.getElementById("spec-tipo").innerHTML = "Type"
    document.getElementById("spec-combustivel").innerHTML = "Fuel"

    
    // Tradução do popup
    document.querySelectorAll('.tab-btn')[0].innerHTML = "Register"
    document.querySelectorAll('.tab-btn')[1].innerHTML = "Login"
    document.querySelector('#registro h1').innerHTML = "Register"
    document.querySelector('#login h1').innerHTML = "Login"
    document.getElementById('botao-registro').value = "Sign Up"
    document.getElementById('botao-login').value = "Sign In"
    
    // Tradução dos labels do formulário
    const registroLabels = document.querySelectorAll('#registro .input-container')
    if(registroLabels[0]) {
      registroLabels[0].childNodes[0].textContent = "Full name"
      registroLabels[0].childNodes[4].textContent = "Date of birth"
      registroLabels[0].childNodes[8].textContent = "E-mail"
      registroLabels[0].childNodes[12].textContent = "Phone"
      registroLabels[0].childNodes[16].textContent = "Password"
      registroLabels[0].childNodes[20].textContent = "Repeat password"
    }
    const loginLabels = document.querySelectorAll('#login .input-container')
    if(loginLabels[0]) {
      loginLabels[0].childNodes[0].textContent = "E-mail"
      loginLabels[0].childNodes[4].textContent = "Password"
    }
  }

}


  var darkmd = 0
function darkmode(){
  const balldm = document.getElementById("balldm")
  const darkspan = document.getElementById("darkmd")
  const body = document.body
  
  balldm.classList.toggle("balldark")
  body.classList.toggle("dark-mode")
  
  if (darkmd == 0){
  balldm.style.backgroundColor = "rgb(255, 232, 206)"
  darkspan.style.backgroundColor = "rgba(0, 0, 0, 1)"
  darkmd = 1
  } else {
  balldm.style.backgroundColor = "rgba(0, 0, 0, 1)"
  darkspan.style.backgroundColor = "rgb(255, 232, 206)"
  darkmd = 0
  }
  
}

// Funções para o popup de login/registro
function openLoginPopup() {
  document.getElementById('loginPopup').style.display = 'block';
}

function closeLoginPopup() {
  document.getElementById('loginPopup').style.display = 'none';
}

function showTab(tabName) {
  // Esconder todas as abas
  const tabContents = document.querySelectorAll('.tab-content');
  tabContents.forEach(tab => {
    tab.classList.remove('active');
  });
  
  // Remover classe active de todos os botões
  const tabBtns = document.querySelectorAll('.tab-btn');
  tabBtns.forEach(btn => {
    btn.classList.remove('active');
  });
  
  // Mostrar a aba selecionada
  document.getElementById(tabName).classList.add('active');
  
  // Adicionar classe active ao botão correspondente
  if(tabName === 'registro') {
    tabBtns[0].classList.add('active');
  } else {
    tabBtns[1].classList.add('active');
  }
}


