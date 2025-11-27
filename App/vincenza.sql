create database vincenza;

use vincenza;

CREATE TABLE veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(50),
    modelo VARCHAR(50),
    ano INT,
    combustivel VARCHAR(30),
    cambio VARCHAR(30),
    preco DECIMAL(10,2),
    foto VARCHAR(255) 
);

INSERT INTO veiculos (marca, modelo, ano, combustivel, cambio, preco, pasta, total_imagens, foto) VALUES 

('Volkswagen', 'Amarok V6', 2023, 'Diesel', 'Automático', 298000.00, 'amarok', 31, 'amarok/1.webp'),

('Audi', 'Q3 Black', 2023, 'Gasolina', 'Automático', 279990.00, 'audiq3', 33, 'audiq3/1.webp'),

('Ford', 'Bronco Sport', 2022, 'Gasolina', 'Automático', 255000.00, 'bronco', 32, 'bronco/1.webp'),

('Honda', 'Civic Touring', 2021, 'Gasolina', 'CVT', 145000.00, 'civic', 31, 'civic/1.webp'),

('Jeep', 'Compass S', 2023, 'Flex', 'Automático', 195000.00, 'compass', 32, 'compass/1.webp'),

('Audi', 'RS e-tron GT', 2024, 'Elétrico', 'Automático', 950000.00, 'etrongt', 32, 'etrongt/1.webp'),

('Mercedes-Benz', 'EQS 450', 2023, 'Elétrico', 'Automático', 1200000.00, 'eqs', 31, 'eqs/1.webp'),

('Fiat', '500e Icon', 2022, 'Elétrico', 'Automático', 215000.00, 'fiat500', 29, 'fiat500/1.webp'),

('Honda', 'HR-V Touring', 2023, 'Gasolina', 'CVT', 188000.00, 'hondahrv', 32, 'hondahrv/1.webp'),

('Mercedes-Benz', 'A200', 2023, 'Gasolina', 'Automático', 310000.00, 'mercedesa180', 32, 'mercedesa180/1.webp'),

('Volkswagen', 'Polo Highline', 2023, 'Flex', 'Automático', 105000.00, 'polo', 31, 'polo/1.webp'),

('BMW', 'X4 M40i', 2022, 'Gasolina', 'Automático', 580000.00, 'x4', 31, 'x4/1.webp');


ALTER TABLE veiculos
ADD COLUMN pasta VARCHAR(255) AFTER foto,       
ADD COLUMN total_imagens INT DEFAULT 1 AFTER pasta; 


-- abrir trava
SET SQL_SAFE_UPDATES = 0;


UPDATE veiculos 
SET pasta = 'amarok', total_imagens = 31, foto = 'amarok/1.webp' 
WHERE modelo LIKE '%amarok%';


UPDATE veiculos 
SET pasta = 'audiq3', total_imagens = 33, foto = 'audiq3/1.webp' 
WHERE modelo LIKE '%Q3%';


UPDATE veiculos 
SET pasta = 'bronco', total_imagens = 32, foto = 'bronco/1.webp' 
WHERE modelo LIKE '%Bronco%';


UPDATE veiculos 
SET pasta = 'civic', total_imagens = 31, foto = 'civic/1.webp' 
WHERE modelo LIKE '%Civic%';


UPDATE veiculos 
SET pasta = 'compass', total_imagens = 32, foto = 'compass/1.webp' 
WHERE modelo LIKE '%Compass%';


UPDATE veiculos 
SET pasta = 'etrongt', total_imagens = 32, foto = 'etrongt/1.webp' 
WHERE modelo LIKE '%E-Tron%';


UPDATE veiculos 
SET pasta = 'eqs', total_imagens = 31, foto = 'eqs/1.webp' 
WHERE modelo LIKE '%EQS%';


UPDATE veiculos 
SET pasta = 'fiat500', total_imagens = 29, foto = 'fiat500/1.webp' 
WHERE modelo LIKE '%500%';


UPDATE veiculos 
SET pasta = 'hondahrv', total_imagens = 32, foto = 'hondahrv/1.webp' 
WHERE modelo LIKE '%HR-V%';


UPDATE veiculos 
SET pasta = 'mercedesa180', total_imagens = 32, foto = 'mercedesa180/1.webp' 
WHERE modelo LIKE '%A180%';


UPDATE veiculos 
SET pasta = 'polo', total_imagens = 31, foto = 'polo/1.webp' 
WHERE modelo LIKE '%Polo%';


UPDATE veiculos 
SET pasta = 'x4', total_imagens = 31, foto = 'x4/1.webp' 
WHERE modelo LIKE '%X4%';

-- fechar trava
SET SQL_SAFE_UPDATES = 1;


ALTER TABLE veiculos
ADD COLUMN categoria VARCHAR(50) AFTER combustivel;




ALTER TABLE veiculos
ADD COLUMN categoria VARCHAR(50) AFTER combustivel;

SET SQL_SAFE_UPDATES = 0;


UPDATE veiculos 
SET categoria = 'Luxury Sport' 
WHERE modelo IN ('RS e-tron GT', 'EQS 450', 'X4 M40i');


UPDATE veiculos 
SET categoria = 'Luxury Sedan' 
WHERE modelo LIKE '%A200%';


UPDATE veiculos 
SET categoria = 'Pickup' 
WHERE modelo LIKE '%Amarok%';


UPDATE veiculos 
SET categoria = 'SUV' 
WHERE modelo IN ('Q3 Black', 'Bronco Sport', 'Compass S', 'HR-V Touring');


UPDATE veiculos 
SET categoria = 'Sedan' 
WHERE modelo LIKE '%Civic%';


UPDATE veiculos 
SET categoria = 'Hatch' 
WHERE modelo IN ('Polo Highline', '500e Icon');

SET SQL_SAFE_UPDATES = 1;

CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    veiculo_id INT NOT NULL,
    data_reserva DATE NOT NULL,
    horario TIME NOT NULL,
    status ENUM('pendente', 'confirmada', 'cancelada') DEFAULT 'pendente',
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (veiculo_id) REFERENCES veiculos(id)
);

ALTER TABLE reservas 
ADD COLUMN valor_total DECIMAL(10,2) DEFAULT 0,
ADD COLUMN dias INT DEFAULT 1;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    data_nascimento DATE,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    senha VARCHAR(255) NOT NULL 
);



CREATE TABLE email_preferences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT UNIQUE,
    emails_promocionais BOOLEAN DEFAULT 1,
    tipos_email JSON,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);


CREATE TABLE email_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    tipo_email VARCHAR(50),
    assunto VARCHAR(255),
    enviado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('enviado', 'erro')
);

ALTER TABLE email_logs ADD COLUMN conteudo TEXT;




CREATE TABLE IF NOT EXISTS propostas_financiamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    veiculo_id INT NOT NULL,
    cliente_nome VARCHAR(255) NOT NULL,
    cliente_email VARCHAR(255) NOT NULL,
    cliente_telefone VARCHAR(20),
    tipo_pagamento ENUM('financiamento', 'debito_vista') NOT NULL,
    valor_entrada DECIMAL(10,2) DEFAULT 0,
    prazo_meses INT DEFAULT NULL,
    taxa_juros DECIMAL(5,2) DEFAULT NULL,
    valor_parcela DECIMAL(10,2) DEFAULT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    status ENUM('pendente', 'aprovada', 'rejeitada') DEFAULT 'pendente',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS avaliacoes_veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proposta_id INT,
    modelo VARCHAR(255) NOT NULL,
    quilometragem INT NOT NULL,
    valor_estimado DECIMAL(10,2) NOT NULL,
    status ENUM('pendente', 'aprovada', 'rejeitada') DEFAULT 'pendente',
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proposta_id) REFERENCES propostas_financiamento(id) ON DELETE SET NULL
);


CREATE TABLE IF NOT EXISTS fotos_avaliacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    avaliacao_id INT NOT NULL,
    nome_arquivo VARCHAR(255) NOT NULL,
    caminho_arquivo VARCHAR(500) NOT NULL,
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (avaliacao_id) REFERENCES avaliacoes_veiculos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS inspecoes_veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    veiculo_id INT NOT NULL,
    cliente_nome VARCHAR(255) NOT NULL,
    cliente_email VARCHAR(255) NOT NULL,
    cliente_telefone VARCHAR(20),
    data_agendada DATE,
    hora_agendada TIME,
    status ENUM('agendada', 'realizada', 'cancelada') DEFAULT 'agendada',
    resultado TEXT,
    custo DECIMAL(10,2) DEFAULT 150.00,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


SELECT * FROM usuarios;
SELECT * FROM usuarios;
SELECT * FROM reservas;

