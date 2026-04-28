-- =============================================================================
-- Central da Missão — Schema completo (gerado a partir de todas as migrations)
-- Executar no phpMyAdmin após criar o banco de dados vazio.
-- Ordem: respeita dependências de foreign keys.
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1. users
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`                  VARCHAR(255) NOT NULL,
  `phone`                 VARCHAR(255) UNIQUE,
  `email`                 VARCHAR(255) UNIQUE,
  `email_verified_at`     TIMESTAMP NULL DEFAULT NULL,
  `password`              VARCHAR(255) NOT NULL,
  `remember_token`        VARCHAR(100) NULL DEFAULT NULL,

  -- profile fields (migration 2026_03_23_175424)
  `role`                  ENUM('participante','coordenador','administrador') NOT NULL DEFAULT 'participante',
  `city`                  VARCHAR(255) NULL DEFAULT NULL,
  `neighborhood`          VARCHAR(255) NULL DEFAULT NULL,
  `referral_code`         VARCHAR(255) UNIQUE NULL DEFAULT NULL,
  `referred_by`           VARCHAR(255) NULL DEFAULT NULL,
  `points`                INT NOT NULL DEFAULT 0,

  -- expanded profile fields (migration 2026_03_23_175424 + 2026_04_04_080000)
  `date_of_birth`         DATE NULL DEFAULT NULL,
  `religion`              VARCHAR(255) NULL DEFAULT NULL,
  `education_level`       VARCHAR(255) NULL DEFAULT NULL,
  `higher_course`         VARCHAR(255) NULL DEFAULT NULL,
  `profession`            VARCHAR(255) NULL DEFAULT NULL,
  `how_known`             TEXT NULL DEFAULT NULL,
  `first_spokesperson`    VARCHAR(255) NULL DEFAULT NULL,
  `pauta1`                VARCHAR(255) NULL DEFAULT NULL,
  `pauta2`                VARCHAR(255) NULL DEFAULT NULL,
  `pauta3`                VARCHAR(255) NULL DEFAULT NULL,
  `political_ambition`    VARCHAR(255) NULL DEFAULT NULL,
  `current_status`        VARCHAR(255) NULL DEFAULT NULL,
  `profile_completed_at`  TIMESTAMP NULL DEFAULT NULL,

  -- force password change (migration base)
  `force_password_change` TINYINT(1) NOT NULL DEFAULT 0,

  `created_at`            TIMESTAMP NULL DEFAULT NULL,
  `updated_at`            TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. password_reset_tokens
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email`      VARCHAR(255) NOT NULL,
  `token`      VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. sessions
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
  `id`            VARCHAR(255) NOT NULL,
  `user_id`       BIGINT UNSIGNED NULL DEFAULT NULL,
  `ip_address`    VARCHAR(45) NULL DEFAULT NULL,
  `user_agent`    TEXT NULL DEFAULT NULL,
  `payload`       LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. cache
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache` (
  `key`        VARCHAR(255) NOT NULL,
  `value`      MEDIUMTEXT NOT NULL,
  `expiration` BIGINT NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key`        VARCHAR(255) NOT NULL,
  `owner`      VARCHAR(255) NOT NULL,
  `expiration` BIGINT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. jobs / job_batches / failed_jobs
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `jobs` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue`        VARCHAR(255) NOT NULL,
  `payload`      LONGTEXT NOT NULL,
  `attempts`     TINYINT UNSIGNED NOT NULL,
  `reserved_at`  INT UNSIGNED NULL DEFAULT NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at`   INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id`             VARCHAR(255) NOT NULL,
  `name`           VARCHAR(255) NOT NULL,
  `total_jobs`     INT NOT NULL,
  `pending_jobs`   INT NOT NULL,
  `failed_jobs`    INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options`        MEDIUMTEXT NULL DEFAULT NULL,
  `cancelled_at`   INT NULL DEFAULT NULL,
  `created_at`     INT NOT NULL,
  `finished_at`    INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid`       VARCHAR(255) NOT NULL,
  `connection` TEXT NOT NULL,
  `queue`      TEXT NOT NULL,
  `payload`    LONGTEXT NOT NULL,
  `exception`  LONGTEXT NOT NULL,
  `failed_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. activities
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activities` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `type`        ENUM('evento_presencial','denuncia','tarefa_manual','convite') NOT NULL DEFAULT 'evento_presencial',
  `date_time`   DATETIME NOT NULL,
  `deadline`    DATETIME NULL DEFAULT NULL,
  `location`    VARCHAR(255) NULL DEFAULT NULL,
  `points`      INT NOT NULL DEFAULT 0,
  `qr_code`     VARCHAR(255) UNIQUE NULL DEFAULT NULL,
  `banner`      VARCHAR(255) NULL DEFAULT NULL,
  `created_by`  BIGINT UNSIGNED NOT NULL,
  `created_at`  TIMESTAMP NULL DEFAULT NULL,
  `updated_at`  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `activities_created_by_foreign`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. activity_user  (pivot: inscrições + RSVP)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_user` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_id`     BIGINT UNSIGNED NOT NULL,
  `user_id`         BIGINT UNSIGNED NOT NULL,
  `status`          ENUM('confirmado','pendente','rejeitado') NOT NULL DEFAULT 'pendente',
  `confirmed_at`    TIMESTAMP NULL DEFAULT NULL,
  `rsvp_confirmed`  TINYINT(1) NOT NULL DEFAULT 0,
  `did_participate` TINYINT(1) NOT NULL DEFAULT 0,
  `points_awarded`  DECIMAL(8,2) NULL DEFAULT NULL,
  `penalty_applied` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`      TIMESTAMP NULL DEFAULT NULL,
  `updated_at`      TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_user_activity_id_user_id_unique` (`activity_id`, `user_id`),
  KEY `idx_au_penalty_lookup` (`rsvp_confirmed`, `did_participate`, `penalty_applied`),
  CONSTRAINT `activity_user_activity_id_foreign`
    FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_user_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. expanded_form_responses
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `expanded_form_responses` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT UNSIGNED NOT NULL,
  `activity_id` BIGINT UNSIGNED NOT NULL,
  `responses`   JSON NOT NULL,
  `created_at`  TIMESTAMP NULL DEFAULT NULL,
  `updated_at`  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `efr_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `efr_activity_id_foreign`
    FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 9. activity_submissions
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_submissions` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_id`      BIGINT UNSIGNED NOT NULL,
  `user_id`          BIGINT UNSIGNED NOT NULL,
  `file_path`        VARCHAR(255) NOT NULL,
  `original_name`    VARCHAR(255) NOT NULL,
  `mime_type`        VARCHAR(255) NULL DEFAULT NULL,
  `file_size`        BIGINT UNSIGNED NULL DEFAULT NULL,
  `status`           VARCHAR(255) NOT NULL DEFAULT 'pending',
  `points_awarded`   INT NULL DEFAULT NULL,
  `reviewed_by`      BIGINT UNSIGNED NULL DEFAULT NULL,
  `reviewed_at`      TIMESTAMP NULL DEFAULT NULL,
  `submitted_at`     TIMESTAMP NULL DEFAULT NULL,
  `comment`          TEXT NULL DEFAULT NULL,
  `reviewer_comment` TEXT NULL DEFAULT NULL,
  `created_at`       TIMESTAMP NULL DEFAULT NULL,
  `updated_at`       TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_submissions_activity_id_user_id_index` (`activity_id`, `user_id`),
  CONSTRAINT `as_activity_id_foreign`
    FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `as_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `as_reviewed_by_foreign`
    FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10. push_tokens
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `push_tokens` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `token`      VARCHAR(512) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `push_tokens_token_unique` (`token`),
  CONSTRAINT `push_tokens_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 11. migrations (tabela interna do Laravel — necessária para o artisan)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch`     INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
  ('0001_01_01_000000_create_users_table', 1),
  ('0001_01_01_000001_create_cache_table', 1),
  ('0001_01_01_000002_create_jobs_table', 1),
  ('2026_03_23_173244_add_phone_to_users_table', 1),
  ('2026_03_23_175424_add_profile_fields_to_users_table', 1),
  ('2026_03_23_175425_create_activities_table', 1),
  ('2026_03_23_175426_create_activity_user_table', 1),
  ('2026_03_23_235200_create_expanded_form_responses_table', 1),
  ('2026_04_04_023346_add_deadline_to_activities_table', 1),
  ('2026_04_04_080000_add_expanded_profile_fields_to_users_table', 1),
  ('2026_04_06_120000_create_activity_submissions_table', 1),
  ('2026_04_08_120000_add_banner_to_activities_table', 1),
  ('2026_04_08_200000_add_comments_to_activity_submissions_table', 1),
  ('2026_04_22_000000_add_rsvp_fields_to_activity_user_table', 1),
  ('2026_04_25_000000_create_push_tokens_table', 1);

SET FOREIGN_KEY_CHECKS = 1;
