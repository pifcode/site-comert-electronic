USE pescarul_expert;

-- Crearea tabelului pentru produse
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(50),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserarea produselor inițiale
INSERT INTO products (name, description, price, image, category, stock) VALUES
('Cârlig Professional', 'Cârlig de înaltă calitate pentru pescuit profesional', 15.00, 'imagini/carlig.jpg', 'carlige', 100),
('Fir Premium', 'Fir rezistent pentru pescuit în ape adânci', 45.00, 'imagini/fir.jpg', 'fire', 50),
('Lansetă Pro', 'Lansetă telescopică pentru pescuit la distanță', 250.00, 'imagini/lanseta.jpg', 'lansete', 30),
('Mulinetă Expert', 'Mulinetă cu rulmenți pentru pescuit de performanță', 180.00, 'imagini/mulineta.jpg', 'mulinete', 40),
('Năvod Special', 'Năvod special pentru pescuit în ape adânci', 95.00, 'imagini/navod.jpg', 'accesorii', 25);

-- Crearea tabelului pentru comenzi
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Crearea tabelului pentru detaliile comenzilor
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
