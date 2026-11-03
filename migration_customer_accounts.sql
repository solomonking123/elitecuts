-- Run this once in phpMyAdmin with the elite-cuts database selected.
ALTER TABLE customers
  ADD COLUMN email VARCHAR(190) NULL AFTER phone,
  ADD COLUMN password_hash VARCHAR(255) NULL AFTER email,
  ADD UNIQUE KEY uq_customers_email (email);
