-- Run this once in phpMyAdmin with the elite-cuts database selected.
ALTER TABLE appointments
ADD COLUMN cancellation_requested TINYINT(1) NOT NULL DEFAULT 0;
