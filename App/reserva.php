<?php
session_start();
require_once 'controllers/EstoqueController.php'; 
require_once 'repositories/VeiculoRepository.php'; 
require_once 'config/Database.php';

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

<link rel="stylesheet" href="reserva.css">

<main class="container">
    <section class="filtros">
        <div class="filtro-grupo">
            <label for="dataRetirada">📅 Data de Retirada:</label>
            <input type="date" id="dataRetirada" class="date-input">
            <small class="date-help">Selecione quando deseja retirar o veículo</small>
        </div>
        <div class="filtro-grupo">
            <label for="dataDevolucao">📅 Data de Devolução:</label>
            <input type="date" id="dataDevolucao" class="date-input">
            <small class="date-help">Selecione quando deseja devolver o veículo</small>
        </div>
        <div class="filtro-grupo">
            <label>⏱️ Período:</label>
            <div class="periodo-info">
                <span id="diasSelecionados">Selecione as datas</span>
            </div>
        </div>
        <button class="btn-limpar" onclick="limparDatas()">🗑️ Limpar</button>
    </section>

    <section id="reserva-grid" class="reserva">
        <!-- Veículos serão carregados via JavaScript -->
    </section>
</main>

<!-- Modal de Reserva -->
<div id="modalReserva" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal()">&times;</span>
        <h2>Finalizar Reserva</h2>
        
        <form id="formReserva" onsubmit="enviarReserva(event)">
            <div class="resumo-veiculo">
                <h3 id="veiculoSelecionado"></h3>
                <p id="periodoReserva"></p>
                <p id="valorTotal" class="valor-destaque"></p>
            </div>

            <div class="form-grupo">
                <label for="nome">Nome Completo *</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="form-grupo">
                <label for="email">E-mail *</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-grupo">
                <label for="telefone">Telefone *</label>
                <input type="tel" id="telefone" name="telefone" required>
            </div>

            <div class="form-grupo">
                <label for="cpf">CPF *</label>
                <input type="text" id="cpf" name="cpf" required maxlength="14" placeholder="000.000.000-00">
            </div>

            <div class="form-grupo">
                <label for="cnh">CNH *</label>
                <input type="text" id="cnh" name="cnh" required>
            </div>

            <div class="form-grupo checkbox">
                <input type="checkbox" id="termos" name="termos" required>
                <label for="termos">Aceito os termos e condições *</label>
            </div>

            <div class="form-acoes">
                <button type="button" class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
                <button type="submit" class="btn-confirmar">Confirmar Reserva</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Sucesso -->
<div id="modalSucesso" class="modal">
    <div class="modal-content sucesso">
        <div class="icone-sucesso">✅</div>
        <h2>Reserva Confirmada!</h2>
        <p>Sua reserva foi realizada com sucesso.</p>
        <p><strong>Código da reserva:</strong> <span id="codigoReserva"></span></p>
        <button class="btn-ok" onclick="fecharModalSucesso()">OK</button>
    </div>
</div>

<script>
    // Dados dos veículos do PHP
    const VEICULOS_DATA = <?php echo json_encode($listaVeiculos); ?>;
    let veiculoSelecionado = null;
    let diasReserva = 0;

    // Função para formatar moeda
    function formatarMoeda(valor) {
        return parseFloat(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    // Função para calcular valor diário baseado no preço do veículo
    function calcularValorDiario(preco) {
        const precoBase = parseFloat(preco);
        // Valor diário = 0.5% do preço do veículo (mínimo R$ 40)
        return Math.max(40, precoBase * 0.005);
    }

    // Carrega os veículos na grade
    function carregarVeiculos() {
        const grid = document.getElementById('reserva-grid');
        grid.innerHTML = '';

        if (VEICULOS_DATA.length === 0) {
            grid.innerHTML = '<p style="text-align:center; width:100%; padding:50px;">Nenhum veículo disponível para reserva.</p>';
            return;
        }

        VEICULOS_DATA.forEach(veiculo => {
            const valorDiario = calcularValorDiario(veiculo.preco);
            const imagemCapa = veiculo.foto || `${veiculo.pasta}/1.webp`;
            
            const card = document.createElement('article');
            card.className = 'card';
            card.innerHTML = `
                <div class="img">
                    <img src="${imagemCapa}" alt="${veiculo.marca} ${veiculo.modelo}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                    <div class="placeholder">🚗 ${veiculo.modelo.toUpperCase()}</div>
                </div>
                <h2>${veiculo.marca} ${veiculo.modelo}</h2>
                <p class="specs">• Ar condicionado • Direção elétrica • ${veiculo.ano} • ${veiculo.combustivel || 'Flex'}</p>
                <p class="preco">R$ <span class="valor-diario">${valorDiario.toFixed(0)}</span>/dia</p>
                <p class="total">Total: R$ <span class="valor-total">0</span></p>
                <button class="cta" onclick="abrirReserva('${veiculo.marca} ${veiculo.modelo}', ${valorDiario}, ${veiculo.id})">Reserve Agora</button>
            `;
            grid.appendChild(card);
        });
    }

    // Atualiza os totais quando as datas mudam
    function atualizarTotais() {
        const dataRetirada = document.getElementById('dataRetirada').value;
        const dataDevolucao = document.getElementById('dataDevolucao').value;
        
        if (dataRetirada && dataDevolucao) {
            const inicio = new Date(dataRetirada);
            const fim = new Date(dataDevolucao);
            const diffTime = Math.abs(fim - inicio);
            diasReserva = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diasReserva > 0) {
                document.getElementById('diasSelecionados').textContent = `${diasReserva} dia(s) selecionado(s)`;
                
                // Atualiza todos os totais dos cards
                document.querySelectorAll('.card').forEach(card => {
                    const valorDiario = parseFloat(card.querySelector('.valor-diario').textContent);
                    const valorTotal = valorDiario * diasReserva;
                    card.querySelector('.valor-total').textContent = valorTotal.toFixed(0);
                });
            } else {
                document.getElementById('diasSelecionados').textContent = 'Datas inválidas';
            }
        } else {
            document.getElementById('diasSelecionados').textContent = 'Selecione as datas';
            diasReserva = 0;
            document.querySelectorAll('.valor-total').forEach(el => el.textContent = '0');
        }
    }

    // Abre modal de reserva
    function abrirReserva(nomeVeiculo, valorDiario, veiculoId) {
        if (diasReserva <= 0) {
            alert('Por favor, selecione as datas de retirada e devolução primeiro.');
            return;
        }

        veiculoSelecionado = { nome: nomeVeiculo, valorDiario, veiculoId };
        const valorTotal = valorDiario * diasReserva;
        
        document.getElementById('veiculoSelecionado').textContent = nomeVeiculo;
        document.getElementById('periodoReserva').textContent = `${diasReserva} dia(s) - ${document.getElementById('dataRetirada').value} a ${document.getElementById('dataDevolucao').value}`;
        document.getElementById('valorTotal').textContent = `Total: ${formatarMoeda(valorTotal)}`;
        
        document.getElementById('modalReserva').style.display = 'block';
    }

    // Fecha modal
    function fecharModal() {
        document.getElementById('modalReserva').style.display = 'none';
    }

    // Fecha modal de sucesso
    function fecharModalSucesso() {
        document.getElementById('modalSucesso').style.display = 'none';
        location.reload(); // Recarrega a página
    }

    // Envia reserva
    function enviarReserva(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        formData.append('veiculo_id', veiculoSelecionado.veiculoId);
        formData.append('data_retirada', document.getElementById('dataRetirada').value);
        formData.append('data_devolucao', document.getElementById('dataDevolucao').value);
        formData.append('dias', diasReserva);
        formData.append('valor_total', veiculoSelecionado.valorDiario * diasReserva);
        
        fetch('processa_reserva.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                document.getElementById('codigoReserva').textContent = data.codigo || 'RES' + Date.now().toString().slice(-6);
                document.getElementById('modalReserva').style.display = 'none';
                document.getElementById('modalSucesso').style.display = 'block';
            } else {
                alert(data.mensagem || 'Erro ao processar reserva');
            }
        })
        .catch(() => alert('Erro ao enviar reserva'));
    }

    // Limpa as datas
    function limparDatas() {
        document.getElementById('dataRetirada').value = '';
        document.getElementById('dataDevolucao').value = '';
        atualizarTotais();
    }

    // Máscara para CPF
    function mascaraCPF(input) {
        let valor = input.value.replace(/\D/g, '');
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        input.value = valor;
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', () => {
        carregarVeiculos();
        
        // Define data mínima como hoje
        const hoje = new Date().toISOString().split('T')[0];
        document.getElementById('dataRetirada').min = hoje;
        document.getElementById('dataDevolucao').min = hoje;
        
        // Listeners para mudança de data
        document.getElementById('dataRetirada').addEventListener('change', atualizarTotais);
        document.getElementById('dataDevolucao').addEventListener('change', atualizarTotais);
        
        // Máscara CPF
        document.getElementById('cpf').addEventListener('input', function() {
            mascaraCPF(this);
        });
        
        // Fecha modal ao clicar fora
        window.addEventListener('click', (event) => {
            const modalReserva = document.getElementById('modalReserva');
            const modalSucesso = document.getElementById('modalSucesso');
            if (event.target === modalReserva) {
                fecharModal();
            }
            if (event.target === modalSucesso) {
                fecharModalSucesso();
            }
        });
    });
</script>

<?php include 'views/partials/footer.php'; ?>