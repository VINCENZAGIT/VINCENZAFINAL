<?php

class EmailService {
    
    /**
     * Envia email usando mail() do PHP
     */
    public static function enviarEmail($destinatario, $assunto, $mensagem, $remetente = 'noreply@vincenza.com') {
        $headers = [
            'From: ' . $remetente,
            'Reply-To: ' . $remetente,
            'Content-Type: text/html; charset=UTF-8',
            'MIME-Version: 1.0'
        ];
        
        $resultado = mail($destinatario, $assunto, $mensagem, implode("\r\n", $headers));
        
        return [
            'sucesso' => $resultado,
            'mensagem' => $resultado ? 'Email enviado com sucesso' : 'Erro ao enviar email'
        ];
    }
    
    /**
     * Template para email de boas-vindas
     */
    public static function emailBoasVindas($nomeUsuario, $emailUsuario) {
        $assunto = 'Bem-vindo à VINCENZA!';
        $mensagem = "
        <html>
        <body>
            <h2>Bem-vindo à VINCENZA, {$nomeUsuario}!</h2>
            <p>Sua conta foi criada com sucesso.</p>
            <p>Agora você pode:</p>
            <ul>
                <li>Navegar pelo nosso catálogo de veículos</li>
                <li>Fazer simulações de financiamento</li>
                <li>Reservar test drives</li>
            </ul>
            <p>Atenciosamente,<br>Equipe VINCENZA</p>
        </body>
        </html>";
        
        return self::enviarEmail($emailUsuario, $assunto, $mensagem);
    }
    
    /**
     * Template para email promocional
     */
    public static function emailPromocional($nomeUsuario, $emailUsuario, $tipoPromocao) {
        $assuntos = [
            'ofertas' => 'Ofertas Especiais VINCENZA!',
            'novidades' => 'Novos Veículos Chegaram!',
            'financiamento' => 'Condições Especiais de Financiamento!'
        ];
        
        $assunto = $assuntos[$tipoPromocao] ?? 'Novidades VINCENZA';
        
        $mensagem = "
        <html>
        <body>
            <h2>Olá, {$nomeUsuario}!</h2>
            <p>Temos novidades especiais para você na VINCENZA!</p>
            <p>Acesse nosso site e confira as melhores ofertas.</p>
            <p>Atenciosamente,<br>Equipe VINCENZA</p>
        </body>
        </html>";
        
        return self::enviarEmail($emailUsuario, $assunto, $mensagem);
    }
}

?>