<div id="login"> <h1>Registro</h1>
    
    <div id="baseLogin">
        <form action="conta.php?acao=registrar" method="POST">
            
            <div class="input-container">
                <label>Nome completo</label>
                <div><input type="text" name="nome" required></div>
                <br>

                <label>Data de nascimento</label>
                <div id="container-flex">
                    <input type="date" name="nascimento" id="date1" required/>        
                </div>
                <br>

                <label>E-mail</label>
                <div><input type="email" name="email" required></div>
                <br>

                <label>Telefone</label>
                <div><input type="tel" name="telefone" required></div>
                <br>

                <label>Senha</label>
                <div><input type="password" name="senha" required></div>
                <br>

                <label>Repetir senha</label>
                <div><input type="password" name="repetir_senha" required></div>
                <br>
            </div>

            <div><input type="submit" value="Cadastrar" id="botao"></div>
        
        </form>
    </div>
</div>


<div id="registro"> <h1>Login</h1>
    
    <div id="baseRegistro">
        <form action="conta.php?acao=login" method="POST">
            
            <div class="input-container">
                <label>E-mail</label>
                <div><input type="email" name="email" required></div>
                <br>

                <label>Senha</label>
                <div><input type="password" name="senha" required></div>
                <br>
            </div>

            <div><input type="submit" value="Entrar" id="botao"></div>

        </form>
    </div>
</div>