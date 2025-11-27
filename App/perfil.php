<?php
session_start();
require_once 'repositories/UsuarioRepository.php';
require_once 'config/Database.php';

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $repo = new UsuarioRepository($db);
    
    require_once 'repositories/EmailRepository.php';
    $emailRepo = new EmailRepository($db);
    
    // Buscar dados do usuário logado
    $usuario = $repo->buscarPorEmail($_SESSION['usuario_email']);
    
    if (!$usuario) {
        session_destroy();
        header('Location: index.php');
        exit;
    }
    
    // Buscar preferências de email
    $preferenciasEmail = $emailRepo->buscarPreferencias($usuario->id);
    
    // Debug - verificar dados carregados
    file_put_contents('debug.log', "Preferências do banco: " . print_r($preferenciasEmail, true) . "\n", FILE_APPEND);
    file_put_contents('debug.log', "emails_promocionais value: " . ($preferenciasEmail ? $preferenciasEmail['emails_promocionais'] : 'null') . "\n", FILE_APPEND);
    file_put_contents('debug.log', "tipos_email raw: " . ($preferenciasEmail ? $preferenciasEmail['tipos_email'] : 'null') . "\n", FILE_APPEND);
    
} catch (Exception $e) {
    $erro = "Erro ao carregar dados do usuário.";
}

include 'views/partials/header.php';
?>

<link rel="stylesheet" href="perfil.css">

<div id="perfil-container">
    <div id="perfil-header">
        <h1>Meu Perfil</h1>
    </div>

    <div id="perfil-content">
        <div id="perfil-foto-section">
            <div id="foto-container">
                <img id="foto-perfil" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150' viewBox='0 0 150 150'%3E%3Crect width='150' height='150' fill='%23cccccc'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial' font-size='16' fill='%23666666'%3EFoto%3C/text%3E%3C/svg%3E" alt="Foto do Perfil">
                <div id="foto-overlay">
                    <i class="fa-solid fa-camera"></i>
                    <span>Alterar Foto</span>
                </div>
            </div>
            <input type="file" id="upload-foto" accept="image/*" style="display: none;">
        </div>

        <div id="perfil-info-section">
            <div id="info-pessoais">
                <h2>Informações Pessoais</h2>
                <div class="info-group">
                    <div class="info-item">
                        <label>Nome Completo</label>
                        <div class="info-value">
                            <span id="nome-display"><?php echo htmlspecialchars($usuario->nome); ?></span>
                            <i class="fa-solid fa-pen edit-icon" onclick="editarCampo('nome')"></i>
                        </div>
                        <input type="text" id="nome-input" class="edit-input" style="display: none;" value="<?php echo htmlspecialchars($usuario->nome); ?>">
                    </div>

                    <div class="info-item">
                        <label>Email</label>
                        <div class="info-value">
                            <span id="email-display"><?php echo htmlspecialchars($usuario->email); ?></span>
                            <i class="fa-solid fa-pen edit-icon" onclick="editarCampo('email')"></i>
                        </div>
                        <input type="email" id="email-input" class="edit-input" style="display: none;" value="<?php echo htmlspecialchars($usuario->email); ?>">
                    </div>

                    <div class="info-item">
                        <label>Telefone</label>
                        <div class="info-value">
                            <span id="telefone-display"><?php echo htmlspecialchars($usuario->telefone); ?></span>
                            <i class="fa-solid fa-pen edit-icon" onclick="editarCampo('telefone')"></i>
                        </div>
                        <input type="tel" id="telefone-input" class="edit-input" style="display: none;" value="<?php echo htmlspecialchars($usuario->telefone); ?>">
                    </div>

                    <div class="info-item">
                        <label>Data de Nascimento</label>
                        <div class="info-value">
                            <span id="nascimento-display"><?php echo date('d/m/Y', strtotime($usuario->data_nascimento)); ?></span>
                            <i class="fa-solid fa-pen edit-icon" onclick="editarCampo('nascimento')"></i>
                        </div>
                        <input type="date" id="nascimento-input" class="edit-input" style="display: none;" value="<?php echo $usuario->data_nascimento; ?>">
                    </div>

                    <div class="info-item">
                        <label>Alterar Senha</label>
                        <div class="info-value">
                            <button class="btn-alterar-senha-inline" onclick="alterarSenha()">
                                <i class="fa-solid fa-key"></i>
                                Alterar Senha
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="info-preferencias">
                <h2>Preferências</h2>
                <div class="info-group">
                    <div class="info-item">
                        <label>Receber E-mails Promocionais</label>
                        <div class="info-value">
                            <label class="switch">
                                <input type="checkbox" id="emails-promocionais" <?php echo ($preferenciasEmail && $preferenciasEmail['emails_promocionais'] == 1) ? 'checked' : ''; ?>>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="info-item" id="tipos-email-section">
                        <label>Tipos de E-mail</label>
                        <div class="checkbox-group">
                            <?php 
                            $tiposEmailSelecionados = [];
                            if ($preferenciasEmail && !empty($preferenciasEmail['tipos_email'])) {
                                $tiposEmailSelecionados = json_decode($preferenciasEmail['tipos_email'], true) ?: [];
                            }
                            ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="tipos-email" value="ofertas" <?php echo in_array('ofertas', $tiposEmailSelecionados) ? 'checked' : ''; ?>>
                                <span class="checkmark"></span>
                                Ofertas e Promoções
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="tipos-email" value="novidades" <?php echo in_array('novidades', $tiposEmailSelecionados) ? 'checked' : ''; ?>>
                                <span class="checkmark"></span>
                                Novidades e Lançamentos
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="tipos-email" value="financiamento" <?php echo in_array('financiamento', $tiposEmailSelecionados) ? 'checked' : ''; ?>>
                                <span class="checkmark"></span>
                                Condições de Financiamento
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="tipos-email" value="eventos" <?php echo in_array('eventos', $tiposEmailSelecionados) ? 'checked' : ''; ?>>
                                <span class="checkmark"></span>
                                Eventos e Test Drives
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div id="info-feedback">
                <h2>Reclamações e Sugestões</h2>
                <div class="info-group">
                    <div class="info-item">
                        <label>Sua opinião é importante para nós</label>
                        <div class="feedback-container">
                            <textarea id="feedback-texto" placeholder="Compartilhe suas reclamações, sugestões ou elogios sobre nossos serviços..." rows="4"></textarea>
                            <div class="feedback-actions">
                                <select id="feedback-tipo">
                                    <option value="sugestao">Sugestão</option>
                                    <option value="reclamacao">Reclamação</option>
                                    <option value="elogio">Elogio</option>
                                </select>
                                <button id="btn-enviar-feedback" onclick="enviarFeedback()">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    Enviar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="perfil-actions">
        <button id="btn-salvar" onclick="salvarPerfil()">
            <i class="fa-solid fa-save"></i>
            Salvar Alterações
        </button>
    </div>
</div>

<script>
    // Dados do usuário do PHP
    const USUARIO_DATA = {
        id: <?php echo $usuario->id; ?>,
        nome: "<?php echo htmlspecialchars($usuario->nome); ?>",
        email: "<?php echo htmlspecialchars($usuario->email); ?>",
        telefone: "<?php echo htmlspecialchars($usuario->telefone); ?>",
        data_nascimento: "<?php echo $usuario->data_nascimento; ?>"
    };

    let campoEditando = null;

    // Função para editar campo
    function editarCampo(campo) {
        if (campoEditando && campoEditando !== campo) {
            cancelarEdicao(campoEditando);
        }

        const display = document.getElementById(campo + '-display');
        const input = document.getElementById(campo + '-input');
        const editIcon = display.parentElement.querySelector('.edit-icon');

        display.style.display = 'none';
        input.style.display = 'block';
        input.focus();
        editIcon.className = 'fa-solid fa-check edit-icon confirm-icon';
        editIcon.onclick = () => confirmarEdicao(campo);

        campoEditando = campo;
    }

    // Função para confirmar edição
    function confirmarEdicao(campo) {
        const display = document.getElementById(campo + '-display');
        const input = document.getElementById(campo + '-input');
        const editIcon = display.parentElement.querySelector('.edit-icon');

        let novoValor = input.value;
        
        // Formatação específica para data
        if (campo === 'nascimento') {
            const data = new Date(novoValor);
            display.textContent = data.toLocaleDateString('pt-BR');
        } else {
            display.textContent = novoValor;
        }

        display.style.display = 'inline';
        input.style.display = 'none';
        editIcon.className = 'fa-solid fa-pen edit-icon';
        editIcon.onclick = () => editarCampo(campo);

        campoEditando = null;
    }

    // Função para cancelar edição
    function cancelarEdicao(campo) {
        const display = document.getElementById(campo + '-display');
        const input = document.getElementById(campo + '-input');
        const editIcon = display.parentElement.querySelector('.edit-icon');

        display.style.display = 'inline';
        input.style.display = 'none';
        editIcon.className = 'fa-solid fa-pen edit-icon';
        editIcon.onclick = () => editarCampo(campo);

        // Restaurar valor original
        input.value = USUARIO_DATA[campo] || input.defaultValue;
    }

    // Controle de preferências de email
    document.getElementById('emails-promocionais').addEventListener('change', function() {
        const tiposSection = document.getElementById('tipos-email-section');
        const checkboxes = document.querySelectorAll('input[name="tipos-email"]');
        
        if (this.checked) {
            tiposSection.style.opacity = '1';
            tiposSection.style.pointerEvents = 'auto';
        } else {
            tiposSection.style.opacity = '0.5';
            tiposSection.style.pointerEvents = 'none';
            checkboxes.forEach(cb => cb.checked = false);
        }
    });

    // Função para salvar perfil
    function salvarPerfil() {
        const dadosAtualizados = {
            nome: document.getElementById('nome-input').value || USUARIO_DATA.nome,
            email: document.getElementById('email-input').value || USUARIO_DATA.email,
            telefone: document.getElementById('telefone-input').value || USUARIO_DATA.telefone,
            data_nascimento: document.getElementById('nascimento-input').value || USUARIO_DATA.data_nascimento,
            emails_promocionais: document.getElementById('emails-promocionais').checked,
            tipos_email: Array.from(document.querySelectorAll('input[name="tipos-email"]:checked')).map(cb => cb.value)
        };

        console.log('Dados a enviar:', dadosAtualizados);
        
        // Enviar via AJAX
        fetch('conta.php?acao=salvar_perfil', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(dadosAtualizados)
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Response text:', text);
            try {
                const data = JSON.parse(text);
                alert(data.mensagem);
                if (data.sucesso) {
                    location.reload();
                }
            } catch (e) {
                console.error('Erro ao parsear JSON:', e);
                alert('Erro na resposta do servidor');
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            alert('Erro ao salvar perfil');
        });
    }

    // Função para alterar senha
    function alterarSenha() {
        const senhaAtual = prompt('Digite sua senha atual:');
        if (!senhaAtual) return;
        
        const novaSenha = prompt('Digite a nova senha (mínimo 6 caracteres):');
        if (!novaSenha) return;
        
        const confirmarSenha = prompt('Confirme a nova senha:');
        if (!confirmarSenha) return;
        
        // Enviar via AJAX
        fetch('conta.php?acao=alterar_senha', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `senha_atual=${encodeURIComponent(senhaAtual)}&nova_senha=${encodeURIComponent(novaSenha)}&confirmar_senha=${encodeURIComponent(confirmarSenha)}`
        })
        .then(response => response.json())
        .then(data => {
            alert(data.mensagem);
            if (data.sucesso) {
                // Opcional: redirecionar para login
            }
        })
        .catch(() => alert('Erro ao alterar senha'));
    }

    // Função para enviar feedback
    function enviarFeedback() {
        const texto = document.getElementById('feedback-texto').value.trim();
        const tipo = document.getElementById('feedback-tipo').value;
        
        if (!texto) {
            alert('Por favor, escreva sua mensagem antes de enviar.');
            return;
        }
        
        fetch('conta.php?acao=enviar_feedback', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `feedback_texto=${encodeURIComponent(texto)}&feedback_tipo=${encodeURIComponent(tipo)}`
        })
        .then(response => response.text())
        .then(text => {
            console.log('Response:', text);
            try {
                const data = JSON.parse(text);
                alert(data.mensagem);
                if (data.sucesso) {
                    document.getElementById('feedback-texto').value = '';
                }
            } catch (e) {
                alert('Erro na resposta do servidor');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao enviar feedback');
        });
    }

    // Upload de foto
    document.getElementById('foto-container').addEventListener('click', function() {
        document.getElementById('upload-foto').click();
    });

    document.getElementById('upload-foto').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('foto-perfil').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<style>
/* Estilos para switch toggle */
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #85d39d;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

/* Estilos para checkboxes */
.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 10px;
}

.checkbox-item {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 14px;
}

.checkbox-item input {
    margin-right: 10px;
}

.checkmark {
    margin-right: 8px;
}

#tipos-email-section {
    transition: opacity 0.3s ease;
}

.confirm-icon {
    color: #27ae60 !important;
}

.btn-alterar-senha-inline {
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-alterar-senha-inline:hover {
    background: linear-gradient(135deg, #ee5a52, #dc4c64);
    transform: translateY(-1px);
}

/* Estilos para seção de feedback */
.feedback-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

#feedback-texto {
    width: 100%;
    padding: 12px;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    resize: vertical;
    min-height: 80px;
    transition: border-color 0.3s;
}

#feedback-texto:focus {
    outline: none;
    border-color: #85d39d;
}

.feedback-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

#feedback-tipo {
    padding: 8px 12px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background: white;
}

#btn-enviar-feedback {
    background: linear-gradient(135deg, #85d39d, #6bb77b);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 6px;
}

#btn-enviar-feedback:hover {
    background: linear-gradient(135deg, #6bb77b, #5a9a68);
    transform: translateY(-1px);
}
</style>

<?php include 'views/partials/footer.php'; ?>