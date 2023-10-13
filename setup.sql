CREATE DATABASE IF NOT EXISTS ForceWan3;

USE ForceWan3;

CREATE TABLE IF NOT EXISTS Streams (
  id INT AUTO_INCREMENT PRIMARY KEY,
  is_on_live BOOLEAN DEFAULT TRUE,
  start_at DATETIME DEFAULT NOW(),
  end_at DATETIME,
  participants_online INT DEFAULT 0,
  total_likes INT DEFAULT 0,
  created_at DATETIME DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS Participants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pseudo VARCHAR(30),
  is_participed BOOLEAN DEFAULT FALSE,
  created_at DATETIME DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS StreamParticipations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  stream_id INT,
  participant_id INT,
  start_participate_at DATETIME,
  end_participate_at DATETIME,
  created_at DATETIME DEFAULT NOW(),
  FOREIGN KEY (stream_id) REFERENCES Streams(id),
  FOREIGN KEY (participant_id) REFERENCES Participants(id)
);