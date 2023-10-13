<?php
    class Model {
        private $db;
        public $dataArrays = [];

        public function __construct($db) {
            $this->db = $db;
        }

        public function insertData($tableName) {
            $conn = $this->db->conn;
            $currentDate = $this->getCurrentDate();
            $insertQuery = "INSERT INTO $tableName (date_time) VALUES ('$currentDate')";
        
            if ($conn->query($insertQuery) === FALSE) {
                error_log("Erreur lors de l'ajout de la ligne : " . $conn->error, 0);
            }
        }
        
        public function getDataByTable($tableName) {
            $conn = $this->db->conn;
            $query = "SELECT * FROM $tableName";
            $result = $conn->query($query);
        
            if ($result !== FALSE && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $this->dataArrays[] = array(
                        'table' => $tableName,
                        'id' => $row['id'], 
                        'date' => $row['created_at'], 
                    );
                }
            }
        }
        
        public function getTotalByTable($tableName) {
            $conn = $this->db->conn;
            $query = "SELECT COUNT(*) as total FROM $tableName";
            $result = $conn->query($query);
        
            if ($result !== FALSE) {
                $row = $result->fetch_assoc();
                return $row['total'];
            }
        }

        public function getIsOnLive($id) {
            if ($id !== 0) {
                $conn = $this->db->conn;
                $query = "SELECT is_on_live FROM Streams WHERE id = $id";
                $result = $conn->query($query);
                if ($result !== FALSE) {
                    $row = $result->fetch_assoc();
                    return ($row['is_on_live'] == 1) ? true : false;
                }
                else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }

        public function createParticipant($pseudo) {
            $conn = $this->db->conn;
            $checkQuery = "SELECT id FROM Participants WHERE pseudo = '$pseudo'";
            $result = $conn->query($checkQuery);
        
            if ($result && $result->num_rows === 0) {
                $insertQuery = "INSERT INTO Participants (pseudo) VALUES ('$pseudo')";
                if ($conn->query($insertQuery) === FALSE) {
                    error_log("Erreur lors de l'ajout de la ligne : " . $conn->error, 0);
                }
            }
        }
        
        public function sortData() {
            usort($this->dataArrays, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }

        public function startNewStream() {
            $conn = $this->db->conn;
            $currentDate = $this->getCurrentDate();
            $insertQuery = "INSERT INTO Streams (is_on_live, start_at, participants_online, total_likes) VALUES (1, '$currentDate', 0, 0)";

            if ($conn->query($insertQuery) === FALSE) {
                error_log("Erreur lors de l'ajout de la ligne : " . $conn->error, 0);
            }
        }

        
        public function getStreamID() {
            $conn = $this->db->conn;
            $getIdQuery = "SELECT id FROM Streams ORDER BY id DESC LIMIT 1";
            $result = $conn->query($getIdQuery);
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['id'];
            } else {
                error_log("Erreur lors de la récupération de l'ID : " . $conn->error, 0);
                return 0;
            }
        }

        public function stopStream($id) {
            $conn = $this->db->conn;
            $currentDate = $this->getCurrentDate();
            $updateQuery = "UPDATE Streams SET is_on_live = FALSE, end_at = '$currentDate' WHERE id = $id";

            if ($conn->query($updateQuery) === FALSE) {
                error_log("Erreur lors de la mise à jour : " . $conn->error, 0);
            }

            $this->stopParticipate('A', $id);
            $this->stopParticipate('B', $id);
            $this->stopParticipate('C', $id);
            $this->stopParticipate('D', $id);
        }

        public function startParticipate($participant, $streamID) {
            $conn = $this->db->conn;
            $currentDate = $this->getCurrentDate();
            $updateQuery = "UPDATE Participants SET is_participed = TRUE WHERE pseudo = '$participant'";
            
            if ($conn->query($updateQuery) === FALSE) {
                error_log("Erreur lors de la mise à jour des participants : " . $conn->error, 0);
                return;
            }
        
            $participantIDQuery = "SELECT id FROM Participants WHERE pseudo = '$participant'";
            $participantResult = $conn->query($participantIDQuery);
            
            if ($participantResult === FALSE) {
                error_log("Erreur lors de la récupération de l'ID du participant : " . $conn->error, 0);
                return;
            }
            
            $row = $participantResult->fetch_assoc();
            $participantID = $row['id'];
        
            $insertQuery = "INSERT INTO StreamParticipations (stream_id, participant_id, start_participate_at) VALUES ($streamID, $participantID, '$currentDate')";
            
            if ($conn->query($insertQuery) === FALSE) {
                error_log("Erreur lors de l'insertion dans StreamParticipations : " . $conn->error, 0);
            }

            $updateParticipantOnline = "UPDATE Streams SET participants_online = participants_online + 1 WHERE id = $streamID";
            if ($conn->query($updateParticipantOnline) === FALSE) {
                error_log("Erreur lors de la mise à jour des participants : " . $conn->error, 0);
                return;
            }
        }
        

        public function stopParticipate($participant, $streamID) {
            $conn = $this->db->conn;
            $currentDate = $this->getCurrentDate();

            $checkParticipationQuery = "SELECT is_participed FROM Participants WHERE pseudo = '$participant'";
            $checkResult = $conn->query($checkParticipationQuery);
            
            if ($checkResult !== FALSE && $checkResult->num_rows > 0) {
                $row = $checkResult->fetch_assoc();
                $isParticiped = $row['is_participed'];
                
                if ($isParticiped) {

                    $updateQuery = "UPDATE Participants SET is_participed = FALSE WHERE pseudo = '$participant'";
                    
                    if ($conn->query($updateQuery) === FALSE) {
                        error_log("Erreur lors de la mise à jour des participants : " . $conn->error, 0);
                        return;
                    }

                    $participantIDQuery = "SELECT id FROM Participants WHERE pseudo = '$participant'";
                    $participantResult = $conn->query($participantIDQuery);
                    
                    if ($participantResult === FALSE) {
                        error_log("Erreur lors de la récupération de l'ID du participant : " . $conn->error, 0);
                        return;
                    }
                    
                    $row = $participantResult->fetch_assoc();
                    $participantID = $row['id'];
                
                    $updateAllQuery = "UPDATE StreamParticipations 
                                        SET end_participate_at = '$currentDate' 
                                        WHERE stream_id = $streamID 
                                        AND participant_id = $participantID 
                                        AND created_at = (
                                            SELECT created_at
                                            FROM (
                                                SELECT created_at
                                                FROM StreamParticipations
                                                WHERE stream_id = $streamID 
                                                AND participant_id = $participantID
                                                ORDER BY ABS(TIMESTAMPDIFF(SECOND, created_at, '$currentDate')) ASC
                                                LIMIT 1
                                            ) AS closest_created_at)";
                
                    if ($conn->query($updateAllQuery) === FALSE) {
                        error_log("Erreur lors de la mise à jour de StreamParticipations : " . $conn->error, 0);
                    }

                    $updateParticipantOnline = "UPDATE Streams SET participants_online = participants_online - 1 WHERE id = $streamID";
                    if ($conn->query($updateParticipantOnline) === FALSE) {
                        error_log("Erreur lors de la mise à jour des participants : " . $conn->error, 0);
                        return;
                    }
                }
            }
        }
        
        public function sendLike() {
            $conn = $this->db->conn;
            $streamID = $this->getStreamID();
            $updateLikesQuery = "UPDATE Streams SET total_likes = total_likes + 1 WHERE id = $streamID";
    
            if ($conn->query($updateLikesQuery) === FALSE) {
                error_log("Erreur lors de la mise à jour des likes : " . $conn->error, 0);
            }
        }

        public function getIsParticipate($participant) {
            $conn = $this->db->conn;
            $query = "SELECT is_participed FROM Participants WHERE pseudo = '$participant'";
            $result = $conn->query($query);
            if ($result !== FALSE) {
                $row = $result->fetch_assoc();
                return ($row['is_participed'] == 1) ? TRUE : FALSE;
            }
        }

        public function getParticipantsOnline($id) {
            $conn = $this->db->conn;
            $query = "SELECT participants_online FROM Streams WHERE id = $id";
            $result = $conn->query($query);
            if ($result !== FALSE) {
                $row = $result->fetch_assoc();
                return $row['participants_online'];
            }
        }

        public function getAllData() {
            $conn = $this->db->conn;
            
            $streamsData = $this->getTableData($conn, 'Streams');
            $participantsData = $this->getTableData($conn, 'Participants');
            $streamParticipationsData = $this->getTableData($conn, 'StreamParticipations');
            
            $allData = array(
                'Streams' => $streamsData,
                'Participants' => $participantsData,
                'StreamParticipations' => $streamParticipationsData
            );
            
            return $allData;
        }
        
        private function getTableData($conn, $tableName) {
            $tableData = array();
            $query = "SELECT * FROM $tableName";
            $result = $conn->query($query);
            
            if ($result !== FALSE && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $tableData[] = $row;
                }
            }
            
            return $tableData;
        }
        
        private function getCurrentDate() {
            date_default_timezone_set('Europe/Paris');
            return date("Y-m-d H:i:s");
        }
    }
?>