<?php

class ValidationService {
    
    /**
     * Valida se os termos de uso e consentimento foram aceitos
     */
    public static function validarTermosAceitos($dadosPost) {
        if (!isset($dadosPost['aceitar_termos']) || $dadosPost['aceitar_termos'] !== '1') {
            return [
                'valido' => false,
                'mensagem' => 'Você deve aceitar os Termos de Uso para se registrar!'
            ];
        }
        
        if (!isset($dadosPost['aceitar_consentimento']) || $dadosPost['aceitar_consentimento'] !== '1') {
            return [
                'valido' => false,
                'mensagem' => 'Você deve aceitar os Termos de Consentimento para se registrar!'
            ];
        }
        
        return [
            'valido' => true,
            'mensagem' => 'Termos aceitos com sucesso'
        ];
    }
    
    /**
     * Valida se as senhas coincidem
     */
    public static function validarSenhas($senha, $repetirSenha) {
        if ($senha !== $repetirSenha) {
            return [
                'valido' => false,
                'mensagem' => 'As senhas não conferem!'
            ];
        }
        
        if (strlen($senha) < 6) {
            return [
                'valido' => false,
                'mensagem' => 'A senha deve ter pelo menos 6 caracteres!'
            ];
        }
        
        return [
            'valido' => true,
            'mensagem' => 'Senhas válidas'
        ];
    }
    
    /**
     * Valida redefinição de senha
     */
    public static function validarRedefinicaoSenha($senhaAtual, $novaSenha, $confirmarSenha, $senhaHashBanco) {
        // Verificar se senha atual está correta
        if (!password_verify($senhaAtual, $senhaHashBanco)) {
            return [
                'valido' => false,
                'mensagem' => 'Senha atual incorreta!'
            ];
        }
        
        // Validar nova senha
        $validacaoNovasSenhas = self::validarSenhas($novaSenha, $confirmarSenha);
        if (!$validacaoNovasSenhas['valido']) {
            return $validacaoNovasSenhas;
        }
        
        // Verificar se nova senha é diferente da atual
        if (password_verify($novaSenha, $senhaHashBanco)) {
            return [
                'valido' => false,
                'mensagem' => 'A nova senha deve ser diferente da senha atual!'
            ];
        }
        
        return [
            'valido' => true,
            'mensagem' => 'Nova senha válida'
        ];
    }
    
    /**
     * Validação completa para registro de usuário
     */
    public static function validarRegistroCompleto($dadosPost) {
        // Validar senhas
        $validacaoSenhas = self::validarSenhas($dadosPost['senha'], $dadosPost['repetir_senha']);
        if (!$validacaoSenhas['valido']) {
            return $validacaoSenhas;
        }
        
        // Validar termos
        $validacaoTermos = self::validarTermosAceitos($dadosPost);
        if (!$validacaoTermos['valido']) {
            return $validacaoTermos;
        }
        
        return [
            'valido' => true,
            'mensagem' => 'Todos os dados são válidos'
        ];
    }
}

?>