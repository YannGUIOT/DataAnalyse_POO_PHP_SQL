<?php
    include_once 'database.php';
    include_once 'model.php';
    include_once 'view.php';

    class Controller {
        private $db;
        private $model;
        private $view;

        public $datas;
        public $totalS;
        public $totalP;
        public $isOnLive;
        public $streamID;
        public $isParticipate_A;
        public $isParticipate_B;
        public $isParticipate_C;
        public $isParticipate_D;
        public $participantsOnline;

        public function __construct($db, $model, $view) {
            $this->db = $db;
            $this->model = $model;
            $this->view = $view;
            $this->model->createParticipant('A');
            $this->model->createParticipant('B');
            $this->model->createParticipant('C');
            $this->model->createParticipant('D');
        }

        public function addData($dataType) {
            $this->model->insertData($dataType);
        }
        
        public function close() {
            $this->db->closeConnection();
        }

        public function processRequest() {  
            $this->model->getDataByTable("Streams");
            $this->model->getDataByTable("Participants");
            
            $this->totalS = $this->model->getTotalByTable('Streams');
            $this->totalP = $this->model->getTotalByTable('Participants');

            $this->streamID = $this->model->getStreamID();
            $this->isOnLive = $this->model->getIsOnLive($this->streamID);
            $this->participantsOnline = $this->model->getParticipantsOnline($this->streamID);
            $this->isParticipate_A = $this->model->getIsParticipate('A');
            $this->isParticipate_B = $this->model->getIsParticipate('B');
            $this->isParticipate_C = $this->model->getIsParticipate('C');
            $this->isParticipate_D = $this->model->getIsParticipate('D');
        }

        public function globalPage() {
            $this->model->sortData();
            $this->view->displayTopPage();
            $this->view->displayRestPage(
                            $this->totalS, 
                            $this->totalP, 
                            $this->model->dataArrays, 
                            $this->isOnLive, 
                            $this->isParticipate_A, 
                            $this->isParticipate_B, 
                            $this->isParticipate_C, 
                            $this->isParticipate_D,
                            $this->participantsOnline);
        }

        public function getAllData() {
            return $this->model->getAllData();
        }

        public function startNewStream() {
            $this->model->startNewStream();
        }

        public function stopStream() {
            $this->model->stopStream($this->streamID);
        }

        public function startParticipate($participant) {
            $this->model->startParticipate($participant, $this->streamID);
        }

        public function stopParticipate($participant) {
            $this->model->stopParticipate($participant, $this->streamID);
        }

        public function sendLike() {
            $this->model->sendLike();
        }
    }
?>