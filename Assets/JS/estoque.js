const container = document.querySelector(".container");
const imgcar = document.querySelector(".carimg");
const busca = document.querySelector(".buscainput");
const buscascarmodelo = document.querySelector(".buscarmodelo");

const estoque=[
    {car:"amarok", i: 31},
    {car:"x4", i: 31},
    {car:"mercedesa180", i: 32},
    {car:"civic", i: 31},
    {car:"fiat500", i: 29},
    {car:"honda-hr-v", i: 32},
    {car:"polo", i: 31},


    {car: "fim"}
];

let max = 31;
let currentImage = 1;
var currentcar = "amarok"
function changecar(){
    var existe = false;
    var search = buscascarmodelo.value.toLowerCase()
    estoque.forEach(car => {
        if(car.car == search){
            console.log("O carro existe");
            currentcar = search;
            imgcar.src = `./${currentcar}/${currentImage}.webp`;
            existe=true;
            max = car.i
        }
        if(car.car == "fim" && existe==false){
            console.log("O carro não existe")
        }
    });
}

function changecar2(){
    var existe = false;
    var search = item.innerText.toLowerCase()
    estoque.forEach(car => {
        if(car.car == search){
            console.log("O carro existe");
            currentcar = search;
            imgcar.src = `./${currentcar}/${currentImage}.webp`;
            existe=true;
            max = car.i
        }
        if(car.car == "fim" && existe==false){
            console.log("O carro não existe")
        }
    });
}


const cursor = {
    isDragging: false,
    initialPosition: 0,
};

const updateimg = (direction) =>{
    

    if(direction >0){
        if(currentImage==max){
            currentImage=1
        }else{
            currentImage++
        }
    }else{
       if(currentImage==1){
            currentImage=max
        }else{
            currentImage--
        }
    }

    imgcar.src = `./${currentcar}/${currentImage}.webp`
}


container.addEventListener("mousedown", (event) => {
    cursor.isDragging = true;
    cursor.initialPosition = event.clientX;
});

container.addEventListener("mouseup", () => {
    cursor.isDragging = false;
});

container.addEventListener("mousemove", ({clientX}) => {
    if(!cursor.isDragging) return;

    const offset = cursor.initialPosition-clientX



    if(Math.abs(offset) >= 20 ){
        updateimg(offset)
        cursor.initialPosition = clientX;
    };
});


let contador = 1;

    function addc() {
      let grid = document.getElementById("bottondiv");
        
      let num = Math.floor(Math.random() * (6 - 1 + 1)) + 1;
      let img = document.createElement("img");
      let item = document.createElement("section");
      item.innerText = estoque[num].car;

      grid.appendChild(item);
      item.appendChild(img);
      img.src = `./${estoque[num].car}/1.webp`;
      item.classList.add("item");
      img.classList.add("imgitem");
      item.addEventListener("click", function( ){
        var existe = false;
    var search = item.innerText.toLowerCase()
    estoque.forEach(car => {
        if(car.car == search){
            console.log("O carro existe");
            currentcar = search;
            imgcar.src = `./${currentcar}/${currentImage}.webp`;
            existe=true;
            max = car.i
        }
        if(car.car == "fim" && existe==false){
            console.log("O carro não existe")
        }
    });
      })

    }

var linguagem = 0
function changelg(){
  const lg = document.getElementById("linguagemslct")
  const lgn = document.getElementById("lgn")
  const loginText = document.getElementById("login-text")
  const homeLink = document.getElementById("home-link")
  const backLink = document.getElementById("back-link")
  const textRecomendacoes = document.getElementById("textrecomendacoes")
  const addItemBtn = document.getElementById("add-item-btn")
  const buscarBtn = document.getElementById("buscarcarro")
  const inputMarca = document.getElementById("input-marca")
  const inputModelo = document.getElementById("input-modelo")
  const inputModelo2 = document.getElementById("input-modelo2")
  const inputModelo3 = document.getElementById("input-modelo3")
  
  if(linguagem == 0){
    lg.style.backgroundImage = "url(Flag_of_the_United_States.svg.png)"
    linguagem = 1;
    lgn.innerHTML = "language"
    loginText.innerHTML = "Login"
    homeLink.innerHTML = "Home"
    backLink.innerHTML = "Back"
    textRecomendacoes.innerHTML = "Recommended for you"
    addItemBtn.innerHTML = "Add Item"
    buscarBtn.value = "Search Car"
    inputMarca.placeholder = " Brand"
    inputModelo.placeholder = " Model"
    inputModelo2.placeholder = " Year"
    inputModelo3.placeholder = " Fuel"
    document.getElementById("input-search").placeholder = " Transmission"
    if(document.getElementById("spec-title")) document.getElementById("spec-title").innerHTML = "Specifications"
    if(document.getElementById("label-nome")) document.getElementById("label-nome").innerHTML = "Name"
    if(document.getElementById("label-marca")) document.getElementById("label-marca").innerHTML = "Brand"
    if(document.getElementById("label-combustivel")) document.getElementById("label-combustivel").innerHTML = "Fuel"
    if(document.getElementById("label-cambio")) document.getElementById("label-cambio").innerHTML = "Transmission"
    if(document.getElementById("label-ano")) document.getElementById("label-ano").innerHTML = "Year"
  } else {
    lg.style.backgroundImage = "url(Flag_of_Brazil.svg.webp)"
    linguagem = 0;
    lgn.innerHTML = "linguagem"
    loginText.innerHTML = "Login"
    homeLink.innerHTML = "Home"
    backLink.innerHTML = "Voltar"
    textRecomendacoes.innerHTML = "Recomendados para você"
    addItemBtn.innerHTML = "Adicionar Item"
    buscarBtn.value = "Buscar Carro"
    inputMarca.placeholder = " Marca"
    inputModelo.placeholder = " Modelo"
    inputModelo2.placeholder = " Ano"
    inputModelo3.placeholder = " Combustível"
    document.getElementById("input-search").placeholder = " Câmbio"
    // Traduzir especificações
    if(document.getElementById("spec-title")) document.getElementById("spec-title").innerHTML = "Especificações"
    if(document.getElementById("label-nome")) document.getElementById("label-nome").innerHTML = "Nome"
    if(document.getElementById("label-marca")) document.getElementById("label-marca").innerHTML = "Marca"
    if(document.getElementById("label-combustivel")) document.getElementById("label-combustivel").innerHTML = "Combustível"
    if(document.getElementById("label-cambio")) document.getElementById("label-cambio").innerHTML = "Câmbio"
    if(document.getElementById("label-ano")) document.getElementById("label-ano").innerHTML = "Ano"
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
