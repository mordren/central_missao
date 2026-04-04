-- SQL para adicionar campos de perfil expandido e deadline
-- Faça backup do banco antes de rodar estes comandos.

-- Adicionar coluna deadline à tabela activities (se necessário)
ALTER TABLE `activities` ADD COLUMN `deadline` DATETIME NULL AFTER `date_time`;

-- Campos de perfil expandido para tabela users
ALTER TABLE `users` ADD COLUMN `date_of_birth` DATE NULL AFTER `email`;
ALTER TABLE `users` ADD COLUMN `religion` VARCHAR(255) NULL AFTER `neighborhood`;
ALTER TABLE `users` ADD COLUMN `education_level` VARCHAR(255) NULL AFTER `religion`;
ALTER TABLE `users` ADD COLUMN `higher_course` VARCHAR(255) NULL AFTER `education_level`;
ALTER TABLE `users` ADD COLUMN `profession` VARCHAR(255) NULL AFTER `higher_course`;
ALTER TABLE `users` ADD COLUMN `how_known` TEXT NULL AFTER `profession`;
ALTER TABLE `users` ADD COLUMN `first_spokesperson` VARCHAR(255) NULL AFTER `how_known`;
ALTER TABLE `users` ADD COLUMN `pauta1` TEXT NULL AFTER `first_spokesperson`;
ALTER TABLE `users` ADD COLUMN `pauta2` TEXT NULL AFTER `pauta1`;
ALTER TABLE `users` ADD COLUMN `pauta3` VARCHAR(255) NULL AFTER `pauta2`;
ALTER TABLE `users` ADD COLUMN `political_ambition` VARCHAR(255) NULL AFTER `pauta3`;
ALTER TABLE `users` ADD COLUMN `current_status` VARCHAR(255) NULL AFTER `political_ambition`;
ALTER TABLE `users` ADD COLUMN `profile_completed_at` TIMESTAMP NULL AFTER `current_status`;

-- Nota: Se algum comando falhar por coluna duplicada, ignore ou remova a linha correspondente.
