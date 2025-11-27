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
                <a href="reserva.php" data-pt="Reserva Online" data-en="Online Booking">Reserva Online</a>
                <a href="catalogo.php" data-pt="Catálogo" data-en="Catalog">Catálogo</a>
                <a href="financeamento.php" data-pt="Financiamento" data-en="Financing">Financiamento</a>
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

    <style>
        .popup-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; }
        .popup-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 0; border-radius: 15px; max-width: 450px; width: 90%; max-height: 85vh; overflow-y: auto; border: 2px solid rgb(0, 0, 0); }
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