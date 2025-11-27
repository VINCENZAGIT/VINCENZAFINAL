// Este código assume que o PHP já definiu a variável CARROS_DATA (dinâmica do banco)
// Ex: const CARROS_DATA = [...];

// Variáveis Globais de Controle
let veiculosFiltrados = [...CARROS_DATA];
let currentCarData = {};
let selectedInstallments = 12; // Valor inicial
let popupCurrentImage = 1;
let popupMax = 1;
let popupCurrentCar = "";
let isDragging = false;
let initialX = 0;

// --- UTILS ---

// Converte string R$ 150.000 para número puro 150000
function parsePrice(priceString) {
    if (typeof priceString !== 'string') return priceString;
    return parseFloat(priceString.replace('R$', '').replace(/\./g, '').replace(',', '.').trim());
}

function formatarMoeda(valor) {
    return parseFloat(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}


// --- LÓGICA DE EXIBIÇÃO DE CARDS NA GRADE (Para Fins de Filtro) ---

function carregarVeiculos() {
    const grid = document.getElementById('cars-grid');
    grid.innerHTML = '';
    
    // Filtro é feito primariamente pelos dropdowns na página
    if (veiculosFiltrados.length === 0) {
        grid.innerHTML = '<p style="text-align:center; width:100%; padding:20px;">Nenhum veículo encontrado para simulação.</p>';
        return;
    }

    veiculosFiltrados.forEach(veiculo => {
        const card = document.createElement('div');
        card.className = 'car-card';
        // O evento de clique precisa passar o NOME/MODELO (que é único)
        card.onclick = () => openCarPopup(veiculo.modelo, veiculo.preco, veiculo.foto || `${veiculo.pasta}/1.webp`);
        
        let imagemCapa = veiculo.foto || `${veiculo.pasta}/1.webp`;
        let precoAntigo = formatarMoeda(veiculo.preco * 1.25); // Exemplo de preço antigo fictício

        card.innerHTML = `
            <img src="${imagemCapa}" alt="${veiculo.modelo}" class="vehicle-img" onerror="this.src='https://via.placeholder.com/320x200?text=Sem+Foto'">
            <h3>${veiculo.marca} ${veiculo.modelo}</h3>
            <div class="price-container">
                <span class="old-price">${precoAntigo}</span>
                <span class="new-price">${formatarMoeda(veiculo.preco)}</span>
            </div>
        `;
        
        grid.appendChild(card);
    });
}

// --- LÓGICA DE FILTROS (Adaptada para Tabela Price) ---

function carregarFiltros() {
    // A lista de carros já foi populada pelo PHP (CARROS_DATA)
    const marcas = [...new Set(CARROS_DATA.map(v => v.marca))];
    // Aqui seria ideal puxar categorias do DB, mas vamos usar valores fixos para o front:
    const categorias = ['hatch', 'sedan', 'suv', 'pickup', 'premium', 'elétrico']; 
    
    // Atualiza apenas os selects que precisam de dados dinâmicos
    const selectMarca = document.getElementById('filter-marca');
    const selectCategoria = document.getElementById('filter-categoria');
    
    selectMarca.innerHTML = '<option value="">Todas as Marcas</option>';
    
    marcas.forEach(marca => {
        const option = document.createElement('option');
        option.value = marca;
        option.textContent = marca;
        selectMarca.appendChild(option);
    });
    
    // Atualiza as categorias (reutilizando o select do seu HTML)
    if (selectCategoria.options.length <= 1) { // Só preenche se estiver vazio
         categorias.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat;
            option.textContent = cat.toUpperCase();
            selectCategoria.appendChild(option);
        });
    }
}

function aplicarFiltros() {
    // Esta função precisaria de um input adicional (input type="text") no seu HTML
    // para buscar por nome/modelo, mas atualmente só temos filtros de select.
    
    const precoFiltro = document.getElementById('filtroPreco').value;
    const categoriaFiltro = document.getElementById('filtroCategoria').value;
    
    veiculosFiltrados = CARROS_DATA.filter(veiculo => {
        let mostrar = true;
        const precoNumerico = parsePrice(veiculo.preco);
        
        // 1. Filtro de Faixa de Preço
        if (precoFiltro) {
            let [min, max] = precoFiltro.includes('+') ? 
                [parseInt(precoFiltro.replace('+', '')), Infinity] : 
                precoFiltro.split('-').map(p => parseInt(p));
            
            if (precoNumerico < min || precoNumerico > max) {
                mostrar = false;
            }
        }
        
        // 2. Filtro de Categoria (Assumindo que você teria essa coluna no DB)
        if (categoriaFiltro && veiculo.categoria !== categoriaFiltro) {
             mostrar = false;
        }
        
        return mostrar;
    });

    carregarVeiculos();
}

function limparFiltros() {
    document.getElementById('filtroPreco').value = '';
    document.getElementById('filtroCategoria').value = '';
    veiculosFiltrados = [...CARROS_DATA];
    carregarVeiculos();
}

// --- LÓGICA DO MODAL DE DETALHES (Para Financiamento) ---

function openCarPopup(carModel, carPrice, carImage) {
    const popup = document.getElementById('car-popup');
    const priceEl = document.getElementById('popup-price');
    const titleEl = document.getElementById('popup-title');
    
    // Encontrar dados completos do carro (agora busca pelo Modelo/Nome)
    let carData = CARROS_DATA.find(car => `${car.marca} ${car.modelo}` === carModel); 
    if (!carData) return;
    
    // Mapeamento dos dados do DB (que vêm em snake_case) para o modal
    const data = {
        name: carModel,
        price: parsePrice(carData.preco),
        folder: carData.pasta,
        maxImages: parseInt(carData.total_imagens)
    };
    
    currentCarData = data;

    // Configurar imagens 360°
    popupCurrentCar = data.folder;
    popupCurrentImage = 1;
    popupMax = data.maxImages;

    // Preencher informações
    titleEl.textContent = `${carData.marca} ${carData.modelo}`;
    priceEl.textContent = formatarMoeda(data.price);
    
    // Preencher Specs
    document.getElementById('popup-model').textContent = carData.modelo;
    document.getElementById('popup-year').textContent = carData.ano;
    document.getElementById('popup-fuel').textContent = carData.combustivel;
    document.getElementById('popup-transmission').textContent = carData.cambio;
    
    updatePopupImage();
    updateInstallmentOptions(); // Atualiza as parcelas com o novo preço
    setupPopup360();
    
    popup.style.display = 'flex';
}

// (Mantenha as funções nextImage, previousImage, updatePopupImage, setupPopup360, closeCarPopup, updateInstallmentOptions, selectInstallment, simulateFinancing, purchaseCar como você as definiu no seu arquivo original, ajustando apenas as referências para 'CARROS_DATA' e 'currentCarData' se for necessário).

// Chamada Inicial
document.addEventListener('DOMContentLoaded', () => {
    carregarFiltros();
    carregarVeiculos();
    // Você pode chamar a função toggleTheme() ou carregarTema() aqui se precisar
});