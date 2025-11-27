<?php
session_start();

require_once 'config/Database.php';
require_once 'models/Veiculo.php'; 
require_once 'repositories/VeiculoRepository.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $repo = new VeiculoRepository($db);
    
 
    $listaVeiculos = $repo->buscarComFiltros([]); 
} catch (Exception $e) {
    $listaVeiculos = []; 
}


include 'views/partials/header.php';
?>

<style>
    #catalog-hero {
        margin-top: 8vh;
        background: rgb(255, 232, 206);
        padding: 2vh 4vh;
        text-align: center;
    }
    #catalog-hero p {
        font-family: 'Montserrat', sans-serif;
        font-size: 1.5em;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin: 0;
        color: #333;
    }
    #filters {
        display: flex;
        justify-content: center;
        gap: 2vh;
        padding: 3vh;
        background: white;
        flex-wrap: wrap;
    }
    #filters select, #filters input {
        padding: 1vh 2vh;
        border: 2px solid rgb(255, 232, 206);
        border-radius: 8px;
        font-size: 1em;
        min-width: 150px;
    }
    #filters button {
        padding: 1vh 3vh;
        background: #333;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1em;
        transition: 0.3s;
    }
    #filters button:hover { background: #555; }
    
    #vehicles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 3vh;
        padding: 4vh;
        max-width: 1400px;
        margin: 0 auto;
        min-height: 50vh;
    }
    
    .vehicle-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: 0.3s;
        cursor: pointer;
    }
    .vehicle-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .vehicle-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }
    .vehicle-info { padding: 2vh; }
    .vehicle-brand { font-size: 0.9em; color: #999; text-transform: uppercase; }
    .vehicle-model { font-size: 1.8em; font-weight: bold; margin: 0.5vh 0; color: #333; }
    .vehicle-specs { display: flex; gap: 1.5vh; margin: 1.5vh 0; font-size: 0.9em; color: #666; }
    .vehicle-price { font-size: 2em; font-weight: bold; color: rgb(200, 120, 60); margin-top: 1vh; }
    
   
    #modal {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.9);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    #modal-content {
        background: white;
        border-radius: 20px;
        width: 90%;
        max-width: 1200px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
    }
    #modal-close {
        position: absolute;
        top: 2vh; right: 3vh;
        font-size: 3em;
        cursor: pointer;
        color: #666;
        z-index: 10000;
    }
    #modal-360 {
        width: 100%;
        height: 60vh;
        background: #f5f5f5;
        position: relative;
        cursor: grab;
        user-select: none;
    }
    #modal-360:active { cursor: grabbing; }
    #modal-360 img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    #modal-info { padding: 4vh; }
    #modal-info h2 { font-size: 2.5em; margin: 0 0 1vh 0; }
    .specs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2vh;
        margin: 3vh 0;
    }
    .spec-box {
        background: rgb(255, 245, 235);
        padding: 2vh;
        border-radius: 10px;
        text-align: center;
    }
    .spec-label { font-size: 0.9em; color: #999; text-transform: uppercase; }
    .spec-value { font-size: 1.5em; font-weight: bold; color: #333; margin-top: 0.5vh; }
    
    
    body.dark-mode #catalog-hero { background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%); }
    body.dark-mode #catalog-hero p { color: #e0e0e0; }
    body.dark-mode #filters { background: #2d2d2d; }
    body.dark-mode #filters select, body.dark-mode #filters input { background: #1a1a1a; color: #e0e0e0; border-color: #444; }
    body.dark-mode .vehicle-card { background: #2d2d2d; }
    body.dark-mode .vehicle-model { color: #e0e0e0; }
    body.dark-mode #modal-content { background: #2d2d2d; color: #e0e0e0; }
    body.dark-mode .spec-box { background: #1a1a1a; }
    body.dark-mode .spec-value { color: #e0e0e0; }
</style>

<div id="catalog-hero">
    <p data-pt="Explore nossa coleção exclusiva de veículos premium" data-en="Explore our exclusive collection of premium vehicles">Explore nossa coleção exclusiva de veículos premium</p>
</div>

<div id="filters">
    <select id="filter-marca"><option value="">Todas as Marcas</option></select>
    <select id="filter-combustivel"><option value="">Combustível</option></select>
    <select id="filter-cambio"><option value="">Câmbio</option></select>
    <input type="number" id="filter-ano" placeholder="Ano">
    <button onclick="aplicarFiltros()">Filtrar</button>
    <button onclick="limparFiltros()">Limpar</button>
</div>

<div id="vehicles-grid"></div>

<div id="modal">
    <div id="modal-content">
        <span id="modal-close" onclick="fecharModal()">&times;</span>
        <div id="modal-360">
            <img id="modal-img" src="">
        </div>
        <div id="modal-info">
            <h2 id="modal-title"></h2>
            <div class="specs-grid">
                <div class="spec-box"><div class="spec-label">Marca</div><div class="spec-value" id="spec-marca"></div></div>
                <div class="spec-box"><div class="spec-label">Ano</div><div class="spec-value" id="spec-ano"></div></div>
                <div class="spec-box"><div class="spec-label">Combustível</div><div class="spec-value" id="spec-combustivel"></div></div>
                <div class="spec-box"><div class="spec-label">Câmbio</div><div class="spec-value" id="spec-cambio"></div></div>
                <div class="spec-box"><div class="spec-label">Preço</div><div class="spec-value" id="spec-preco"></div></div>
            </div>
        </div>
    </div>
</div>

<script>
   
    const veiculos = <?php echo json_encode($listaVeiculos); ?>;
    
 
    veiculos.forEach(v => {

        v.totalImagens = v.total_imagens ? parseInt(v.total_imagens) : 1;

   
        if(!isNaN(v.preco)) {
            v.preco = parseFloat(v.preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }
        
     
        if(!v.pasta) {
            v.pasta = v.marca.toLowerCase() + '-' + v.modelo.toLowerCase().replace(/ /g, '-');
        }
    });

    let veiculosFiltrados = [...veiculos];
    let veiculoAtual = null;
    let imagemAtual = 1;
    let isDragging = false;
    let startX = 0;
    
 
    const EXTENSAO_FOTOS = '.webp'; 

    function carregarVeiculos() {
        const grid = document.getElementById('vehicles-grid');
        grid.innerHTML = '';

        if(veiculosFiltrados.length === 0) {
            grid.innerHTML = '<p style="text-align:center; width:100%; padding:50px;">Nenhum veículo encontrado com esses filtros.</p>';
            return;
        }

        veiculosFiltrados.forEach(veiculo => {
            const card = document.createElement('div');
            card.className = 'vehicle-card';
            card.onclick = () => abrirModal(veiculo);
            
        
            let imagemCapa = veiculo.foto ? veiculo.foto : (veiculo.pasta + '/1.webp');
            
            card.innerHTML = `
                <img class="vehicle-img" src="${imagemCapa}" alt="${veiculo.modelo}" onerror="this.src='https://via.placeholder.com/300x200?text=Sem+Foto'">
                <div class="vehicle-info">
                    <div class="vehicle-brand">${veiculo.marca}</div>
                    <div class="vehicle-model">${veiculo.modelo}</div>
                    <div class="vehicle-specs">
                        <span>${veiculo.ano}</span>
                        <span>${veiculo.combustivel}</span>
                        <span>${veiculo.cambio}</span>
                    </div>
                    <div class="vehicle-price">${veiculo.preco}</div>
                </div>
            `;
            
            grid.appendChild(card);
        });
    }

    function carregarFiltros() {
        const marcas = [...new Set(veiculos.map(v => v.marca))];
        const combustiveis = [...new Set(veiculos.map(v => v.combustivel))];
        const cambios = [...new Set(veiculos.map(v => v.cambio))];

        const filterMarca = document.getElementById('filter-marca');
        const filterCombustivel = document.getElementById('filter-combustivel');
        const filterCambio = document.getElementById('filter-cambio');

    
        filterMarca.innerHTML = '<option value="">Todas as Marcas</option>';
        filterCombustivel.innerHTML = '<option value="">Combustível</option>';
        filterCambio.innerHTML = '<option value="">Câmbio</option>';

        marcas.forEach(marca => {
            const option = document.createElement('option');
            option.value = marca;
            option.textContent = marca;
            filterMarca.appendChild(option);
        });

        combustiveis.forEach(combustivel => {
            const option = document.createElement('option');
            option.value = combustivel;
            option.textContent = combustivel;
            filterCombustivel.appendChild(option);
        });

        cambios.forEach(cambio => {
            const option = document.createElement('option');
            option.value = cambio;
            option.textContent = cambio;
            filterCambio.appendChild(option);
        });
    }

    function aplicarFiltros() {
        const marca = document.getElementById('filter-marca').value;
        const combustivel = document.getElementById('filter-combustivel').value;
        const cambio = document.getElementById('filter-cambio').value;
        const ano = document.getElementById('filter-ano').value;

        veiculosFiltrados = veiculos.filter(veiculo => {
            return (!marca || veiculo.marca === marca) &&
                   (!combustivel || veiculo.combustivel === combustivel) &&
                   (!cambio || veiculo.cambio === cambio) &&
                   (!ano || veiculo.ano.toString() === ano);
        });

        carregarVeiculos();
    }

    function limparFiltros() {
        document.getElementById('filter-marca').value = '';
        document.getElementById('filter-combustivel').value = '';
        document.getElementById('filter-cambio').value = '';
        document.getElementById('filter-ano').value = '';
        veiculosFiltrados = [...veiculos];
        carregarVeiculos();
    }

    function abrirModal(veiculo) {
        veiculoAtual = veiculo;
        imagemAtual = 1;
        
        document.getElementById('modal-title').textContent = `${veiculo.marca} ${veiculo.modelo}`;
        document.getElementById('spec-marca').textContent = veiculo.marca;
        document.getElementById('spec-ano').textContent = veiculo.ano;
        document.getElementById('spec-combustivel').textContent = veiculo.combustivel;
        document.getElementById('spec-cambio').textContent = veiculo.cambio;
        document.getElementById('spec-preco').textContent = veiculo.preco;
        
        atualizarImagem360();
        document.getElementById('modal').style.display = 'flex';
    }

    function fecharModal() {
        document.getElementById('modal').style.display = 'none';
    }

    function atualizarImagem360() {
        if (veiculoAtual) {
           
            let caminhoImagem;
            if(veiculoAtual.totalImagens > 1) {
                caminhoImagem = `${veiculoAtual.pasta}/${imagemAtual}.webp`;
            } else {
                caminhoImagem = veiculoAtual.foto ? veiculoAtual.foto : 'https://via.placeholder.com/800x400?text=Sem+360';
            }
            
            document.getElementById('modal-img').src = caminhoImagem;
        }
    }

   
    document.getElementById('modal-360').addEventListener('mousedown', (e) => {
      
        if(veiculoAtual && veiculoAtual.totalImagens > 1) {
            isDragging = true;
            startX = e.clientX;
        }
    });

    document.addEventListener('mousemove', (e) => {
        if (isDragging && veiculoAtual) {
            const deltaX = e.clientX - startX;
            if (Math.abs(deltaX) > 10) {
                if (deltaX > 0) {
                    imagemAtual = imagemAtual < veiculoAtual.totalImagens ? imagemAtual + 1 : 1;
                } else {
                    imagemAtual = imagemAtual > 1 ? imagemAtual - 1 : veiculoAtual.totalImagens;
                }
                atualizarImagem360();
                startX = e.clientX;
            }
        }
    });

    document.addEventListener('mouseup', () => { isDragging = false; });

   
    document.getElementById('modal').addEventListener('click', (e) => {
        if (e.target.id === 'modal') { fecharModal(); }
    });

  
    document.addEventListener('DOMContentLoaded', () => {
        carregarFiltros();
        carregarVeiculos();
    });
</script>

<?php include 'views/partials/footer.php'; ?>
                