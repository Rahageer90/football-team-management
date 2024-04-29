CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','coach','player','physio','doctor') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `contact_info` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `accounts` (`account_id`, `username`, `password`, `email`, `role`, `created_at`, `name`, `position`, `contact_info`) VALUES
(1, 'admin', '12345678', 'admin@example.com', 'admin', '2024-04-19 10:02:53', NULL, NULL, NULL),
(2, 'player', '12345678', 'player1@example.com', 'player', '2024-04-19 10:14:51', 'Rahim', NULL, NULL),
(3, 'physio', '12345678', 'physio@example.com', 'physio', '2024-04-19 10:15:31', NULL, NULL, NULL),
(4, 'doctor', '12345678', 'doctor@example.com', 'doctor', '2024-04-19 10:15:58', NULL, NULL, NULL),
(5, 'coach', '12345678', 'coach@example.com', 'coach', '2024-04-19 10:16:17', NULL, NULL, NULL);



CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `player_id` int(11) DEFAULT NULL,
  `doctor_or_physio_id` int(11) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `diagnosis` varchar(255) DEFAULT NULL,
  `treatment` varchar(255) DEFAULT NULL,
  `estimated_recovery_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `balanced_diet` (
  `player_id` int(11) NOT NULL,
  `food_details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `balanced_diet` (`player_id`, `food_details`) VALUES
(2, 'dfszfczsfdsadf');



CREATE TABLE `injuries` (
  `injury_id` int(11) NOT NULL,
  `player_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date_of_injury` date DEFAULT NULL,
  `status` enum('injured','not injured') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `matches` (
  `match_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `opponent` varchar(100) DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `result` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `match_lineups` (
  `match_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `position_played` varchar(100) DEFAULT NULL,
  `minutes_played` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `training_sessions` (
  `training_session_id` int(11) NOT NULL,
  `coach_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `focus_areas` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);


ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `appointments_ibfk_1` (`player_id`),
  ADD KEY `appointments_ibfk_2` (`doctor_or_physio_id`);


ALTER TABLE `balanced_diet`
  ADD PRIMARY KEY (`player_id`);


ALTER TABLE `injuries`
  ADD PRIMARY KEY (`injury_id`),
  ADD KEY `injuries_ibfk_1` (`player_id`);


ALTER TABLE `matches`
  ADD PRIMARY KEY (`match_id`);


ALTER TABLE `match_lineups`
  ADD PRIMARY KEY (`match_id`,`player_id`),
  ADD KEY `match_lineups_ibfk_2` (`player_id`);


ALTER TABLE `training_sessions`
  ADD PRIMARY KEY (`training_session_id`),
  ADD KEY `training_sessions_ibfk_1` (`coach_id`);


ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `injuries`
  MODIFY `injury_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `matches`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `training_sessions`
  MODIFY `training_session_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `accounts` (`Account_ID`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_or_physio_id`) REFERENCES `accounts` (`Account_ID`);


ALTER TABLE `balanced_diet`
  ADD CONSTRAINT `balanced_diet_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `accounts` (`Account_ID`);


ALTER TABLE `injuries`
  ADD CONSTRAINT `injuries_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `accounts` (`Account_ID`);


ALTER TABLE `match_lineups`
  ADD CONSTRAINT `match_lineups_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`Match_ID`),
  ADD CONSTRAINT `match_lineups_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `accounts` (`Account_ID`);


ALTER TABLE `training_sessions`
  ADD CONSTRAINT `training_sessions_ibfk_1` FOREIGN KEY (`coach_id`) REFERENCES `accounts` (`Account_ID`);
COMMIT;

ALTER TABLE `matches`
ADD COLUMN `time` TIME DEFAULT NULL;

ALTER TABLE `training_sessions`
ADD COLUMN `time` TIME DEFAULT NULL;

ALTER TABLE match_lineups
ADD COLUMN goals_scored int(11) DEFAULT NULL;
