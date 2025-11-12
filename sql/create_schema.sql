-- Create clients table
CREATE TABLE IF NOT EXISTS clients
(
    id
    INT
    AUTO_INCREMENT
    PRIMARY
    KEY,
    name
    VARCHAR
(
    255
) NOT NULL,
    email VARCHAR
(
    255
),
    phone VARCHAR
(
    50
),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

-- Create invoices table
CREATE TABLE IF NOT EXISTS invoices
(
    id
    INT
    AUTO_INCREMENT
    PRIMARY
    KEY,
    invoice_number
    VARCHAR
(
    50
) UNIQUE NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE,
    client_id INT NOT NULL,
    subtotal DECIMAL
(
    10,
    2
) NOT NULL DEFAULT 0.00,
    tax_rate DECIMAL
(
    5,
    2
) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL
(
    10,
    2
) NOT NULL DEFAULT 0.00,
    total DECIMAL
(
    10,
    2
) NOT NULL DEFAULT 0.00,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY
(
    client_id
) REFERENCES clients
(
    id
)
                                                   ON DELETE RESTRICT
    );

-- Create invoice_items table
CREATE TABLE IF NOT EXISTS invoice_items
(
    id
    INT
    AUTO_INCREMENT
    PRIMARY
    KEY,
    invoice_id
    INT
    NOT
    NULL,
    description
    TEXT
    NOT
    NULL,
    amount
    DECIMAL
(
    10,
    2
) NOT NULL,
    is_taxed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY
(
    invoice_id
) REFERENCES invoices
(
    id
) ON DELETE CASCADE
    );

-- Insert sample clients
INSERT INTO clients (name, email, phone, address)
VALUES ('Client 1', 'invoices@client1.com', '+271231234', '123 Client 1 Street, Cape Town, 7000'),
       ('Client 2', 'invoices@client2.com', '+279876543', '987 Client 2 Street, Cape Town, 7000');
