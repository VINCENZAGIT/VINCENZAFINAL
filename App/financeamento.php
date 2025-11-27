<?php
session_start();
require_once 'controllers/EstoqueController.php'; 
require_once 'repositories/VeiculoRepository.php'; 
require_once 'config/Database.php';

// Processa requisições AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        switch ($_POST['action']) {
            case 'salvar_proposta':
                $tipo_pagamento = $_POST['tipo_pagamento'] === 'debit' ? 'debito_vista' : 'financiamento';
                $sql = "INSERT INTO propostas_financiamento (veiculo_id, cliente_nome, cliente_email, cliente_telefone, tipo_pagamento, valor_entrada, prazo_meses, taxa_juros, valor_parcela, valor_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $_POST['veiculo_id'], $_POST['cliente_nome'], $_POST['cliente_email'], $_POST['cliente_telefone'],
                    $tipo_pagamento, $_POST['valor_entrada'], $_POST['prazo_meses'], $_POST['taxa_juros'],
                    $_POST['valor_parcela'], $_POST['valor_total']
                ]);
                echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
                exit;
                
            case 'avaliar_veiculo':
                $modelo = strtolower($_POST['modelo']);
                $km = intval($_POST['quilometragem']);
                $valor_base = 50000;
                if (strpos($modelo, 'civic') !== false) $valor_base = 80000;
                if (strpos($modelo, 'corolla') !== false) $valor_base = 85000;
                if (strpos($modelo, 'onix') !== false) $valor_base = 45000;
                $depreciacao = ($km / 10000) * 0.05;
                $valor_final = max($valor_base * (1 - $depreciacao), $valor_base * 0.3);
                echo json_encode(['success' => true, 'valor_estimado' => $valor_final]);
                exit;
                
            case 'agendar_inspecao':
                $sql = "INSERT INTO inspecoes_veiculos (veiculo_id, cliente_nome, cliente_email, cliente_telefone, data_agendada, hora_agendada) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $_POST['veiculo_id'], $_POST['cliente_nome'], $_POST['cliente_email'], 
                    $_POST['cliente_telefone'], $_POST['data_agendada'], $_POST['hora_agendada']
                ]);
                echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
                exit;
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

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

<link rel="stylesheet" href="financeamento.css"> 

<main class="container">
    <section class="hero-section">
        <h2>Financiamento Facilitado</h2>
        <p>Realize o sonho do carro próprio com taxas especiais, entrada facilitada e parcelas que cabem no seu orçamento</p>
    </section>

    <section class="filtros">
        <div class="filtro-grupo">
            <label>Faixa de Preço:</label>
            <select id="filtroPreco">
                <option value="">Todos os preços</option>
                <option value="0-50000">Até R$ 50.000</option>
                <option value="50000-100000">R$ 50.000 - R$ 100.000</option>
                <option value="100000-150000">R$ 100.000 - R$ 150.000</option>
                <option value="150000+">Acima de R$ 150.000</option>
            </select>
        </div>
        <div class="filtro-grupo">
            <label>Categoria:</label>
            <select id="filtroCategoria">
                <option value="">Todas as categorias</option>
                <option value="hatch">Hatch</option>
                <option value="sedan">Sedan</option>
                <option value="suv">SUV</option>
                <option value="pickup">Pickup</option>
                <option value="premium">Premium</option>
            </select>
        </div>
        <button class="btn-filtrar" onclick="aplicarFiltros()">Filtrar</button>
        <button class="btn-limpar" onclick="limparFiltros()">Limpar</button>
    </section>

    <section id="cars-grid" class="cars-grid">
        </section>

    <section id="pagination" class="pagination">
        <p id="car-count-info" style="color: #666; font-style: italic;"></p>
    </section>

    <div id="car-popup" class="modal">
        <div class="modal-content popup-content" style="max-width: 1200px; width: 95%;">
            <span class="close popup-close" onclick="closeCarPopup()">&times;</span>
            <h2 id="popup-title" style="text-align: center; margin-bottom: 30px;">Simular Financiamento</h2>
            
            <div class="popup-layout" style="gap: 40px;">
                <div class="popup-car-360">
                    <div class="image-container" style="margin-bottom: 20px; height: 450px;">
                        <img id="popup-carimg" class="popup-carimg" src="" alt="Veículo" style="height: 450px; width: 100%; object-fit: cover;">
                        <div class="image-controls">
                            <button onclick="previousImage()" class="img-btn">◀</button>
                            <span class="image-counter"><span id="current-img">1</span>/<span id="total-imgs">1</span></span>
                            <button onclick="nextImage()" class="img-btn">▶</button>
                        </div>
                        <div class="drag-hint">Use as setas ou teclado (← →) para navegar</div>
                    </div>
                </div>
                
                <div class="popup-info">
                    <div class="popup-specs" style="margin-bottom: 25px;">
                        <h3 style="margin-bottom: 15px;">Especificações</h3>
                        <div class="spec-grid">
                            <div class="spec-item" style="padding: 12px 0;">
                                <span class="spec-label">Modelo:</span>
                                <span id="popup-model" class="spec-value"></span>
                            </div>
                            <div class="spec-item" style="padding: 12px 0;">
                                <span class="spec-label">Ano:</span>
                                <span id="popup-year" class="spec-value"></span>
                            </div>
                            <div class="spec-item" style="padding: 12px 0;">
                                <span class="spec-label">Combustível:</span>
                                <span id="popup-fuel" class="spec-value">Flex</span>
                            </div>
                            <div class="spec-item" style="padding: 12px 0;">
                                <span class="spec-label">Câmbio:</span>
                                <span id="popup-transmission" class="spec-value">Automático</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="popup-purchase">
                        <h3 style="margin-bottom: 20px;">Opções de Financiamento</h3>
                        <div class="price-highlight" style="margin-bottom: 25px;">
                            <span class="price-label">À vista:</span>
                            <span id="popup-price" class="price-value"></span>
                        </div>
                        
                        <div class="payment-options" style="margin-bottom: 25px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 15px; color: #2c3e50;">Formas de Pagamento</h4>
                            <div class="payment-option" style="margin-bottom: 15px;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="radio" name="paymentType" value="financing" checked style="margin-right: 10px;">
                                    <span>Financiamento</span>
                                </label>
                            </div>
                            <div class="payment-option" style="margin-bottom: 15px;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="radio" name="paymentType" value="debit" style="margin-right: 10px;">
                                    <span style="color: #27ae60; font-weight: bold;">Débito à vista (15% desconto)</span>
                                </label>
                                <div id="debit-price" style="margin-left: 25px; color: #27ae60; font-size: 1.2em; display: none;"></div>
                            </div>
                        </div>
                        
                        <div class="vehicle-trade-section" style="margin-bottom: 25px; padding: 15px; background: #fff3cd; border-radius: 8px; border: 1px solid #ffeaa7;">
                            <h4 style="margin: 0 0 15px; color: #856404;">Tem um veículo para dar de entrada?</h4>
                            <label style="display: flex; align-items: center; cursor: pointer; margin-bottom: 15px;">
                                <input type="checkbox" id="hasTradeIn" style="margin-right: 10px;">
                                <span>Sim, quero avaliar meu veículo</span>
                            </label>
                            <div id="trade-in-form" style="display: none;">
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; margin-bottom: 5px;">Marca/Modelo do seu veículo:</label>
                                    <input type="text" id="tradeVehicleModel" placeholder="Ex: Honda Civic 2018" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; margin-bottom: 5px;">Quilometragem:</label>
                                    <input type="number" id="tradeVehicleKm" placeholder="Ex: 50000" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; margin-bottom: 5px;">Fotos do veículo (múltiplas):</label>
                                    <input type="file" id="tradeVehiclePhotos" multiple accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <button type="button" onclick="avaliarVeiculo()" style="background: #ffc107; color: #212529; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Solicitar Avaliação</button>
                                <div id="trade-value-result" style="margin-top: 15px; display: none; padding: 10px; background: #d4edda; border-radius: 4px; color: #155724;"></div>
                            </div>
                        </div>
                        
                        <form id="simulation-form" onsubmit="event.preventDefault(); calcularFinanciamento();" style="margin-bottom: 20px;">
                            <div id="financing-options" class="financing-section">
                                <div class="filtro-grupo" style="margin-bottom: 20px;">
                                    <label for="entradaValor" style="margin-bottom: 8px; display: block;">Valor de Entrada:</label>
                                    <input type="number" id="entradaValor" value="0" min="0" required style="padding: 12px 14px; border: 2px solid #e1e8ed; border-radius: 12px; font-size: 14px; width: 100%; box-sizing: border-box;">
                                </div>

                                <div class="filtro-grupo" style="margin-bottom: 20px;">
                                    <label for="tempoMeses" style="margin-bottom: 8px; display: block;">Prazo (Meses):</label>
                                    <select id="tempoMeses" required style="padding: 12px 14px; border: 2px solid #e1e8ed; border-radius: 12px; font-size: 14px; width: 100%; background: white; box-sizing: border-box;">
                                        <option value="12">12x</option>
                                        <option value="24">24x</option>
                                        <option value="36">36x</option>
                                        <option value="48">48x</option>
                                        <option value="60">60x</option>
                                    </select>
                                </div>

                                <div class="filtro-grupo" style="margin-bottom: 25px;">
                                    <label for="taxaJuros" style="margin-bottom: 8px; display: block;">Taxa de Juros (% a.m.):</label>
                                    <input type="number" id="taxaJuros" value="1.5" step="0.01" min="0.5" required style="padding: 12px 14px; border: 2px solid #e1e8ed; border-radius: 12px; font-size: 14px; width: 100%; box-sizing: border-box;">
                                </div>
                            
                            <div class="purchase-actions" style="margin-bottom: 20px;">
                                <button type="submit" id="btn-simulate" class="btn-simulate" style="margin-right: 10px;">Simular Financiamento</button>
                                <button type="button" class="btn-purchase" onclick="purchaseCar()">Solicitar Proposta</button>
                                <button type="button" class="btn-inspection" onclick="solicitarInspecao()" style="background: #17a2b8; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-left: 10px;">Relatório de Inspeção</button>
                            </div>
                        </form>
                        
                        <div id="resultado" style="margin-top: 20px; display: none; background: linear-gradient(135deg, #e8f5e8, #f0f8f0); padding: 15px; border-radius: 8px;">
                            <h3 style="margin: 0 0 10px; color: #2c3e50;">Resultado da Simulação</h3>
                            <p id="parcelaMensal" style="font-size: 1.5em; color: #27ae60; margin: 5px 0;">Parcela: R$ 0,00</p>
                            <p id="totalFinanciado" style="font-size: 1em; color: #666; margin: 5px 0;">Total Financiado: R$ 0,00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<script>
    // --- JS: RECEBE DADOS DO PHP E INICIALIZA TUDO ---
    const CARROS_DATA = <?php echo json_encode($listaVeiculos); ?>;
    let veiculosFiltrados = [...CARROS_DATA];
    let currentCarData = null; // Armazena o carro selecionado para o cálculo

    function formatarMoeda(valor) {
        return parseFloat(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    // Função que desenha os cards na grade
    function carregarVeiculos() {
        const grid = document.getElementById('cars-grid');
        const countInfo = document.getElementById('car-count-info');
        grid.innerHTML = '';
        
        if (veiculosFiltrados.length === 0) {
            grid.innerHTML = '<p style="text-align:center; width:100%; padding:50px;">Nenhum veículo encontrado para simulação.</p>';
            countInfo.textContent = 'Nenhum veículo encontrado';
            return;
        }
        
        // Atualiza contador
        countInfo.textContent = `Exibindo ${veiculosFiltrados.length} veículo(s) disponível(is) para financiamento`;

        veiculosFiltrados.forEach(veiculo => {
            const card = document.createElement('div');
            card.className = 'car-card';
            
            // Onclick abre o modal e configura os dados
            card.onclick = () => openCarPopup(veiculo);
            card.style.cursor = 'pointer'; 
            
            let imagemCapa = veiculo.foto || `${veiculo.pasta}/1.webp`;
            let precoAntigo = formatarMoeda(parseFloat(veiculo.preco) * 1.25); // 25% de desconto p/ visual
            
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

    // Variáveis para controle de imagens 360°
    let currentImageIndex = 1;
    let totalImages = 1;
    let imageBasePath = '';
    
    // Abre o modal e preenche os campos do formulário com o carro selecionado
    function openCarPopup(carData) {
        currentCarData = carData; // Salva o objeto globalmente
        const popup = document.getElementById('car-popup');
        
        // Preenche dados visuais do modal
        document.getElementById('popup-title').textContent = `${carData.marca} ${carData.modelo}`;
        document.getElementById('popup-model').textContent = carData.modelo;
        document.getElementById('popup-year').textContent = carData.ano;
        document.getElementById('popup-price').textContent = formatarMoeda(carData.preco);
        
        // Calcula e exibe preço com desconto à vista
        const precoDesconto = parseFloat(carData.preco) * 0.85; // 15% desconto
        document.getElementById('debit-price').textContent = `Preço à vista: ${formatarMoeda(precoDesconto)}`;
        
        // Configura imagens 360°
        currentImageIndex = 1;
        totalImages = 31; // Assumindo 31 imagens para visualização 360°
        imageBasePath = carData.pasta || carData.modelo.toLowerCase().replace(/\s+/g, '-');
        
        // Atualiza contador de imagens
        document.getElementById('current-img').textContent = currentImageIndex;
        document.getElementById('total-imgs').textContent = totalImages;
        
        // Preenche o valor de entrada mínimo (10%)
        const entradaMinima = parseFloat(carData.preco) * 0.10;
        document.getElementById('entradaValor').min = entradaMinima.toFixed(2);
        document.getElementById('entradaValor').value = entradaMinima.toFixed(0); 

        // Imagem inicial
        updateCarImage();
        
        // Esconde resultado anterior
        document.getElementById('resultado').style.display = 'none';
        
        // Reset forms
        document.querySelector('input[name="paymentType"][value="financing"]').checked = true;
        document.getElementById('hasTradeIn').checked = false;
        document.getElementById('trade-in-form').style.display = 'none';
        togglePaymentType();

        popup.style.display = 'flex';
    }
    
    // Função para atualizar a imagem do carro
    function updateCarImage() {
        const imgElement = document.getElementById('popup-carimg');
        const imagePath = `${imageBasePath}/${currentImageIndex}.webp`;
        
        imgElement.src = imagePath;
        imgElement.onerror = function() {
            // Se a imagem não existir, usa a foto padrão ou primeira imagem
            this.src = currentCarData.foto || `${imageBasePath}/1.webp`;
        };
        
        document.getElementById('current-img').textContent = currentImageIndex;
    }

    // Fórmula de Cálculo (já validada)
    function calcularFinanciamento() {
        if (!currentCarData) return alert('Selecione um veículo.');
        
        const paymentType = document.querySelector('input[name="paymentType"]:checked').value;
        
        if (paymentType === 'debit') {
            const precoDesconto = parseFloat(currentCarData.preco) * 0.85;
            document.getElementById('parcelaMensal').textContent = `Pagamento à vista: ${formatarMoeda(precoDesconto)}`;
            document.getElementById('totalFinanciado').textContent = `Economia de 15%: ${formatarMoeda(parseFloat(currentCarData.preco) - precoDesconto)}`;
            document.getElementById('resultado').style.display = 'block';
            return;
        }

        const precoBase = parseFloat(currentCarData.preco);
        const entrada = parseFloat(document.getElementById('entradaValor').value);
        const taxaMensal = parseFloat(document.getElementById('taxaJuros').value) / 100; // Taxa mensal
        const prazo = parseInt(document.getElementById('tempoMeses').value);
        const entradaMinima = precoBase * 0.10;
        
        if (entrada < entradaMinima) {
            alert(`O valor de entrada deve ser de pelo menos ${formatarMoeda(entradaMinima)}.`);
            return;
        }

        const valorFinanciado = precoBase - entrada;

        // Fórmula Tabela Price: PMT = P * i / [1 - (1 + i)^-n]
        const parcela = valorFinanciado * taxaMensal / (1 - Math.pow(1 + taxaMensal, -prazo));

        document.getElementById('parcelaMensal').textContent = formatarMoeda(parcela);
        document.getElementById('totalFinanciado').textContent = `Total Financiado: ${formatarMoeda(valorFinanciado)}`;
        document.getElementById('resultado').style.display = 'block';
    }
    
    function closeCarPopup() { document.getElementById('car-popup').style.display = 'none'; }
    
    // Funções para controles de imagem
    function previousImage() {
        if (currentImageIndex > 1) {
            currentImageIndex--;
        } else {
            currentImageIndex = totalImages; // Volta para a última imagem
        }
        updateCarImage();
    }
    
    function nextImage() {
        if (currentImageIndex < totalImages) {
            currentImageIndex++;
        } else {
            currentImageIndex = 1; // Volta para a primeira imagem
        }
        updateCarImage();
    }
    
    async function purchaseCar() {
        if (!currentCarData) return alert('Selecione um veículo.');
        
        let nome, email, telefone;
        
        // Verifica se usuário está logado (verifica se existe sessão)
        <?php if (isset($_SESSION['usuario_logado'])): ?>
            // Usuário logado - usa dados da sessão
            nome = '<?php echo $_SESSION['usuario_logado']['nome'] ?? ''; ?>';
            email = '<?php echo $_SESSION['usuario_logado']['email'] ?? ''; ?>';
            telefone = '<?php echo $_SESSION['usuario_logado']['telefone'] ?? ''; ?>';
            
            if (!confirm(`Confirmar proposta para:\nNome: ${nome}\nE-mail: ${email}\nTelefone: ${telefone}`)) {
                return;
            }
        <?php else: ?>
            // Usuário não logado - solicita dados
            nome = prompt('Nome:');
            email = prompt('E-mail:');
            telefone = prompt('Telefone:');
            
            if (!nome || !email) {
                alert('Nome e e-mail são obrigatórios!');
                return;
            }
        <?php endif; ?>
        
        const paymentType = document.querySelector('input[name="paymentType"]:checked').value;
        const entrada = parseFloat(document.getElementById('entradaValor').value) || 0;
        const prazo = parseInt(document.getElementById('tempoMeses').value) || null;
        const taxa = parseFloat(document.getElementById('taxaJuros').value) || null;
        
        let valorTotal, valorParcela = null;
        
        if (paymentType === 'debit') {
            valorTotal = parseFloat(currentCarData.preco) * 0.85;
        } else {
            valorTotal = parseFloat(currentCarData.preco);
            if (prazo && taxa) {
                const valorFinanciado = valorTotal - entrada;
                const taxaMensal = taxa / 100;
                valorParcela = valorFinanciado * taxaMensal / (1 - Math.pow(1 + taxaMensal, -prazo));
            }
        }
        
        const dados = {
            action: 'salvar_proposta',
            dados: {
                veiculo_id: currentCarData.id,
                cliente_nome: nome,
                cliente_email: email,
                cliente_telefone: telefone,
                tipo_pagamento: paymentType,
                valor_entrada: entrada,
                prazo_meses: prazo,
                taxa_juros: taxa,
                valor_parcela: valorParcela,
                valor_total: valorTotal
            }
        };
        
        try {
            const formData = new FormData();
            formData.append('action', 'salvar_proposta');
            Object.keys(dados.dados).forEach(key => {
                formData.append(key, dados.dados[key]);
            });
            
            const response = await fetch('financeamento.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(`Proposta #${result.id} salva com sucesso! Nossa equipe entrará em contato.`);
            } else {
                alert('Erro: ' + result.error);
            }
        } catch (error) {
            alert('Erro ao enviar proposta: ' + error.message);
        }
    }
    
    function togglePaymentType() {
        const paymentType = document.querySelector('input[name="paymentType"]:checked').value;
        const financingSection = document.getElementById('financing-options');
        const debitPrice = document.getElementById('debit-price');
        const btnSimulate = document.getElementById('btn-simulate');
        
        if (paymentType === 'debit') {
            financingSection.style.display = 'none';
            debitPrice.style.display = 'block';
            btnSimulate.style.display = 'none';
        } else {
            financingSection.style.display = 'block';
            debitPrice.style.display = 'none';
            btnSimulate.style.display = 'inline-block';
        }
    }
    
    async function avaliarVeiculo() {
        const modelo = document.getElementById('tradeVehicleModel').value;
        const km = document.getElementById('tradeVehicleKm').value;
        const fotos = document.getElementById('tradeVehiclePhotos').files;
        
        if (!modelo || !km || fotos.length === 0) {
            alert('Por favor, preencha todos os campos e adicione pelo menos uma foto.');
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('action', 'avaliar_veiculo');
            formData.append('modelo', modelo);
            formData.append('quilometragem', km);
            
            const response = await fetch('financeamento.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                const valorEstimado = result.valor_estimado;
                
                document.getElementById('trade-value-result').innerHTML = `
                    <strong>Avaliação Estimada:</strong> ${formatarMoeda(valorEstimado)}<br>
                    <small>*Valor sujeito a inspeção presencial. Nossa equipe entrará em contato em até 24h.</small>
                `;
                document.getElementById('trade-value-result').style.display = 'block';
                
                document.getElementById('entradaValor').value = valorEstimado;
            } else {
                alert('Erro na avaliação: ' + result.error);
            }
        } catch (error) {
            alert('Erro ao avaliar veículo: ' + error.message);
        }
    }
    
    async function solicitarInspecao() {
        if (!currentCarData) return alert('Selecione um veículo.');
        
        const inspecaoInfo = `
RELATÓRIO DE INSPEÇÃO PRÉ-COMPRA

Veículo: ${currentCarData.marca} ${currentCarData.modelo} ${currentCarData.ano}
Preço: ${formatarMoeda(currentCarData.preco)}

Nossa inspeção inclui:
✓ Verificação completa do motor
✓ Sistema de freios e suspensão
✓ Parte elétrica e eletrônica
✓ Carroceria e pintura
✓ Documentação e histórico
✓ Teste de estrada

Tempo estimado: 2-3 horas
Custo: R$ 150,00 (deduzido do valor final se efetuar a compra)

Deseja agendar a inspeção?`;
        
        if (confirm(inspecaoInfo)) {
            let nome, email, telefone;
            
            <?php if (isset($_SESSION['usuario_logado'])): ?>
                nome = '<?php echo $_SESSION['usuario_logado']['nome'] ?? ''; ?>';
                email = '<?php echo $_SESSION['usuario_logado']['email'] ?? ''; ?>';
                telefone = '<?php echo $_SESSION['usuario_logado']['telefone'] ?? ''; ?>';
            <?php else: ?>
                nome = prompt('Nome:');
                email = prompt('E-mail:');
                telefone = prompt('Telefone:');
                if (!nome || !email) {
                    alert('Nome e e-mail são obrigatórios!');
                    return;
                }
            <?php endif; ?>
            
            const data = prompt('Data preferida (YYYY-MM-DD):');
            const hora = prompt('Horário preferido (HH:MM):');
            
            if (!data || !hora) {
                alert('Data e horário são obrigatórios!');
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('action', 'agendar_inspecao');
                formData.append('veiculo_id', currentCarData.id);
                formData.append('cliente_nome', nome);
                formData.append('cliente_email', email);
                formData.append('cliente_telefone', telefone);
                formData.append('data_agendada', data);
                formData.append('hora_agendada', hora);
                
                const response = await fetch('financeamento.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(`Inspeção #${result.id} agendada com sucesso! Nossa equipe entrará em contato.`);
                } else {
                    alert('Erro: ' + result.error);
                }
            } catch (error) {
                alert('Erro ao agendar inspeção: ' + error.message);
            }
        }
    }
    
    // Funções de filtro (implementação básica)
    function aplicarFiltros() {
        const filtroPreco = document.getElementById('filtroPreco').value;
        const filtroCategoria = document.getElementById('filtroCategoria').value;
        
        veiculosFiltrados = [...CARROS_DATA];
        
        // Filtro por preço
        if (filtroPreco) {
            if (filtroPreco === '0-50000') {
                veiculosFiltrados = veiculosFiltrados.filter(v => parseFloat(v.preco) <= 50000);
            } else if (filtroPreco === '50000-100000') {
                veiculosFiltrados = veiculosFiltrados.filter(v => parseFloat(v.preco) > 50000 && parseFloat(v.preco) <= 100000);
            } else if (filtroPreco === '100000-150000') {
                veiculosFiltrados = veiculosFiltrados.filter(v => parseFloat(v.preco) > 100000 && parseFloat(v.preco) <= 150000);
            } else if (filtroPreco === '150000+') {
                veiculosFiltrados = veiculosFiltrados.filter(v => parseFloat(v.preco) > 150000);
            }
        }
        
        // Filtro por categoria (baseado no tipo e marca do veículo)
        if (filtroCategoria) {
            veiculosFiltrados = veiculosFiltrados.filter(v => {
                const tipo = (v.tipo || '').toLowerCase();
                const marca = (v.marca || '').toLowerCase();
                const modelo = (v.modelo || '').toLowerCase();
                const categoria = filtroCategoria.toLowerCase();
                
                switch(categoria) {
                    case 'hatch':
                        return tipo.includes('hatch') || modelo.includes('hatch') || 
                               modelo.includes('polo') || modelo.includes('gol') || modelo.includes('onix');
                    case 'sedan':
                        return tipo.includes('sedan') || modelo.includes('sedan') || 
                               modelo.includes('civic') || modelo.includes('corolla') || modelo.includes('jetta');
                    case 'suv':
                        return tipo.includes('suv') || modelo.includes('suv') || 
                               modelo.includes('hr-v') || modelo.includes('tiguan') || modelo.includes('x4');
                    case 'pickup':
                        return tipo.includes('pickup') || modelo.includes('pickup') || 
                               modelo.includes('amarok') || modelo.includes('hilux') || modelo.includes('ranger');
                    case 'premium':
                        return marca.includes('bmw') || marca.includes('mercedes') || marca.includes('audi') || 
                               marca.includes('lexus') || marca.includes('volvo');
                    default:
                        return tipo.includes(categoria) || marca.includes(categoria) || modelo.includes(categoria);
                }
            });
        }
        
        carregarVeiculos();
    }
    
    function limparFiltros() {
        document.getElementById('filtroPreco').value = '';
        document.getElementById('filtroCategoria').value = '';
        veiculosFiltrados = [...CARROS_DATA];
        carregarVeiculos();
    } 
    
    // Inicialização ao carregar a página
    document.addEventListener('DOMContentLoaded', () => {
        carregarVeiculos(); // Desenha o grid inicial
        
        // Event listeners para opções de pagamento
        document.querySelectorAll('input[name="paymentType"]').forEach(radio => {
            radio.addEventListener('change', togglePaymentType);
        });
        
        // Event listener para checkbox de veículo de entrada
        document.getElementById('hasTradeIn').addEventListener('change', function() {
            const tradeForm = document.getElementById('trade-in-form');
            tradeForm.style.display = this.checked ? 'block' : 'none';
        });
        
        // Adiciona navegação por teclado no modal
        document.addEventListener('keydown', (e) => {
            const modal = document.getElementById('car-popup');
            if (modal.style.display === 'flex') {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    previousImage();
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    nextImage();
                } else if (e.key === 'Escape') {
                    closeCarPopup();
                }
            }
        });
    });

</script>

<?php include 'views/partials/footer.php'; ?>