ALTER TABLE orders
ADD COLUMN payment_id VARCHAR(255) NOT NULL AFTER status,
ADD COLUMN payment_status VARCHAR(50) NOT NULL AFTER payment_id,
ADD COLUMN currency VARCHAR(3) NOT NULL DEFAULT 'EUR' AFTER shipping_postal_code,
ADD COLUMN amount_eur DECIMAL(10,2) NOT NULL AFTER currency;
