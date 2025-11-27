
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vincenza</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bakbak+One&family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Oswald:wght@200..700&family=Outfit:wght@100..900&family=Quicksand:wght@300..700&family=Staatliches&display=swap" rel="stylesheet">
    
    <script src="https://kit.fontawesome.com/619d5231f4.js" crossorigin="anonymous"></script>
    
    <link rel="stylesheet" href="inicio.css">
    <link rel="stylesheet" href="style.css">
    
    <script src="inicio.js"></script>
</head>
<body style="margin: 0;">
    
    <div id="upperbar">
        <div id="upper1"> 
            <img id="logo" src="1000018033.png" alt="Logo"> <p style="font-family: 'Quicksand', sans-serif"> Vincenza</p>
        </div>
        
        <div id="upper2">
            <a href="index.php"> <b class="lnk-home" data-pt="início" data-en="home">início</b> <span></span></a>
            <a href="estoque.php"> <b class="lnk2" data-pt="estoque" data-en="stock">estoque</b> <span></span></a>
            
            <?php if(isset($_SESSION['usuario_nome'])): ?>
                <div class="user-menu">
                    <div class="user-info">
                        <i class="fa-regular fa-circle-user fa-lg"></i>
                        <span class="user-name" style="font-family: 'Oswald', sans-serif;">Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
                    </div>
                    <a href="conta.php?acao=logout" class="btn-logout" title="Sair">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </a>
                </div>
            <?php else: ?>
                <a href="#" onclick="openLoginPopup()"> <b class="lnk1" data-pt="registro" data-en="register">conta</b> <span></span></a>
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