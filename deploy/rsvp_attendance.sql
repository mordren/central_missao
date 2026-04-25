-- ============================================================
-- RSVP / Attendance System — Raw SQL (phpMyAdmin-ready)
-- SEGURO para re-executar: usa IF NOT EXISTS em tudo.
-- Não dá erro se as colunas/index já existirem.
-- ============================================================

-- Cada ALTER é separado para que um não bloqueie o outro
ALTER TABLE `activity_user` ADD COLUMN IF NOT EXISTS `rsvp_confirmed`  TINYINT(1)   NOT NULL DEFAULT 0    AFTER `confirmed_at`;
ALTER TABLE `activity_user` ADD COLUMN IF NOT EXISTS `did_participate` TINYINT(1)   NOT NULL DEFAULT 0    AFTER `rsvp_confirmed`;
ALTER TABLE `activity_user` ADD COLUMN IF NOT EXISTS `points_awarded`  DECIMAL(8,2) NULL                  AFTER `did_participate`;
ALTER TABLE `activity_user` ADD COLUMN IF NOT EXISTS `penalty_applied` TINYINT(1)   NOT NULL DEFAULT 0    AFTER `points_awarded`;

-- Index (seguro re-executar)
CREATE INDEX IF NOT EXISTS `idx_au_penalty_lookup`
    ON `activity_user` (`rsvp_confirmed`, `did_participate`, `penalty_applied`);



-- -------------------------------------------------------
-- 3. Reference: full CREATE TABLE (existing + new columns)
--    Use this if you need to recreate the table from scratch.
-- -------------------------------------------------------
/*
CREATE TABLE `activity_user` (
    `id`              BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `activity_id`     BIGINT UNSIGNED   NOT NULL,
    `user_id`         BIGINT UNSIGNED   NOT NULL,
    `status`          ENUM('confirmado','pendente','rejeitado')
                                        NOT NULL DEFAULT 'pendente',
    `confirmed_at`    TIMESTAMP         NULL,
    `rsvp_confirmed`  TINYINT(1)        NOT NULL DEFAULT 0,
    `did_participate` TINYINT(1)        NOT NULL DEFAULT 0,
    `points_awarded`  DECIMAL(8,2)      NULL,
    `penalty_applied` TINYINT(1)        NOT NULL DEFAULT 0,
    `created_at`      TIMESTAMP         NULL,
    `updated_at`      TIMESTAMP         NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `activity_user_activity_id_user_id_unique` (`activity_id`, `user_id`),
    KEY `idx_au_penalty_lookup` (`rsvp_confirmed`, `did_participate`, `penalty_applied`),
    CONSTRAINT `activity_user_activity_id_foreign`
        FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
    CONSTRAINT `activity_user_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/


-- ============================================================
-- Example / Utility Queries
-- ============================================================

-- A) Penalty candidates: RSVPed but never showed up, mission >24 h old
SELECT
    au.user_id,
    u.name                          AS user_name,
    au.activity_id,
    a.title                         AS mission_title,
    CEIL(a.points * 0.5)            AS penalty_points
FROM activity_user au
JOIN activities a ON a.id  = au.activity_id
JOIN users      u ON u.id  = au.user_id
WHERE a.date_time       <= DATE_SUB(NOW(), INTERVAL 24 HOUR)
  AND au.rsvp_confirmed  = 1
  AND au.did_participate  = 0
  AND au.penalty_applied  = 0;

-- B) Count RSVPs per activity
SELECT
    a.id,
    a.title,
    COUNT(au.user_id) AS rsvp_count
FROM activities a
LEFT JOIN activity_user au
       ON au.activity_id = a.id
      AND au.rsvp_confirmed = 1
GROUP BY a.id, a.title;

-- C) Users who earned double points (RSVPed + participated)
SELECT
    u.id,
    u.name,
    au.activity_id,
    au.points_awarded
FROM activity_user au
JOIN users u ON u.id = au.user_id
WHERE au.rsvp_confirmed  = 1
  AND au.did_participate  = 1;

-- D) Check RSVP / participation status for one user + activity
SELECT
    rsvp_confirmed,
    did_participate,
    points_awarded,
    penalty_applied
FROM activity_user
WHERE user_id    = 1   -- replace with actual user_id
  AND activity_id = 5;  -- replace with actual activity_id
