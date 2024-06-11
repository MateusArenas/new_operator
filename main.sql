CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) NOT NULL,
    type INTEGER DEFAULT 1,
    slack_id VARCHAR(50),
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    image_url VARCHAR(255),
    session VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tickets (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    reason INTEGER NOT NULL,
    description TEXT,
    status INTEGER DEFAULT 1,
    channel_id VARCHAR(50),
    user_id INTEGER,
    operator_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (operator_id) REFERENCES users(id)
);

INSERT INTO users (nome, email, password, slack_id) 
VALUES ('Mateus Arenas', 'mateusarenas97@gmail.com', '$2y$12$3qetARAXd6NJZwlDxgbjRu2Z4yPALrtJWbOg9uI4GMiCO7iG6DSqO', 'D077FM5BPFE')
