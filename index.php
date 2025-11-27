<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bakbak+One&family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Oswald:wght@200..700&family=Outfit:wght@100..900&family=Quicksand:wght@300..700&family=Staatliches&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/619d5231f4.js" crossorigin="anonymous"></script>   
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">   
    <script src="inicio.js"></script>
    <link rel="stylesheet" href="inicio.css">
    <link rel="stylesheet" href="style.css"> <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vincenza - Home</title>
</head>
<body style="margin: 0;">
    
    <div id="upperbar">
        <div id="upper1"> 
            <img id="logo" src="1000018033.png"> <p style="font-family: 'Quicksand', sans-serif"> Vincenza</p>
        </div>
        
        <div id="upper2">
            <a href="estoque.php"> <b class="lnk2" data-pt="estoque" data-en="stock">estoque</b> <span></span></a>
            
            <?php if(isset($_SESSION['usuario_nome'])): ?>
                
                <div class="user-menu">
                    <a href="perfil.php" class="user-info">
                        <i class="fa-regular fa-circle-user fa-lg"></i>
                        <span class="user-name">Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
                    </a>
                    
                    <a href="conta.php?acao=logout" class="btn-logout" title="Sair da conta">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span></span>
                    </a>
                </div>

            <?php else: ?>
                
                <a href="#" onclick="openLoginPopup()"> 
                    <b class="lnk1" data-pt="registro" data-en="register">conta</b> 
                    <span></span>
                </a>
            
            <?php endif; ?>
            <a href="catalogo.php"> <b class="lnk2" data-pt="catálogo" data-en="catalog">catálogo</b> <span></span></a>
            <a href="reserva.php"> <b class="lnk3" data-pt="reserva" data-en="booking">reserva</b> <span></span></a>
            <a href="financeamento.php"> <b class="lnk4" data-pt="financiamento" data-en="financing">financiamento</b> <span></span></a>
        </div>

        <div id="upper3">
            <div><span id="lgn" onclick="changelg()" data-pt="linguagem" data-en="language">linguagem</span> <span id="linguagemslct" style="background-image:url(Flag_of_Brazil.svg.webp); background-position: center; background-size: cover;"></span></div>
            <div><span onclick="darkmode()" id="darkmd"><span id="balldm"></span></span></div>
        </div>
    </div>

    <div id="carrossel">
        <p id="img1" onclick="passcar()" style="background-image: url(carro1.png);">
            <i class="fa-solid fa-angle-left fa-2xl" style="color: rgb(255, 255, 255); width: 100%; height: 100%; font-size: 500%; display: flex; align-items: center; justify-content: center;"></i>
        </p>
        <p id="img2" style="background-image: url(carro2.png);"></p>
        <p id="img3" onclick="backcar()" style="background-image: url(carro3.png);">
            <i class="fa-solid fa-angle-left fa-2xl" style="color: rgb(255, 255, 255); rotate: 180deg; width: 100%; height: 100%; font-size: 500%; display: flex; align-items: center; justify-content: center;"></i>
        </p>
        <p id="barpass" class="ocultarbarra" style="width: 15%; position:absolute; border: 0;"></p>
        <p id="barback" class="ocultarbarra" style="width: 15%; position:absolute; border: 0;"></p>
    </div>

    <div id="center">
        <div id="searchdiv">
            <div id="nameshow" style="width: min-content;">
                <p id="destaque" style="padding-left: 0.5vh;" data-pt="Destaque" data-en="Featured Car">Destaque</p> <span style="color: rgba(0, 0, 0, 0); width: 6vh; height: 3.2vh;"></span>
            </div>
            <div id="carrobusca">
                <p id="carroshow"></p>
                <div id="descricaoshow">
                    <div style="border: solid rgb(255, 232, 206) 1px; width: 100%; border-radius: 3px;">
                        <span id="sp1" style="width: 20%;">
                            <li id="spec-nome" data-pt="Nome" data-en="Name">Nome</li>
                            <li id="spec-marca" data-pt="Marca" data-en="Brand">Marca</li>
                            <li id="spec-cor" data-pt="Cor" data-en="Color">Cor</li>
                            <li id="spec-preco" data-pt="Preço" data-en="Price">Preço</li>
                            <li id="spec-fabricante" data-pt="Fabricante" data-en="Manufacturer">Fabricante</li>
                            <li id="spec-tipo" data-pt="Tipo" data-en="Type">Tipo</li>
                            <li id="spec-combustivel" data-pt="Combustivel" data-en="Fuel">Combustivel</li>
                        </span>
                        <span id="sp2" style="width: 80%;">
                            <li id="spec-nome2" data-pt="New Honda Civic EXL" data-en="New Honda Civic EXL">New Honda Civic EXL</li>
                            <li id="spec-marca2" data-pt="Honda" data-en="Honda">Honda</li>
                            <li id="spec-cor2" data-pt="Vermelho" data-en="Red">Vermelho</li>
                            <li id="spec-preco2" data-pt="R$ 109.900" data-en="$109,900">R$ 109.900</li>
                            <li id="spec-fabricante2" data-pt="Honda" data-en="Honda">Honda</li>
                            <li id="spec-tipo2" data-pt="Sedan" data-en="Sedan">Sedan</li>
                            <li id="spec-combustivel2" data-pt="Flex" data-en="Flex">Flex</li>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="bottonbar">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Vincenza</h4>
                <p data-pt="Sua concessionária online de confiança" data-en="Your trusted online dealership">Sua concessionária online de confiança</p>
                <p data-pt="© 2025 Vincenza. Todos os direitos reservados." data-en="© 2025 Vincenza. All rights reserved.">© 2025 Vincenza. Todos os direitos reservados.</p>
            </div>
            <div class="footer-section">
                <h4 data-pt="Links Úteis" data-en="Useful Links">Links Úteis</h4>
                <a href="estoque.php" data-pt="Estoque" data-en="Stock">Estoque</a>
                <a href="reserva.html" data-pt="Reserva Online" data-en="Online Booking">Reserva Online</a>
                <a href="catalogo.php" data-pt="Catálogo" data-en="Catalog">Catálogo</a>
                <a href="#" data-pt="Financiamento" data-en="Financing">Financiamento</a>
            </div>
            <div class="footer-section">
                <h4 data-pt="Contato" data-en="Contact">Contato</h4>
                <p>(31) 9999-9999</p>
                <p>@vincenzadealership@gmail.com</p>
                <p>Minas Gerais, MG</p>
            </div>
            <div class="footer-section">
                <h4 data-pt="Legal" data-en="Legal">Legal</h4>
                <a href="termos-de-uso.php" data-pt="Termos de Uso" data-en="Terms of Use">Termos de Uso</a>
                <a href="consentimento-dados.php" data-pt="Termos de Consentimento" data-en="Consent Terms">Termos de Consentimento</a>
                <a href="#" data-pt="Política de Cookies" data-en="Cookie Policy">Política de Cookies</a>
            </div>
        </div>
    </div>

    <div id="loginPopup" class="popup-overlay" style="display: none;">
        <div class="popup-content">
            <span class="close-btn" onclick="closeLoginPopup()">&times;</span>
            
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('registro')" data-pt="Registro" data-en="Register">Registro</button>
                <button class="tab-btn" onclick="showTab('login')" data-pt="Login" data-en="Login">Login</button>
            </div>
            
            <?php include 'views/partials/modal_login.php'; ?>
            
        </div>
    </div>

    <!-- Modal de Termos -->
    <div id="termsPopup" class="popup-overlay" style="display: none; z-index: 1001;">
        <div class="terms-popup-content">
            <span class="close-btn" onclick="closeTermsPopup()">&times;</span>
            <h2>Termos e Condições</h2>
            
            <div class="terms-tabs">
                <button class="terms-tab-btn active" onclick="showTermsTab('termos-uso')">Termos de Uso</button>
                <button class="terms-tab-btn" onclick="showTermsTab('consentimento')">Consentimento</button>
            </div>
            
            <div id="termos-uso" class="terms-tab-content active">
                <div class="terms-text">
                    <iframe src="termos-de-uso.php" style="width: 100%; height: 350px; border: none; border-radius: 5px;"></iframe>
                </div>
                <div class="terms-checkbox">
                    <label>
                        <input type="checkbox" id="check-termos-uso">
                        Li e aceito os Termos de Uso
                    </label>
                </div>
            </div>
            
            <div id="consentimento" class="terms-tab-content">
                <div class="terms-text">
                    <iframe src="termos-de-consentimento.php" style="width: 100%; height: 350px; border: none; border-radius: 5px;"></iframe>
                </div>
                <div class="terms-checkbox">
                    <label>
                        <input type="checkbox" id="check-consentimento">
                        Li e aceito os Termos de Consentimento
                    </label>
                </div>
            </div>
            
            <div class="terms-actions">
                <button class="btn-cancel" onclick="closeTermsPopup()">Cancelar</button>
                <button class="btn-accept" onclick="acceptTermsAndRegister()">Aceitar e Cadastrar</button>
            </div>
        </div>
    </div>

    <script>
        function showTermsPopup() {
            document.getElementById('termsPopup').style.display = 'block';
        }
        
        function closeTermsPopup() {
            document.getElementById('termsPopup').style.display = 'none';
        }
        
        function showTermsTab(tabName) {
            document.querySelectorAll('.terms-tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.terms-tab-btn').forEach(btn => btn.classList.remove('active'));
            
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
        
        function acceptTermsAndRegister() {
            const termosCheck = document.getElementById('check-termos-uso').checked;
            const consentimentoCheck = document.getElementById('check-consentimento').checked;
            
            if (!termosCheck || !consentimentoCheck) {
                alert('Você deve aceitar ambos os termos para prosseguir.');
                return;
            }
            
            document.getElementById('aceitar_termos').value = '1';
            document.getElementById('aceitar_consentimento').value = '1';
            
            closeTermsPopup();
            document.querySelector('#registro form').submit();
        }
    </script>

    <style>
        .popup-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; }
        .popup-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 20px; border-radius: 15px; max-width: 400px; width: 90%; max-height: 80vh; overflow-y: auto; }
        
        .terms-popup-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 20px; border-radius: 15px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; }
        .terms-tabs { display: flex; margin-bottom: 20px; border-bottom: 2px solid #eee; }
        .terms-tab-btn { background: none; border: none; padding: 10px 20px; cursor: pointer; font-weight: 600; }
        .terms-tab-btn.active { border-bottom: 2px solid #007bff; color: #007bff; }
        .terms-tab-content { display: none; }
        .terms-tab-content.active { display: block; }
        .terms-text { max-height: 300px; overflow-y: auto; margin-bottom: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px; }
        .terms-checkbox { margin: 15px 0; }
        .terms-checkbox label { display: flex; align-items: center; cursor: pointer; }
        .terms-checkbox input { margin-right: 10px; }
        .terms-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
        .btn-cancel, .btn-accept { padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .btn-cancel { background: #6c757d; color: white; }
        .btn-accept { background: #007bff; color: white; }
        .btn-accept:disabled { background: #ccc; cursor: not-allowed; }
        .close-btn { position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer; color: #000; z-index: 1001; }
        .close-btn:hover { color: #666; }
        .tabs { display: flex; background-color: rgb(255, 232, 206); border-radius: 15px 15px 0 0; }
        .tab-btn { flex: 1; padding: 15px; border: none; background-color: transparent; cursor: pointer; font-size: 16px; font-weight: bold; border-bottom: 2px solid transparent; }
        .tab-btn.active { border-bottom: 2px solid rgb(0, 0, 0); }
        .tab-content { display: none !important; padding: 20px; background-color: rgb(255, 232, 206); border-radius: 0 0 15px 15px; }
        .tab-content.active { display: block !important; }
        #registro, #login { width: 100%; display: flex; flex-direction: column; align-content: space-between; min-height: 50vh; border: none; border-radius: 0; padding: 0; text-align: center; }
    </style>

    <script>
        function openLoginPopup() { document.getElementById('loginPopup').style.display = 'block'; }
        function closeLoginPopup() { document.getElementById('loginPopup').style.display = 'none'; }
        
        function showTab(tabName) {
            document.getElementById('registro').classList.remove('active');
            document.getElementById('login').classList.remove('active');
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            if (event) event.currentTarget.classList.add('active');
        }
        
        window.onclick = function(event) {
            const popup = document.getElementById('loginPopup');
            if (event.target === popup) { closeLoginPopup(); }
        }
    </script>

</body>
</html>