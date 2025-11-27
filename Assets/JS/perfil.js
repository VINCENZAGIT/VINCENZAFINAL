// Variável para controlar qual campo está sendo editado
let campoEditando = null;

// Função para editar um campo
function editarCampo(campo) {
    // Se já está editando outro campo, cancela a edição anterior
    if (campoEditando && campoEditando !== campo) {
        cancelarEdicao(campoEditando);
    }

    const display = document.getElementById(`${campo}-display`);
    const input = document.getElementById(`${campo}-input`);
    const icon = display.parentElement.querySelector('.edit-icon');

    // Esconde o display e mostra o input
    display.style.display = 'none';
    input.style.display = 'block';
    input.value = display.textContent;
    input.focus();

    // Muda o ícone para salvar
    icon.className = 'fa-solid fa-check edit-icon';
    icon.onclick = () => salvarCampo(campo);

    campoEditando = campo;

    // Adiciona evento para salvar com Enter
    input.onkeypress = function(e) {
        if (e.key === 'Enter') {
            salvarCampo(campo);
        } else if (e.key === 'Escape') {
            cancelarEdicao(campo);
        }
    };
}

// Função para salvar um campo
function salvarCampo(campo) {
    const display = document.getElementById(`${campo}-display`);
    const input = document.getElementById(`${campo}-input`);
    const icon = display.parentElement.querySelector('.edit-icon');

    // Validação básica
    if (!input.value.trim()) {
        alert('Campo não pode estar vazio!');
        return;
    }

    // Validações específicas
    if (campo === 'email' && !validarEmail(input.value)) {
        alert('Email inválido!');
        return;
    }

    if (campo === 'cpf' && !validarCPF(input.value)) {
        alert('CPF inválido!');
        return;
    }

    // Atualiza o display
    display.textContent = input.value;
    
    // Mostra o display e esconde o input
    display.style.display = 'block';
    input.style.display = 'none';

    // Volta o ícone para editar
    icon.className = 'fa-solid fa-pen edit-icon';
    icon.onclick = () => editarCampo(campo);

    campoEditando = null;

    // Simula salvamento no servidor
    console.log(`Campo ${campo} atualizado para: ${input.value}`);
}

// Função para cancelar edição
function cancelarEdicao(campo) {
    const display = document.getElementById(`${campo}-display`);
    const input = document.getElementById(`${campo}-input`);
    const icon = display.parentElement.querySelector('.edit-icon');

    // Mostra o display e esconde o input
    display.style.display = 'block';
    input.style.display = 'none';

    // Volta o ícone para editar
    icon.className = 'fa-solid fa-pen edit-icon';
    icon.onclick = () => editarCampo(campo);

    campoEditando = null;
}

// Função para validar email
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Função para validar CPF
function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]/g, '');
    
    if (cpf.length !== 11) return false;
    
    // Verifica se todos os dígitos são iguais
    if (/^(\d)\1{10}$/.test(cpf)) return false;
    
    // Validação do primeiro dígito verificador
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let resto = 11 - (soma % 11);
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(9))) return false;
    
    // Validação do segundo dígito verificador
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    resto = 11 - (soma % 11);
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(10))) return false;
    
    return true;
}

// Função para alterar foto
document.addEventListener('DOMContentLoaded', function() {
    const fotoContainer = document.getElementById('foto-container');
    const uploadFoto = document.getElementById('upload-foto');
    const fotoPerfil = document.getElementById('foto-perfil');

    fotoContainer.addEventListener('click', function() {
        uploadFoto.click();
    });

    uploadFoto.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                fotoPerfil.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
});

// Função para salvar perfil completo
function salvarPerfil() {
    // Se há campo sendo editado, salva primeiro
    if (campoEditando) {
        salvarCampo(campoEditando);
    }

    // Coleta todos os dados do perfil
    const dadosPerfil = {
        nome: document.getElementById('nome-display').textContent,
        email: document.getElementById('email-display').textContent,
        telefone: document.getElementById('telefone-display').textContent,
        nascimento: document.getElementById('nascimento-display').textContent,
        cpf: document.getElementById('cpf-display').textContent,
        cep: document.getElementById('cep-display').textContent,
        endereco: document.getElementById('endereco-display').textContent,
        cidade: document.getElementById('cidade-display').textContent,
        estado: document.getElementById('estado-display').textContent
    };

    // Simula envio para o servidor
    console.log('Salvando perfil:', dadosPerfil);
    
    // Feedback visual
    const btnSalvar = document.getElementById('btn-salvar');
    const textoOriginal = btnSalvar.innerHTML;
    
    btnSalvar.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
    btnSalvar.disabled = true;
    
    setTimeout(() => {
        btnSalvar.innerHTML = '<i class="fa-solid fa-check"></i> Salvo!';
        setTimeout(() => {
            btnSalvar.innerHTML = textoOriginal;
            btnSalvar.disabled = false;
        }, 2000);
    }, 1500);
}

// Função para alterar senha
function alterarSenha() {
    const senhaAtual = prompt('Digite sua senha atual:');
    if (!senhaAtual) return;

    const novaSenha = prompt('Digite a nova senha:');
    if (!novaSenha) return;

    const confirmarSenha = prompt('Confirme a nova senha:');
    if (novaSenha !== confirmarSenha) {
        alert('As senhas não coincidem!');
        return;
    }

    if (novaSenha.length < 6) {
        alert('A senha deve ter pelo menos 6 caracteres!');
        return;
    }

    // Simula alteração de senha
    console.log('Alterando senha...');
    alert('Senha alterada com sucesso!');
}

// Máscara para CPF
document.addEventListener('DOMContentLoaded', function() {
    const cpfInput = document.getElementById('cpf-input');
    
    cpfInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    });
});

// Máscara para telefone
document.addEventListener('DOMContentLoaded', function() {
    const telefoneInput = document.getElementById('telefone-input');
    
    telefoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
        e.target.value = value;
    });
});

// Máscara para CEP
document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep-input');
    
    cepInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
        e.target.value = value;
    });
});

// Busca CEP automaticamente
document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep-input');
    
    cepInput.addEventListener('blur', function() {
        const cep = this.value.replace(/\D/g, '');
        
        if (cep.length === 8) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('endereco-input').value = data.logradouro;
                        document.getElementById('cidade-input').value = data.localidade;
                        document.getElementById('estado-input').value = data.uf;
                    }
                })
                .catch(error => {
                    console.log('Erro ao buscar CEP:', error);
                });
        }
    });
});