-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-12-10 10:21:55
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `group_work`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `reservations`
--

CREATE TABLE `reservations` (
  `reserve_id` int(11) NOT NULL COMMENT '予約ID (自動連番 主キー）',
  `user_id` int(11) NOT NULL COMMENT 'usersテーブルのuser_idと紐づけ',
  `reserve_date` date NOT NULL COMMENT '予約日',
  `slot_id` int(11) NOT NULL COMMENT 'time_slotsテーブルのslot_idと紐づけ',
  `seat_type` enum('counter','table','zashiki') NOT NULL COMMENT '席タイプ（counter、table、zashiki）',
  `num_people` int(11) NOT NULL COMMENT '予約人数',
  `created_at` date NOT NULL COMMENT '予約作成日時'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- テーブルのデータのダンプ `reservations`
--

INSERT INTO `reservations` (`reserve_id`, `user_id`, `reserve_date`, `slot_id`, `seat_type`, `num_people`, `created_at`) VALUES
(9, 18, '2025-12-05', 1, 'table', 11, '2025-12-04'),
(10, 13, '2025-12-19', 1, 'zashiki', 2, '2025-12-04'),
(12, 13, '2025-12-10', 4, 'counter', 3, '2025-12-04'),
(13, 13, '2025-12-11', 4, 'zashiki', 15, '2025-12-04'),
(14, 13, '2025-12-06', 1, 'table', 9, '2025-12-05'),
(15, 13, '2025-12-10', 5, 'table', 3, '2025-12-05'),
(16, 13, '2025-12-11', 5, 'counter', 1, '2025-12-10'),
(17, 13, '2025-12-11', 7, 'counter', 1, '2025-12-10');

-- --------------------------------------------------------

--
-- テーブルの構造 `time_slots`
--

CREATE TABLE `time_slots` (
  `slot_id` int(11) NOT NULL COMMENT '時間枠ID（自動連番 主キー）',
  `slot_time` time NOT NULL COMMENT '時間枠',
  `max_counter` int(11) NOT NULL DEFAULT 6 COMMENT 'カウンターの席数',
  `max_table` int(11) NOT NULL DEFAULT 32 COMMENT 'テーブルの席数',
  `max_zashiki` int(11) NOT NULL DEFAULT 24 COMMENT '座敷の席数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- テーブルのデータのダンプ `time_slots`
--

INSERT INTO `time_slots` (`slot_id`, `slot_time`, `max_counter`, `max_table`, `max_zashiki`) VALUES
(1, '17:00:00', 6, 32, 24),
(4, '17:30:00', 6, 32, 24),
(5, '18:00:00', 6, 32, 24),
(6, '18:30:00', 6, 32, 24),
(7, '19:00:00', 6, 32, 24),
(8, '19:30:00', 6, 32, 24),
(9, '20:00:00', 6, 32, 24),
(10, '20:30:00', 6, 32, 24),
(11, '21:00:00', 6, 32, 24),
(12, '21:30:00', 6, 32, 24),
(13, '22:00:00', 6, 32, 24),
(14, '22:30:00', 6, 32, 24);

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL COMMENT '会員ID（主キー・自動連番）',
  `name` varchar(255) NOT NULL COMMENT '氏名',
  `email` varchar(255) NOT NULL COMMENT 'メールアドレス（ログイン用）',
  `password` varchar(255) NOT NULL COMMENT 'パスワード',
  `tel` varchar(255) NOT NULL COMMENT '電話番号',
  `created_at` date NOT NULL DEFAULT current_timestamp() COMMENT '登録日'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `tel`, `created_at`) VALUES
(13, 'テスト太郎', 'test@gmail.com', '$2y$10$Imx/hT4h5JjXXnIU4mqFJuMrqiahkxvFPHk.vtNJ2PJ6NwPQeVsR2', '00012345678', '2025-12-04'),
(18, 'テスト花子', 't@gmail.com', '$2y$10$.Wel7X3vySOQ/.12WqGKteqxFJlEZ6kdRLsoGPYWmGUvACsJ44Pxa', '00000000000', '2025-12-04');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reserve_id`),
  ADD KEY `fk_reservations_user` (`user_id`),
  ADD KEY `fk_reservations_slot` (`slot_id`);

--
-- テーブルのインデックス `time_slots`
--
ALTER TABLE `time_slots`
  ADD PRIMARY KEY (`slot_id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reserve_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '予約ID (自動連番 主キー）', AUTO_INCREMENT=18;

--
-- テーブルの AUTO_INCREMENT `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `slot_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '時間枠ID（自動連番 主キー）', AUTO_INCREMENT=15;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '会員ID（主キー・自動連番）', AUTO_INCREMENT=21;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_res_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reservations_slot` FOREIGN KEY (`slot_id`) REFERENCES `time_slots` (`slot_id`),
  ADD CONSTRAINT `fk_reservations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
