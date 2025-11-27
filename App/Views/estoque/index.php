<!DOCTYPE html>
<html lang="pt-br">
<head>
    <script src="https://kit.fontawesome.com/619d5231f4.js" crossorigin="anonymous"></script>  
    <link rel="stylesheet" href="estoque.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque</title>
    <style>
        
        .layout-principal {
            display: flex;
            align-items: flex-start; 
            gap: 20px; 
            padding: 20px;
            max-width: 1600px;
            margin: 0 auto; 
        }

        
        #area-vitrine {
            width: 40%; 
            min-width: 350px; 
       
            position: sticky;
            top: 100px; 
            z-index: 10;
        }

        #area-vitrine .section360 {
            width: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
            border: 1px solid #ddd;
        }
        
        #area-vitrine .carimg {
            height: 300px;
            object-fit: cover;
        }

      
        #catalog {
            flex: 1; 
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start; 
            gap: 20px;
            padding: 0; 
            background-color: transparent; 
        }


        #catalog .section360 {
            width: 250px;
            flex-grow: 1;
            max-width: 300px;
        }

        @media (max-width: 900px) {
            .layout-principal {
                flex-direction: column;
            }
            #area-vitrine {
                width: 100%;
                position: static; 
                margin-bottom: 20px;
            }
            #catalog {
                width: 100%;
                justify-content: center;
            }
        }

    
        .card-clicavel { cursor: pointer; transition: transform 0.2s; }
        .card-clicavel:hover { transform: translateY(-5px); border-color: #007bff; }
    </style>
</head>
<body>

    

       <div id="buscadiv" style="padding-top: 10vh;">
            <form method="GET" action="" style="display: contents;">
                <li>
                    <input class="buscarmodelo" type="text" name="marca" id="input-marca" placeholder=" Marca" value="<?php echo htmlspecialchars($_GET['marca'] ?? ''); ?>">
                    <input type="text" name="modelo" id="input-modelo" placeholder=" Modelo" value="<?php echo htmlspecialchars($_GET['modelo'] ?? ''); ?>">
                </li>
                <li>
                    <input type="number" name="ano" id="input-modelo2" placeholder=" Ano" value="<?php echo htmlspecialchars($_GET['ano'] ?? ''); ?>">
                    <input type="text" name="combustivel" id="input-modelo3" placeholder=" Combustível" value="<?php echo htmlspecialchars($_GET['combustivel'] ?? ''); ?>">
                </li>
                <li>
                    <input type="text" name="cambio" id="input-search" style="width: 48%;" placeholder=" Câmbio" value="<?php echo htmlspecialchars($_GET['cambio'] ?? ''); ?>">
                    <button type="button" onclick="limparFiltros()" style="width: 24%; height: 5vh; border-radius: 5px; border: 1px solid #777; cursor:pointer; background-color: #ff6b6b; color: white; font-weight: bold; font-size: 0.9em;">Limpar</button>
                    <input id="buscarcarro" type="submit" value="Buscar" style="width: 24%; cursor:pointer;">
                </li>
            </form>
        </div>
    </div>    

    <p id="textrecomendacoes" style="text-align: center; margin-bottom: 10px;">
        <?php echo empty($_GET) ? 'Clique em um veículo da lista (direita) para ver detalhes (esquerda)' : 'Resultados da busca'; ?>
    </p>

    <div class="layout-principal">

        <?php 
            $destaque = !empty($veiculos) ? $veiculos[0] : null;
        ?>

        <?php if ($destaque): ?>
        <div id="area-vitrine">
            <div class="section360"> 
                <section class="container">
                    <?php $imgDestaque = !empty($destaque->foto) ? $destaque->foto : 'https://via.placeholder.com/800x400?text=Sem+Foto'; ?>
                    <img id="vitrine-img" class="carimg" src="<?php echo $imgDestaque; ?>" alt="Destaque" style="cursor: grab;">
                    
                    <div id="especificacoes">
                        <h3 id="spec-title" style="display:flex; justify-content:space-between;">
                            <span>Destaque</span>
                            <span id="vitrine-preco" style="color: #4CAF50; font-size: 1.2em;">
                                <?php echo isset($destaque->preco) ? 'R$ ' . number_format($destaque->preco, 2, ',', '.') : 'Consulte'; ?>
                            </span>
                        </h3>
                        
                        <div class="spec-grid">
                            <div class="spec-item"><span class="spec-label">Marca</span><span class="spec-value" id="vitrine-marca"><?php echo $destaque->marca; ?></span></div>
                            <div class="spec-item"><span class="spec-label">Modelo</span><span class="spec-value" id="vitrine-modelo"><?php echo $destaque->modelo; ?></span></div>
                            <div class="spec-item"><span class="spec-label">Ano</span><span class="spec-value" id="vitrine-ano"><?php echo $destaque->ano; ?></span></div>
                            <div class="spec-item"><span class="spec-label">Combustível</span><span class="spec-value" id="vitrine-combustivel"><?php echo $destaque->combustivel; ?></span></div>
                            <div class="spec-item"><span class="spec-label">Câmbio</span><span class="spec-value" id="vitrine-cambio"><?php echo $destaque->cambio; ?></span></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php endif; ?>


        <div id="catalog">
            <?php if (empty($veiculos)): ?>
                <p style="color: black; text-align: center; width: 100%; padding: 50px; font-size: 1.2em;">Nenhum veículo encontrado.</p>
            <?php else: ?>
                
                <?php foreach ($veiculos as $v): ?>
                    <?php 
                        $dadosJson = htmlspecialchars(json_encode($v), ENT_QUOTES, 'UTF-8');
                        $imagem = !empty($v->foto) ? $v->foto : 'https://via.placeholder.com/300x200?text=Sem+Foto'; 
                    ?>
                    
                    <div class="section360 card-clicavel" onclick="trocarCarro(<?php echo $dadosJson; ?>)">
                        <section class="container">
                            <img class="carimg" src="<?php echo $imagem; ?>" alt="<?php echo $v->modelo; ?>">
                            
                            <div id="especificacoes">
                                <h3 style="display:flex; justify-content:space-between; font-size: 1em; margin-bottom: 5px;">
                                    <span><?php echo $v->modelo; ?></span>
                                </h3>
                                <div class="spec-grid">
                                    <div class="spec-item"><span class="spec-label">Ano</span><span class="spec-value"><?php echo $v->ano; ?></span></div>
                                    <div class="spec-item"><span class="spec-label">Preço</span><span class="spec-value" style="color: green;"><?php echo isset($v->preco) ? 'R$ ' . number_format($v->preco, 2, ',', '.') : ''; ?></span></div>
                                </div>
                            </div>
                        </section>
                    </div>
                <?php endforeach; ?>
                
            <?php endif; ?>
        </div>

    </div> <div id="bottondiv"></div>
  

   <script>
  
    var linguagem = 0;
    var darkmd = 0;
    

    var currentcar = 'amarok';
    var max = 31;
    var currentImage = 1;
    
    function changelg() {
        const lg = document.getElementById("linguagemslct");
        const lgn = document.getElementById("lgn");
        const loginText = document.getElementById("login-text");
        const homeLink = document.getElementById("home-link");
        const backLink = document.getElementById("back-link");
        const textRecomendacoes = document.getElementById("textrecomendacoes");
        const buscarBtn = document.getElementById("buscarcarro");
        const inputMarca = document.getElementById("input-marca");
        const inputModelo = document.getElementById("input-modelo");
        const inputModelo2 = document.getElementById("input-modelo2");
        const inputModelo3 = document.getElementById("input-modelo3");
        const inputSearch = document.getElementById("input-search");
        
        if(linguagem == 0) {
            lg.style.backgroundImage = "url(public/bandeiras/Flag_of_the_United_States.svg.png)";
            linguagem = 1;
            lgn.innerHTML = "language";
            loginText.innerHTML = "Login";
            homeLink.innerHTML = "Home";
            backLink.innerHTML = "Back";
            textRecomendacoes.innerHTML = "Click on a vehicle from the list (right) to see details (left)";
            buscarBtn.value = "Search";
            inputMarca.placeholder = " Brand";
            inputModelo.placeholder = " Model";
            inputModelo2.placeholder = " Year";
            inputModelo3.placeholder = " Fuel";
            inputSearch.placeholder = " Transmission";
            document.querySelector('button[onclick="limparFiltros()"]').innerHTML = "Clear";
            
            document.querySelectorAll('.spec-label').forEach(label => {
                if(label.textContent === 'Marca') label.textContent = 'Brand';
                if(label.textContent === 'Modelo') label.textContent = 'Model';
                if(label.textContent === 'Ano') label.textContent = 'Year';
                if(label.textContent === 'Combustível') label.textContent = 'Fuel';
                if(label.textContent === 'Câmbio') label.textContent = 'Transmission';
                if(label.textContent === 'Preço') label.textContent = 'Price';
            });
        } else {
            lg.style.backgroundImage = "url(public/bandeiras/Flag_of_Brazil.svg.webp)";
            linguagem = 0;
            lgn.innerHTML = "linguagem";
            loginText.innerHTML = "Login";
            homeLink.innerHTML = "Home";
            backLink.innerHTML = "Voltar";
            textRecomendacoes.innerHTML = "Clique em um veículo da lista (direita) para ver detalhes (esquerda)";
            buscarBtn.value = "Buscar";
            inputMarca.placeholder = " Marca";
            inputModelo.placeholder = " Modelo";
            inputModelo2.placeholder = " Ano";
            inputModelo3.placeholder = " Combustível";
            inputSearch.placeholder = " Câmbio";
            document.querySelector('button[onclick="limparFiltros()"]').innerHTML = "Limpar";
            
            document.querySelectorAll('.spec-label').forEach(label => {
                if(label.textContent === 'Brand') label.textContent = 'Marca';
                if(label.textContent === 'Model') label.textContent = 'Modelo';
                if(label.textContent === 'Year') label.textContent = 'Ano';
                if(label.textContent === 'Fuel') label.textContent = 'Combustível';
                if(label.textContent === 'Transmission') label.textContent = 'Câmbio';
                if(label.textContent === 'Price') label.textContent = 'Preço';
            });
        }
    }
    
    function darkmode() {
        const balldm = document.getElementById("balldm");
        const darkspan = document.getElementById("darkmd");
        const body = document.body;
        
        balldm.classList.toggle("balldark");
        body.classList.toggle("dark-mode");
        
        if (darkmd == 0) {
            balldm.style.backgroundColor = "rgb(255, 232, 206)";
            darkspan.style.backgroundColor = "rgba(0, 0, 0, 1)";
            darkmd = 1;
        } else {
            balldm.style.backgroundColor = "rgba(0, 0, 0, 1)";
            darkspan.style.backgroundColor = "rgb(255, 232, 206)";
            darkmd = 0;
        }
    }

    function limparFiltros() {
        document.getElementById('input-marca').value = '';
        document.getElementById('input-modelo').value = '';
        document.getElementById('input-modelo2').value = '';
        document.getElementById('input-modelo3').value = '';
        document.getElementById('input-search').value = '';
        
        const mensagem = linguagem == 0 ? 'Use os filtros acima para buscar veículos.' : 'Use the filters above to search for vehicles.';
        document.getElementById('catalog').innerHTML = '<p style="color: black; text-align: center; width: 100%; padding: 50px; font-size: 1.2em;">' + mensagem + '</p>';
        
        const textoRecomendacao = linguagem == 0 ? 'Clique em um veículo da lista (direita) para ver detalhes (esquerda)' : 'Click on a vehicle from the list (right) to see details (left)';
        document.getElementById('textrecomendacoes').textContent = textoRecomendacao;
    }

  
    function trocarCarro(carro) {
        console.log('Dados do carro recebidos:', carro); 
        
     
        if(window.innerWidth < 900) {
            document.getElementById('upeerdiv').scrollIntoView({ behavior: 'smooth' });
        }
        
      
        let imgElement = document.getElementById('vitrine-img');
        
       
        let caminhoFoto = carro.foto || 'https://via.placeholder.com/800x400?text=Sem+Foto';
        
        console.log('Caminho da foto:', caminhoFoto); 
        
      
        imgElement.src = '';
        setTimeout(() => {
            imgElement.src = caminhoFoto;
        }, 10);

  
        document.getElementById('vitrine-marca').textContent = carro.marca || 'N/A';
        document.getElementById('vitrine-modelo').textContent = carro.modelo || 'N/A';
        document.getElementById('vitrine-ano').textContent = carro.ano || 'N/A';
        document.getElementById('vitrine-combustivel').textContent = carro.combustivel || 'N/A';
        document.getElementById('vitrine-cambio').textContent = carro.cambio || 'N/A';
        
  
        let precoFormatado = 'Consulte';
        if (carro.preco && !isNaN(parseFloat(carro.preco))) {
            precoFormatado = parseFloat(carro.preco).toLocaleString('pt-BR', { 
                style: 'currency', 
                currency: 'BRL' 
            });
        }
        document.getElementById('vitrine-preco').textContent = precoFormatado;
        
      
        if (typeof currentcar !== 'undefined' && carro.pasta) {
            currentcar = carro.pasta;
            if (typeof max !== 'undefined' && carro.total_imagens) {
                max = parseInt(carro.total_imagens);
            }
        }
    }
    

    const cursor = {
        isDragging: false,
        initialPosition: 0,
    };

    const updateimg = (direction) => {
        if(direction > 0) {
            if(currentImage == max) {
                currentImage = 1;
            } else {
                currentImage++;
            }
        } else {
            if(currentImage == 1) {
                currentImage = max;
            } else {
                currentImage--;
            }
        }

        const imgElement = document.getElementById('vitrine-img');
        if (currentcar && max > 1) {
            imgElement.src = `./${currentcar}/${currentImage}.webp`;
        }
    };

   
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.container');
        const imgElement = document.getElementById('vitrine-img');
        
        if (container && imgElement) {
            container.addEventListener('mousedown', (event) => {
                cursor.isDragging = true;
                cursor.initialPosition = event.clientX;
                imgElement.style.cursor = 'grabbing';
            });

            container.addEventListener('mouseup', () => {
                cursor.isDragging = false;
                imgElement.style.cursor = 'grab';
            });

            container.addEventListener('mousemove', ({clientX}) => {
                if(!cursor.isDragging) return;

                const offset = cursor.initialPosition - clientX;

                if(Math.abs(offset) >= 20) {
                    updateimg(offset);
                    cursor.initialPosition = clientX;
                }
            });
            
         
            imgElement.addEventListener('dragstart', (e) => e.preventDefault());
        }
    });
   
</script>
    
</body>
</html>


